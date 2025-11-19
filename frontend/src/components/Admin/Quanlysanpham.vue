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

            <div class="mb-3">
                <label>Hình ảnh chính</label>
                <input type="file" class="form-control" />
            </div>

            <div class="mb-3">
                <label>Tên sản phẩm</label>
                <input type="text" class="form-control" />
            </div>

            <div class="mb-3">
                <label>Danh mục</label>
                <select class="form-select">
                <option v-for="dm in categories" :value="dm">{{ dm }}</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Thương hiệu</label>
                <select class="form-select">
                <option v-for="th in brands" :value="th">{{ th }}</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Ảnh phụ</label>
                <input type="file" class="form-control" multiple />
            </div>

            <div class="mb-3">
                <label>Biến thể màu sắc</label>
                <select v-model="selectedColor" class="form-select">
                <option v-for="c in colors" :value="c">{{ c }}</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Biến thể size</label>
                <select class="form-select">
                <option v-for="s in sizes" :value="s">{{ s }}</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Số lượng (tùy theo từng màu/size)</label>
                <input type="number" class="form-control" />
            </div>

            <button class="btn btn-primary">Lưu</button>
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

const totalPages = computed(() => Math.ceil(filteredProducts.value.length / perPage))

const paginatedProducts = computed(() => {
  const start = (page.value - 1) * perPage
  return filteredProducts.value.slice(start, start + perPage)
})

// Form Data
const categories = ["Điện thoại", "Laptop", "Tablet"]
const brands = ["Apple", "Samsung", "Xiaomi"]
const colors = ["Đỏ", "Đen", "Trắng", "Xanh"]
const sizes = [36, 37, 38,39,40,41,42]
const selectedColor = ref("")
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
</style>
