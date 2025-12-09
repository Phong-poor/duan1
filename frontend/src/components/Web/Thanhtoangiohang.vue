<template>
  <HeaderWeb />

  <!-- LOADING POPUP -->
  <div v-if="loadingOrder" class="overlay">
    <div class="loader"></div>
  </div>

  <!-- RESULT POPUP -->
  <div v-if="orderStatus !== null" class="result-popup" :class="orderStatus ? 'success' : 'fail'">
    <div class="icon">
      <i v-if="orderStatus" class="fas fa-check-circle"></i>
      <i v-else class="fas fa-times-circle"></i>
    </div>
    <p>{{ orderStatus ? "Đặt hàng thành công!" : "Đặt hàng thất bại!" }}</p>
  </div>

  <div class="bg-light">
    <div class="container py-5">
      <h3 class="fw-bold mb-4">GIỎ HÀNG</h3>

      <div class="row g-4">
        <!-- LEFT CART -->
        <div class="col-lg-8">
          <div class="bg-white p-3 rounded shadow-sm">
            <div class="row fw-semibold border-bottom pb-2 small text-uppercase text-center">
              <div class="col-2">Hình ảnh</div>
              <div class="col-4">Sản phẩm</div>
              <div class="col-2">Số Lượng</div>
              <div class="col-2">Giá</div>
              <div class="col-1">Tổng</div>
              <div class="col-1"></div>
            </div>
            <div v-for="item in cart" :key="item.id_giohang"
              class="row align-items-center py-3 border-bottom text-center">

              <div class="col-2">
                <img :src="`http://localhost/duan1/backend/${item.hinhAnhgoc}`" class="img-fluid rounded" />
              </div>

              <div class="col-4 text-start product-name">
                <div class="fw-semibold">{{ item.tenSP }}</div>
                <div class="text-secondary small">
                  (Màu: {{ item.mausac }} + Size {{ item.size }})
                </div>
              </div>

              <div class="col-2">
                <input type="number" class="form-control text-center" v-model="item.so_luong"
                  @change="updateQty(item)" />
              </div>

              <div class="col-2">{{ formatPrice(getPrice(item)) }}</div>

              <div class="col-1 fw-bold">
                {{ formatPrice(item.so_luong * getPrice(item)) }}
              </div>

              <div class="col-1">
                <span class="delete-x" @click="deleteItem(item)">x</span>
              </div>

            </div>


            <div v-if="cart.length === 0" class="text-center py-4">
              Giỏ hàng trống!
            </div>
          </div>
        </div>

        <!-- RIGHT SUMMARY -->
        <div class="col-lg-4">
          <div class="bg-white p-3 rounded shadow-sm">
            <h5 class="fw-semibold mb-3">Tóm Tắt</h5>

            <div class="d-flex justify-content-between small py-1">
              <span>Tạm tính:</span>
              <span>{{ formatPrice(totalPrice) }} VNĐ</span>
            </div>

            <div class="d-flex justify-content-between small py-1">
              <span>Phí vận chuyển:</span>
              <span>30,000 VNĐ</span>
            </div>

            <div class="d-flex justify-content-between small text-danger py-1">
              <span>Giảm giá:</span>
              <span>-10,000 VNĐ</span>
            </div>
            <div class="mt-3">
              <input type="text" class="form-control" placeholder="Nhập mã voucher" />
            </div>

            <hr />

            <div class="d-flex justify-content-between fw-bold text-danger">
              <span>Tổng thanh toán:</span>
              <span>{{ formatPrice(totalPrice + 30000 - 10000) }} VNĐ</span>
            </div>
          </div>
        </div>
      </div>

      <!-- FORM ĐẶT HÀNG -->
      <h3 class="fw-bold mt-5 mb-3">HOÀN TẤT ĐƠN HÀNG</h3>

      <div class="bg-white p-4 rounded shadow-sm">
        <h5 class="fw-semibold mb-3">1. Thông Tin Giao Hàng</h5>

        <div class="row g-3">
          <div class="col-md-6">
            <input v-model="form.ten" type="text" class="form-control" placeholder="Họ tên" />
          </div>
          <div class="col-md-6">
            <input v-model="form.sdt" type="text" class="form-control" placeholder="Số điện thoại" />
          </div>
          <div class="col-md-6">
            <input v-model="form.email" type="email" class="form-control" placeholder="Email" />
          </div>
          <div class="col-md-6">
            <input v-model="form.tinh" type="text" class="form-control" placeholder="Tỉnh / TP" />
          </div>
        </div>

        <input v-model="form.diachi" type="text" class="form-control mt-3" placeholder="Địa chỉ" />
        <textarea v-model="form.ghichu" class="form-control mt-3" rows="3" placeholder="Ghi chú"></textarea>

        <h5 class="fw-semibold mt-4 mb-2">2. Thanh Toán</h5>

        <div class="list-group">
          <label class="list-group-item">
            <input v-model="form.pttt" value="COD" type="radio" /> COD
          </label>
          <label class="list-group-item">
            <input v-model="form.pttt" value="bank" type="radio" /> Ngân hàng
          </label>
          <label class="list-group-item">
            <input v-model="form.pttt" value="momo" type="radio" /> Ví Momo
          </label>
        </div>

        <button class="btn btn-dark px-4 mt-4" @click="submitOrder">
          Đặt hàng
        </button>
      </div>

    </div>
  </div>

  <footerWeb />
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import HeaderWeb from "../../Header-web.vue";
import footerWeb from "../../footer-web.vue";
import { useRouter } from "vue-router";

