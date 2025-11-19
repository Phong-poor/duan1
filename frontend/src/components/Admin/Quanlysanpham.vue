<template>
  <div class="app-wrapper d-flex">
    <!-- Sidebar -->
    <aside class="sidebar bg-dark text-white p-3">
        <img :src="logoImage" alt="Logo" class="logo-img">
        <ul class="sidebar-menu">
            <router-link to="/Dashboard" class="menu-item" active-class="active">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </router-link>
            <router-link to="/" class="menu-item" active-class="active">
                <i class="fa-solid fa-box"></i> Sản phẩm
            </router-link>
            <router-link to="/Quanlydanhmuc" class="menu-item" active-class="active">
                <i class="fa-solid fa-layer-group"></i> Danh mục
            </router-link>
            <router-link to="/Quanlythuonghieu" class="menu-item" active-class="active">
                <i class="fa-solid fa-bookmark"></i> Thương hiệu
            </router-link>
            <router-link to="Quanlydonhang" class="menu-item" active-class="active">
                <i class="fa-solid fa-cart-shopping"></i> Đơn hàng
            </router-link>
            <router-link to="/Quanlykhachhang" class="menu-item" active-class="active">
                <i class="fa-solid fa-users"></i> Khách hàng
            </router-link>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="main-content flex-grow-1">
      <header class="admin-header">
        <HeaderAdmin/>
      </header>


      <div class="content-section p-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="fw-bold">Quản lý sản phẩm</h3>
          <button class="btn btn-primary me-2 back "  @click="scrollToForm">Thêm sản phẩm</button>
        </div>

        <!-- Search -->
        <input v-model="search" type="text" class="form-control mb-3" placeholder="🔍 Tìm sản phẩm..." />

        <!-- Product Table -->
        <table class="table table-bordered text-center">
          <thead class="table-secondary">
            <tr>
              <th>Mã SP</th>
              <th>Hình ảnh</th>
              <th>Tên SP</th>
              <th>Danh mục</th>
              <th>Thương hiệu</th>
              <th>Giá</th>
              <th>Giảm giá</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="sp in paginatedProducts" :key="sp.id">
              <td>{{ sp.id }}</td>
              <td><img :src="sp.image" class="thumb" /></td>
              <td>{{ sp.name }}</td>
              <td>{{ sp.category }}</td>
              <td>{{ sp.brand }}</td>
              <td>{{ sp.price }} đ</td>
              <td>{{ sp.discount }}%</td>
              <td>
                <button class="btn btn-warning btn-sm" @click="scrollToForm">Sửa</button>
                <button class="btn btn-danger btn-sm ms-2">Xóa</button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-center my-3 gap-2">
            <button class="btn btn-secondary btn-sm" :disabled="page===1" @click="page--">Trước</button>
            <span>Trang {{ page }} / {{ totalPages }}</span>
            <button class="btn btn-secondary btn-sm" :disabled="page===totalPages" @click="page++">Sau</button>
        </div>

        <!-- Add Product Form -->
        <div class="card p-4 mt-4" id="add-form">
          <h4 class="fw-bold mb-3">Thêm sản phẩm</h4>

          <!-- Ảnh chính -->
          <div class="mb-3">
            <label>Hình ảnh chính</label>
            <input type="file" class="form-control" @change="onMainImageChange" />
          </div>

          <!-- Tên sản phẩm -->
          <div class="mb-3">
            <label>Tên sản phẩm</label>
            <input v-model="products.name" type="text" class="form-control" />
          </div>

          <!-- Danh mục -->
          <div class="mb-3">
            <label>Danh mục</label>
            <select v-model="products.category" class="form-select">
              <option v-for="dm in categories" :value="dm.id">{{ dm.ten }}</option>
            </select>
          </div>

          <!-- Thương hiệu -->
          <div class="mb-3">
            <label>Thương hiệu</label>
            <select v-model="products.brand" class="form-select">
              <option v-for="th in brands" :value="th.id">{{ th.ten }}</option>
            </select>
          </div>

          <!-- Ảnh phụ -->
          <div class="mb-3">
            <label>Ảnh phụ (nhiều ảnh)</label>
            <input type="file" multiple class="form-control" @change="onExtraImagesChange" />

            <!-- Preview ảnh phụ -->
            <div class="d-flex flex-wrap mt-3 gap-2">
              <div
                v-for="(img, index) in extraImagesPreview"
                :key="index"
                class="position-relative"
              >
                <img :src="img" class="preview-img" />

                <!-- Nút xóa ảnh -->
                <button class="btn btn-danger btn-sm delete-img-btn" @click="removeExtraImage(index)">
                  ✖
                </button>
              </div>
            </div>
          </div>

          <!-- BIẾN THỂ -->
          <div class="variant-box border p-3 mb-3">
            <h5 class="fw-semibold">Biến thể (màu + size + số lượng)</h5>

            <div v-for="(v, index) in variants" :key="index" class="border p-3 rounded mt-2">
              <div class="row g-2">

                <div class="col-md-4">
                  <label>Màu sắc</label>
                  <select v-model="v.color" class="form-select">
                    <option v-for="c in colors" :value="c.id">{{ c.ten }}</option>
                  </select>
                </div>

                <div class="col-md-4">
                  <label>Size</label>
                  <select v-model="v.size" class="form-select">
                    <option v-for="s in sizes" :value="s.id">{{ s.size }}</option>
                  </select>
                </div>

                <div class="col-md-3">
                  <label>Số lượng</label>
                  <input v-model="v.quantity" type="number" class="form-control" />
                </div>

                <div class="col-md-1 d-flex align-items-end">
                  <button class="btn btn-danger btn-sm" @click="removeVariant(index)">✖</button>
                </div>

              </div>
            </div>

            <button class="btn btn-success btn-sm mt-2" @click="addVariant">➕ Thêm biến thể</button>
          </div>

          <button class="btn btn-primary" @click="saveProduct">Lưu sản phẩm</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import logoImage from '../../assets/logo.png'
