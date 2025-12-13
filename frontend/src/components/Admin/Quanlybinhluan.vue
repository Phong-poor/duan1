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
        <router-link to="/Quanlybaiviet" class="menu-item" active-class="active">
          <i class="fa-solid fa-newspaper"></i> Bài viết
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
            <h3 class="fw-bold">Quản lý bình luận</h3>
        </div>

        <!-- Search -->
        <input v-model="search" type="text" class="form-control mb-3" placeholder="🔍 Tìm theo nội dung hoặc email..." />

        <!-- Comments Table -->
        <table class="table table-bordered text-center">
            <thead class="table-secondary">
                <tr>
                    <th>ID</th>
                    <th>Khách hàng</th>
                    <th>Sản phẩm</th>
                    <th>Nội dung</th>
                    <th>Số sao</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th>Báo cáo</th>
                    <th>Hành động</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="c in paginatedItems" :key="c.id_binhluan">
                <td>{{ c.id_binhluan }}</td>
                <td>{{ c.tenKH }}</td>
                <td>{{ c.tenSP }}</td>
                <td>{{ c.noidung }}</td>
                <td>
                    <span v-for="i in 5" :key="i">
                        <i 
                        class="fa-solid fa-star"
                        :style="{ color: i <= c.sosao ? '#ffc107' : '#ccc' }"
                        ></i>
                    </span>
                </td>
                <td>{{ c.thoigianbinhluan }}</td>
                <td>{{ c.trangthai }}</td>
                <td :class="c.report_status === 'Đã báo cáo' ? 'text-danger fw-bold' : ''">
                    {{ c.report_status }}
                </td>
                <td>
                    <button 
                        v-if="c.report_status === 'Đã báo cáo'" 
                        class="btn btn-danger btn-sm"
                        @click="hideComment(c)"
                    >
                        Ẩn
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

        <!-- Update Form -->
        <div v-if="selectedComment" class="card p-4 mt-4">
          <h4 class="fw-bold mb-3">Cập nhật trạng thái bình luận</h4>

          <div class="mb-3">
            <label>Nội dung:</label>
            <textarea class="form-control" disabled>{{ selectedComment.noidung }}</textarea>
          </div>

          <div class="mb-3">
            <label>Trạng thái</label>
            <select v-model="selectedStatus" class="form-select">
              <option value="Hiển thị">Hiển thị</option>
              <option value="Ẩn">Ẩn</option>
            </select>
          </div>

          <button class="btn btn-primary" @click="updateComment">Cập nhật</button>
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
const comments = ref([]);

const selectedComment = ref(null);
const selectedStatus = ref("Hiển thị");

const page = ref(1);
const perPage = 6;

/* Load comments */
const loadComments = async () => {
  try {
    const res = await fetch("http://localhost/duan1/backend/api/Admin/GetComment.php");
    const data = await res.json();

    if (data.status === "success") {
      comments.value = data.data;
    }
  } catch (err) {
    console.error("Lỗi tải bình luận:", err);
  }
};

onMounted(loadComments);

/* Filter */
const filtered = computed(() =>
  comments.value.filter(
    (c) =>
      c.noidung.toLowerCase().includes(search.value.toLowerCase()) ||
      c.tenKH.toLowerCase().includes(search.value.toLowerCase())
  )
);

/* Pagination */
const totalPages = computed(() => Math.ceil(filtered.value.length / perPage));

const paginatedItems = computed(() => {
  const start = (page.value - 1) * perPage;
  return filtered.value.slice(start, start + perPage);
});

/* Update Comment */
const hideComment = async (c) => {
  if (!confirm("Bạn có chắc muốn ẨN bình luận này không?")) return;

  const res = await fetch("http://localhost/duan1/backend/api/Admin/UpdateComment.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      id: c.id_binhluan,
      trangthai: "Ẩn"
    }),
  });

  const data = await res.json();
  alert(data.msg);
  loadComments();
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
