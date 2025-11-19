<template>
  <main class="container">

    <!-- ====================== HERO CAROUSEL ===================== -->
    <section class="hero-carousel">
      <div
        class="slide-container"
        :style="{ transform: `translateX(-${currentIndex * (100 / slides.length)}%)` }"
      >
        <div
          v-for="(slide, i) in slides"
          :key="i"
          class="slide"
          :style="{ backgroundImage: `url(${slide.img})` }"
        >
          <h1 v-html="slide.title"></h1>
          <p v-html="slide.text"></p>
          <a :href="slide.link" class="slide-cta" :style="slide.ctaStyle">
            {{ slide.cta }}
          </a>
        </div>
      </div>

      <button class="slide-nav-btn" id="prevBtn" @click="prevSlide">❮</button>
      <button class="slide-nav-btn" id="nextBtn" @click="nextSlide">❯</button>
    </section>

    <!-- ====================== FLASH SALE ===================== -->
    <section class="flash-sale-hour">
      <div class="container">
        <div class="sale-header">
          <h2 style="color: white; font-size: 30px;">
            ⚡️ FLASH SALE KHUNG GIỜ VÀNG ⚡️
          </h2>
        </div>

        <div class="timer-container-new">
          <p>Chỉ còn lại:</p>
          <div class="sale-timer">
            <div class="timer-box">{{ hours }}</div>
            <span class="timer-separator">:</span>
            <div class="timer-box">{{ minutes }}</div>
            <span class="timer-separator">:</span>
            <div class="timer-box">{{ seconds }}</div>
          </div>
        </div>

        <!-- ===== PRODUCTS ===== -->
        <div class="sale-product-grid">
          <div
            class="sale-product-card"
            v-for="(p, i) in flashSaleProducts"
            :key="i"
          >
            <img :src="p.img" :alt="p.name" />
            <h3>{{ p.name }}</h3>
            <p class="original-price">{{ p.oldPrice }}</p>
            <p class="sale-price">{{ p.price }}</p>

            <div class="sale-actions">
              <a href="#" class="sale-btn">Mua ngay</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ====================== BEST SELLER CAROUSEL ===================== -->
    <h2 class="section-title">SẢN PHẨM BÁN CHẠY</h2>

    <section class="product-carousel-wrap">
      <div class="product-list-container">
        <div class="product-list" ref="bestSellerList">
          <div
            class="product-card-slide"
            v-for="(p, i) in bestSellers"
            :key="i"
          >
            <img :src="p.img" :alt="p.name" />
            <h3>{{ p.name }}</h3>
            <div class="stars">{{ p.stars }}</div>
            <p>{{ p.price }}</p>

            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a href="#" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>
        </div>
      </div>

      <button
        class="carousel-nav-btn"
        @click="scrollList(bestSellerList, 'prev')"
      >
        ❮
      </button>
      <button
        class="carousel-nav-btn"
        @click="scrollList(bestSellerList, 'next')"
      >
        ❯
      </button>
    </section>

    <!-- ====================== POPULAR CAROUSEL ===================== -->
    <h2 class="section-title">GIÀY PHỔ BIẾN</h2>

    <section class="product-carousel-wrap">
      <div class="product-list-container">
        <div class="product-list" ref="popularList">
          <div
            class="product-card-slide"
            v-for="(p, i) in popularProducts"
            :key="i"
          >
            <img :src="p.img" :alt="p.name" />
            <h3>{{ p.name }}</h3>
            <div class="stars">{{ p.stars }}</div>
            <p>{{ p.price }}</p>

            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a href="#" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>
        </div>
      </div>

      <button class="carousel-nav-btn" @click="scrollList(popularList, 'prev')">❮</button>
      <button class="carousel-nav-btn" @click="scrollList(popularList, 'next')">❯</button>
    </section>

    <!-- (… GIỮ NGUYÊN CÁC PHẦN CÒN LẠI NHƯ HTML GỐC …) -->

  </main>
</template>

<script setup>
import { ref, onMounted } from "vue";

