<template>
  <!-- overlay: click ngoài đóng -->
  <div class="modal-overlay" @click.self="$emit('close')" @keydown.esc.prevent="$emit('close')" tabindex="-1" ref="overlay">
    <div class="modal-box" role="dialog" aria-modal="true" aria-label="Thanh toán nhanh">
      <div v-if="loading" class="loading">Đang tải...</div>
      <div v-else-if="error" class="error">{{ error }}</div>

      <div v-else class="mini-box">
        <!-- ẢNH -->
        <div class="left">
          <img :src="productImage" class="product-img" :alt="product.tenSP || 'Sản phẩm'" />
        </div>

        <!-- THÔNG TIN -->
        <div class="right">
          <h2 class="product-name">{{ product.tenSP }}</h2>

          <p class="price">
            {{ formatPrice(product.giaSauGiam) }}
            <span v-if="product.coGiamGia" class="old-price">
              {{ formatPrice(product.giaSP) }}
            </span>
          </p>

          <!-- SIZE -->
          <div class="block" v-if="sizes.length">
            <label class="label">Kích cỡ (size):</label>
            <div class="size-row">
              <button
                v-for="s in sizes"
                :key="s.value"
                :disabled="s.disabled"
                :class="['btn-size', { active: s.value === selectedSize }]"
                @click="selectSize(s.value)"
                type="button"
              >
                {{ s.value }}
              </button>
            </div>
          </div>

          <!-- MÀU -->
          <div class="block" v-if="colors.length">
            <label class="label">Màu sắc:</label>
            <div class="color-row">
              <button
                v-for="c in colors"
                :key="c.id"
                :class="['color-circle', { active: selectedColor?.id === c.id }]"
                :style="{ backgroundColor: c.swatch }"
                @click="selectColor(c)"
                :title="c.label"
                type="button"
              />
            </div>
            <p class="stock">Kho: {{ currentStock }}</p>
          </div>

          <!-- SỐ LƯỢNG -->
          <div class="block">
            <label class="label">Số lượng:</label>
            <div class="qty-row">
              <button @click="changeQty(-1)" class="qty-btn" type="button">-</button>
              <input
                type="number"
                class="qty-input"
                v-model.number="quantity"
                @change="clampQuantity"
                min="1"
                :max="Math.max(1, currentStock)"
              />
              <button @click="changeQty(1)" class="qty-btn" type="button">+</button>
            </div>
            <p v-if="currentStock === 0" class="text-danger" style="margin-top:6px">Sản phẩm tạm hết hàng</p>
          </div>

          <!-- THÊM GIỎ -->
          <button class="add-cart-btn" @click="addToCart" :disabled="isAdding || currentStock === 0">
            <span v-if="isAdding">Đang thêm...</span>
            <span v-else>THÊM VÀO GIỎ HÀNG</span>
          </button>

          <button class="back-detail" @click="goDetail" type="button">
            ← Xem chi tiết sản phẩm
          </button>
        </div>
      </div>
    </div>

    <!-- POPUP MINI -->
    <div v-if="popupVisible" class="popup-toast">
      {{ popupMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from "vue";
import { useRouter } from "vue-router";

const props = defineProps({
  id_sanpham: { type: [Number, String], required: true }
});
const emit = defineEmits(["close"]);
const router = useRouter();

const popupVisible = ref(false);
const popupMessage = ref("");
let popupTimer;

const product = ref({});
const sizes = ref([]);
const colors = ref([]);
const selectedSize = ref(null);
const selectedColor = ref(null);
const currentStock = ref(0);
const quantity = ref(1);
const loading = ref(true);
const error = ref(null);
const isAdding = ref(false);
const productImage = ref("");

const formatPrice = (n) =>
  new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(n ?? 0);

watch(() => props.id_sanpham, (newId) => {
  if (newId) fetchData();
}, { immediate: true });

async function fetchData() {
  loading.value = true;
  error.value = null;
  product.value = {};
  sizes.value = [];
  colors.value = [];
  selectedSize.value = null;
  selectedColor.value = null;
  currentStock.value = 0;
  quantity.value = 1;

  try {
    const id = props.id_sanpham;

    const res = await fetch(`https://miraeshoes.shop/backend/api/Web/chitiet.php?id=${encodeURIComponent(id)}`);
    const data = await res.json();

    if (!data || !data.success) {
      error.value = data?.message || "Không lấy được dữ liệu";
      loading.value = false;
      return;
    }

    product.value = data.data || {};
    productImage.value = `https://miraeshoes.shop/backend/${product.value.hinhAnhgoc ?? ""}`;

    const variants = Array.isArray(product.value.variants) ? product.value.variants : [];

    const uniqueSizes = [...new Set(variants.map(v => v.size).filter(Boolean))].map(Number);
    const allSizes = [38,39,40,41,42];
    sizes.value = allSizes.map(s => ({ value: s, disabled: !uniqueSizes.includes(Number(s)) }));

    const seen = new Set();
    colors.value = variants.reduce((acc, v) => {
      if (!v || seen.has(v.id_mausac)) return acc;
      seen.add(v.id_mausac);
      acc.push({
        id: v.id_mausac,
        label: v.mausac,
        swatch: v.mausac === "Đen" ? "black" : v.mausac === "Trắng" ? "white" : v.mausac === "Xám" ? "gray" : "#ccc"
      });
      return acc;
    }, []);

    selectedSize.value = uniqueSizes.length ? uniqueSizes[0] : sizes.value.find(s=>!s.disabled)?.value ?? null;
    selectedColor.value = colors.value.length ? colors.value[0] : null;

    updateStock();

  } catch (e) {
    console.error(e);
    error.value = "Lỗi server";
  } finally {
    loading.value = false;
    await nextTick();
    const overlay = document.querySelector(".modal-overlay");
    if (overlay) overlay.focus();
  }
}

function updateStock() {
  const variants = product.value.variants || [];

  const vt = variants.find(v =>
    String(v.size) == String(selectedSize.value) &&
    String(v.id_mausac) == String(selectedColor.value?.id)
  );

  currentStock.value = vt ? Number(vt.so_luong || 0) : 0;
  if (quantity.value > currentStock.value) quantity.value = Math.max(1, currentStock.value);
}

function selectSize(v) { selectedSize.value = v; updateStock(); }
function selectColor(v) { selectedColor.value = v; updateStock(); }

function changeQty(n) {
  const next = quantity.value + n;
  if (next < 1) quantity.value = 1;
  else if (next > currentStock.value) quantity.value = currentStock.value;
  else quantity.value = next;
}

function clampQuantity() {
  if (quantity.value < 1) quantity.value = 1;
  if (quantity.value > currentStock.value) quantity.value = currentStock.value;
}

async function addToCart() {
  isAdding.value = true;

  try {
    const user = JSON.parse(localStorage.getItem("currentUser") || "{}");

    const variants = product.value.variants || [];
    const variant = variants.find(
      v => String(v.size) == String(selectedSize.value) &&
           String(v.id_mausac) == String(selectedColor.value?.id)
    );

    if (!variant) {
      triggerPopup("❌ Không tìm thấy biến thể phù hợp");
      return;
    }

    await fetch(`https://miraeshoes.shop/backend/api/Web/Cart.php?action=add`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id_khachhang: user.id_khachhang,
        id_bienthe: variant.id_bienthe,
        so_luong: quantity.value
      })
    });

    emit("close");
    triggerPopup("✔ Đã thêm vào giỏ hàng!");

  } catch (e) {
    console.error(e);
    triggerPopup("❌ Lỗi khi thêm vào giỏ!");
  } finally {
    isAdding.value = false;
  }
}

