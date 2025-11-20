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
        <HeaderAdmin />
      </header>

      <div class="content-section p-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="fw-bold">Quản lý đơn hàng</h3>
        </div>

        <!-- Search -->
        <input v-model="search" type="text" class="form-control mb-3" placeholder="🔍 Tìm theo tên hoặc email..." />

        <!-- Orders Table -->
        <table class="table table-bordered text-center">
          <thead class="table-secondary">
            <tr>
              <th>ID</th>
              <th>Khách hàng</th>
              <th>Email</th>
              <th>SĐT</th>
              <th>Thanh toán</th>
              <th>Tổng</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="o in paginatedOrders" :key="o.id">
              <td>{{ o.id }}</td>
              <td>{{ o.name }}</td>
              <td>{{ o.email }}</td>
              <td>{{ o.phone }}</td>
              <td>{{ o.payment }}</td>
              <td>{{ formatPrice(o.total) }}</td>
              <td>
                <span class="badge"
                  :class="{
                    'bg-warning': o.status === 'Chờ xác nhận',
                    'bg-primary': o.status === 'Đã xác nhận',
                    'bg-info': o.status === 'Đang giao hàng',
                    'bg-success': o.status === 'Thành công'
                  }"
                >{{ o.status }}</span>
              </td>
              <td>
                <button class="btn btn-info btn-sm" @click="viewDetails(o)">Xem</button>
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

        <!-- Order Detail -->
        <div v-if="selectedOrder" class="card p-4 mt-4">
          <h4 class="fw-bold mb-3">Chi tiết đơn hàng #{{ selectedOrder.id }}</h4>

          <table class="table table-bordered text-center">
            <thead class="table-light">
              <tr>
                <th>ID SP</th>
                <th>Tên sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Tổng</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in selectedOrder.items" :key="item.id">
                <td>{{ item.id }}</td>
                <td>{{ item.name }}</td>
                <td>{{ formatPrice(item.price) }}</td>
                <td>{{ item.qty }}</td>
                <td>{{ formatPrice(item.price * item.qty) }}</td>
              </tr>
            </tbody>
          </table>

          <h5 class="text-end fw-bold mt-3">Tổng đơn hàng: {{ formatPrice(selectedOrder.total) }}</h5>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import HeaderAdmin from "../../Header-admin.vue";
import logoImage from "../../assets/logo.png";

// Search keyword
const search = ref("");

// Fake Orders
const orders = ref([
  {
    id: 1,
    name: "Nguyễn Văn A",
    email: "a@gmail.com",
    phone: "0909123456",
    payment: "COD",
    status: "Chờ xác nhận",
    total: 25990000,
    items: [
      { id: 1, name: "iPhone 15", price: 25990000, qty: 1 }
    ]
  },
  {
    id: 2,
    name: "Trần Thị B",
    email: "b@gmail.com",
    phone: "0988111222",
    payment: "Chuyển khoản",
    status: "Đang giao hàng",
    total: 44500000,
    items: [
      { id: 2, name: "Samsung S24", price: 22500000, qty: 1 },
      { id: 3, name: "AirPods Pro", price: 22000000, qty: 1 }
    ]
  }
]);

const page = ref(1);
const perPage = 5;

// Filter
const filteredOrders = computed(() =>
  orders.value.filter(
    (o) =>
      o.name.toLowerCase().includes(search.value.toLowerCase()) ||
      o.email.toLowerCase().includes(search.value.toLowerCase())
  )
);

// Pagination
const totalPages = computed(() => Math.ceil(filteredOrders.value.length / perPage));

const paginatedOrders = computed(() => {
  const start = (page.value - 1) * perPage;
  return filteredOrders.value.slice(start, start + perPage);
});

// Selected order for detail view
const selectedOrder = ref(null);

const viewDetails = (order) => {
  selectedOrder.value = order;
};

const formatPrice = (num) =>
  num.toLocaleString("vi-VN", { style: "currency", currency: "VND" });
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
