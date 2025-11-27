<template>
  <div class="app-wrapper d-flex">
    <!-- Sidebar -->
    <aside class="sidebar bg-dark text-white p-3">
      <img :src="logoImage" alt="Logo" class="logo-img">
      <ul class="sidebar-menu">
        <router-link to="/Dashboard" class="menu-item" active-class="active">
          <i class="fa-solid fa-chart-line"></i> Dashboard
        </router-link>

        <router-link to="/Quanlysanpham" class="menu-item" active-class="active">
          <i class="fa-solid fa-box"></i> Sản phẩm
        </router-link>

        <router-link to="/Quanlydanhmuc" class="menu-item" active-class="active">
          <i class="fa-solid fa-layer-group"></i> Danh mục
        </router-link>

        <router-link to="/Quanlythuonghieu" class="menu-item" active-class="active">
          <i class="fa-solid fa-bookmark"></i> Thương hiệu
        </router-link>

        <router-link to="/Quanlymausac" class="menu-item" active-class="active">
          <i class="fa-solid fa-palette"></i> Màu sắc
        </router-link>

        <router-link to="/Quanlysize" class="menu-item" active-class="active">
          <i class="fa-solid fa-maximize"></i>   Size
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
        <HeaderAdmin />
      </header>

      <div class="content-section p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="fw-bold">Quản lý thương hiệu</h3>
          <button class="btn btn-primary" @click="scrollToForm">Thêm thương hiệu</button>
        </div>

        <!-- Search -->
        <input v-model="search" type="text" class="form-control mb-3" placeholder="🔍 Tìm thương hiệu..." />

        <!-- Brand Table -->
        <table class="table table-bordered text-center">
          <thead class="table-secondary">
            <tr>
              <th>Mã TH</th>
              <th>Tên thương hiệu</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="br in paginatedBrands" :key="br.id_thuonghieu">
              <td>{{ br.maTH }}</td>
              <td>{{ br.tenTH }}</td>
              <td>
                <button class="btn btn-warning btn-sm" @click="editBrand(br)">Sửa</button>
                <button class="btn btn-danger btn-sm ms-2" @click="deleteBrand(br.id_thuonghieu)">Xóa</button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-center my-3 gap-2">
          <button class="btn btn-secondary btn-sm" :disabled="page === 1" @click="page--">Trước</button>
          <span>Trang {{ page }} / {{ totalPages }}</span>
          <button class="btn btn-secondary btn-sm" :disabled="page === totalPages" @click="page++">Sau</button>
        </div>

        <!-- Brand Form -->
        <div class="card p-4 mt-4" id="add-form">
          <h4 class="fw-bold mb-3">{{ isEdit ? "Cập nhật thương hiệu" : "Thêm thương hiệu" }}</h4>

          <div class="mb-3">
            <label>Tên thương hiệu</label>
            <input v-model="form.tenTH" type="text" class="form-control" placeholder="Nhập tên thương hiệu..." />
            <p v-if="errors.tenTH" class="text-danger mt-1">{{ errors.tenTH }}</p>
          </div>

          <button class="btn btn-success" @click="saveBrand">
            {{ isEdit ? "Cập nhật" : "Thêm mới" }}
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import logoImage from "../../assets/logo.png";
import HeaderAdmin from "../../Header-admin.vue";

/* --- STATE --- */
const brands = ref([]);
const search = ref("");
const page = ref(1);
const perPage = 5;

const form = ref({
  id_thuonghieu: null,
  tenTH: ""
});

// Lỗi form
const errors = ref({});
const isEdit = ref(false);

/* --- LOAD BRAND --- */
const loadBrands = async () => {
  try {
    const res = await fetch("http://localhost/duan1/backend/api/Admin/GetBrand.php");
    brands.value = await res.json();
  } catch (e) {
    console.log("Lỗi load thương hiệu", e);
  }
};

/* --- SAVE BRAND (ADD / UPDATE) --- */
const saveBrand = async () => {
  errors.value = {};

  if (!form.value.tenTH.trim()) {
    errors.value.tenTH = "Tên thương hiệu không được để trống!";
    return;
  }

  const url = isEdit.value
    ? "http://localhost/duan1/backend/api/Admin/UpdateBrand.php"
    : "http://localhost/duan1/backend/api/Admin/AddBrand.php";

  await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(form.value)
  });

  resetForm();
  loadBrands();
};

/* --- DELETE BRAND --- */
const deleteBrand = async (id) => {
  if (!confirm("Bạn có chắc muốn xóa không?")) return;

  await fetch(`http://localhost/duan1/backend/api/Admin/DeleteBrand.php?id=${id}`);

  loadBrands();
};

/* --- EDIT BRAND --- */
const editBrand = (br) => {
  form.value = {
    id_thuonghieu: br.id_thuonghieu,
    tenTH: br.tenTH
  };
  isEdit.value = true;
  scrollToForm();
};

/* --- RESET --- */
const resetForm = () => {
  form.value = { id_thuonghieu: null, tenTH: "" };
  errors.value = {};
  isEdit.value = false;
};

/* --- PAGINATION --- */
const filteredBrands = computed(() =>
  brands.value.filter((b) =>
    b.tenTH.toLowerCase().includes(search.value.toLowerCase())
  )
);

const totalPages = computed(() =>
  Math.ceil(filteredBrands.value.length / perPage)
);

const paginatedBrands = computed(() => {
  const start = (page.value - 1) * perPage;
  return filteredBrands.value.slice(start, start + perPage);
});

/* --- SCROLL FORM --- */
const scrollToForm = () => {
  const form = document.getElementById("add-form");
  if (form) form.scrollIntoView({ behavior: "smooth", block: "start" });
};

/* INIT */
loadBrands();
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
