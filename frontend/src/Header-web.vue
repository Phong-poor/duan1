<script setup>
import { ref, computed } from "vue";
import logoImage from './assets/logone.png';

const dropdownOpen = ref(false);

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value;
};

// Lấy user từ localStorage
const currentUser = computed(() => {
  const user = localStorage.getItem("user");
  return user ? JSON.parse(user) : null;
});

// Kiểm tra xem có phải admin không
const isAdmin = computed(() => {
  return currentUser.value && currentUser.value.role === "admin";
});

// Logout
const logout = () => {
  localStorage.removeItem("user");
  window.location.href = "/Dangnhap";
};
</script>

<template>
  <header class="main-header">
    <div class="inner-header">

      <div class="logo">
        <router-link to="/">
          <img :src="logoImage" alt="Logo" />
        </router-link>
      </div>

      <nav class="nav-links">
        <router-link to="/" class="menu-item" active-class="active">Trang chủ</router-link>
        <router-link to="/Sanpham" class="menu-item" active-class="active">Sản phẩm</router-link>
        <router-link to="/tintuc" class="menu-item" active-class="active">Tin tức</router-link>
        <router-link to="/Gioithieu" class="menu-item" active-class="active">Giới thiệu</router-link>
        <router-link to="/Sanpham" class="menu-item" active-class="active">Liên hệ</router-link>
      </nav>

      <div class="actions">
        <div class="search-box">
          <input type="text" placeholder="Tìm kiếm..." />
          <button><i class="fas fa-search"></i></button>
        </div>

        <div class="icons">
          <a href="#" class="icon-heart"><i class="fas fa-heart"></i></a>
          <router-link to="/Thanhtoangiohang" class="icon-shopping-cart">
            <i class="fas fa-shopping-cart"></i>
          </router-link>

          <div class="user-dropdown-wrapper">
            <a href="#" class="icon-user" @click.prevent="toggleDropdown">
              <i class="fas fa-user"></i>
            </a>

            <div v-if="dropdownOpen" class="user-dropdown">

              <!-- Chỉ hiện nếu đã đăng nhập -->
              <router-link v-if="currentUser" to="/Thongtin" class="item">
                Thông tin người dùng
              </router-link>
              
              <!-- Chỉ admin mới thấy -->
              <router-link v-if="isAdmin" to="/Dashboard" class="item">
                Quản lý trang web
              </router-link>

              <!-- Nếu chưa đăng nhập -->
              <router-link v-if="!currentUser" to="/Dangnhap" class="item">
                Đăng nhập
              </router-link>

              <!-- Nếu đã đăng nhập -->
              <a v-if="currentUser" href="#" class="item" @click.prevent="logout">
                Đăng xuất
              </a>
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
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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
</style>