import HeaderAdmin from '../../Header-admin.vue'

const search = ref("")

const scrollToForm = () => {
  const form = document.getElementById("add-form");
  if (form) form.scrollIntoView({ behavior: "smooth", block: "start" });
};
// Demo Data
const products = ref([
  { id: 1, image: "https://via.placeholder.com/60", name: "iPhone 15", category: "Điện thoại", brand: "Apple", price: "25,990,000", discount: 5 },
  { id: 2, image: "https://via.placeholder.com/60", name: "Samsung S24", category: "Điện thoại", brand: "Samsung", price: "22,500,000", discount: 10 },
  // ... thêm nhiều sp
])

const page = ref(1)
const perPage = 5

const filteredProducts = computed(() => {
  return products.value.filter(p =>
    p.name.toLowerCase().includes(search.value.toLowerCase())
  )
})
const mainImage = ref(null);
const extraImages = ref([]);
const extraImagesPreview = ref([]); 

// Xóa ảnh phụ
const removeExtraImage = (index) => {
  extraImages.value.splice(index, 1);
  extraImagesPreview.value.splice(index, 1);
};
const variants = ref([
  { color: "", size: "", quantity: 0 }
]);

const addVariant = () => {
  variants.value.push({ color: "", size: "", quantity: 0 });
};

const removeVariant = (index) => {
  variants.value.splice(index, 1);
};
const onMainImageChange = (e) => {
  mainImage.value = e.target.files[0];
};

// Handle ảnh phụ
const onExtraImagesChange = (e) => {
  const files = Array.from(e.target.files);

  files.forEach((file) => {
    extraImages.value.push(file);

    const previewURL = URL.createObjectURL(file);
    extraImagesPreview.value.push(previewURL);
  });
};
const saveProduct = () => {
  console.log("Dữ liệu gửi lên server:");
  console.table(products.value);
  console.log("Ảnh chính:", mainImage.value);
  console.log("Ảnh phụ:", extraImages.value);
  console.log("Biến thể:", variants.value);

  alert("Gửi API ở đây (sanpham → sanpham_hinhanhphu → bienthe)");
};
const totalPages = computed(() => Math.ceil(filteredProducts.value.length / perPage))

const paginatedProducts = computed(() => {
  const start = (page.value - 1) * perPage
  return filteredProducts.value.slice(start, start + perPage)
})

// Form Data
const categories = ref([
  { id: 1, ten: "Áo thun" },
  { id: 2, ten: "Giày" },
]);

const brands = ref([
  { id: 1, ten: "Nike" },
  { id: 2, ten: "Adidas" },
]);

const colors = ref([
  { id: 1, ten: "Đỏ" },
  { id: 2, ten: "Đen" },
  { id: 3, ten: "Xanh" },
]);

const sizes = ref([
  { id: 1, size: "36" },
  { id: 2, size: "37" },
  { id: 3, size: "38" },
  { id: 4, size: "39" },
]);
</script>

<style scoped>
.logo-img {
  width: 120px; 
  height: auto;
  margin-left: 25px;
}

.sidebar {
  width: 240px;
  background: linear-gradient(180deg, #1b1c1f, #111);
  color: white;
  position: fixed;
  left: 0;
  top: 0;
  bottom: 0;
  overflow-y: auto;
}

.brand-title {
  font-size: 18px;
  margin-top: 10px;
  font-weight: 600;
  color: #ffffffd9;
}

.sidebar-menu {
  padding: 0;
  display: flex;
  flex-direction: column;
}

.menu-item {
  padding: 12px 20px;
  margin: 6px 12px;
  border-radius: 8px;
  font-size: 15px;
  display: flex;
  align-items: center;
  gap: 12px;
  color: white;
  text-decoration: none;
  transition: 0.25s ease;
}

.menu-item:hover {
  background: #2c2d31;
  transform: translateX(5px);
}

/* Khi router-link trùng route */
.menu-item.active {
  background: #0d6efd;
  color: white;
  box-shadow: 0 4px 10px rgba(13,110,253,0.4);
  transform: translateX(5px);
}
.main-content {
  margin-left: 240px !important;
  margin-top: 70px;
  overflow-y: visible; /* hoặc bỏ overflow luôn */
  height: auto; /* để trang tự dài */
}

.hover-item:hover {
  background: #444;
  cursor: pointer;
}
.thumb {
  width: 60px;
  height: 60px;
  object-fit: cover;
}
header.admin-header {
  position: fixed;
  top: 0;
  left: 240px;  /* bằng đúng width sidebar của bạn */
  right: 0;
  z-index: 999;
}
.content-section {
  padding-top: 80px; /* độ cao header của bạn */
}
.variant-box {
  background: #f8f9fa;
  border-radius: 6px;
}

.variant-box h5 {
  margin-bottom: 10px;
}
.preview-img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 6px;
  border: 1px solid #ddd;
}

.delete-img-btn {
  position: absolute;
  top: -6px;
  right: -6px;
  border-radius: 50%;
  padding: 2px 6px;
  font-size: 12px;
}
</style>
