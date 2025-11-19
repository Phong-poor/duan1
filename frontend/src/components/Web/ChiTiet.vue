<template>
  <Header/>
  <div class="product-detail-wrapper">
    <main class="container product-detail-page">
      <div class="product-content">
        <section class="product-gallery">
          <img
            class="main-image"
            :src="selectedImage.src"
            :alt="selectedImage.alt"
          />

          <div class="thumbnail-gallery">
            <img
              v-for="thumb in thumbnails"
              :key="thumb.id"
              :src="thumb.src"
              :alt="thumb.alt"
              :class="{ active: thumb.id === selectedImage.id }"
              @click="selectImage(thumb)"
            />
          </div>
        </section>

        <section class="product-info">
          <nav class="breadcrumb">
            <a href="#">NIKE</a> / <a href="#">Air Max</a> / Air Max 97 OG Silver
          </nav>

          <h1>NIKE AIR MAX 97 OG SILVER BULLET</h1>
          <p class="sku">Mã sản phẩm: DM0028-100</p>

          <div class="rating">
            <i class="fas fa-star" />
            <i class="fas fa-star" />
            <i class="fas fa-star" />
            <i class="fas fa-star" />
            <i class="fas fa-star-half-alt" /> (4.8/5) | Đã bán 250+
          </div>

          <div class="price-box">
            <span class="current-price">3,700,000 VNĐ</span>
            <span class="original-price">4,100,000 VNĐ</span>
            <span class="discount-tag">GIẢM 10%</span>
          </div>

          <div class="option-group">
            <label>Kích cỡ (Size):</label>
            <div class="size-options">
              <button
                v-for="size in sizes"
                :key="size.value"
                :class="{
                  selected: size.value === selectedSize,
                  'out-of-stock': size.disabled,
                }"
                type="button"
                :disabled="size.disabled"
                @click="selectSize(size)"
              >
                {{ size.value }}
              </button>
            </div>
          </div>

          <div class="option-group">
            <label>Màu sắc:</label>
            <div class="color-options">
              <span
                v-for="color in colors"
                :key="color.id"
                :title="color.label"
                :class="{ selected: color.id === selectedColor }"
                :style="{
                  backgroundColor: color.swatch,
                  borderColor: color.border ?? '#ccc',
                }"
                @click="selectColor(color)"
              />
            </div>
          </div>

          <div class="action-buttons">
            <button class="btn-add-to-cart" type="button" @click="handleAddToCart">
              <i class="fas fa-shopping-cart" /> THÊM VÀO GIỎ
            </button>
            <button class="btn-buy-now" type="button" @click="handleBuyNow">
              MUA NGAY (Giao hàng nhanh)
            </button>
          </div>

          <section class="product-policy">
            <h3><i class="fas fa-shield-alt" /> Chính sách mua hàng</h3>
            <ul>
              <li><i class="fas fa-truck" /> Miễn phí vận chuyển** cho đơn hàng trên 2.000.000 VNĐ.</li>
              <li><i class="fas fa-undo" /> Đổi size thoải mái** trong vòng 7 ngày (giữ nguyên tag).</li>
              <li><i class="fas fa-certificate" /> Cam kết chính hãng 100%, đền gấp đôi nếu phát hiện hàng giả.</li>
            </ul>
          </section>

          <section class="product-description">
            <h3>Mô tả chi tiết</h3>
            <p>
              Nike Air Max 97 OG Silver Bullet là biểu tượng của văn hóa sneaker, lấy cảm hứng từ tàu cao tốc Nhật Bản.
              Với thiết kế gợn sóng đặc trưng và lớp đệm Air Max full-length tiên tiến, đôi giày mang lại sự thoải mái tối đa và phong cách đường phố không thể nhầm lẫn.
              Phiên bản Silver Bullet này là bản tái phát hành được săn đón nhất mọi thời đại.
            </p>
            <p>
              * <strong>Chất liệu:</strong> Da tổng hợp cao cấp, lưới thoáng khí.
              * <strong>Đặc điểm:</strong> Hệ thống dây buộc ẩn, lớp phản quang 3M.
              * <strong>Phù hợp:</strong> Đi chơi, đi làm, phối hợp thời trang đường phố (streetwear).
            </p>
          </section>
        </section>
      </div>

      <section class="related-products">
        <h2 class="section-title">SẢN PHẨM LIÊN QUAN</h2>
        <div class="product-grid-wrapper">
          <button class="carousel-arrow left" type="button" @click="scrollCarousel(-1)">
            <i class="fas fa-chevron-left" />
          </button>

          <div class="product-grid" ref="carouselRef">
            <article v-for="product in relatedProducts" :key="product.id" class="product-card">
              <img :src="product.image" :alt="product.title" />
              <h3>{{ product.title }}</h3>
              <div class="stars">{{ product.rating }}</div>
              <p class="original-price-small">{{ product.originalPrice }}</p>
              <p class="current-price-large">{{ product.currentPrice }}</p>
              <div class="product-actions">
                <a href="#" class="action-btn">Chi tiết</a>
                <a href="#" class="action-btn">Mua ngay</a>
              </div>
            </article>
          </div>

          <button class="carousel-arrow right" type="button" @click="scrollCarousel(1)">
            <i class="fas fa-chevron-right" />
          </button>
        </div>
      </section>
    </main>

    <transition name="popup">
      <div v-if="popupVisible" class="popup-toast">
        {{ popupMessage }}
      </div>
    </transition>
  </div>
  <Footer/>