function goDetail() {
  router.push(`/ChiTiet?id=${props.id_sanpham}`);
  emit("close");
}

function triggerPopup(msg) {
  popupMessage.value = msg;
  popupVisible.value = true;
  if (popupTimer) clearTimeout(popupTimer);
  popupTimer = setTimeout(() => popupVisible.value = false, 1500);
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.55);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 99999;
  outline: none;
}

.modal-box {
  width: 95%;
  max-width: 780px;
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  max-height: 92vh;
  overflow-y: auto;
  animation: scaleIn .18s ease;
}

@keyframes scaleIn {
  from { transform: scale(.96); opacity:0 }
  to { transform: scale(1); opacity:1 }
}

.popup-toast {
  position: fixed;
  top: 20px;
  right: 25px;
  background: #28a745;
  color: white;
  padding: 12px 20px;
  border-radius: 6px;
  font-weight: 600;
  box-shadow: 0 3px 10px rgba(0,0,0,0.25);
  z-index: 999999;
  animation: popupIn .3s ease;
}

@keyframes popupIn {
  from { opacity: 0; transform: translateX(40px); }
  to   { opacity: 1; transform: translateX(0); }
}

.mini-box { display:flex; gap:20px; }
.left { flex:1 }
.product-img { width:100%; height:330px; object-fit:cover; border-radius:8px }
.right { flex:1 }
.product-name { font-size:24px; font-weight:700 }
.price { font-size:22px; color:#d80000 }
.old-price { text-decoration:line-through; color:#777; margin-left:6px }
.block { margin:15px 0 }
.label { font-weight:600 }
.size-row button { padding:6px 12px; margin-right:5px; border-radius:4px; border:1px solid #aaa; background:#fff; cursor:pointer }
.btn-size.active { background:#007bff; color:#fff; border-color:#007bff }
.color-row { display:flex; gap:8px }
.color-circle { width:22px; height:22px; border-radius:50%; border:2px solid #666; cursor:pointer; padding:0; }
.color-circle.active { box-shadow:0 0 6px #007bff; border-color:#007bff }
.qty-row { display:flex; align-items:center; gap:6px }
.qty-btn { width:28px; height:28px; border:none; background:#ddd; border-radius:4px; cursor:pointer }
.qty-input { width:70px; text-align:center; padding:6px; border:1px solid #ddd; border-radius:6px }
.add-cart-btn { width:100%; padding:12px; background:#222; color:#fff; border:none; border-radius:6px; margin-top:10px; cursor:pointer }
.add-cart-btn:disabled { opacity:0.6; cursor:not-allowed }
.back-detail { background:none; border:none; color:#007bff; margin-top:8px; cursor:pointer }
.loading, .error { padding:18px; border-radius:8px; text-align:center; }
.error { background:#fff; color:#333 }
</style>
