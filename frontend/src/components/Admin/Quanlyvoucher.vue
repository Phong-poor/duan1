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

    <!-- Main Content -->
    <div class="main-content flex-grow-1">
      <header class="admin-header">
        <HeaderAdmin />
      </header>

      <div class="content-section p-4">

        <!-- Title -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="fw-bold">Quản lý voucher</h3>

          <button class="btn btn-primary" @click="openCreate">
            + Thêm voucher
          </button>
        </div>

        <div class="d-flex gap-2 mb-3">
            <!-- INPUT SEARCH -->
            <input
                v-model="search"
                type="text"
                class="form-control"
                placeholder="🔍 Tìm theo mã hoặc mô tả..."
                style="max-width: 300px"
            />
            <!-- FILTER STATUS -->
            <select v-model="filterStatus" class="form-select" style="max-width: 200px">
                <option value="">Tất cả trạng thái</option>
                <option value="Hoạt động">Hoạt động</option>
                <option value="Hết hạn">Hết hạn</option>
            </select>
        </div>

        <!-- Voucher Table -->
        <table class="table table-bordered text-center">
          <thead class="table-secondary">
            <tr>
              <th>ID</th>
              <th>Mã</th>
              <th>Giảm</th>
              <th>Điều kiện</th>
              <th>Thời gian</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="v in paginatedItems" :key="v.id_voucher">
              <td>{{ v.id_voucher }}</td>

              <td class="fw-bold">{{ v.ma_voucher }}</td>

              <td>
                <span v-if="v.loai_giam === 'VND'">
                  {{ formatMoney(v.gia_tri) }}
                </span>
                <span v-else>
                  {{ v.gia_tri }}% <br />
                  <small>(Tối đa {{ formatMoney(v.toi_da) }})</small>
                </span>
              </td>

              <td>
                ĐH {{ v.dieu_kien_loai }} {{ formatMoney(v.dieu_kien) }}
              </td>

              <td>
                {{ v.ngay_bat_dau }} → <br /> {{ v.ngay_het_han }}
              </td>

              <td :class="v.trang_thai === 'Hoạt động' ? 'text-success' : 'text-danger fw-bold'">
                {{ v.trang_thai }}
              </td>
              <td>
                <button class="btn btn-warning btn-sm" @click="selectVoucher(v)">
                  Sửa
                </button>
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
      </div>
      <!-- FORM THÊM / SỬA VOUCHER -->
      <div v-if="showForm" class="card p-4 mt-4">
        <h4 class="fw-bold mb-3">
          {{ isEdit ? "Cập nhật voucher" : "Thêm voucher mới" }}
        </h4>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Mã voucher</label>
            <input v-model="form.ma_voucher" class="form-control" :readonly="isEdit" />
          </div>

          <div class="col-md-6 mb-3">
            <label>Loại giảm</label>
            <select v-model="form.loai_giam" class="form-select">
              <option value="VND">Giảm tiền</option>
              <option value="%">Giảm phần trăm</option>
            </select>
          </div>

          <div class="col-md-6 mb-3">
            <label>Giá trị</label>
            <input v-model="form.gia_tri" type="number" class="form-control" />
          </div>

          <div class="col-md-6 mb-3" v-if="form.loai_giam === '%'">
            <label>Tối đa</label>
            <input v-model="form.toi_da" type="number" class="form-control" />
          </div>

          <div class="col-md-6 mb-3">
            <label>Điều kiện loại</label>
            <select v-model="form.dieu_kien_loai" class="form-select">
              <option value=">=">Tổng đơn ≥ điều kiện</option>
              <option value="<=">Tổng đơn ≤ điều kiện</option>
            </select>
          </div>

          <div class="col-md-6 mb-3">
            <label>Điều kiện giá trị</label>
            <input v-model="form.dieu_kien" type="number" class="form-control" />
          </div>

          <div class="col-md-12 mb-3">
            <label>Mô tả</label>
            <textarea v-model="form.mo_ta" class="form-control"></textarea>
          </div>

          <div class="col-md-6 mb-3">
            <label>Ngày bắt đầu</label>
            <input v-model="form.ngay_bat_dau" type="date" class="form-control" />
          </div>

          <div class="col-md-6 mb-3">
            <label>Ngày hết hạn</label>
            <input v-model="form.ngay_het_han" type="date" class="form-control" />
          </div>
        </div>

        <div class="d-flex justify-content-end mt-2">
          <button class="btn btn-secondary me-2" @click="closeForm">Hủy</button>
          <button class="btn btn-success" @click="saveForm">
            {{ isEdit ? "Cập nhật" : "Thêm mới" }}
          </button>
        </div>
      </div>
    </div>
  </div>

