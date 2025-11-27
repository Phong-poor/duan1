<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
const router = useRouter();
const goTo = (path) => {
  router.push(path);
};
import HeaderWeb from '../../Header-web.vue';
import footerWeb from '../../footer-web.vue';
// import banner1 from '@/assets/banner-slide-1.png';
// import banner2 from '@/assets/banner-slide-2.jpg';
// import banner3 from '@/assets/banner-slide-3.jpg';
// import imgSale1 from '@/assets/images (1).jpg';
// import imgSale2 from '@/assets/images (2).jpg';
// import imgSale3 from '@/assets/images (3).jpg';
// import imgSale4 from '@/assets/images (4).jpg';
// import imgSale5 from '@/assets/images (5).jpg';
// import bannerGiay from '@/assets/bannergiay.jpg';
// import imgNews1 from '@/assets/images (3).jpg';
// import imgNews2 from '@/assets/images (4).jpg';
// import imgNews3 from '@/assets/images (2).jpg';
// import imgCv2110 from '@/assets/cv-2110.jpg';
// import imgCanhan3 from '@/assets/canhan3.jpg';
// import imgCanhan1 from '@/assets/canhan1.jpg';

onMounted(() => {
  // 1. CAROUSEL BANNER LỚN
  const slideContainer = document.getElementById('slideContainer');
  const slides = document.querySelectorAll('.hero-carousel .slide');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  let currentIndex = 0;
  const totalSlides = slides.length;

  function updateSlidePosition() {
    const slideWidthPercentage = 100 / totalSlides;
    const offset = -currentIndex * slideWidthPercentage;
    slideContainer.style.transform = `translateX(${offset}%)`;
  }

  function goToNextSlide() {
    currentIndex = (currentIndex + 1) % totalSlides;
    updateSlidePosition();
  }

  function goToPrevSlide() {
    currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
    updateSlidePosition();
  }

  let slideInterval = setInterval(goToNextSlide, 5000);

  nextBtn?.addEventListener('click', () => {
    clearInterval(slideInterval);
    goToNextSlide();
    slideInterval = setInterval(goToNextSlide, 5000);
  });

  prevBtn?.addEventListener('click', () => {
    clearInterval(slideInterval);
    goToPrevSlide();
    slideInterval = setInterval(goToNextSlide, 5000);
  });

  // 2. FLASH SALE TIMER
  function updateFlashSaleTimer() {
    const endTime = new Date();
    endTime.setHours(endTime.getHours() + 4);

    const timerFunction = setInterval(() => {
      const now = new Date().getTime();
      const distance = endTime.getTime() - now;

      if (distance < 0) {
        clearInterval(timerFunction);
        const hBox = document.getElementById('hoursBox');
        const mBox = document.getElementById('minutesBox');
        const sBox = document.getElementById('secondsBox');
        if (hBox && mBox && sBox) {
          hBox.textContent = '00';
          mBox.textContent = '00';
          sBox.textContent = '00';
        }
        return;
      }

      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      const hBox = document.getElementById('hoursBox');
      const mBox = document.getElementById('minutesBox');
      const sBox = document.getElementById('secondsBox');
      if (hBox && mBox && sBox) {
        hBox.textContent = String(hours).padStart(2, '0');
        mBox.textContent = String(minutes).padStart(2, '0');
        sBox.textContent = String(seconds).padStart(2, '0');
      }
    }, 1000);
  }

  updateFlashSaleTimer();

  // 3. PRODUCT CAROUSEL
  function updateProductNav(listElement, targetId) {
    const index = targetId.slice(-1);
    const prevButton = document.getElementById(`prevProductBtn${index}`);
    const nextButton = document.getElementById(`nextProductBtn${index}`);

    if (!listElement || !prevButton || !nextButton) return;

    if (listElement.scrollLeft <= 5) {
      prevButton.disabled = true;
    } else {
      prevButton.disabled = false;
    }

    if (listElement.scrollLeft + listElement.clientWidth >= listElement.scrollWidth - 5) {
      nextButton.disabled = true;
    } else {
      nextButton.disabled = false;
    }
  }

  document.querySelectorAll('.carousel-nav-btn').forEach(button => {
    button.addEventListener('click', () => {
      const targetId = button.getAttribute('data-target');
      if (!targetId) return;
      const productList = document.getElementById(targetId);

      if (productList) {
        const containerWidth = productList.parentElement?.offsetWidth ?? productList.clientWidth;
        const scrollAmount = containerWidth - 40;

        if (button.id.startsWith('next')) {
          productList.scrollLeft += scrollAmount;
        } else if (button.id.startsWith('prev')) {
          productList.scrollLeft -= scrollAmount;
        }

        setTimeout(() => updateProductNav(productList, targetId), 300);
      }
    });
  });

  function initProductCarousels() {
    const lists = document.querySelectorAll('.product-list');
    lists.forEach(list => {
      updateProductNav(list, list.id);
      list.addEventListener('scroll', () => updateProductNav(list, list.id));
    });
  }

  window.addEventListener('load', initProductCarousels);
  window.addEventListener('resize', initProductCarousels);
  initProductCarousels();
});
</script>

