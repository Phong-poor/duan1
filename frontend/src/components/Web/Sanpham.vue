<template>
  <div class="nike-page">
    <HeaderWeb />

    <main class="container">

      <!-- BANNER -->
      <section class="brand-banner">
        <div class="slide-container" ref="slideContainer">
          <div class="slide active">
            <img :src="bannerSlide1" />
            <h1>THẾ GIỚI CỦA NIKE AIR</h1>
          </div>
          <div class="slide">
            <img :src="bannerSlide2" />
            <h1>ĐẲNG CẤP CÔNG NGHỆ NIKE</h1>
          </div>
          <div class="slide">
            <img :src="bannerSlide3" />
            <h1>CHẠY BỨT TỐC – AIR MAX</h1>
          </div>
        </div>
      </section>

      <h2 class="section-title">Sản phẩm</h2>
      <!-- FILTER -->
      <div class="filter-bar">
        <div>
          <label>Sắp xếp theo:</label>
          <select v-model="sortBy" @change="applyFilters">
            <option value="moi_nhat">Mới nhất</option>
            <option value="gia_tang">Giá tăng dần</option>
            <option value="gia_giam">Giá giảm dần</option>
          </select>
        </div>

        <div class="filter-inline">
          <label>Loại:</label>
          <select v-model="selectedCategory" @change="applyFilters">
            <option :value="null">Tất cả</option>
            <option v-for="cat in categories" :key="cat.id_danhmuc" :value="cat">{{ cat.tenDM }}</option>
          </select>
        </div>
      </div>

      <!-- ======= LAYOUT 2 CỘT ======= -->
      <div class="layout-2col">

        <!-- CỘT TRÁI -->
        <aside class="left-menu">
          <h3>Danh mục sản phẩm</h3>
          <ul>
            <li @click="chooseBrand(null)" :class="{ active: selectedBrand === null }">
              Tất cả
            </li>
            <li v-for="item in brands" :key="item.id_thuonghieu" @click="chooseBrand(item)"
              :class="{ active: selectedBrand && selectedBrand.id_thuonghieu === item.id_thuonghieu }">
              {{ item.tenTH }}
            </li>
          </ul>
        </aside>



        <!-- CỘT PHẢI -->
        <section class="right-content">

          <!-- GRID SẢN PHẨM -->
          <section class="product-grid">
            <div class="product-card" v-for="p in products" :key="p.id_sanpham">
              <img :src="`http://localhost/duan1/backend/${p.hinhAnhgoc}`" @error="$event.target.src = imgSale1" />
              <h3>{{ p.tenSP }}</h3>
              <p v-if="p.coGiamGia">
                <span style="text-decoration: line-through; color: #999; font-size: 0.9em; margin-right: 5px;">
                  {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(p.giaSP) }}
                </span>
                <span style="color: #d20505;">
                  {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(p.giaSauGiam) }}
                </span>
              </p>
              <p v-else>
                {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(p.giaSP) }}
              </p>

              <div class="product-actions">
                <button class="action-btn" @click="goBuyNow(p.id_sanpham)">
                  Mua ngay
                </button>

                <RouterLink :to="`/ChiTiet?id=${p.id_sanpham}`" class="action-btn secondary">
                  Chi tiết
                </RouterLink>
              </div>

            </div>
          </section>

          <!-- PHÂN TRANG -->
          <div class="pagination">
            <button @click.prevent="changePage(currentPage - 1)" :disabled="currentPage === 1">«</button>

            <button v-for="page in totalPages" :key="page" @click.prevent="changePage(page)"
              :class="{ active: currentPage === page }">
              {{ page }}
            </button>

            <button @click.prevent="changePage(currentPage + 1)" :disabled="currentPage === totalPages">»</button>
          </div>

        </section>
      </div>
    </main>
    <Thanhtoanmini v-if="showMini" :id_sanpham="miniID" @close="showMini = false" />


    <footerWeb />
  </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref, computed, watch } from "vue";
import { useRouter, useRoute } from "vue-router";
import Thanhtoanmini from "../Web/Thanhtoanmini.vue";

const router = useRouter();
const route = useRoute();
const goTo = (path) => router.push(path);

