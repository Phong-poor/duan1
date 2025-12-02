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
          <i class="fa-solid fa-maximize"></i>Size
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
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="fw-bold">Quản lý đơn hàng</h3>
        </div>

        <!-- Search -->
        <div class="mb-3 d-flex gap-2">
          <input v-model="search" type="text" class="form-control" placeholder="🔍 Tìm theo tên, email, mã đơn..." />
          <button class="btn btn-outline-secondary" @click="fetchOrders">Tải lại</button>
        </div>

        <!-- Loading / Error -->
        <div v-if="loading" class="mb-3">Đang tải dữ liệu...</div>
        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <!-- Orders Table -->
        <table class="table table-bordered text-center" v-if="!loading && orders.length">
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
            <tr v-for="o in paginatedOrders" :key="o.id_donhang">
              <td>{{ o.maDatHang }}</td>
              <td>{{ o.tenKH || o.ten_khach || '—' }}</td>
              <td>{{ o.email || '—' }}</td>
              <td>{{ o.sodienthoai || o.phone || '—' }}</td>
              <td>{{ o.payment || o.PTTT || '—' }}</td>
              <td>{{ formatPrice(o.tongtien || o.total || 0) }}</td>
              <td>
                <span class="badge"
                  :class="badgeClass(normalizeStatus(o.trangthai || o.status))"
                >{{ normalizeStatus(o.trangthai || o.status) }}</span>
              </td>
              <td>
                <div class="d-flex flex-column gap-1">
                  <button class="btn btn-info btn-sm mt-1" @click="viewDetails(o)">Xem</button>
                  <!-- NÚT CHUYỂN TRẠNG THÁI THEO QUY TẮC -->
                  <button
                    v-if="nextStatus(o)"
                    class="btn btn-sm"
                    :class="statusButtonClass(nextStatus(o))"
                    @click="confirmChangeStatus(o, nextStatus(o))"
                  >
                    {{ nextStatus(o) }}
                  </button>

                  <!-- NÚT XEM -->
                  
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-if="!loading && !orders.length" class="text-center text-muted">
          Chưa có đơn hàng.
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center my-3 gap-2" v-if="totalPages > 1">
          <button class="btn btn-secondary btn-sm" :disabled="page === 1" @click="goPrev">Trước</button>
          <span>Trang {{ page }} / {{ totalPages }}</span>
          <button class="btn btn-secondary btn-sm" :disabled="page === totalPages" @click="goNext">Sau</button>
        </div>

        <!-- Order Detail -->
        <div v-if="selectedOrder" class="card p-4 mt-4">
          <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold mb-3">Chi tiết đơn hàng #{{ selectedOrder.id_donhang }}</h4>
            <div>
              <button class="btn btn-outline-secondary btn-sm" @click="selectedOrder = null">Đóng</button>
            </div>
          </div>

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
              <tr v-for="item in selectedOrder.items" :key="item.id_hoadonchitiet || item.id_sanpham">
                <td>{{ item.maSP }}</td>
                <td>{{ item.tenSP || item.name || '—' }}</td>
                <td>{{ formatPrice(item.giaSP || 0) }}</td>
                <td>{{ item.soLuongMua || item.qty || 0 }}</td>
                <td>{{ formatPrice((item.giaSP || 0) * (item.soLuongMua || item.qty || 0)) }}</td>
              </tr>
            </tbody>
          </table>

          <h5 class="text-end fw-bold mt-3">Tổng đơn hàng: {{ formatPrice(selectedOrder.tongtien || selectedOrder.total || 0) }}</h5>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import HeaderAdmin from "../../Header-admin.vue";
import logoImage from "../../assets/logo.png";

// reactive state
const orders = ref([]);
const loading = ref(false);
const error = ref(null);
const page = ref(1);
const perPage = ref(10);
const search = ref("");
const selectedOrder = ref(null);

