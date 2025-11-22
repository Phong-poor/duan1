<template>
  <div class="nike-page">
    <HeaderWeb />
    <main class="container">
      <section class="brand-banner">
        <div class="slide-container" ref="slideContainer">
          <div class="slide active">
            <img :src="bannerSlide1" alt="Nike Air Slide 1" />
            <h1>THẾ GIỚI CỦA NIKE AIR</h1>
          </div>
          <div class="slide">
            <img :src="bannerSlide2" alt="Nike Air Slide 2" />
            <h1>ĐẲNG CẤP CÔNG NGHỆ NIKE</h1>
          </div>
          <div class="slide">
            <img :src="bannerSlide3" alt="Nike Air Slide 3" />
            <h1>CHẠY BỨT TỐC – AIR MAX</h1>
          </div>
        </div>
      </section>

      <div class="filter-bar">
        <div>
          <label for="sort">Sắp xếp theo:</label>
          <select id="sort">
            <option value="moi_nhat">Mới nhất</option>
            <option value="gia_tang">Giá tăng dần</option>
            <option value="gia_giam">Giá giảm dần</option>
          </select>
        </div>
        <div class="filter-inline">
          <label for="filter_size">Kích cỡ:</label>
          <select id="filter_size">
            <option value="all">Tất cả</option>
            <option value="40">40</option>
            <option value="41">41</option>
            <option value="42">42</option>
          </select>

          <label for="filter_type">Loại:</label>
          <select id="filter_type">
            <option value="all">Tất cả</option>
            <option value="air_max">Air Max</option>
            <option value="dunk">Dunk</option>
            <option value="jordan">Jordan</option>
          </select>
        </div>
      </div>

      <h2 class="section-title">TẤT CẢ GIÀY NIKE (100+)</h2>

      <section class="product-grid">
        <div class="product-card">
          <img :src="productImg1" alt="Nike Dunk Low Retro Panda" />
          <h3>Nike Dunk Low Retro Panda</h3>
          <div class="stars">★★★★★</div>
          <p>3,500,000 VNĐ</p>
          <div class="product-actions">
            <button class="action-btn" type="button">Mua ngay</button>
            <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
          </div>
        </div>

        <div class="product-card">
          <img :src="productImg2" alt="Nike Air Force 1 '07 LV8" />
          <h3>Nike Air Force 1 '07 LV8</h3>
          <div class="stars">★★★★☆</div>
          <p>2,890,000 VNĐ</p>
          <div class="product-actions">
            <button class="action-btn" type="button">Mua ngay</button>
            <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
          </div>
        </div>

        <div class="product-card">
          <img :src="productImg2" alt="Nike Air Force 1 '07 LV8" />
          <h3>Nike Air Force 1 '07 LV8</h3>
          <div class="stars">★★★★☆</div>
          <p>2,890,000 VNĐ</p>
          <div class="product-actions">
            <button class="action-btn" type="button">Mua ngay</button>
            <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
          </div>
        </div>

        <div class="product-card">
          <img :src="productImg2" alt="Nike Air Force 1 '07 LV8" />
          <h3>Nike Air Force 1 '07 LV8</h3>
          <div class="stars">★★★★☆</div>
          <p>2,890,000 VNĐ</p>
          <div class="product-actions">
            <button class="action-btn" type="button">Mua ngay</button>
            <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
          </div>
        </div>

        <div class="product-card">
          <img :src="productImg2" alt="Nike Air Force 1 '07 LV8" />
          <h3>Nike Air Force 1 '07 LV8</h3>
          <div class="stars">★★★★☆</div>
          <p>2,890,000 VNĐ</p>
          <div class="product-actions">
            <button class="action-btn" type="button">Mua ngay</button>
            <a @click.prevent="goTo('chiTiet')" class="action-btn secondary">Chi tiết</a>
          </div>
        </div>
      </section>
    </main>
    <footerWeb />
  </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref }from 'vue';
import { useRouter } from 'vue-router';
const router=useRouter();
const goTo = (path) => {
  router.push(path);
};
import HeaderWeb from '../../Header-web.vue';
import footerWeb from '../../footer-web.vue';
// import bannerSlide1 from '@/assets/banner-slide-1.png';
// import bannerSlide2 from '@/assets/banner-slide-2.jpg';
// import bannerSlide3 from '@/assets/banner-slide-3.jpg';
// import productImg1 from '@/assets/images (2).jpg';
// import productImg2 from '@/assets/images (3).jpg';

const slideContainer = ref(null);
let slideTimer;

onMounted(() => {
  const slides = slideContainer.value?.querySelectorAll('.slide') ?? [];
  let currentSlide = 0;

  const showSlide = (index) => {
    slides.forEach((slide) => slide.classList.remove('active'));
    slides[index]?.classList.add('active');
  };

  slideTimer = setInterval(() => {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
  }, 4000);
});

onBeforeUnmount(() => {
  if (slideTimer) {
    clearInterval(slideTimer);
  }
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500;700&display=swap');

.nike-page {
  font-family: 'Roboto', sans-serif;
  background-color: #f7f7f7;
  color: #1a1a1a;
}

.container {
  width: 90%;
  max-width: 1300px;
  margin: 0 auto;
  padding-bottom: 60px;
}

.brand-banner {
  position: relative;
  height: 350px;
  overflow: hidden;
  border-radius: 6px;
  margin: 40px 0;
}

.slide-container {
  width: 100%;
  height: 100%;
  position: relative;
}

.slide {
  position: absolute;
  width: 100%;
  height: 100%;
  opacity: 0;
  transition: opacity 1s ease-in-out;
}

.slide.active {
  opacity: 1;
}

.slide img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.slide h1 {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #fff;
  font-size: 48px;
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
  letter-spacing: 4px;
  text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.6);
  text-align: center;
  width: 100%;
}

.filter-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #ffffff;
  padding: 15px 20px;
  margin-bottom: 30px;
  border-radius: 5px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
  flex-wrap: wrap;
  gap: 15px;
}

.filter-bar label {
  font-weight: 500;
  color: #555;
  margin-right: 10px;
}

.filter-bar select {
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 14px;
}

.filter-inline {
  display: flex;
  align-items: center;
  gap: 20px;
}

.section-title {
  font-family: 'Montserrat', sans-serif;
  font-size: 32px;
  font-weight: 600;
  text-align: center;
  margin: 40px 0 30px;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 20px;
  margin-bottom: 50px;
}

.product-card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  padding: 15px;
  text-align: center;
  transition: transform 0.2s, box-shadow 0.2s;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.product-card img {
  width: 100%;
  height: 190px;
  border-radius: 5px;
  object-fit: cover;
  margin-bottom: 8px;
}

.product-card h3 {
  font-size: 15px;
  font-weight: 500;
  margin: 10px 0 5px;
  color: #333;
}

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

.product-actions {
  opacity: 0;
  transition: opacity 0.2s;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.product-card:hover .product-actions {
  opacity: 1;
}

.action-btn {
  background-color: #007bff;
  color: white;
  padding: 7px 12px;
  border: none;
  border-radius: 4px;
  font-weight: 500;
  font-size: 13px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.action-btn.secondary {
  background-color: #555;
}

.action-btn:hover {
  background-color: #0056b3;
}

@media (max-width: 1024px) {
  .product-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 768px) {
  .slide h1 {
    font-size: 32px;
  }

  .product-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 540px) {
  .product-grid {
    grid-template-columns: 1fr;
  }
}
</style>

