<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import logoImage from './assets/logone.png';


const router = useRouter();

// Search
const tuKhoaTimKiem = ref("");
const ketQuaTimKiem = ref([]);
const hienThiKetQua = ref(false);
const currentUser = ref(null); 
const danhSachThuongHieu = ref([]);
let thoiGianChoTimKiem = null;

const xuLyTimKiem = () => {
  clearTimeout(thoiGianChoTimKiem);
  if (!tuKhoaTimKiem.value.trim()) {
    ketQuaTimKiem.value = [];
    hienThiKetQua.value = false;
    return;
  }
  
  thoiGianChoTimKiem = setTimeout(async () => {
    // Tìm kiếm sản phẩm bình thường
    const response = await axios.get(`http://localhost/duan1/backend/api/Web/SanPham.php?search=${tuKhoaTimKiem.value}`);
    if (response.data.success) {
      ketQuaTimKiem.value = response.data.data;
      hienThiKetQua.value = true;
    }
  }, 300);
};

// Xử lý khi bấm Enter
const xuLyEnter = () => {
  const tuKhoa = tuKhoaTimKiem.value.toLowerCase().trim();
  const thuongHieuKhop = danhSachThuongHieu.value.find(
    th => th.tenTH.toLowerCase() === tuKhoa
  );
  
  if (thuongHieuKhop) {
    // Nếu khớp thương hiệu -> chuyển sang trang Sản phẩm với filter
    hienThiKetQua.value = false;
    tuKhoaTimKiem.value = "";
    
    // Sử dụng replace để force reload khi đang ở trang Sanpham
    router.replace({
      name: 'Sanpham',
      query: { 
        id_thuonghieu: thuongHieuKhop.id_thuonghieu,
        _t: Date.now() // Thêm timestamp để force reload
      }
    });
  }
};

const denTrangChiTiet = (product) => {
  hienThiKetQua.value = false;
  tuKhoaTimKiem.value = "";
  router.push({ name: 'ChiTiet', query: { id: product.id_sanpham } });
};

// đóng dropdown
const xuLyClickBenNgoai = (event) => {
  const searchBox = document.querySelector('.search-box-container');
  if (searchBox && !searchBox.contains(event.target)) {
    hienThiKetQua.value = false;
  }
};

// Lấy danh sách thương hiệu
const layDanhSachThuongHieu = async () => {
  const response = await axios.get('http://localhost/duan1/backend/api/Web/ThuongHieu.php');
  if (response.data.success) {
    danhSachThuongHieu.value = response.data.data;
  }
};

onMounted(() => {
  document.addEventListener('click', xuLyClickBenNgoai);
  const user = localStorage.getItem("currentUser");
  if (user) {
    currentUser.value = JSON.parse(user);
  }
  layDanhSachThuongHieu();
});

onUnmounted(() => {
  document.removeEventListener('click', xuLyClickBenNgoai);
});

// Dropdown
const dropdownOpen = ref(false);
const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value;
};



// User state definition moved up

// Kiểm tra admin
const isAdmin = computed(() => currentUser.value?.role === "admin");

// Logout
const logout = () => {
  localStorage.removeItem("currentUser");
  currentUser.value = null;
  window.location.href = "/Dangnhap";
};


</script>

<template>
  <header class="main-header">
    <div class="inner-header">
      <div class="logo">
        <router-link to="/"><img :src="logoImage" alt="Logo" /></router-link>
      </div>

      <nav class="nav-links">
        <router-link to="/" class="menu-item" active-class="active">
                 Trang chủ
            </router-link>
        <router-link to="/Sanpham" class="menu-item" active-class="active">
                 Sản phẩm
            </router-link>
        <router-link to="/tintuc" class="menu-item" active-class="active">
                 Tin tức
            </router-link>
        <router-link to="/Gioithieu" class="menu-item" active-class="active">
                 Giới thiệu
            </router-link>
        <router-link to="/Lienhe" class="menu-item" active-class="active">
                 Liên hệ
            </router-link>
      </nav>

      <div class="actions">
        <div class="search-box-container" style="position: relative;">
          <div class="search-box">
            <input 
              type="text" 
              placeholder="Tìm kiếm..." 
              v-model="tuKhoaTimKiem"
              @input="xuLyTimKiem"
              @keyup.enter="xuLyEnter"
              @focus="hienThiKetQua = true"
            />
            <button><i class="fas fa-search"></i></button>
          </div>
          
          <div v-if="hienThiKetQua && ketQuaTimKiem.length > 0" class="search-dropdown">
            <div 
              v-for="product in ketQuaTimKiem" 
              :key="product.id_sanpham" 
              class="search-item"
              @click="denTrangChiTiet(product)"
            >
              <img :src="'http://localhost/duan1/backend/' + product.hinhAnhgoc" alt="" class="item-img" />
              <div class="item-info">
                <div class="item-name">{{ product.tenSP }}</div>
                <div class="item-price">{{ Number(product.giaSauGiam || product.giaSP).toLocaleString() }}đ</div>
              </div>
            </div>
          </div>
        </div>

        <div class="icons">
          <a href="#" class="icon-heart"><i class="fas fa-heart"></i></a>
          <router-link to="/Thanhtoangiohang" class="icon-shopping-cart"><i class="fas fa-shopping-cart"></i></router-link>

          <div class="user-dropdown-wrapper">
            <a href="#" class="icon-user" @click.prevent="toggleDropdown">
              <i class="fas fa-user"></i>
            </a>

            <div v-if="dropdownOpen" class="user-dropdown">
              
              <!-- Đã đăng nhập -->
              <template v-if="currentUser">
                <router-link to="/Thongtin" class="item">Thông tin người dùng</router-link>
                <router-link v-if="isAdmin" to="/Dashboard" class="item">Quản lý trang web</router-link>
                <a href="#" class="item" @click.prevent="logout">Đăng xuất</a>
              </template>

              <!-- Chưa đăng nhập -->
              <template v-else>
                <router-link to="/Dangnhap" class="item">Đăng nhập</router-link>
              </template>

            </div>
          </div>

        </div>
      </div>
    </div>
  </header>