</template>

<script setup>
import { onBeforeUnmount, ref } from 'vue';
import Header from '../../header.vue';
import Footer from '../../footer.vue';

import heroImage from '@/assets/bannergiay.jpg';
import thumb1 from '@/assets/images (4).jpg';
import thumb2 from '@/assets/images (2).jpg';
import thumb3 from '@/assets/images (5).jpg';
import thumb4 from '@/assets/images (1).jpg';
import related1 from '@/assets/images (2).jpg';
import related2 from '@/assets/images (3).jpg';
import related3 from '@/assets/images (4).jpg';
import related4 from '@/assets/cv-2110.jpg';
import related5 from '@/assets/images.jpg';
import related6 from '@/assets/bannergiay2.jpg';

const thumbnails = [
  { id: 0, src: heroImage, alt: 'Nike Air Max Hero' },
  { id: 1, src: thumb1, alt: 'View 1' },
  { id: 2, src: thumb2, alt: 'View 2' },
  { id: 3, src: thumb3, alt: 'View 3' },
  { id: 4, src: thumb4, alt: 'View 4' },
];

const selectedImage = ref(thumbnails[0]);

const selectImage = (image) => {
  selectedImage.value = image;
};

const sizes = [
  { value: '39', disabled: false },
  { value: '40', disabled: false },
  { value: '41', disabled: false },
  { value: '42', disabled: false },
  { value: '43', disabled: true },
  { value: '44', disabled: false },
  { value: '45', disabled: false },
];
const selectedSize = ref('40');

const selectSize = (size) => {
  if (size.disabled) return;
  selectedSize.value = size.value;
};

const colors = [
  { id: 'silver', label: 'Silver Bullet', swatch: 'silver' },
  { id: 'black', label: 'Black/White', swatch: 'black' },
  { id: 'navy', label: 'Navy Blue', swatch: 'navy' },
  { id: 'white', label: 'Triple White', swatch: 'white', border: '#000' },
];
const selectedColor = ref(colors[0]);

const selectColor = (color) => {
  selectedColor.value = color;
};

const relatedProducts = [
  {
    id: 1,
    image: related1,
    title: 'Nike Dunk High Retro Black/Red',
    rating: '★★★★★',
    originalPrice: '3,800,000 VNĐ',
    currentPrice: '3,000,000 VNĐ',
  },
  {
    id: 2,
    image: related2,
    title: "Nike Air Max 1 'Patta' Wave",
    rating: '★★★★☆',
    originalPrice: '5,500,000 VNĐ',
    currentPrice: '4,800,000 VNĐ',
  },
  {
    id: 3,
    image: related3,
    title: "Nike Air Force 1 'White'",
    rating: '★★★★★',
    originalPrice: '2,890,000 VNĐ',
    currentPrice: '2,500,000 VNĐ',
  },
  {
    id: 4,
    image: related4,
    title: "Nike Blazer Mid '77 Jumbo",
    rating: '★★★★☆',
    originalPrice: '2,990,000 VNĐ',
    currentPrice: '2,600,000 VNĐ',
  },
  {
    id: 5,
    image: related5,
    title: 'Nike Jordan 1 Mid Grey/Black',
    rating: '★★★★★',
    originalPrice: '4,200,000 VNĐ',
    currentPrice: '3,900,000 VNĐ',
  },
  {
    id: 6,
    image: related6,
    title: 'Nike x Sacai VaporWaffle',
    rating: '★★★★★',
    originalPrice: '8,000,000 VNĐ',
    currentPrice: '7,500,000 VNĐ',
  },
];

const carouselRef = ref(null);

const scrollCarousel = (direction) => {
  carouselRef.value?.scrollBy({
    left: direction * 500,
    behavior: 'smooth',
  });
};

