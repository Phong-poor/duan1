<template>
  <div class="app-wrapper d-flex">

    <!-- SIDEBAR -->
    <aside class="sidebar bg-dark text-white p-3">
      <img :src="logoImage" class="logo-img" />
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
        <router-link to="/Quanlydonhang" class="menu-item" active-class="active">
          <i class="fa-solid fa-cart-shopping"></i> Đơn hàng
        </router-link>
        <router-link to="/Quanlybinhluan" class="menu-item" active-class="active">
          <i class="fa-solid fa-comment"></i> Đánh giá
        </router-link>
        <router-link to="/Quanlyvoucher" class="menu-item" active-class="active">
          <i class="fa-solid fa-ticket"></i> Voucher
        </router-link>
        <router-link to="/Quanlylienhe" class="menu-item" active-class="active">
          <i class="fa-solid fa-message"></i> Liên hệ
        </router-link>
        <router-link to="/Quanlykhachhang" class="menu-item" active-class="active">
          <i class="fa-solid fa-users"></i> Khách hàng
        </router-link>
      </ul>
    </aside>

    <!-- MAIN -->
    <div class="main-content flex-grow-1">
      <header class="admin-header">
        <HeaderAdmin />
      </header>

      <div class="content-section p-4">

        <h3 class="fw-bold mb-4">Dashboard</h3>

        <!-- CARDS -->
        <div class="row g-3" v-if="dashboard">
          <div class="col-md-3" v-for="c in revenueCards" :key="c.title">
            <div class="stat-card p-3">
              <h6 class="text-muted">{{ c.title }}</h6>
              <h3 class="fw-bold">{{ formatPrice(c.value) }}</h3>
            </div>
          </div>
        </div>

        <!-- CHART -->
        <div class="row mt-4" v-if="dashboard">
          <div class="col-md-6">
            <div class="chart-card p-3">
              <h5 class="fw-bold mb-3">Biểu đồ số lượng mua</h5>
              <canvas id="qtyChart"></canvas>
            </div>
          </div>

          <div class="col-md-6">
            <div class="chart-card p-3">
              <h5 class="fw-bold mb-3">Biểu đồ doanh thu theo tháng</h5>
              <canvas id="moneyChart"></canvas>
            </div>
          </div>
        </div>

        <!-- LOW STOCK -->
        <div class="mt-5 card p-4" v-if="dashboard">
          <h4 class="fw-bold">⚠ Sản phẩm sắp hết hàng</h4>

          <table class="table table-bordered text-center mt-3">
            <thead class="table-secondary">
              <tr>
                <th>STT</th>
                <th>Sản phẩm</th>
                <th>Màu</th>
                <th>Size</th>
                <th>Số lượng</th>
                <th>Hành động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in dashboard.low_stock" :key="item.id_bienthe">
                <td class="fw-bold">{{ Number(index) + 1 }}</td>
                <td>{{ item.tenSP }}</td>
                <td>{{ item.mausac }}</td>
                <td>{{ item.size }}</td>
                <td class="text-danger fw-bold">{{ item.so_luong }}</td>
                <td>
                  <button class="btn btn-sm btn-primary" @click="openAddStock(item)">
                    + Thêm số lượng
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- TOP PRODUCTS -->
        <div class="mt-5 card p-4" v-if="dashboard">
          <h4 class="fw-bold">🔥 Top 5 sản phẩm bán chạy nhất</h4>

          <table class="table table-striped text-center mt-3">
            <thead>
              <tr>
                <th>Sản phẩm</th>
                <th>Số lượng bán</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="p in dashboard.top_products" :key="p.id_sanpham">
                <td>{{ p.tenSP }}</td>
                <td class="fw-bold">{{ p.total_qty }}</td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
  <div v-if="showPopup" class="popup-backdrop">
    <div class="popup-box">
      <h5>Thêm số lượng cho: {{ selectedItem.tenSP }}</h5>

      <label class="mt-2">Nhập số lượng thêm:</label>
      <input type="number" class="form-control mt-1"
            v-model="addQty" min="1">

      <div class="d-flex justify-content-end mt-3">
        <button class="btn btn-secondary me-2" @click="showPopup=false">Đóng</button>
        <button class="btn btn-success" @click="submitAddStock">Xác nhận</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, computed } from "vue";
import HeaderAdmin from "../../Header-admin.vue";
import logoImage from "../../assets/logo.png";
import Chart from "chart.js/auto";

const dashboard = ref(null);

onMounted(async () => {
  const res = await fetch("http://localhost/duan1/backend/api/Admin/dashboard.php");
  dashboard.value = await res.json();

  if (dashboard.value.status) {
    await nextTick();
    loadCharts();
  }
});

// ============== FORMAT 12 THÁNG ==============
function convertToArray12(source, field) {
  const arr = Array(12).fill(0);
  source.forEach(r => {
    const month = Number(r.thang);
    if (month >= 1 && month <= 12) {
      arr[month - 1] = Number(r[field]);
    }
  });
  return arr;
}

// ============== LOAD CHARTS ==============
function loadCharts() {
  const qty = convertToArray12(dashboard.value.chart_qty, "qty");
  const money = convertToArray12(dashboard.value.chart_money, "total");

  new Chart(document.getElementById("qtyChart"), {
    type: "line",
    data: {
      labels: Array.from({ length: 12 }, (_, i) => i + 1),
      datasets: [
        {
          label: "Số lượng đã mua",
          data: qty,
          borderColor: "#0d6efd",
          borderWidth: 2,
          tension: 0.3
        }
      ]
    }
  });

  new Chart(document.getElementById("moneyChart"), {
    type: "bar",
    data: {
      labels: Array.from({ length: 12 }, (_, i) => i + 1),
      datasets: [
        {
          label: "Doanh thu",
          data: money,
          backgroundColor: "#198754"
        }
      ]
    }
  });
}

// ============== REVENUE CARDS ==============
const revenueCards = computed(() => {
  if (!dashboard.value) return [];
  return [
    { title: "Tổng doanh thu", value: dashboard.value.summary.total },
    { title: "Doanh thu năm nay", value: dashboard.value.summary.year },
    { title: "Doanh thu tháng", value: dashboard.value.summary.month },
    { title: "Doanh thu tuần", value: dashboard.value.summary.week }
  ];
});

function formatPrice(num) {
  return Number(num).toLocaleString("vi-VN", {
    style: "currency",
    currency: "VND"
  });
}
// ====== POPUP ADD STOCK ======
const showPopup = ref(false);
const selectedItem = ref({});
const addQty = ref(1);

const openAddStock = (item) => {
  selectedItem.value = item;
  addQty.value = 1;
  showPopup.value = true;
};

const submitAddStock = async () => {
  const id = selectedItem.value.id_bienthe;

  const res = await fetch("http://localhost/duan1/backend/api/Admin/updateStock.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      id_bienthe: id,
      so_luong: addQty.value
    })
  });

  const data = await res.json();

  if (data.success) {
    alert("Cập nhật thành công!");
    showPopup.value = false;

    // cập nhật lại dashboard
    const res2 = await fetch("http://localhost/duan1/backend/api/Admin/dashboard.php");
    dashboard.value = await res2.json();
  } else {
    alert("Lỗi: " + data.message);
  }
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
.popup-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.popup-box {
  background: white;
  padding: 20px;
  width: 400px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

</style>