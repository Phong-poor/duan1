<template>
  <HeaderWeb />
  <div class="product-detail-wrapper">
    <main class="container product-detail-page">
      <div v-if="loading" class="loading-state">Đang tải sản phẩm...</div>
      <div v-else-if="error" class="error-state">{{ error }}</div>
      <div v-else-if="product" class="product-content">
        <!-- GALLERY TRÁI -->
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

        <!-- THÔNG TIN SẢN PHẨM PHẢI -->
        <section class="product-info">
          <nav class="breadcrumb">
            <a href="#">{{ product.tenDM || 'Danh mục' }}</a> / 
            <a href="#">{{ product.tenTH || 'Thương hiệu' }}</a> / 
            {{ product.tenSP }}
          </nav>

          <h1>{{ product.tenSP }}</h1>
          <p class="sku">Mã sản phẩm: {{ product.maSP }}</p>

          <div class="rating">
            <i class="fas fa-star" />
            <i class="fas fa-star" />
            <i class="fas fa-star" />
            <i class="fas fa-star" />
            <i class="fas fa-star-half-alt" /> (4.8/5) | Đã bán 250+
          </div>

          <div class="price-box">
            <span class="current-price">
              {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.giaSauGiam) }}
            </span>
            <span v-if="product.coGiamGia" class="original-price">
              {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.giaSP) }}
            </span>
            <span v-if="product.coGiamGia" class="discount-tag">GIẢM GIÁ</span>
          </div>

          <!-- SIZE -->
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

          <!-- MÀU -->
          <div class="option-group">
            <label>Màu sắc:</label>
            <div class="color-options">
              <span
                v-for="color in colors"
                :key="color.id"
                :title="color.label"
                :class="{ selected: color.id === selectedColor.id }"
                :style="{
                  backgroundColor: color.swatch,
                  borderColor: color.border ?? '#ccc',
                }"
                @click="selectColor(color)"
              />
            </div>
            <p class="selected-color-label">
              Màu đã chọn:
              <strong>{{ selectedColor.label }}</strong>
            </p>
            <p class="selected-color-label">
              Số lượng:
              <strong>{{ currentStock }}</strong>
            </p>
          </div>

          <!-- NÚT HÀNH ĐỘNG -->
          <div class="action-buttons">
            <button
              class="btn-add-to-cart"
              type="button"
              @click="handleAddToCart"
            >
              <i class="fas fa-shopping-cart" /> THÊM VÀO GIỎ
            </button>
            <button class="btn-buy-now" type="button" @click="handleBuyNow">
              MUA NGAY (Giao hàng nhanh)
            </button>
          </div>

          <!-- CHÍNH SÁCH -->
          <section class="product-policy">
            <h3><i class="fas fa-shield-alt" /> Chính sách mua hàng</h3>
            <ul>
              <li>
                <i class="fas fa-truck" /> Miễn phí vận chuyển** cho đơn hàng
                trên 2.000.000 VNĐ.
              </li>
              <li>
                <i class="fas fa-undo" /> Đổi size thoải mái** trong vòng 7 ngày
                (giữ nguyên tag).
              </li>
              <li>
                <i class="fas fa-certificate" /> Cam kết chính hãng 100%, đền
                gấp đôi nếu phát hiện hàng giả.
              </li>
            </ul>
          </section>

          <!-- MÔ TẢ -->
          <section class="product-description">
            <h3>Mô tả chi tiết</h3>
            <p>
              Sản phẩm chính hãng 100%, cam kết chất lượng. 
              Thiết kế hiện đại, phù hợp với mọi phong cách. 
              Chất liệu cao cấp, bền đẹp theo thời gian. 
              Đổi trả trong vòng 7 ngày nếu có lỗi từ nhà sản xuất.
            </p>
          </section>
        </section>
      </div>

      <!-- BÌNH LUẬN -->
      <section class="product-comments-section">
        <div class="container">
          <h3>Bình luận sản phẩm</h3>
          
          <!-- Danh sách bình luận -->
          <div class="comment-list">
            <div class="comment-item">
              <div class="comment-header">
                <div class="comment-stars">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                </div>
                <span class="comment-time">2 giờ trước</span>
              </div>
              <p class="comment-content">Sản phẩm rất đẹp, chất lượng tốt!</p>
            </div>

            <div class="comment-item">
              <div class="comment-header">
                <div class="comment-stars">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                </div>
                <span class="comment-time">1 ngày trước</span>
              </div>
              <p class="comment-content">Giao hàng nhanh, đóng gói cẩn thận.</p>
            </div>

            <div class="comment-item">
              <div class="comment-header">
                <div class="comment-stars">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                </div>
                <span class="comment-time">3 ngày trước</span>
              </div>
              <p class="comment-content">Giá hợp lý, đáng tiền!</p>
            </div>
          </div>
        </div>
      </section>

      <!-- SẢN PHẨM LIÊN QUAN -->
      <section class="related-products">
        <h2 class="section-title">SẢN PHẨM LIÊN QUAN</h2>
        <div class="product-grid-wrapper">
          <button
            class="carousel-arrow left"
            type="button"
            @click="scrollCarousel(-1)"
          >
            <i class="fas fa-chevron-left" />
          </button>

          <div class="product-grid" ref="carouselRef">
            <article
              v-for="prod in relatedProducts"
              :key="prod.id_sanpham"
              class="product-card"
            >
              <img :src="`http://localhost/duan1/backend/${prod.hinhAnhgoc}`" :alt="prod.tenSP" />
              <h3>{{ prod.tenSP }}</h3>
              <div class="stars">★★★★★</div>
              <p v-if="prod.coGiamGia" class="original-price-small">
                {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(prod.giaSP) }}
              </p>
              <p class="current-price-large">
                {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(prod.giaSauGiam) }}
              </p>
              <div class="product-actions">
                <RouterLink :to="`/ChiTiet?id=${prod.id_sanpham}`" class="action-btn">Chi tiết</RouterLink>
                <a href="#" class="action-btn">Mua ngay</a>
              </div>
            </article>
          </div>

          <button
            class="carousel-arrow right"
            type="button"
            @click="scrollCarousel(1)"
          >
            <i class="fas fa-chevron-right" />
          </button>
        </div>
      </section>
    </main>

    <!-- POPUP TOAST -->
    <transition name="popup">
      <div v-if="popupVisible" class="popup-toast">
        {{ popupMessage }}
      </div>
    </transition>
  </div>
  <FooterWeb/>
