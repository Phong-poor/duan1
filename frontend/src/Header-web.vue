<script setup>
import logoImage from './assets/logone.png';
</script>

<template>
  <header class="main-header">
    <div class="inner-header">
      
      <div class="logo">
        <a href="#">
          <img :src="logoImage" alt="Logo" />
        </a>
      </div>

      <nav class="nav-links">
        <a href="#">TRANG CHỦ</a>
        <a href="#">SẢN PHẨM</a>
        <a href="#">TIN TỨC</a>
        <a href="#">GIỚI THIỆU</a>
        <a href="#">LIÊN HỆ</a>
      </nav>

      <div class="actions">
        <div class="search-box">
          <input type="text" placeholder="Tìm kiếm..." />
          <button><i class="fas fa-search"></i></button>
        </div>
        <div class="icons">
          <a href="#" class="icon-heart"><i class="fas fa-heart"></i></a>
          <a href="#" class="icon-shopping-cart"><i class="fas fa-shopping-cart"></i></a>
          <a href="#" class="icon-user"><i class="fas fa-user"></i></a>
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
  width: 100%;                /* full ngang */
  position: fixed;            /* cố định trên cùng */
  top: 0;                     /* sát viền trên */
  left: 0;                    /* sát bên trái */
  right: 0;                   /* sát bên phải */
  background-color: #e0e0e0;
  padding: 5px 0;             /* mỏng */
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  z-index: 9999;              /* luôn nổi */
}

/* Container */
.inner-header {
  width: 100%;
  max-width: 1400px;          /* rộng tối đa (tuỳ chỉnh) */
  margin: 0 auto;
  padding: 0 20px;            /* để nội dung không đụng sát màn hình */
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
</style>
