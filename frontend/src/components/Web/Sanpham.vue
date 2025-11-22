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
          <select>
            <option value="moi_nhat">Mới nhất</option>
            <option value="gia_tang">Giá tăng dần</option>
            <option value="gia_giam">Giá giảm dần</option>
          </select>
        </div>

        <div class="filter-inline">
          <label>Kích cỡ:</label>
          <select>
            <option value="all">Tất cả</option>
            <option value="40">40</option>
            <option value="41">41</option>
            <option value="42">42</option>
          </select>

          <label>Loại:</label>
          <select>
            <option value="all">Tất cả</option>
            <option value="air_max">Air Max</option>
            <option value="dunk">Dunk</option>
            <option value="jordan">Jordan</option>
          </select>
        </div>
      </div>

      <!-- ======= LAYOUT 2 CỘT ======= -->
      <div class="layout-2col">

        <!-- CỘT TRÁI -->
        <aside class="left-menu">
          <h3>Danh mục sản phẩm</h3>
          <ul>
            <li 
              v-for="item in categories" 
              :key="item"
              @click="chooseCategory(item)"
              :class="{ active: selectedCategory === item }"
            >
              {{ item }}
            </li>
          </ul>
        </aside>

        

        <!-- CỘT PHẢI -->
        <section class="right-content">

          <!-- GRID SẢN PHẨM -->
          <section class="product-grid">
            <div class="product-card" v-for="p in paginatedProducts" :key="p.id">
              <img :src="p.img" />
              <h3>{{ p.title }}</h3>
              <div class="stars">{{ p.stars }}</div>
              <p>{{ p.price }}</p>

              <div class="product-actions">
                <button class="action-btn">Mua ngay</button>
                <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
              </div>
            </div>
          </section>

          <!-- PHÂN TRANG -->
          <div class="pagination">
            <button 
              @click.prevent="changePage(currentPage - 1)" 
              :disabled="currentPage === 1"
            >«</button>

            <button 
              v-for="page in totalPages" 
              :key="page"
              @click.prevent="changePage(page)"
              :class="{ active: currentPage === page }"
            >
              {{ page }}
            </button>

            <button 
              @click.prevent="changePage(currentPage + 1)" 
              :disabled="currentPage === totalPages"
            >»</button>
          </div>

        </section>
      </div>
    </main>

    <footerWeb />
  </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref, computed } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const goTo = (path) => router.push(path);

import HeaderWeb from "../../Header-web.vue";
import footerWeb from "../../footer-web.vue";

import bannerSlide1 from "../../assets/banner-slide-1.png";
import bannerSlide2 from "../../assets/banner-slide-2.jpg";
import bannerSlide3 from "../../assets/banner-slide-3.jpg";

import productImg2 from "../../assets/images (3).jpg";

const slideContainer = ref(null);
let slideTimer;

onMounted(() => {
  const slides = slideContainer.value.querySelectorAll(".slide");
  let current = 0;

  slideTimer = setInterval(() => {
    slides.forEach((s) => s.classList.remove("active"));
    current = (current + 1) % slides.length;
    slides[current].classList.add("active");
  }, 4000);
});

onBeforeUnmount(() => clearInterval(slideTimer));

/* CATEGORY CLICK */
const categories = ["Puma", "Nike", "Adidas", "Jordan", "Vans", "Converse", "Louis"];
const selectedCategory = ref(null);
const chooseCategory = (cat) => selectedCategory.value = cat;

/* ======================= PAGINATION ======================= */
const currentPage = ref(1);
const perPage = 8;

// Demo sản phẩm
const products = Array.from({ length: 24 }, (_, i) => ({
  id: i + 1,
  title: "Nike Air Force 1 '07 LV8",
  img: productImg2,
  stars: "★★★★★",
  price: "2,890,000 VNĐ"
}));

const totalPages = computed(() => Math.ceil(products.length / perPage));

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * perPage;
  return products.slice(start, start + perPage);
});

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};
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
  width: 100vw;              /* kéo full chiều ngang màn hình */
  margin-left: 50%;          /* căn giữa khi dùng 100vw */
  transform: translateX(-50%);
  height: 350px;
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
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
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
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
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
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  transition: 0.2s;
}

.product-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
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

.stars {
  color: #ffc107;
  margin: 3px 0;
  font-size: 14px;
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
  .left-menu { width: 100%; }
  .right-content { width: 100%; }
  .product-grid { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 768px) {
  .brand-banner { height: 260px; }
  .slide h1 { font-size: 28px; }
  .filter-bar { flex-direction: column; gap: 15px; }
  .product-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 540px) {
  .brand-banner { height: 200px; }
  .slide h1 { font-size: 22px; }
  .product-grid { grid-template-columns: 1fr; }
}
.section-title{
  text-align: center;
  font-size: 40px;
}
</style>
