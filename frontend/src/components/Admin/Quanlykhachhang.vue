<template>
  <div class="app-wrapper d-flex">

    <!-- Sidebar -->
    <aside class="sidebar bg-dark text-white p-3">
      <img :src="logoImage" alt="Logo" class="logo-img" />

      <ul class="sidebar-menu">
        <router-link to="/" class="menu-item" active-class="active">
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
        <!-- Title + Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="fw-bold">Quản lý khách hàng</h3>
        </div>

        <!-- Search -->
        <input v-model="search" type="text" class="form-control mb-3" placeholder="🔍 Tìm theo tên hoặc email..." />

        <!-- Customer Table -->
        <table class="table table-bordered text-center">
          <thead class="table-secondary">
            <tr>
              <th>ID</th>
              <th>Tên</th>
              <th>Email</th>
              <th>SĐT</th>
              <th>Chức năng</th>
              <th>Hành động</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="c in paginatedItems" :key="c.id">
              <td>{{ c.id }}</td>
              <td>{{ c.name }}</td>
              <td>{{ c.email }}</td>
              <td>{{ c.phone }}</td>
              <td>
                <span class="badge bg-primary" v-if="c.role === 'Admin'">Admin</span>
                <span class="badge bg-secondary" v-else>User</span>
              </td>
              <td>
                <button class="btn btn-danger btn-sm" @click="deleteUser(c.id)">Xóa</button>
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

        <!-- Role Update Form -->
        <div class="card p-4 mt-4">
          <h4 class="fw-bold mb-3">Phân quyền tài khoản</h4>

          <div class="mb-3">
            <label>Chọn khách hàng</label>
            <select v-model="selectedUser" class="form-select">
              <option disabled value="">-- Chọn --</option>
              <option v-for="u in customers" :value="u.id">
                {{ u.name }} ({{ u.email }})
              </option>
            </select>
          </div>

          <div class="mb-3">
            <label>Chức năng</label>
            <select v-model="selectedRole" class="form-select">
              <option value="User">User</option>
              <option value="Admin">Admin</option>
            </select>
          </div>

          <button class="btn btn-primary" @click="updateRole">Cập nhật</button>
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

// Demo customer list
const customers = ref([
  { id: 1, name: "Nguyễn Văn A", email: "a@gmail.com", phone: "0909123456", role: "User" },
  { id: 2, name: "Trần Văn B", email: "b@gmail.com", phone: "0988222333", role: "Admin" },
  { id: 3, name: "Phạm Thị C", email: "c@gmail.com", phone: "0911222333", role: "User" },
]);

// Pagination
const page = ref(1);
const perPage = 5;

const filtered = computed(() =>
  customers.value.filter(
    (c) =>
      c.name.toLowerCase().includes(search.value.toLowerCase()) ||
      c.email.toLowerCase().includes(search.value.toLowerCase())
  )
);

const totalPages = computed(() => Math.ceil(filtered.value.length / perPage));

const paginatedItems = computed(() => {
  const start = (page.value - 1) * perPage;
  return filtered.value.slice(start, start + perPage);
});

// Role update form
const selectedUser = ref("");
const selectedRole = ref("User");

const updateRole = () => {
  if (!selectedUser.value) return alert("Vui lòng chọn khách hàng!");

  const user = customers.value.find((c) => c.id === selectedUser.value);
  if (user) {
    user.role = selectedRole.value;
    alert("✔ Cập nhật thành công!");
  }
};

const deleteUser = (id) => {
  customers.value = customers.value.filter((c) => c.id !== id);
};
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