<template>
  <HeaderWeb/>
  <main class="container main-content">
    <!-- SLIDE BANNER LỚN -->
    <section class="hero-carousel">
      <div class="slide-container" id="slideContainer">
        <div class="slide" :style="{ backgroundImage: `url(${banner1})` }">
          <h1 style="font-size: 48px;">GIÀY CHÍNH HÃNG</h1>
          <p style="font-size: 36px; font-weight: 600; margin-bottom: 0;">LI-NING</p>
          <a href="#" class="slide-cta">TRẢI NGHIỆM KHÔNG GIỚI HẠN</a>
        </div>

        <div class="slide" :style="{ backgroundImage: `url(${banner2})` }">
          <h1>SALE CUỐI MÙA</h1>
          <p>Giảm đến 40% cho các dòng giày chạy bộ Adidas!</p>
          <a href="#" class="slide-cta" style="background-color: white; color: #ff4500;">SĂN SALE NGAY</a>
        </div>

        <div class="slide" :style="{ backgroundImage: `url(${banner3})` }">
          <h1>NEW BALANCE 550</h1>
          <p>Bộ sưu tập retro đã trở lại. Đặt hàng ngay!</p>
          <a href="#" class="slide-cta">XEM NGAY</a>
        </div>
      </div>

      <button class="slide-nav-btn" id="prevBtn">❮</button>
      <button class="slide-nav-btn" id="nextBtn">❯</button>
    </section>

    <!-- FLASH SALE -->
    <section class="flash-sale-hour">
      <div class="container">
        <div class="sale-header">
          <h2 style="color: white; font-size: 30px;">⚡️ FLASH SALE KHUNG GIỜ VÀNG ⚡️</h2>
        </div>

        <div class="timer-container-new">
          <p>Chỉ còn lại:</p>
          <div class="sale-timer">
            <div class="timer-box" id="hoursBox">00</div>
            <span class="timer-separator">:</span>
            <div class="timer-box" id="minutesBox">00</div>
            <span class="timer-separator">:</span>
            <div class="timer-box" id="secondsBox">00</div>
          </div>
        </div>

        <div class="sale-product-grid">
          <div class="sale-product-card">
            <img :src="imgSale1" alt="Giày Sale 1" />
            <h3>Nike Blazer Mid '77</h3>
            <p class="original-price">2,600,000 VNĐ</p>
            <p class="sale-price">1,500,000 VNĐ</p>

            <div class="sale-actions">
              <a href="#" class="sale-btn">Mua ngay</a>
            </div>
          </div>

          <div class="sale-product-card">
            <img :src="imgSale1" alt="Giày Sale 2" />
            <h3>Adidas Samba Classic</h3>
            <p class="original-price">1,990,000 VNĐ</p>
            <p class="sale-price">1,190,000 VNĐ</p>

            <div class="sale-actions">
              <a href="#" class="sale-btn">Mua ngay</a>
            </div>
          </div>

          <div class="sale-product-card">
            <img :src="imgSale3" alt="Giày Sale 3" />
            <h3>Puma Caven Dime</h3>
            <p class="original-price">1,550,000 VNĐ</p>
            <p class="sale-price">999,000 VNĐ</p>

            <div class="sale-actions">
              <a href="#" class="sale-btn">Mua ngay</a>
            </div>
          </div>

          <div class="sale-product-card">
            <img :src="imgSale4" alt="Giày Sale 4" />
            <h3>Vans Sk8-Hi Black</h3>
            <p class="original-price">1,800,000 VNĐ</p>
            <p class="sale-price">900,000 VNĐ</p>

            <div class="sale-actions">
              <a href="#" class="sale-btn">Mua ngay</a>
            </div>
          </div>

          <div class="sale-product-card">
            <img :src="imgSale5" alt="Giày Sale 5" />
            <h3>NB 327 Black/White</h3>
            <p class="original-price">3,100,000 VNĐ</p>
            <p class="sale-price">1,999,000 VNĐ</p>

            <div class="sale-actions">
              <a href="#" class="sale-btn">Mua ngay</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SẢN PHẨM BÁN CHẠY -->
    <h2 class="section-title">SẢN PHẨM BÁN CHẠY</h2>
    <section class="product-carousel-wrap">
      <div class="product-list-container">
        <div class="product-list" id="bestSellerList">
          <div class="product-card-slide">
            <img :src="imgSale5" alt="Nike Air Force 1" />
            <h3>Nike Air Force 1 Low</h3>
            <div class="stars">★★★★☆</div>
            <p>2,890,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')"class="action-btn secondary">Chi tiết</a>
            </div>
          </div>

          <div class="product-card-slide">
            <img :src="imgSale3" alt="Adidas Ultraboost" />
            <h3>Adidas Ultraboost 22</h3>
            <div class="stars">★★★★★</div>
            <p>3,990,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>

          <div class="product-card-slide">
            <img :src="imgSale3" alt="Air Jordan 1" />
            <h3>Air Jordan 1 Low 'Panda'</h3>
            <div class="stars">★★★★★</div>
            <p>4,500,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>

          <div class="product-card-slide">
            <img :src="imgSale2" alt="Converse Chuck Taylor" />
            <h3>Converse Chuck Taylor All Star Classic</h3>
            <div class="stars">★★★★☆</div>
            <p>1,500,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>

          <div class="product-card-slide">
            <img :src="imgSale1" alt="Vans Old Skool" />
            <h3>Vans Old Skool Black/White</h3>
            <div class="stars">★★★★☆</div>
            <p>1,690,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>

          <div class="product-card-slide">
            <img :src="imgSale1" alt="Puma RS-X" />
            <h3>Puma RS-X Pop</h3>
            <div class="stars">★★★★☆</div>
            <p>2,990,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>

          <div class="product-card-slide">
            <img :src="imgSale3" alt="New Balance 550" />
            <h3>New Balance 550 White/Grey</h3>
            <div class="stars">★★★★★</div>
            <p>3,700,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>

          <div class="product-card-slide">
            <img :src="imgSale3" alt="Yeezy 350" />
            <h3>Adidas Yeezy Boost 350 V2</h3>
            <div class="stars">★★★★★</div>
            <p>8,500,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>

          <div class="product-card-slide">
            <img :src="imgSale3" alt="Nike Dunk" />
            <h3>Nike Dunk Low Retro Blue</h3>
            <div class="stars">★★★★☆</div>
            <p>3,700,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>

          <div class="product-card-slide">
            <img :src="imgSale5" alt="New Balance 574" />
            <h3>New Balance 574 Core</h3>
            <div class="stars">★★★★☆</div>
            <p>2,190,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>
        </div>
      </div>
      <button class="carousel-nav-btn" id="prevProductBtn1" data-target="bestSellerList">❮</button>
      <button class="carousel-nav-btn" id="nextProductBtn1" data-target="bestSellerList">❯</button>
    </section>

    <!-- GIÀY PHỔ BIẾN -->
    <h2 class="section-title">GIÀY PHỔ BIẾN</h2>
    <section class="product-carousel-wrap">
      <div class="product-list-container">
        <div class="product-list" id="popularKicksList">
          <div class="product-card-slide">
            <img :src="imgSale1" alt="Popular Sneaker 1" />
            <h3>New Balance 990v6</h3>
            <div class="stars">★★★★★</div>
            <p>5,500,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>

          <div class="product-card-slide">
            <img :src="imgSale3" alt="Popular Sneaker 2" />
            <h3>Nike Blazer Low Jumbo</h3>
            <div class="stars">★★★★☆</div>
            <p>2,350,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>

          <div class="product-card-slide">
            <img :src="imgSale4" alt="Popular Sneaker 3" />
            <h3>Converse Chuck 70 Classic</h3>
            <div class="stars">★★★★☆</div>
            <p>1,990,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>

          <div class="product-card-slide">
            <img :src="imgSale3" alt="Popular Sneaker 4" />
            <h3>Adidas Stan Smith White/Green</h3>
            <div class="stars">★★★★★</div>
            <p>2,590,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>

          <div class="product-card-slide">
            <img :src="imgSale4" alt="Popular Sneaker 5" />
            <h3>Puma Suede Classic</h3>
            <div class="stars">★★★★☆</div>
            <p>1,800,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>

          <div class="product-card-slide">
            <img :src="imgSale2" alt="Popular Sneaker 6" />
            <h3>Vans Checkerboard Slip-On</h3>
            <div class="stars">★★★★☆</div>
            <p>1,490,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>

          <div class="product-card-slide">
            <img :src="imgSale5" alt="Popular Sneaker 7" />
            <h3>Nike Air Max 90 Black</h3>
            <div class="stars">★★★★★</div>
            <p>3,190,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>

          <div class="product-card-slide">
            <img :src="imgSale4" alt="Popular Sneaker 8" />
            <h3>Adidas Gazelle Green</h3>
            <div class="stars">★★★★☆</div>
            <p>2,400,000 VNĐ</p>
            <div class="product-actions">
              <a href="#" class="action-btn">Mua ngay</a>
              <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
            </div>
          </div>
        </div>
      </div>
      <button class="carousel-nav-btn" id="prevProductBtn2" data-target="popularKicksList">❮</button>
      <button class="carousel-nav-btn" id="nextProductBtn2" data-target="popularKicksList">❯</button>
    </section>

    <!-- GIỚI THIỆU XSHOP -->
    <section class="xshop-section">
      <div class="content-box">
        <div class="left">
          <p>
            Hơn 10 năm phát triển, XSHOP luôn mang đến những mẫu giày chất lượng tốt nhất
            với giá cả hợp lí nhất đến tay người tiêu dùng với hệ thống cửa hàng Số 1 Đắk Lắk
            và bán online khắp Việt Nam.
          </p>

          <div class="stat">
            <h1>1 349 841</h1>
            <span>Số Sản Phẩm Đã Bán</span>
          </div>

          <div class="stat">
            <h1>567 392</h1>
            <span>Khách Hàng Đã Mua</span>
          </div>
        </div>

        <div class="right">
          <img :src="bannerGiay" alt="Giày" />
        </div>
      </div>
    </section>

    <!-- TIN TỨC -->
    <section class="news-section">
      <h2 class="news-title">Tin tức mới nhất</h2>

      <div class="news-container">
        <div class="news-card">
          <div class="img-box">
            <img :src="imgNews1" alt="Giày" />
            <span class="tag">Tin tức thời trang</span>
          </div>

          <div class="news-content">
            <h3>7 cách bảo quản giày thể thao tốt nhất</h3>
            <a href="baiiviet.html" class="more">XEM CHI TIẾT ></a>
          </div>
        </div>

        <div class="news-card">
          <div class="img-box">
            <img :src="imgNews2" alt="Sneaker trắng" />
            <span class="tag">Mẹo Shopping</span>
          </div>

          <div class="news-content">
            <h3>Giữ “phong độ” cho Sneaker trắng ra sao</h3>
            <a href="baiiviet.html" class="more">XEM CHI TIẾT ></a>
          </div>
        </div>

        <div class="news-card">
          <div class="img-box">
            <img :src="imgNews3" alt="Giày phối đồ" />
            <span class="tag">Mẹo Shopping</span>
          </div>
          <div class="news-content">
            <h3>Top 5 cách phối đồ với giày thể thao</h3>
            <a href="baiiviet.html"class="more">XEM CHI TIẾT ></a>
          </div>
        </div>
      </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="testimonials-section">
      <h2 class="section-title" style="color: #333; margin-top: 0;">KHÁCH HÀNG NÓI GÌ</h2>
      <div class="container review-grid">
        <div class="review-card">
          <div class="stars">★★★★★</div>
          <p>"Giày chính hãng 100%, chất lượng tuyệt vời. Đóng gói cẩn thận và giao hàng siêu nhanh!"</p>
          <div class="reviewer-info">
            <img :src="imgCv2110" alt="Avatar" />
            <div class="reviewer-details">
              <strong>Anh Duy</strong>
              <small>Đã mua: Nike Air Force 1</small>
            </div>
          </div>
        </div>

        <div class="review-card">
          <div class="stars">★★★★★</div>
          <p>"Dịch vụ chăm sóc khách hàng rất tốt, tư vấn nhiệt tình. Sẽ ủng hộ Kicks Haven lâu dài."</p>
          <div class="reviewer-info">
            <img :src="imgCanhan3" alt="Avatar" />
            <div class="reviewer-details">
              <strong>Minh Hà</strong>
              <small>Đã mua: Adidas Ultraboost</small>
            </div>
          </div>
        </div>

        <div class="review-card">
          <div class="stars">★★★★☆</div>
          <p>"Mẫu mã đa dạng, giá cả cạnh tranh. Có thể tìm thấy những mẫu hiếm mà shop khác không có."</p>
          <div class="reviewer-info">
            <img :src="imgCanhan1" alt="Avatar" />
            <div class="reviewer-details">
              <strong>Quốc Bảo</strong>
              <small>Đã mua: Jordan 1 Low</small>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <footerWeb/>