import HeaderWeb from "../../Header-web.vue";
import footerWeb from "../../footer-web.vue";

import bannerSlide1 from "../../assets/banner-slide-1.png";
import bannerSlide2 from "../../assets/banner-slide-2.jpg";
import bannerSlide3 from "../../assets/banner-slide-3.jpg";
import imgSale1 from '../../assets/images (1).jpg'; // Fallback image


const showMini = ref(false);
const miniID = ref(null);

const slideContainer = ref(null);
let slideTimer;

// Filter states
const sortBy = ref('moi_nhat');
const selectedBrand = ref('');
const brands = ref([]);




onMounted(async () => {
  const slides = slideContainer.value.querySelectorAll(".slide");
  let current = 0;

  slideTimer = setInterval(() => {
    slides.forEach((s) => s.classList.remove("active"));
    current = (current + 1) % slides.length;
    slides[current].classList.add("active");
  }, 4000);

  fetchCategories();
  fetchBrands();
  fetchProducts();
});

onBeforeUnmount(() => clearInterval(slideTimer));

/* CATEGORY */
const categories = ref([]);
const selectedCategory = ref(null);

const fetchCategories = async () => {
  try {
    const res = await fetch('http://localhost/duan1/backend/api/Web/DanhMuc.php');
    const data = await res.json();
    if (data.success) {
      categories.value = data.data;
    }
  } catch (error) {
    console.error("Lỗi lấy danh mục:", error);
  }
};

const fetchBrands = async () => {
  try {
    const res = await fetch('http://localhost/duan1/backend/api/Web/ThuongHieu.php');
    const data = await res.json();
    if (data.success) {
      brands.value = data.data;
    }
  } catch (error) {
    console.error("Lỗi lấy thương hiệu:", error);
  }
};

const chooseBrand = (brand) => {
  selectedBrand.value = brand;
  currentPage.value = 1;
  fetchProducts();
};


const chooseCategory = (cat) => {
  selectedCategory.value = cat;
  currentPage.value = 1;
  fetchProducts();
};

const applyFilters = () => {
  currentPage.value = 1;
  fetchProducts();
};

// Theo dõi thay đổi query params từ URL
watch(() => route.query.id_thuonghieu, (newBrandId) => {
  if (newBrandId) {
    const brand = brands.value.find(b => b.id_thuonghieu == newBrandId);
    if (brand) {
      selectedBrand.value = brand;
      currentPage.value = 1;
      fetchProducts();
    }
  } else {
    selectedBrand.value = null;
    currentPage.value = 1;
    fetchProducts();
  }
});

/* PRODUCTS & PAGINATION */
const products = ref([]);
const currentPage = ref(1);
const perPage = 8;
const totalProducts = ref(0);

const fetchProducts = async () => {
  try {
    let url = `http://localhost/duan1/backend/api/Web/SanPham.php?limit=${perPage}&offset=${(currentPage.value - 1) * perPage}`;

    if (selectedCategory.value) {
      url += `&id_danhmuc=${selectedCategory.value.id_danhmuc}`;
    }
    if (selectedBrand.value) {
      url += `&id_thuonghieu=${selectedBrand.value.id_thuonghieu}`;
    }
    if (sortBy.value) {
      url += `&sort=${sortBy.value}`;
    }

    const res = await fetch(url);
    const data = await res.json();

    if (data.success) {
      products.value = data.data;
      totalProducts.value = data.total;
    }
  } catch (error) {
    console.error("Lỗi lấy sản phẩm:", error);
  }
};

const totalPages = computed(() => Math.ceil(totalProducts.value / perPage));

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
    fetchProducts();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};


function goBuyNow(id) {
  const user = localStorage.getItem("currentUser");

  if (!user) {
    const returnURL = `/Thanhtoanmini?id_sanpham=${id}`;
    router.push(`/Login?return=${encodeURIComponent(returnURL)}`);
    return;
  }

  miniID.value = id;
  showMini.value = true;
}

</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500;700&display=swap');

.nike-page {
  font-family: 'Roboto', sans-serif;
  background-color: #f7f7f7;
}