const router = useRouter();

/* POPUP STATE */
const loadingOrder = ref(false);
const orderStatus = ref(null);

/* USER + CART */
const API = "http://localhost/duan1/backend/api/Web/Cart.php";
const user = JSON.parse(localStorage.getItem("currentUser") || "{}");
const cart = ref([]);

const form = ref({
  ten: "",
  sdt: "",
  email: "",
  tinh: "",
  diachi: "",
  ghichu: "",
  pttt: "COD",
});

/* AUTO-FILL USER INFO WHEN OPEN PAGE */
onMounted(() => {
  if (user && user.id_khachhang) {
    form.value.ten = user.tenKH || "";
    form.value.sdt = user.sodienthoai || "";
    form.value.email = user.email || "";
  }

  loadCart();
});

const getPrice = (item) => (item.giamgiaSP > 0 ? item.giamgiaSP : item.giaSP);
const formatPrice = (n) => new Intl.NumberFormat("vi-VN").format(n);

const loadCart = async () => {
  if (!user.id_khachhang) return;
  const res = await axios.get(`${API}?action=list&id_khachhang=${user.id_khachhang}`);
  if (res.data.success) cart.value = res.data.data;
};

const updateQty = async (item) => {
  if (item.so_luong < 1) item.so_luong = 1;
  if (item.so_luong > item.tonkho) item.so_luong = item.tonkho;

  await axios.post(`${API}?action=update`, {
    id_giohang: item.id_giohang,
    so_luong: item.so_luong,
  });
};

const totalPrice = computed(() =>
  cart.value.reduce((sum, item) => sum + item.so_luong * getPrice(item), 0)
);

/* SUBMIT ORDER */
const submitOrder = async () => {
  if (!user.id_khachhang) {
    orderStatus.value = false;
    return;
  }

  if (!form.value.ten || !form.value.sdt || !form.value.diachi) {
    orderStatus.value = false;
    return;
  }

  loadingOrder.value = true;

  try {
    const res = await axios.post("http://localhost/duan1/backend/api/Web/Checkout.php", {
      id_khachhang: user.id_khachhang,
      tenKH: form.value.ten,
      sodienthoai: form.value.sdt,
      diachi: form.value.diachi,
      ghichu: form.value.ghichu,
      pttt: form.value.pttt,
    });

    loadingOrder.value = false;

    if (res.data.success) {
      orderStatus.value = true;
      cart.value = [];

      setTimeout(() => router.push("/"), 2000);
    } else {
      orderStatus.value = false;
    }
  } catch (err) {
    loadingOrder.value = false;
    orderStatus.value = false;
  }
};

const deleteItem = async (item) => {
  if (!confirm("Xóa sản phẩm này?")) return;

  try {
    const res = await axios.post(
      `${API}?action=delete`,
      {
        id_giohang: item.id_giohang
      },
      { headers: { "Content-Type": "application/json" } }
    );

    if (res.data.success) {
      // xoá khỏi giao diện
      cart.value = cart.value.filter(i => i.id_giohang !== item.id_giohang);
    } else {
      alert("Xóa không thành công!");
    }

  } catch (err) {
    console.error(err);
    alert("Lỗi kết nối server!");
  }
};


</script>

<style scoped>
/* Overlay Loading */
.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 99999;
}

.loader {
  width: 55px;
  height: 55px;
  border: 6px solid #ddd;
  border-top: 6px solid #007bff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Result Popup */
.result-popup {
  position: fixed;
  top: 30%;
  left: 50%;
  transform: translateX(-50%);
  background: white;
  padding: 25px 35px;
  border-radius: 12px;
  text-align: center;
  z-index: 100000;
  animation: fadeIn .3s ease;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
}

.result-popup.success .icon i {
  color: #28a745;
  font-size: 55px;
}

.result-popup.fail .icon i {
  color: #dc3545;
  font-size: 55px;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translate(-50%, -20px);
  }

  to {
    opacity: 1;
    transform: translate(-50%, 0);
  }
}

.bg-light {
  margin-top: 50px;
}

.product-name {
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.delete-x {
  color: #dc3545;
  font-size: 20px;
  font-weight: bold;
  cursor: pointer;
  transition: .2s;
  display: inline-block;
}

.delete-x:hover {
  color: #a8001f;
}

.voucher-section {
margin-top: 12px;
}
.voucher-section input {
width: 100%;
padding: 10px;
border: 1px solid #ccc;
border-radius: 6px;
font-size: 14px;
}
.voucher-section input:focus {
border-color: #007bff;
outline: none;
}
</style>