</template>
<style scoped>
/* --- CSS TỔNG THỂ --- */
.container {
  width: 90%;
  max-width: 1300px;
  margin: 0 auto;
}
.main-content {
  padding-top: 80px; /* Tạo khoảng trống cho header fixed */
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
  width: 300%;
}

.slide {
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
  padding: 0 40px;
  box-sizing: border-box;
}

.product-list-container {
  overflow: hidden;
}

.product-list {
  display: flex;
  gap: 20px;
  overflow-x: scroll;
  scroll-behavior: smooth;
  padding-bottom: 15px;
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.product-list::-webkit-scrollbar {
  display: none;
}

.product-card-slide,
.product-card {
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

#prevProductBtn1,
#prevProductBtn2 {
  left: 0;
}

#nextProductBtn1,
#nextProductBtn2 {
  right: 0;
}

.carousel-nav-btn:disabled {
  background-color: rgba(0, 0, 0, 0.2);
  cursor: not-allowed;
  opacity: 0.5;
}

/* BUTTONS ON HOVER */
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

/* TESTIMONIALS & FOOTER */
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

/* XSHOP SECTION */
.xshop-section {
  width: 100%;
  padding: 50px 0;
  display: flex;
  justify-content: center;
}

.content-box {
  width: 100%;
  background: #fff;
  border: 3px solid #3fa9f5;
  display: flex;
  padding: 30px;
}

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

/* NEWS SECTION */
.news-section {
  text-align: center;
  padding: 50px 0;
}

.news-title {
  font-size: 36px;
  margin-bottom: 40px;
}

.news-container {
  display: flex;
  justify-content: center;
  gap: 40px;
}

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

.img-box {
  position: relative;
}

.img-box img {
  width: 100%;
  height: 220px;
  object-fit: cover;
}

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

.news-content {
  padding: 20px;
  text-align: left;
}

.news-content h3 {
  font-size: 17px;
  margin-bottom: 15px;
}

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