/* CONTAINER */
container {
  width: 90%;
  max-width: 1300px;
  margin: 0 auto;
}

/* BANNER */
.brand-banner {
  width: 100vw;
  /* kéo full chiều ngang màn hình */
  margin-left: 50%;
  /* căn giữa khi dùng 100vw */
  transform: translateX(-50%);
  height: 480px;
  overflow: hidden;
  position: relative;
  border-radius: 0;
}

.slide-container {
  width: 100%;
  height: 100%;
  position: relative;
}

.slide {
  position: absolute;
  width: 100%;
  height: 100%;
  opacity: 0;
  transition: 1s;
}

.slide.active {
  opacity: 1;
}

.slide img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.slide h1 {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 48px;
  color: white;
  text-shadow: 2px 3px 8px black;
}

/* FILTER */
.filter-bar {
  display: flex;
  justify-content: space-between;
  background: white;
  padding: 15px 20px;
  border-radius: 6px;
  margin-bottom: 25px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.filter-inline {
  display: flex;
  align-items: center;
  gap: 20px;
}

/* LAYOUT 2 CỘT */
.layout-2col {
  display: flex;
  gap: 25px;
}

/* MENU TRÁI */
.left-menu {
  width: 22%;
  background: white;
  padding: 18px;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  max-height: 350px;
  overflow-y: auto;
}

.left-menu h3 {
  margin-bottom: 12px;
  font-size: 20px;
  font-weight: 600;
}

.left-menu ul {
  list-style: none;
  padding: 0;
}

.left-menu li {
  padding: 8px 0;
  border-bottom: 1px solid #eee;
  cursor: pointer;
  font-size: 16px;
  transition: 0.2s;
}

.left-menu li:hover {
  color: #007bff;
  text-decoration: underline;
}

.left-menu li.active {
  color: #007bff;
  font-weight: 600;
  text-decoration: underline;
}

/* CỘT PHẢI */
.right-content {
  width: 78%;
}

/* GRID */
.product-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}

/* CARD */
.product-card {
  background: white;
  border-radius: 12px;
  padding: 10px;
  text-align: center;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  transition: 0.2s;
}

.product-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.product-card img {
  width: 100%;
  height: 170px;
  border-radius: 10px;
  object-fit: cover;
  margin-bottom: 8px;
}

.product-card h3 {
  font-size: 15px;
  font-weight: 600;
  margin: 5px 0;
}
.product-card p {
  font-size: 16px;
  font-weight: 700;
  color: #007bff;
  margin: 3px 0 6px;
}

/* FIX NÚT ĐỀU NHAU + KHOẢNG CÁCH */
.product-actions {
  opacity: 0;
  transition: 0.2s;
  display: flex;
  justify-content: center;
  gap: 12px;
}

.product-card:hover .product-actions {
  opacity: 1;
  margin-top: 10px;
}

.action-btn {
  background: #007bff;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  min-width: 105px;
  text-align: center;
  font-weight: 600;
}

.action-btn.secondary {
  background: #444;
}

/* PHÂN TRANG */
.pagination {
  margin-top: 25px;
  margin: 40px 0 40px;
  display: flex;
  justify-content: center;
  gap: 8px;
}

.pagination button {
  padding: 8px 14px;
  border-radius: 6px;
  border: 1px solid #ddd;
  background: white;
  cursor: pointer;
}

.pagination button.active {
  background: #007bff;
  color: white;
  border-color: #007bff;
}

.pagination button:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .layout-2col {
    flex-direction: column;
  }

  .left-menu {
    width: 100%;
  }

  .right-content {
    width: 100%;
  }

  .product-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 768px) {
  .brand-banner {
    height: 260px;
  }

  .slide h1 {
    font-size: 28px;
  }

  .filter-bar {
    flex-direction: column;
    gap: 15px;
  }

  .product-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 540px) {
  .brand-banner {
    height: 200px;
  }

  .slide h1 {
    font-size: 22px;
  }

  .product-grid {
    grid-template-columns: 1fr;
  }
}

.section-title {
  text-align: center;
  font-size: 40px;
}

.section-title {
  margin: 40px 0;
  /* Trên 40px – Dưới 40px */
  text-align: center;
}
</style>
