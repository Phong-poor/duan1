<template>
  <div class="app-wrapper d-flex">
    <!-- Sidebar -->
    <aside class="sidebar bg-dark text-white p-3">
      <img :src="logoImage" alt="Logo" class="logo-img" />
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
          <i class="fa-solid fa-maximize"></i> Size
        </router-link>
        <router-link to="/Quanlykhachhang" class="menu-item" active-class="active">
          <i class="fa-solid fa-users"></i> Khách hàng
        </router-link>
        <router-link to="/Quanlydonhang" class="menu-item" active-class="active">
          <i class="fa-solid fa-cart-shopping"></i> Đơn hàng
        </router-link>
      </ul>
    </aside>

    <!-- Main content -->
    <div class="main-content flex-grow-1">
      <header class="admin-header">
        <HeaderAdmin />
      </header>

      <div class="content-section p-4">

        <!-- Title -->
        <h3 class="fw-bold mb-4">Dashboard</h3>

        <!-- Revenue cards -->
        <div class="row g-3">
          <div class="col-md-3" v-for="c in revenueCards" :key="c.title">
            <div class="stat-card p-3">
              <h6 class="text-muted">{{ c.title }}</h6>
              <h3 class="fw-bold">{{ formatPrice(c.value) }}</h3>
            </div>
          </div>
        </div>

        <!-- Charts -->
        <div class="row mt-4">
          <div class="col-md-6">
            <div class="chart-card p-3">
              <h5 class="fw-bold mb-3">Biểu đồ số lượng đã mua</h5>
              <canvas id="qtyChart"></canvas>
            </div>
          </div>

          <div class="col-md-6">
            <div class="chart-card p-3">
              <h5 class="fw-bold mb-3">Biểu đồ tổng tiền theo tháng</h5>
              <canvas id="moneyChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Low stock table -->
        <div class="mt-5 card p-4">
          <h4 class="fw-bold">⚠ Sản phẩm sắp hết hàng</h4>

          <table class="table table-bordered text-center mt-3">
            <thead class="table-secondary">
              <tr>
                <th>Sản phẩm</th>
                <th>Màu</th>
                <th>Size</th>
                <th>Số lượng</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="v in lowStock" :key="v.key">
                <td>{{ v.product }}</td>
                <td>{{ v.color }}</td>
                <td>{{ v.size }}</td>
                <td class="text-danger fw-bold">{{ v.stock }}</td>
              </tr>

              <tr v-if="lowStock.length === 0">
                <td colspan="4" class="text-muted">Không có sản phẩm nào sắp hết hàng</td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import HeaderAdmin from "../../Header-admin.vue";
import logoImage from "../../assets/logo.png";
import Chart from "chart.js/auto";

// ------------------------
// MOCK SAMPLE DATA
// ------------------------
const orders = ref([
  {
    id: 1,
    status: "Thành công",
    total: 25990000,
    created_at: "2024-02-10",
    items: [{ id: 1, name: "iPhone 15", qty: 1, price: 25990000 }],
  },
  {
    id: 2,
    status: "Thành công",
    total: 44500000,
    created_at: "2024-02-20",
    items: [
      { id: 2, name: "Samsung S24", qty: 1, price: 22500000 },
      { id: 3, name: "AirPods Pro", qty: 1, price: 22000000 },
    ],
  },
]);

const products = ref([
  {
    name: "Nike Air Force 1",
    variants: [
      { color: "Trắng", size: 40, stock: 5 },
      { color: "Trắng", size: 41, stock: 20 },
    ],
  },
  {
    name: "Adidas UltraBoost",
    variants: [
      { color: "Đen", size: 42, stock: 3 },
      { color: "Đen", size: 43, stock: 50 },
    ],
  },
]);

// ------------------------
// REVENUE STATISTICS
// ------------------------

const now = new Date();

const revenueAll = computed(() =>
  orders.value
    .filter((o) => o.status === "Thành công")
    .reduce((s, o) => s + o.total, 0)
);

const revenueYear = computed(() =>
  orders.value
    .filter(
      (o) => o.status === "Thành công" && new Date(o.created_at).getFullYear() === now.getFullYear()
    )
    .reduce((s, o) => s + o.total, 0)
);

const revenueMonth = computed(() =>
  orders.value
    .filter((o) => {
      const d = new Date(o.created_at);
      return o.status === "Thành công" && d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear();
    })
    .reduce((s, o) => s + o.total, 0)
);

const revenueWeek = computed(() => {
  const startOfWeek = new Date(now);
  startOfWeek.setDate(now.getDate() - now.getDay());

  return orders.value
    .filter((o) => {
      const d = new Date(o.created_at);
      return o.status === "Thành công" && d >= startOfWeek;
    })
    .reduce((s, o) => s + o.total, 0);
});

const revenueCards = computed(() => [
  { title: "Tổng doanh thu", value: revenueAll.value },
  { title: "Doanh thu năm nay", value: revenueYear.value },
  { title: "Doanh thu tháng này", value: revenueMonth.value },
  { title: "Doanh thu tuần này", value: revenueWeek.value },
]);

// ------------------------
// LOW STOCK DETECTION
// ------------------------
const lowStock = computed(() => {
  const list = [];
  products.value.forEach((p) => {
    p.variants.forEach((v) => {
      if (v.stock < 10) {
        list.push({
          key: p.name + v.color + v.size,
          product: p.name,
          color: v.color,
          size: v.size,
          stock: v.stock,
        });
      }
    });
  });
  return list;
});

// ------------------------
// CHARTS
// ------------------------

const qtyChart = ref(null);
const moneyChart = ref(null);

onMounted(() => {
  loadCharts();
});

function loadCharts() {
  const months = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"];

  const qtyData = Array(12).fill(0);
  const moneyData = Array(12).fill(0);

  orders.value.forEach((o) => {
    if (o.status === "Thành công") {
      const m = new Date(o.created_at).getMonth();
      o.items.forEach((i) => (qtyData[m] += i.qty));
      moneyData[m] += o.total;
    }
  });

  // Line chart - qty
  new Chart(document.getElementById("qtyChart"), {
    type: "line",
    data: {
      labels: months,
      datasets: [
        {
          label: "Số lượng đã mua",
          data: qtyData,
          borderColor: "#0d6efd",
          borderWidth: 2,
        },
      ],
    },
  });

  // Bar chart - money
  new Chart(document.getElementById("moneyChart"), {
    type: "bar",
    data: {
      labels: months,
      datasets: [
        {
          label: "Tổng tiền bán",
          data: moneyData,
          backgroundColor: "#198754",
        },
      ],
    },
  });
}

// ------------------------
// UTILITY
// ------------------------
function formatPrice(num) {
  return num.toLocaleString("vi-VN", { style: "currency", currency: "VND" });
}
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
  margin-left: 240px;
  margin-top: 70px;
}

header.admin-header {
  position: fixed;
  top: 0;
  left: 240px;
  right: 0;
  z-index: 999;
}

.stat-card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.chart-card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.content-section {
  padding-top: 80px;
}
</style>