const popupVisible = ref(false);
const popupMessage = ref('');
let popupTimer;

const triggerPopup = (message) => {
  popupMessage.value = message;
  popupVisible.value = true;
  if (popupTimer) {
    clearTimeout(popupTimer);
  }
  popupTimer = setTimeout(() => {
    popupVisible.value = false;
  }, 1500);
};

const handleAddToCart = () => {
  if (!selectedSize.value || !selectedColor.value) {
    triggerPopup('⚠ Vui lòng chọn size và màu!');
    return;
  }

  triggerPopup(`✔ Đã thêm: Size ${selectedSize.value} – Màu ${selectedColor.value.label}`);
};

const handleBuyNow = () => {
  triggerPopup('🚚 Đặt hàng thành công! Chúng tôi sẽ liên hệ ngay.');
};

const cleanupPopup = () => {
  if (popupTimer) {
    clearTimeout(popupTimer);
  }
};

onBeforeUnmount(cleanupPopup);
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500;700&display=swap');
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');

.product-detail-wrapper {
  font-family: 'Roboto', sans-serif;
  background-color: #f7f7f7;
  color: #1a1a1a;
  min-height: 100vh;
  padding-top: 110px;
  padding-bottom: 40px;
}

.container {
  width: 90%;
  max-width: 1300px;
  margin: 0 auto;
}

.product-detail-page {
  padding: 20px 0;
}

.product-content {
  display: flex;
  gap: 20px;
  background-color: #fff;
  padding: 15px;
  border-radius: 6px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.product-gallery {
  flex: 0 0 400px;
  max-width: 400px;
  position: sticky;
  top: 15px;
  align-self: flex-start;
  padding-right: 15px;
}

.main-image {
  width: 100%;
  height: 400px;
  object-fit: cover;
  border-radius: 4px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  margin-bottom: 6px;
}

.thumbnail-gallery {
  display: flex;
  gap: 8px;
  overflow-x: auto;
  padding: 0;
}

.thumbnail-gallery img {
  width: 70px;
  height: 70px;
  object-fit: cover;
  border-radius: 3px;
  cursor: pointer;
  border: 2px solid transparent;
  transition: border 0.2s;
}

.thumbnail-gallery img.active {
  border-color: #007bff;
}

.product-info {
  flex-grow: 1;
}

.breadcrumb {
  font-size: 11px;
  margin-bottom: 4px;
  color: #777;
}

.product-info h1 {
  font-size: 28px;
  font-weight: 700;
  margin: 0 0 4px;
  color: #1a1a1a;
}

.sku {
  font-size: 11px;
  margin-bottom: 6px;
  color: #999;
}

.rating {
  font-size: 14px;
  margin-bottom: 10px;
  color: #ffc107;
}

.price-box {
  border-top: 1px solid #eee;
  border-bottom: 1px solid #eee;
  padding: 8px 0;
  margin-bottom: 10px;
}

.current-price {
  font-family: 'Montserrat', sans-serif;
  font-size: 32px;
  font-weight: 700;
  color: #e53935;
}

.original-price {
  font-size: 14px;
  color: #999;
  text-decoration: line-through;
  margin-left: 6px;
}

.discount-tag {
  background-color: #e53935;
  color: #fff;
  padding: 1px 4px;
  border-radius: 3px;
  font-size: 10px;
  font-weight: 500;
  margin-left: 5px;
}

.option-group {
  margin-bottom: 15px;
}

.option-group label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 5px;
  color: #333;
}

.size-options button {
  padding: 5px 8px;
  margin-right: 5px;
  border: 1px solid #ccc;
  background-color: #fff;
  cursor: pointer;
  border-radius: 3px;
  font-size: 12px;
  min-width: 25px;
  height: 25px;
  line-height: 1;
  font-weight: 500;
  transition: all 0.2s;
}

.size-options button.selected {
  border-color: #007bff;
  background-color: #007bff;
  color: #fff;
}

.size-options button.out-of-stock {
  opacity: 0.5;
  text-decoration: line-through;
  cursor: not-allowed;
}

.color-options span {
  display: inline-block;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 2px solid #ccc;
  margin-right: 8px;
  cursor: pointer;
}

.color-options span.selected {
  border-color: #1a1a1a;
  box-shadow: 0 0 0 2px #007bff;
}

.action-buttons {
  display: flex;
  gap: 8px;
  margin-bottom: 15px;
}

.btn-add-to-cart,
.btn-buy-now {
  flex-grow: 1;
  padding: 10px;
  border: none;
  border-radius: 3px;
  font-size: 14px;
  font-weight: 600;
  text-transform: uppercase;
  cursor: pointer;
  transition: background-color 0.3s, transform 0.1s;
}

