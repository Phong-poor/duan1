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
            </tr>
          </thead>

          <tbody>
            <tr v-for="v in paginatedItems" :key="v.id_voucher">
              <td>{{ v.id_voucher }}</td>

              <td class="fw-bold">{{ v.ma_voucher }}</td>

              <td>
                <span v-if="v.loai_giam === 'money'">
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
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-center my-3 gap-2">
          <button class="btn btn-secondary btn-sm" :disabled="page === 1" @click="page--">Trước</button>
          <span>Trang {{ page }} / {{ totalPages }}</span>
          <button class="btn btn-secondary btn-sm" :disabled="page === totalPages" @click="page++">Sau</button>
        </div>

        <!-- Update Form -->
        <div v-if="selectedVoucher" class="card p-4 mt-4">
          <h4 class="fw-bold mb-3">Cập nhật voucher</h4>

          <div class="mb-3">
            <label>Mã voucher:</label>
            <input class="form-control" v-model="selectedVoucher.ma_voucher" />
          </div>

          <div class="mb-3">
            <label>Giá trị giảm</label>
            <input class="form-control" v-model="selectedVoucher.gia_tri" />
          </div>

          <div class="mb-3">
            <label>Điều kiện</label>
            <input class="form-control" v-model="selectedVoucher.dieu_kien" />
          </div>

          <div class="mb-3">
            <label>Trạng thái</label>
            <select v-model="selectedVoucher.trang_thai" class="form-select">
              <option value="Hoạt động">Hoạt động</option>
              <option value="Ẩn">Ẩn</option>
            </select>
          </div>

          <button class="btn btn-primary" @click="updateVoucher">Cập nhật</button>
        </div>
      </div>
      <!-- CREATE FORM -->
        <div v-if="showCreate" class="card p-4 mt-4">
            <h4 class="fw-bold mb-3">Thêm voucher mới</h4>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Mã voucher</label>
                    <input v-model="newVoucher.ma_voucher" class="form-control" readonly />
                </div>

                <div class="col-md-6 mb-3">
                    <label>Loại giảm</label>
                    <select v-model="newVoucher.loai_giam" class="form-select">
                        <option value="money">Giảm tiền</option>
                        <option value="percent">Giảm phần trăm</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Giá trị</label>
                    <input v-model="newVoucher.gia_tri" type="number" class="form-control" />
                </div>

                <div class="col-md-6 mb-3" v-if="newVoucher.loai_giam === 'percent'">
                    <label>Giảm tối đa</label>
                    <input v-model="newVoucher.toi_da" type="number" class="form-control" />
                </div>

                <div class="col-md-6 mb-3">
                    <label>Điều kiện loại</label>
                    <select v-model="newVoucher.dieu_kien_loai" class="form-select">
                        <option value=">">Tổng đơn > điều kiện</option>
                        <option value="<">Tổng đơn < điều kiện</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Điều kiện giá trị</label>
                    <input v-model="newVoucher.dieu_kien" type="number" class="form-control" />
                </div>

                <div class="col-md-12 mb-3">
                    <label>Mô tả</label>
                    <textarea v-model="newVoucher.mo_ta" class="form-control"></textarea>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Ngày bắt đầu</label>
                    <input v-model="newVoucher.ngay_bat_dau" type="date" class="form-control" />
                </div>

                <div class="col-md-6 mb-3">
                    <label>Ngày hết hạn</label>
                    <input v-model="newVoucher.ngay_het_han" type="date" class="form-control" />
                </div>
            </div>

            <div class="d-flex justify-content-end mt-2">
                <button class="btn btn-secondary me-2" @click="showCreate = false">Hủy</button>
                <button class="btn btn-success" @click="createVoucher">Thêm mới</button>
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

const selectedVoucher = ref(null);

const page = ref(1);
const perPage = 6;

// Load voucher list
const loadVoucher = async () => {
  const res = await fetch("http://localhost/duan1/backend/api/Admin/GetVoucher.php");
  const data = await res.json();
  if (data.status === "success") {
    vouchers.value = data.data;
  }
};

onMounted(loadVoucher);
// Popup tạo voucher
const showCreate = ref(false);

const newVoucher = ref({
  ma_voucher: "",
  loai_giam: "money",
  gia_tri: "",
  toi_da: "",
  dieu_kien_loai: ">",
  dieu_kien: "",
  mo_ta: "",
  ngay_bat_dau: "",
  ngay_het_han: "",
});
const generateCode = () => {
  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  let code = "";
  for (let i = 0; i < 8; i++) {
    code += chars[Math.floor(Math.random() * chars.length)];
  }
  return "VC-" + code; // Ví dụ: VC-92KD7FPA
};
// mở form
const openCreate = () => {
  showCreate.value = true;

  // reset form
  newVoucher.value = {
    ma_voucher: generateCode(),
    loai_giam: "money",
    gia_tri: "",
    toi_da: "",
    dieu_kien_loai: ">",
    dieu_kien: "",
    mo_ta: "",
    ngay_bat_dau: "",
    ngay_het_han: "",
  };
};
const createVoucher = async () => {
  // validate đơn giản
  if (!newVoucher.value.ma_voucher || !newVoucher.value.gia_tri) {
    alert("Mã và giá trị voucher không được để trống!");
    return;
  }

  const res = await fetch("http://localhost/duan1/backend/api/Admin/CreateVoucher.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(newVoucher.value),
  });

  const data = await res.json();

  alert(data.msg);

  if (data.status === "success") {
    showCreate.value = false;
    loadVoucher(); // load danh sách lại
  }
};
const filterStatus = ref("");
// Filter
const filtered = computed(() =>
  vouchers.value.filter((v) => {
    const matchSearch =
      v.ma_voucher.toLowerCase().includes(search.value.toLowerCase()) ||
      v.mo_ta.toLowerCase().includes(search.value.toLowerCase());

    const matchStatus =
      filterStatus.value === "" || v.trang_thai === filterStatus.value;

    return matchSearch && matchStatus;
  })
);

// Pagination
const totalPages = computed(() => Math.ceil(filtered.value.length / perPage));

const paginatedItems = computed(() => {
  const start = (page.value - 1) * perPage;
  return filtered.value.slice(start, start + perPage);
});

// Format money
const formatMoney = (n) =>
  Number(n).toLocaleString("vi-VN", { style: "currency", currency: "VND" });

// chọn voucher để sửa
const selectVoucher = (v) => {
  selectedVoucher.value = { ...v };
};

// cập nhật voucher
const updateVoucher = () => {
  alert("Chưa viết API update — bạn bảo mình làm tiếp nhé!");
};

// ẩn / hiện voucher
const toggleStatus = async (v) => {
  alert("Chưa viết API toggle — bạn yêu cầu mình làm tiếp nhé!");
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
