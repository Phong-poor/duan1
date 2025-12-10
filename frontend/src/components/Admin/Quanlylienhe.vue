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
        
        <router-link to="Quanlydonhang" class="menu-item" active-class="active">
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
            <h3 class="fw-bold">Quản lý liên hệ</h3>
        </div>

        <!-- Search -->
        <input v-model="search" type="text" class="form-control mb-3" placeholder="🔍 Tìm theo nội dung hoặc email..." />

        <!-- Comments Table -->
        <!-- TABLE -->
        <table class="table table-bordered text-center">
          <thead class="table-secondary">
            <tr>
              <th>ID</th>
              <th>Tên KH</th>
              <th>Email</th>
              <th>Chủ đề</th>
              <th>Nội dung</th>
              <th>Ngày tạo</th>
              <th>Trạng thái</th>
              <th>Trả lời</th>
              <th>Ngày phản hồi</th>
              <th>Hành động</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="c in paginatedItems" :key="c.id_lienhe">
              <td>{{ c.id_lienhe }}</td>
              <td>{{ c.ten_khachhang }}</td>
              <td>{{ c.email }}</td>
              <td>{{ c.chu_de }}</td>
              <td>{{ c.noi_dung }}</td>
              <td>{{ c.ngay_tao }}</td>
              <td :class="c.trang_thai === 'Chưa phản hồi' ? 'text-danger' : 'text-success'">
                {{ c.trang_thai }}
              </td>
              <td>{{ c.tra_loi || "—" }}</td>
              <td>{{ c.ngay_phan_hoi || "—" }}</td>

              <td>
                <button 
                  class="btn btn-primary btn-sm"
                  @click="openReply(c)"
                >
                  Trả lời
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

        <!-- MODAL TRẢ LỜI -->
        <div v-if="selected" class="card p-4 mt-4">
          <h4 class="fw-bold">Trả lời liên hệ</h4>

          <div class="mb-3">
            <label>Nội dung khách gửi:</label>
            <textarea class="form-control" disabled>{{ selected.noi_dung }}</textarea>
          </div>

          <div class="mb-3">
            <label>Trả lời</label>
            <textarea v-model="replyText" rows="4" class="form-control"></textarea>
          </div>

          <button class="btn btn-success" @click="sendReply">
            Gửi phản hồi
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import HeaderAdmin from "../../Header-admin.vue";
import logoImage from "../../assets/logo.png";

// DATA
const listLienHe = ref([]);
const selected = ref(null);
const replyText = ref("");
const search = ref("");
const page = ref(1);
const perPage = 5;
// Load danh sách liên hệ
const loadLienHe = async () => {
  const res = await fetch("http://localhost/duan1/backend/api/Admin/GetLienHe.php");
  const data = await res.json();

  if (data.status === "success") {
    listLienHe.value = data.data;
  }
};

onMounted(loadLienHe);

// MỞ FORM TRẢ LỜI
const openReply = (item) => {
  selected.value = item;
  replyText.value = item.tra_loi || "";
};

// GỬI TRẢ LỜI
const sendReply = async () => {
  const res = await fetch("http://localhost/duan1/backend/api/Admin/ReplyLienHe.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      id_lienhe: selected.value.id_lienhe,
      tra_loi: replyText.value
    })
  });

  const data = await res.json();

  alert(data.msg);
  selected.value = null;
  loadLienHe();
};
const filtered = computed(() =>
  listLienHe.value.filter((c) =>
    c.noi_dung.toLowerCase().includes(search.value.toLowerCase()) ||
    c.email.toLowerCase().includes(search.value.toLowerCase())
  )
);

const totalPages = computed(() =>
  Math.ceil(filtered.value.length / perPage) || 1
);

const paginatedItems = computed(() => {
  const start = (page.value - 1) * perPage;
  return filtered.value.slice(start, start + perPage);
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