//
// ====================== HERO CAROUSEL ======================
//

const currentIndex = ref(0);

const slides = [
  {
    img: "img/banner-giay-slide-1.jpg",
    title: "GIÀY CHÍNH HÃNG",
    text: "LI-NING",
    cta: "TRẢI NGHIỆM KHÔNG GIỚI HẠN",
    link: "#",
    ctaStyle: "",
  },
  {
    img: "img/anhbannergiay-slide-2.jpg",
    title: "SALE CUỐI MÙA",
    text: "Giảm đến 40% cho Adidas!",
    cta: "SĂN SALE NGAY",
    link: "#",
    ctaStyle: "background:white; color:#ff4500",
  },
  {
    img: "img/anhgiay-slide-3.jpg",
    title: "NEW BALANCE 550",
    text: "Bộ sưu tập retro đã trở lại",
    cta: "XEM NGAY",
    link: "#",
    ctaStyle: "",
  },
];

let slideAuto;

const nextSlide = () => {
  currentIndex.value = (currentIndex.value + 1) % slides.length;
};
const prevSlide = () => {
  currentIndex.value =
    (currentIndex.value - 1 + slides.length) % slides.length;
};

onMounted(() => {
  slideAuto = setInterval(nextSlide, 5000);
});

//
// ====================== FLASH SALE TIMER ======================
//

const hours = ref("00");
const minutes = ref("00");
const seconds = ref("00");

function startTimer() {
  const end = new Date();
  end.setHours(end.getHours() + 4);

  setInterval(() => {
    const now = new Date();
    let distance = end - now;

    if (distance < 0) distance = 0;

    hours.value = String(
      Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
    ).padStart(2, "0");

    minutes.value = String(
      Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))
    ).padStart(2, "0");

    seconds.value = String(
      Math.floor((distance % (1000 * 60)) / 1000)
    ).padStart(2, "0");
  }, 1000);
}

//
// ====================== PRODUCT CAROUSELS ======================
//

const bestSellerList = ref(null);
const popularList = ref(null);

function scrollList(listRef, direction) {
  const el = listRef.value;
  if (!el) return;

  const amount = el.clientWidth - 40;
  el.scrollLeft += direction === "next" ? amount : -amount;
}

//
// DATA (flash sale, best seller...) – bạn có thể thay bằng API sau này
//

const flashSaleProducts = [];  
const bestSellers = [];  
const popularProducts = [];

onMounted(() => {
  startTimer();
});
</script>