</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import HeaderAdmin from "../../Header-admin.vue";
import logoImage from "../../assets/logo.png";

const search = ref("");
const vouchers = ref([]);

const page = ref(1);
const perPage = 6;

// =============================
// LOAD VOUCHER
// =============================
const loadVoucher = async () => {
  const res = await fetch("http://localhost/duan1/backend/api/Admin/GetVoucher.php");
  const data = await res.json();
  if (data.status === "success") vouchers.value = data.data;
};

onMounted(loadVoucher);

// =============================
// FORM GỘP
// =============================
const showForm = ref(false);
const isEdit = ref(false);

const form = ref({
  id_voucher: "",
  ma_voucher: "",
  loai_giam: "",
  gia_tri: "",
  toi_da: "",
  dieu_kien_loai: "",
  dieu_kien: "",
  mo_ta: "",
  ngay_bat_dau: "",
  ngay_het_han: "",
});

// Tạo mã voucher
const generateCode = () => {
  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  let code = "";
  for (let i = 0; i < 8; i++) code += chars[Math.floor(Math.random() * chars.length)];
  return "VC-" + code;
};

// =============================
//  MỞ FORM THÊM
// =============================
const openCreate = () => {
  isEdit.value = false;
  showForm.value = true;

  form.value = {
    id_voucher: "",
    ma_voucher: generateCode(),
    loai_giam: "",
    gia_tri: "",
    toi_da: "",
    dieu_kien_loai: "",
    dieu_kien: "",
    mo_ta: "",
    ngay_bat_dau: "",
    ngay_het_han: "",
  };

  window.scrollTo({ top: 0, behavior: "smooth" });
};

// =============================
//  MỞ FORM SỬA
// =============================
const selectVoucher = (v) => {
  isEdit.value = true;
  showForm.value = true;

  form.value = { ...v };

  window.scrollTo({ top: 0, behavior: "smooth" });
};

// =============================
//  LƯU FORM (THÊM + SỬA)
// =============================
const saveForm = async () => {
  if (!form.value.ma_voucher || !form.value.gia_tri) {
    alert("Không được để trống mã và giá trị voucher!");
    return;
  }

  let url = "";
  if (isEdit.value) {
    url = "http://localhost/duan1/backend/api/Admin/UpdateVoucher.php";
  } else {
    url = "http://localhost/duan1/backend/api/Admin/CreateVoucher.php";
  }

  const res = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(form.value),
  });

  const data = await res.json();
  alert(data.msg);

  if (data.status === "success") {
    showForm.value = false;
    loadVoucher();
  }
};

const closeForm = () => {
  showForm.value = false;
};

// =============================
// LỌC & PHÂN TRANG
// =============================
const filterStatus = ref("");

const filtered = computed(() =>
  vouchers.value.filter((v) => {
    const matchSearch =
      v.ma_voucher.toLowerCase().includes(search.value.toLowerCase()) ||
      v.mo_ta.toLowerCase().includes(search.value.toLowerCase());

    const matchStatus = filterStatus.value === "" || v.trang_thai === filterStatus.value;

    return matchSearch && matchStatus;
  })
);

const totalPages = computed(() => Math.ceil(filtered.value.length / perPage));

const paginatedItems = computed(() => {
  const start = (page.value - 1) * perPage;
  return filtered.value.slice(start, start + perPage);
});

// Format tiền
const formatMoney = (n) =>
  Number(n).toLocaleString("vi-VN", { style: "currency", currency: "VND" });

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