.btn-add-to-cart {
  background-color: #333;
  color: #fff;
}

.btn-add-to-cart:hover {
  background-color: #1a1a1a;
  transform: translateY(-1px);
}

.btn-buy-now {
  background-color: #007bff;
  color: #fff;
}

.btn-buy-now:hover {
  background-color: #0056b3;
  transform: translateY(-1px);
}

.product-policy h3,
.product-description h3 {
  font-family: 'Montserrat', sans-serif;
  font-size: 16px;
  font-weight: 600;
  margin-top: 15px;
  margin-bottom: 8px;
  color: #1a1a1a;
  border-bottom: 2px solid #007bff;
  padding-bottom: 3px;
  display: inline-block;
}

.product-policy ul {
  padding-left: 18px;
}

.product-policy ul li,
.product-description p {
  line-height: 1.5;
  color: #555;
  font-size: 12px;
}

.product-description p strong {
  color: #1a1a1a;
}

.related-products {
  padding: 40px 0;
  background-color: #a50138;
  position: relative;
  overflow: hidden;
  margin-top: 30px;
  border-radius: 8px;
}

.related-products::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: repeating-linear-gradient(
    45deg,
    rgba(255, 255, 255, 0.2),
    rgba(255, 255, 255, 0.2) 20px,
    transparent 20px,
    transparent 40px
  );
  opacity: 0.1;
  z-index: 0;
}

.related-products .section-title {
  font-family: 'Montserrat', sans-serif;
  font-size: 30px;
  font-weight: 700;
  text-align: center;
  margin: 0 0 30px;
  color: #1a1a1a;
  position: relative;
  z-index: 1;
}

.related-products .section-title::before {
  content: '🔥';
  margin-right: 10px;
}

.product-grid-wrapper {
  position: relative;
  margin: 0 -20px;
}

.product-grid {
  display: flex;
  gap: 20px;
  padding: 0 20px;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  scroll-padding-left: 20px;
  scrollbar-width: none;
}

.product-grid::-webkit-scrollbar {
  display: none;
}

.product-card {
  flex: 0 0 calc(20% - 20px);
  scroll-snap-align: start;
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
  text-align: center;
  padding-bottom: 15px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  overflow: hidden;
  position: relative;
  z-index: 1;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
}

.product-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  margin-bottom: 10px;
  border-radius: 12px 12px 0 0;
}

.product-card h3 {
  font-size: 16px;
  margin: 10px 10px 5px;
  font-weight: 600;
  color: #333;
}

.product-card .stars {
  font-size: 14px;
  color: #ffc107;
  margin-bottom: 5px;
}

.original-price-small {
  font-size: 13px;
  color: #999;
  text-decoration: line-through;
  margin: 0;
}

.current-price-large {
  font-family: 'Montserrat', sans-serif;
  font-size: 22px;
  font-weight: 700;
  color: #e53935;
  margin: 5px 0 15px;
}

.product-card .action-btn {
  display: inline-block;
  padding: 8px 15px;
  font-size: 12px;
  background-color: #007bff;
  color: #fff;
  text-decoration: none;
  border-radius: 5px;
  transition: background-color 0.3s;
  margin: 0 4px;
}

.product-card .action-btn:hover {
  background-color: #0056b3;
}

.carousel-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background-color: rgba(0, 0, 0, 0.6);
  color: #fff;
  border: none;
  padding: 10px 15px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 20px;
  z-index: 2;
  transition: background-color 0.3s;
}

.carousel-arrow:hover {
  background-color: rgba(0, 0, 0, 0.8);
}

.carousel-arrow.left {
  left: 10px;
}

.carousel-arrow.right {
  right: 10px;
}

.popup-toast {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 12px 18px;
  background: #28a745;
  color: #fff;
  border-radius: 6px;
  font-size: 14px;
  z-index: 9999;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.popup-enter-active,
.popup-leave-active {
  transition: opacity 0.3s;
}

.popup-enter-from,
.popup-leave-to {
  opacity: 0;
}

@media (max-width: 1024px) {
  .product-content {
    flex-direction: column;
  }

  .product-gallery {
    position: static;
    max-width: 100%;
  }

  .product-card {
    flex: 0 0 calc(50% - 20px);
  }
}

@media (max-width: 768px) {
  .carousel-arrow {
    display: none;
  }

  .product-card {
    flex: 0 0 calc(70% - 20px);
  }
}

@media (max-width: 540px) {
  .product-card {
    flex: 0 0 calc(90% - 20px);
  }
}
</style>


