<template>
  <div class="app-wrapper d-flex">
    <!-- Sidebar -->
    <aside class="sidebar bg-dark text-white p-3">
        <img :src="logoImage" alt="Logo" class="logo-img" />
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
            <router-link to="/Quanlydonhang" class="menu-item" active-class="active">
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
          <h3 class="fw-bold">Quản lý danh mục</h3>
          <button class="btn btn-primary" @click="scrollToForm">+ Thêm danh mục</button>
        </div>

        <!-- Search -->
        <input v-model="search" type="text" class="form-control mb-3" placeholder="🔍 Tìm danh mục..." />

        <!-- Table -->
        <table class="table table-bordered text-center">
          <thead class="table-secondary">
            <tr>
              <th>ID</th>
              <th>Tên danh mục</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="dm in paginatedCategories" :key="dm.id">
              <td>{{ dm.id }}</td>
              <td>{{ dm.name }}</td>
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

        <!-- Add Category Form -->
        <div class="card p-4 mt-4" id="add-form">
          <h4 class="fw-bold mb-3">Thêm / Chỉnh sửa danh mục</h4>

          <div class="mb-3">
            <label>Tên danh mục</label>
            <input type="text" class="form-control" />
          </div>

          <div class="mb-3">
            <label>Mô tả</label>
            <textarea rows="4" class="form-control"></textarea>
          </div>

          <button class="btn btn-success">Lưu danh mục</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import HeaderAdmin from "../../Header-admin.vue";
import logoImage from "../../assets/logo.png";

const search = ref("");
const scrollToForm = () => {
  const form = document.getElementById("add-form");
  if (form) form.scrollIntoView({ behavior: "smooth", block: "start" });
};

// Demo Data
const categories = ref([
  { id: 1, name: "Điện thoại"},
  { id: 2, name: "Laptop"},
  { id: 3, name: "Tablet"}
]);

const page = ref(1);
const perPage = 5;

const filteredCategories = computed(() => {
  return categories.value.filter((c) =>
    c.name.toLowerCase().includes(search.value.toLowerCase())
  );
});

const totalPages = computed(() => Math.ceil(filteredCategories.value.length / perPage));

const paginatedCategories = computed(() => {
  const start = (page.value - 1) * perPage;
  return filteredCategories.value.slice(start, start + perPage);
});
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
.card {
  background: #ffffff;
  color: rgb(0, 0, 0);
  border-radius: 10px;
}
header.admin-header {
  position: fixed;
  top: 0;
  left: 240px;
  right: 0;
  z-index: 999;
}
.content-section {
  padding-top: 80px;
}
</style>