</template>
<style scoped>
/* RESET MARGIN/PADDING TOÀN TRANG */
:global(body) {
  margin: 0;
  padding: 0;
}

/* ============================ */
/*  HEADER FULL-WIDTH + FIX TOP */
/* ============================ */

.main-header {
  width: 100%;
  /* full ngang */
  position: fixed;
  /* cố định trên cùng */
  top: 0;
  /* sát viền trên */
  left: 0;
  /* sát bên trái */
  right: 0;
  /* sát bên phải */
  background-color: #e0e0e0;
  padding: 5px 0;
  /* mỏng */
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  z-index: 9999;
  /* luôn nổi */
}

/* Container */
.inner-header {
  width: 100%;
  max-width: 1400px;
  /* rộng tối đa (tuỳ chỉnh) */
  margin: 0 auto;
  padding: 0 20px;
  /* để nội dung không đụng sát màn hình */
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
}

/* LOGO */
.logo img {
  max-height: 65px;
  width: auto;
}

/* NAVIGATION */
.nav-links {
  display: flex;
  gap: 25px;
  flex-grow: 1;
  justify-content: center;
}

.nav-links a {
  color: #444;
  text-decoration: none;
  font-weight: 600;
  transition: 0.2s;
}

.nav-links a:hover {
  color: #007bff;
  transform: translateY(-2px);
}

/* ACTION AREA */
.actions {
  display: flex;
  align-items: center;
  gap: 15px;
}

/* SEARCH BOX */
.search-box {
  display: flex;
  align-items: center;
  background-color: #8c8c8c;
  padding: 5px 12px;
  border-radius: 20px;
}

.search-box input {
  background: transparent;
  border: none;
  outline: none;
  color: #fff;
  font-size: 14px;
  flex: 1;
}

.search-box input::placeholder {
  color: #ffffff;
}

.search-box button {
  background: none;
  border: none;
  font-size: 16px;
  color: #fff;
  cursor: pointer;
}

/* ACTION ICONS */
.icons a {
  font-size: 20px;
  margin-left: 10px;
  color: #444;
  transition: 0.2s;
}

.icons a:hover {
  color: #007bff;
  transform: scale(1.1);
}

.icon-heart {
  color: #e53935;
}

.icon-heart:hover {
  color: #e53935 !important;
}

/* ============================ */
/*       RESPONSIVE CSS         */
/* ============================ */

@media (max-width: 992px) {
  .inner-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .nav-links {
    margin-top: 10px;
  }

  .actions {
    width: 100%;
    justify-content: space-between;
    margin-top: 15px;
  }
}

@media (max-width: 576px) {
  .actions {
    flex-direction: column;
    align-items: flex-start;
  }

  .search-box {
    width: 100%;
    margin-bottom: 10px;
  }
}
.user-dropdown-wrapper {
  position: relative;
  display: inline-block;
}

.icon-user i {
  font-size: 20px;
  color: #333;
  cursor: pointer;
}

.user-dropdown {
  position: absolute;
  top: 110%;
  right: 0;
  background: #fff;
  border-radius: 6px;
  width: 200px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  padding: 8px 0;
  z-index: 999;
}

.user-dropdown .item {
  display: block;
  padding: 10px 15px;
  font-size: 14px;
  color: #333;
  text-decoration: none;
  transition: background 0.2s;
}

.user-dropdown .item:hover {
  background: #f2f2f2;
}

/* SEARCH DROPDOWN */
.search-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  margin-top: 8px;
  max-height: 400px;
  overflow-y: auto;
  z-index: 1000;
  padding: 8px 0;
}

.search-item {
  display: flex;
  align-items: center;
  padding: 10px 15px;
  cursor: pointer;
  transition: background 0.2s;
  gap: 12px;
}

.search-item:hover {
  background-color: #f8f9fa;
}

.item-img {
  width: 40px;
  height: 40px;
  object-fit: cover;
  border-radius: 4px;
  border: 1px solid #eee;
}

.item-info {
  flex: 1;
}

.item-name {
  font-size: 14px;
  font-weight: 500;
  color: #333;
  margin-bottom: 2px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.item-price {
  font-size: 13px;
  color: #e53935;
  font-weight: 600;
}

/* Custom scrollbar for dropdown */
.search-dropdown::-webkit-scrollbar {
  width: 6px;
}

.search-dropdown::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.search-dropdown::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 4px;
}

.search-dropdown::-webkit-scrollbar-thumb:hover {
  background: #b3b3b3;
}
</style>