// ---------- Fetch orders from API ----------
const fetchOrders = async () => {
  loading.value = true;
  error.value = null;
  try {
    const res = await fetch(`http://localhost/duan1/backend/api/Admin/getOrders.php`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    // data expected to be array of order rows from donhang
    orders.value = Array.isArray(data) ? data : [];
    // reset page if out of range
    if (page.value > totalPages.value) page.value = totalPages.value || 1;
  } catch (err) {
    error.value = "Không tải được danh sách đơn hàng: " + err.message;
    orders.value = [];
  } finally {
    loading.value = false;
  }
};

// run once
fetchOrders();

// ---------- Helpers ----------
const normalizeStatus = (s) => {
  if (!s && s !== "") return "Chưa rõ";
  return String(s);
};
const nextStatus = (order) => {
  const current = normalizeStatus(order.trangthai || order.status).toLowerCase();

  if (current.includes("chờ")) return "Đã xác nhận";
  if (current.includes("đã xác nhận")) return "Đang giao hàng";
  if (current.includes("đang giao hàng")) return "Thành công";

  // nếu đã hủy hoặc trả hàng → không hiển thị nút đổi trạng thái
  if (current.includes("hủy") || current.includes("trả")) return null;

  // nếu đã thành công → không còn nút cập nhật
  if (current.includes("thành công")) return null;

  return null;
};
const statusButtonClass = (status) => {
  if (!status) return "";

  if (status === "Đã xác nhận") return "btn-primary";
  if (status === "Đang giao hàng") return "btn-warning";
  if (status === "Thành công") return "btn-success";

  return "btn-secondary";
};

const badgeClass = (status) => {
  // trả về lớp bootstrap phù hợp
  if (status.includes("Chờ")) return "bg-warning";
  if (status.includes("xác nhận") || status.includes("Xác nhận") || status === "Đã xác nhận") return "bg-primary";
  if (status.includes("Đang giao") || status === "Đang giao hàng") return "bg-info";
  if (status.includes("Thành")) return "bg-success";
  return "bg-secondary";
};

const formatPrice = (num) => {
  const n = Number(num || 0);
  return n.toLocaleString("vi-VN", { style: "currency", currency: "VND" });
};

// ---------- Filter & Pagination ----------
const filteredOrders = computed(() =>
  orders.value.filter((o) => {
    const q = search.value.trim().toLowerCase();
    if (!q) return true;
    const name = (o.name || o.ten_khach || "").toString().toLowerCase();
    const email = (o.email || "").toString().toLowerCase();
    const ma = (o.maDatHang || "").toString().toLowerCase();
    const phone = (o.sodienthoai || o.phone || "").toString().toLowerCase();
    return name.includes(q) || email.includes(q) || ma.includes(q) || phone.includes(q);
  })
);

const totalPages = computed(() => Math.max(1, Math.ceil(filteredOrders.value.length / perPage.value)));

const paginatedOrders = computed(() => {
  const start = (page.value - 1) * perPage.value;
  return filteredOrders.value.slice(start, start + perPage.value);
});

// ensure page within bounds if filtered changes
watch(filteredOrders, () => {
  if (page.value > totalPages.value) page.value = totalPages.value;
});

// ---------- Pagination controls ----------
const goPrev = () => {
  if (page.value > 1) page.value--;
};
const goNext = () => {
  if (page.value < totalPages.value) page.value++;
};

// ---------- View details (calls API) ----------
const viewDetails = async (order) => {
  // set selectedOrder skeleton first (to show basic info)
  selectedOrder.value = { ...order, items: [] };

  try {
    const id = order.id_donhang || order.id || order.id_order;
    if (!id) {
      selectedOrder.value.items = [];
      return;
    }
    const res = await fetch(`http://localhost/duan1/backend/api/Admin/getOrderDetail.php?id=${encodeURIComponent(id)}`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    // data expected items with fields: id_hoadonchitiet, id_sanpham, tenSP, gia, soLuongMua
    selectedOrder.value.items = Array.isArray(data) ? data : [];
    // update total if provided by API
    if (!selectedOrder.value.tongtien && data.total) selectedOrder.value.tongtien = data.total;
  } catch (err) {
    // show error but keep selectedOrder open
    selectedOrder.value.items = [];
    error.value = "Lỗi khi tải chi tiết đơn: " + err.message;
  }
};

// ---------- Update status ----------
const updateStatusApi = async (id, status) => {
  try {
    const res = await fetch(`http://localhost/duan1/backend/api/Admin/updateStatus.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id_donhang: id,
        trangthai: status
      }),
    });

    return await res.json();
  } catch (err) {
    return { success: false, message: err.message };
  }
};



const changeStatus = async (order, status) => {
  const id = order.id_donhang;
  if (!id) {
    alert("Không tìm thấy id đơn hàng.");
    return;
  }
  const resp = await updateStatusApi(id, status);
  if (resp && resp.success) {
    const idx = orders.value.findIndex((x) => (x.id_donhang || x.id) == id);
    if (idx !== -1) {
      orders.value[idx].trangthai = status;
      orders.value[idx].status = status;
    }
    if (selectedOrder.value && (selectedOrder.value.id_donhang || selectedOrder.value.id) == id) {
      selectedOrder.value.trangthai = status;
      selectedOrder.value.status = status;
    }
    await fetchOrders();
    alert("Cập nhật trạng thái thành công!");
  } else {
    alert("Cập nhật thất bại: " + (resp.message || "Không xác định"));
  }
};

const confirmChangeStatus = (order, status) => {
  if (confirm(`Bạn có chắc muốn đổi trạng thái đơn #${order.maDatHang} → "${status}" ?`)) {
    changeStatus(order, status);
  }
};

// manual refresh single order (re-fetch list)
const refreshOrder = (order) => {
  // simple approach: re-fetch all orders
  fetchOrders();
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
  overflow-y: visible;
  height: auto;
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
  left: 240px;
  right: 0;
  z-index: 999;
}

.content-section {
  padding-top: 80px;
}
</style>