</template>


<script setup>
import { onBeforeUnmount, ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import HeaderWeb from '../../Header-web.vue';
import FooterWeb from '../../footer-web.vue';
import heroImage from '../../assets/bannergiay.jpg'; // Fallback image

const route = useRoute();
const product = ref(null);
const loading = ref(true);
const error = ref(null);
const currentStock = ref(0);

// Fallback data for UI elements not yet in DB
const thumbnails = ref([]);
const selectedImage = ref({ src: heroImage, alt: 'Loading...' });

const sizes = ref([]);
const selectedSize = ref(null);

const colors = ref([]);
const selectedColor = ref(null);

// Map Vietnamese color names to CSS colors (approximate)
const colorMap = {
  'Đỏ': 'red',
  'Xanh': 'blue',
  'Cam': 'orange',
  'Vàng': 'yellow',
  'Đen': 'black',
  'Trắng': 'white',
  'Xám': 'grey',
  'Nâu': 'brown',
  'Tím': 'purple',
  'Hồng': 'pink'
};

const fetchProductDetail = async () => {
  loading.value = true;
  error.value = null;
  const id = route.query.id;

  if (!id) {
    error.value = "Không tìm thấy ID sản phẩm";
    loading.value = false;
    return;
  }

  try {
    const response = await fetch(`http://localhost/duan1/backend/api/Web/chitiet.php?id=${id}`);
    const data = await response.json();

    if (data.success) {
      product.value = data.data;
      
      // Update image
      const baseUrl = 'http://localhost/duan1/backend/';
      const mainImg = `${baseUrl}${data.data.hinhAnhgoc}`;
      
      // Setup gallery
      let gallery = [{ id: 0, src: mainImg, alt: data.data.tenSP }];
      
      if (data.data.gallery && Array.isArray(data.data.gallery)) {
        data.data.gallery.forEach((img, index) => {
          gallery.push({
            id: index + 1,
            src: `${baseUrl}${img}`,
            alt: `${data.data.tenSP} - View ${index + 1}`
          });
        });
      }
      
      thumbnails.value = gallery;
      selectedImage.value = gallery[0];

      // Process Variants
      if (data.data.variants && Array.isArray(data.data.variants)) {
        // Get available sizes from variants
        const availableSizes = [...new Set(data.data.variants.map(v => v.size))];
        
        // Define all possible sizes (38-42)
        const allSizes = [38, 39, 40, 41, 42];
        
        // Create size array with availability status
        sizes.value = allSizes.map(s => ({
          value: s,
          disabled: !availableSizes.includes(s) // Disable if not in stock
        }));

        // Extract unique colors
        const uniqueColors = [];
        const seenColors = new Set();
        
        data.data.variants.forEach(v => {
          if (!seenColors.has(v.id_mausac)) {
            seenColors.add(v.id_mausac);
            uniqueColors.push({
              id: v.id_mausac,
              label: v.mausac,
              swatch: colorMap[v.mausac] || '#ccc', // Fallback color
              border: v.mausac === 'Trắng' ? '#000' : null
            });
          }
        });
        colors.value = uniqueColors;

        // Auto select first available options
        const firstAvailableSize = sizes.value.find(s => !s.disabled);
        if (firstAvailableSize) selectedSize.value = firstAvailableSize.value;
        if (colors.value.length > 0) selectedColor.value = colors.value[0];
      }

      // Process Related Products
      if (data.data.relatedProducts && Array.isArray(data.data.relatedProducts)) {
        relatedProducts.value = data.data.relatedProducts;
      }
      
    } else {
      error.value = data.message || "Lỗi tải sản phẩm";
    }
  } catch (err) {
    console.error("Error fetching product:", err);
    error.value = "Lỗi kết nối server";
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchProductDetail().then(() => {
    updateAvailability();
  });
});
// Watch for route changes (e.g. clicking related product)
watch(() => route.query.id, () => {
  fetchProductDetail();
});

const selectImage = (image) => {
  selectedImage.value = image;
};

const selectSize = (size) => {
  if (size.disabled) return;
  selectedSize.value = size.value;
  updateAvailability();
};

const selectColor = (color) => {
  selectedColor.value = color;
  updateAvailability();
};

// Check if selected combination is available
const updateAvailability = () => {
  if (!product.value?.variants || !selectedSize.value || !selectedColor.value) return;
  
  const variant = product.value.variants.find(
    v => v.size == selectedSize.value && v.id_mausac == selectedColor.value.id
  );

  currentStock.value = variant ? variant.so_luong : 0;
};

/* SẢN PHẨM LIÊN QUAN */
const relatedProducts = ref([]);

const carouselRef = ref(null);
const scrollCarousel = (direction) => {
  carouselRef.value?.scrollBy({ left: direction * 300, behavior: "smooth" });
};

/* POPUP TOAST */
const popupVisible = ref(false);
const popupMessage = ref("");
let popupTimer;

const triggerPopup = (message) => {
  popupMessage.value = message;
  popupVisible.value = true;
  if (popupTimer) clearTimeout(popupTimer);
  popupTimer = setTimeout(() => { popupVisible.value = false; }, 1500);
};

const handleAddToCart = () => {
  if (!selectedSize.value) {
    triggerPopup("⚠ Vui lòng chọn size!");
    return;
  }
  if (!selectedColor.value) {
    triggerPopup("⚠ Vui lòng chọn màu!");
    return;
  }
  
  // Find the variant
  const variant = product.value?.variants?.find(
    v => v.size == selectedSize.value && v.id_mausac == selectedColor.value.id
  );
  
  if (!variant || variant.so_luong <= 0) {
    triggerPopup("⚠ Sản phẩm này hiện hết hàng!");
    return;
  }
  
  triggerPopup(`✔ Đã thêm: ${product.value?.tenSP} - Size ${selectedSize.value} - ${selectedColor.value.label}`);
  // TODO: Add to cart logic with variant.id_bienthe
};

const handleBuyNow = () => {
  if (!selectedSize.value) {
    triggerPopup("⚠ Vui lòng chọn size!");
    return;
  }
  if (!selectedColor.value) {
    triggerPopup("⚠ Vui lòng chọn màu!");
    return;
  }
  
  const variant = product.value?.variants?.find(
    v => v.size == selectedSize.value && v.id_mausac == selectedColor.value.id
  );
  
  if (!variant || variant.so_luong <= 0) {
    triggerPopup("⚠ Sản phẩm này hiện hết hàng!");
    return;
  }
  
  triggerPopup("🚚 Đặt hàng thành công!");
  // TODO: Navigate to checkout with variant.id_bienthe
};

const cleanupPopup = () => {
  if (popupTimer) clearTimeout(popupTimer);
};

onBeforeUnmount(cleanupPopup);
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500;700&display=swap");
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css");

.product-detail-wrapper {
  font-family: "Roboto", sans-serif;
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
  flex: 0 0 35%;
  max-width: 400px;
  position: sticky;
  top: 20px;
  align-self: flex-start;
}

.product-gallery img {
  width: 100%;
  height: auto;
  border-radius: 4px;
}

.main-image {
  width: 100%;
  height: 320px;
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
  font-family: "Montserrat", sans-serif;
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

.selected-color-label {
  font-size: 12px;
  margin-top: 4px;
  color: #555;
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
  font-family: "Montserrat", sans-serif;
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

/* COMMENTS SECTION */
.product-comments-section {
  background-color: #fff;
  padding: 30px 0;
  margin: 30px 0;
}

.product-comments-section .container {
  width: 90%;
  max-width: 1300px;
  margin: 0 auto;
}

.product-comments-section h3 {
  font-family: "Montserrat", sans-serif;
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 15px;
  color: #1a1a1a;
}

.comment-form {
  margin-bottom: 20px;
}

.comment-form textarea {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-family: "Roboto", sans-serif;
  font-size: 13px;
  resize: vertical;
  margin-bottom: 10px;
}

.comment-form textarea:focus {
  outline: none;
  border-color: #007bff;
}

.btn-submit-comment {
  background-color: #007bff;
  color: #fff;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-submit-comment:hover {
  background-color: #0056b3;
}

.btn-submit-comment i {
  margin-right: 5px;
}

.comment-list {
  margin-top: 15px;
}

.no-comments {
  text-align: center;
  color: #999;
  font-size: 13px;
  padding: 20px;
  font-style: italic;
}

.comment-item {
  background-color: #f9f9f9;
  padding: 12px;
  border-radius: 6px;
  margin-bottom: 10px;
  border-left: 3px solid #007bff;
}

.comment-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.comment-stars {
  color: #ffc107;
  font-size: 12px;
}

.comment-time {
  font-size: 11px;
  color: #999;
}

.comment-content {
  font-size: 13px;
  color: #555;
  line-height: 1.5;
  margin: 0;
}

/* RELATED PRODUCTS */
.related-products {
  padding: 40px 0;
  background-color: #a50138;
  position: relative;
  overflow: hidden;
  margin-top: 30px;
  border-radius: 8px;
}

.related-products::before {
  content: "";
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
  font-family: "Montserrat", sans-serif;
  font-size: 30px;
  font-weight: 700;
  text-align: center;
  margin: 0 0 30px;
  color: #1a1a1a;
  position: relative;
  z-index: 1;
}

.related-products .section-title::before {
  content: "🔥";
  margin-right: 10px;
}

.product-grid-wrapper {
  position: relative;
  max-width: 1000px; /* Tăng từ 1000px lên 1200px */
  margin: 0 auto;
  padding: 0 40px; /* Giảm padding để lấy thêm không gian */
}

.product-grid {
  display: flex;
  gap: 15px;
  padding: 0 10px;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  scroll-padding-left: 10px;
  scrollbar-width: none;
}

.product-grid::-webkit-scrollbar {
  display: none;
}

.product-card {
  flex: 0 0 calc(25% - 15px);
  min-width: 215px;
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
  height: 180px;
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
  font-family: "Montserrat", sans-serif;
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
  background-color: rgba(255, 255, 255, 0.9);
  color: #a50138;
  border: none;
  padding: 12px 16px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 22px;
  z-index: 10;

  /* Đổ bóng để nổi bật */
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.25);

  /* Hover effect */
  transition: 0.25s ease;
}

.carousel-arrow:hover {
  background-color: #fff;
  transform: translateY(-50%) scale(1.1);
}

/* Đặt sát mép ngoài vùng đỏ */
.carousel-arrow.left {
  left: -80px;   /* đẩy sát mép trái */
}

.carousel-arrow.right {
  right: -80px;  /* đẩy sát mép phải */
}


/* POPUP TOAST */
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

/* RESPONSIVE */
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