<style>
        /* --- CSS TỔNG THỂ --- */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
            color: #1a1a1a;
        }

        .container {
            width: 90%;
            max-width: 1300px;
            margin: 0 auto;
        }

        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 24px;
            font-weight: 700;
            margin: 40px 0 20px;
            text-align: center;
            color: #2b2a2a;
            text-transform: uppercase;
        }


        /* --- SLIDE BANNER LỚN --- */
        .hero-carousel {
            width: 100%;
            overflow: hidden;
            position: relative;
            margin-bottom: 40px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .slide-container {
            display: flex;
            transition: transform 0.5s ease-in-out;
            /* Cập nhật chiều rộng cho 3 slides */
            width: 300%;
        }

        .slide {
            /* Chiếm 1/3 tổng chiều rộng của slide-container */
            width: calc(100% / 3);
            height: 300px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            color: #fff;
            background-position: center center;
            background-size: cover;
            background-repeat: no-repeat;
            position: relative;
            padding: 0 5%;
            box-sizing: border-box;
        }

        .slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .slide h1,
        .slide p,
        .slide a {
            z-index: 2;
            position: relative;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
            font-family: 'Montserrat', sans-serif;
            text-align: left;
        }

        .slide h1 {
            font-size: 40px;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .slide-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.4);
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 24px;
            z-index: 10;
            opacity: 0.8;
            transition: opacity 0.3s, background-color 0.3s;
        }

        .slide-nav-btn:hover {
            opacity: 1;
            background-color: #007bff;
        }

        #prevBtn {
            left: 0;
            border-radius: 0 5px 5px 0;
        }

        #nextBtn {
            right: 0;
            border-radius: 5px 0 0 5px;
        }

        .slide-cta {
            background-color: #007bff;
            color: white;
            padding: 10px 25px;
            text-decoration: none;
            font-weight: 600;
            border-radius: 4px;
            margin-top: 15px;
            transition: background-color 0.2s;
        }

        .slide-cta:hover {
            background-color: #0056b3;
        }


        /* --- FLASH SALE --- */
        .flash-sale-hour {
            background-color: #a80a0a;
            color: white;
            padding: 20px 0 40px;
            margin-bottom: 40px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .sale-header h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 30px;
            font-weight: 700;
            margin: 0 0 20px;
            color: #ffc107;
            text-transform: uppercase;
        }

        .timer-container-new {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 10px;
        }

        .timer-container-new p {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 10px;
            margin-top: 0;
        }

        .sale-timer {
            display: flex;
            gap: 10px;
            font-weight: 700;
        }

        .timer-box {
            background-color: white;
            color: #d20505;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 24px;
            line-height: 1;
        }

        .timer-separator {
            font-size: 24px;
            line-height: 1.5;
        }

        .sale-product-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            width: 90%;
            max-width: 1200px;
            margin: 20px auto 0;
        }

        .sale-product-card {
            background-color: white;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Vùng nút */

        .sale-actions {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 12px 0;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            justify-content: center;
            transform: translateY(100%);
            transition: 0.3s ease;
        }

        /* hover vào nút hiện lên */
        .sale-product-card:hover .sale-actions {
            transform: translateY(0);
        }

        /* nút mua */
        .sale-btn {
            background: #d20505;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            border-radius: 4px;
            transition: 0.2s;
        }

        .sale-btn:hover {
            background: #a50404;
        }

        .sale-product-card img {
            width: 100%;
            height: 180px;
            border-radius: 10px;
            object-fit: cover;
            display: block;
        }

        .sale-product-card h3 {
            font-size: 14px;
            color: #333;
            margin: 5px 0;
        }

        .original-price {
            color: #888;
            text-decoration: line-through;
            font-size: 12px;
            margin: 0;
        }

        .sale-price {
            color: #d20505;
            font-weight: 700;
            font-size: 18px;
            margin: 0;
        }


        /* --- PRODUCT CAROUSEL --- */
        .product-carousel-wrap {
            position: relative;
            margin-bottom: 50px;
            width: 100%;
            /* Cần thiết để các nút điều hướng nằm ngoài */
            padding: 0 40px;
            box-sizing: border-box;
        }

        .product-list-container {
            /* Vẫn ẩn thanh cuộn và giữ khung nhìn */
            overflow: hidden;
        }

        .product-list {
            display: flex;
            gap: 20px;
            /* Thay đổi: Sử dụng cuộn ngang để dễ dàng cuộn bằng JavaScript */
            overflow-x: scroll;
            scroll-behavior: smooth;
            padding-bottom: 15px;

            /* Ẩn thanh cuộn mặc định */
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .product-list::-webkit-scrollbar {
            display: none;
        }

        .product-card-slide,
        .product-card {
            /* Thiết lập chiều rộng cho 5 sản phẩm/hàng */
            flex: 0 0 calc((100% - (4 * 20px)) / 5);
            min-width: calc((100% - (4 * 20px)) / 5);

            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            padding: 15px;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
        }

        .product-card-slide:hover,
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .product-card-slide img,
        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            display: block;
        }

        .product-card-slide h3,
        .product-card h3 {
            font-size: 15px;
            font-weight: 500;
            margin: 10px 0 5px;
            color: #333;
        }

        .product-card-slide p,
        .product-card p {
            font-size: 17px;
            font-weight: 700;
            color: #007bff;
            margin: 5px 0 10px;
        }

        .stars {
            color: #ffc107;
            font-size: 14px;
        }

        /* Nút điều hướng sản phẩm */
        .carousel-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.6);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 20px;
            z-index: 10;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            line-height: 18px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            transition: background-color 0.2s, transform 0.2s;
        }

        .carousel-nav-btn:hover {
            background-color: #007bff;
            transform: translateY(-50%) scale(1.05);
        }

        /* Đặt các nút ra ngoài mép của product-list-container */
        #prevProductBtn1,
        #prevProductBtn2,
        #prevProductBtn3 {
            left: 0;
        }

        #nextProductBtn1,
        #nextProductBtn2,
        #nextProductBtn3 {
            right: 0;
        }

        .carousel-nav-btn:disabled {
            background-color: rgba(0, 0, 0, 0.2);
            cursor: not-allowed;
            opacity: 0.5;
        }

        /* --- BUTTONS ON HOVER (Không thay đổi) --- */
        .product-card-slide .product-actions,
        .product-card .product-actions {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 10px 0;
            background-color: rgba(255, 255, 255, 0.95);
            transform: translateY(100%);
            transition: transform 0.3s ease-out;
            z-index: 5;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .product-card-slide:hover .product-actions,
        .product-card:hover .product-actions {
            transform: translateY(0);
        }

        .action-btn {
            background-color: #007bff;
            color: white;
            padding: 7px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            font-size: 13px;
            display: inline-block;
            transition: background-color 0.2s;
        }

        .action-btn.secondary {
            background-color: #555;
        }

        .action-btn:hover {
            background-color: #0056b3;
        }

        .action-btn.secondary:hover {
            background-color: #333;
        }

        /* --- TESTIMONIALS & FOOTER (Không thay đổi) --- */
        .testimonials-section {
            background-color: #ffffff;
            padding: 60px 0;
            text-align: center;
            margin-top: 40px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }

        .review-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .review-card {
            background-color: #fcfcfc;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            padding: 30px;
            text-align: left;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }

        .reviewer-info img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
        }

        .reviewer-details {
            font-size: 14px;
        }

        .reviewer-details strong {
            display: block;
            color: #1a1a1a;
            font-weight: 600;
        }

        .reviewer-details small {
            color: #888;
        }

        .review-card p {
            font-style: italic;
            color: #444;
            margin-bottom: 10px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* SECTION bao ngoài */
        .xshop-section {
            width: 100%;
            padding: 50px 0;
            display: flex;
            justify-content: center;
        }

        /* Khung bên trong */
        .content-box {
            width: 100%;
            background: #fff;
            border: 3px solid #3fa9f5;
            display: flex;
            padding: 30px;
        }

        /* Text bên trái */
        .left {
            width: 45%;
            padding-right: 30px;
            line-height: 1.6;
        }

        .stat {
            margin-top: 20px;
        }

        .stat h1 {
            font-size: 40px;
            margin: 0;
        }

        .stat span {
            font-size: 14px;
        }

        /* Ảnh bên phải */
        .right {
            width: 55%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .right img {
            width: 100%;
            border-radius: 8px;
            object-fit: cover;
        }

        /* Tiêu đề */
        .news-section {
            text-align: center;
            padding: 50px 0;
        }

        .news-title {
            font-size: 36px;
            margin-bottom: 40px;
        }

        /* Container thẻ tin */
        .news-container {
            display: flex;
            justify-content: center;
            gap: 40px;
        }

        /* Card tin tức */
        .news-card {
            width: 280px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .news-card:hover {
            transform: translateY(-5px);
        }

        /* Hình ảnh */
        .img-box {
            position: relative;
        }

        .img-box img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        /* Tag màu xanh */
        .tag {
            position: absolute;
            top: 12px;
            left: 12px;
            background: #58d68d;
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }

        /* Nội dung */
        .news-content {
            padding: 20px;
            text-align: left;
        }

        .news-content h3 {
            font-size: 17px;
            margin-bottom: 15px;
        }

        /* Link xem chi tiết */
        .more {
            font-size: 13px;
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }

        .more:hover {
            text-decoration: underline;
        }
    </style>
