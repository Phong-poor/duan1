<template>
  <HeaderWeb/> 

  <div class="account-page">
    <main class="account-container">
      <div class="profile-container">

        <!-- SIDEBAR -->
        <aside class="profile-sidebar">
          <div class="profile-info">
            <h3>Xin Chào, <strong>{{ user.tenKH || '' }}</strong>!</h3>
            <p class="profile-email">{{ user.email }}</p>
          </div>

          <div class="profile-menu">
            <a :class="{active: activeTab === 'info'}" @click="activeTab = 'info'">
              Thông tin chung
            </a>
            <a :class="{active: activeTab === 'orders'}" @click="activeTab = 'orders'">
              Quản lý đơn hàng
            </a>
            <a :class="{active: activeTab === 'voucher'}" @click="activeTab = 'voucher'">
              Voucher
            </a>
          </div>
        </aside>

        <!-- MAIN CONTENT -->
        <section class="profile-content" v-if="activeTab === 'info'">
          <h2 class="section-title-profile">Thông Tin Cá Nhân</h2>

          <div class="card">
            <h4>Chi Tiết Tài Khoản</h4>

            <!-- ================= VIEW MODE ================= -->
            <div v-if="!isEditing">
              <div class="info-row">
                <span>Họ và Tên:</span>
                <span>{{ user.tenKH }}</span>
              </div>

              <div class="info-row">
                <span>Email:</span>
                <span>{{ user.email }}</span>
              </div>

              <div class="info-row">
                <span>Số Điện Thoại:</span>
                <span>{{ user.sodienthoai }}</span>
              </div>

              <div class="info-row">
                <span>Ngày Sinh:</span>
                <span>{{ formatDate(user.ngaysinh) }}</span>
              </div>

              <div class="info-row">
                <span>Giới tính:</span>
                <span>{{ user.gioitinh || "Chưa cập nhật" }}</span>
              </div>
              <button class="edit-btn" @click="startEdit">Chỉnh sửa thông tin</button>
            </div>

            <!-- ================= EDIT MODE ================= -->
            <div v-else>
              <div class="info-row column">
                <span>Họ và Tên:</span>
                <input v-model="editUser.tenKH" class="edit-input" />
                <p class="error-msg" v-if="errors.tenKH">{{ errors.tenKH }}</p>
              </div>

              <div class="info-row column">
                <span>Email:</span>
                <input v-model="editUser.email" class="edit-input" />
                <p class="error-msg" v-if="errors.email">{{ errors.email }}</p>
              </div>

              <div class="info-row column">
                <span>Số Điện Thoại:</span>
                <input v-model="editUser.sodienthoai" class="edit-input" />
                <p class="error-msg" v-if="errors.sodienthoai">{{ errors.sodienthoai }}</p>
              </div>

              <div class="info-row column">
                <span>Ngày Sinh:</span>
                <input type="date" v-model="editUser.ngaysinh" class="edit-input" />
                <p class="error-msg" v-if="errors.ngaysinh">{{ errors.ngaysinh }}</p>
              </div>

              <div class="info-row column">
                <span>Giới tính:</span>

                <select v-model="editUser.gioitinh" class="edit-input">
                  <option value="">-- Chọn giới tính --</option>
                  <option value="Nam">Nam</option>
                  <option value="Nữ">Nữ</option>
                </select>

                <p class="error-msg" v-if="errors.gioitinh">{{ errors.gioitinh }}</p>
              </div>

              <div class="button-row">
                <button class="save-btn" @click="saveInfo">Lưu Thông Tin</button>
                <button class="cancel-btn" @click="cancelEdit">Hủy</button>
              </div>
            </div>
          </div>

        </section>

        <!-- =================== SECTION: ĐƠN HÀNG GẦN ĐÂY =================== -->
        <section class="order-section" v-if="activeTab === 'orders'">
          <h2 class="section-title-profile">Lịch Sử Đơn Hàng</h2>
          <div class="card">
            <table class="order-table">
              <thead>
                <tr>
                  <th>Mã đơn</th>
                  <th>Ngày đặt</th>
                  <th>Tổng tiền</th>
                  <th>Trạng thái</th>
                  <th>Hành động</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="o in paginatedOrders" :key="o.id_donhang">
                  <td>#{{ o.maDatHang }}</td>
                  <td>{{ formatDate(o.thoigiantao) }}</td>
                  <td>{{ formatMoney(o.tongtien) }} VNĐ</td>
                  <td>
                    <span :class="statusClass(o.trangthai)">
                      {{ o.trangthai }}
                    </span>
                  </td>
                  <td class="action-cell">
                    <!-- Nút Xem -->
                    <button class="btn btn-primary" @click="openOrderPopup(o.id_donhang)">
                      Xem
                    </button>
                    <!-- Nút Huỷ đơn -->
                    <button
                      v-if="['Chờ xác nhận', 'Đã xác nhận', 'Đang giao hàng'].includes(o.trangthai)"
                      class="btn btn-danger"
                      @click="openReasonPopup('cancel', o.id_donhang)"
                    >
                      Huỷ đơn
                    </button>
                    <!-- Nút Trả hàng -->
                    <button
                      v-if="o.trangthai === 'Thành công'"
                      class="btn btn-danger"
                      @click="openReasonPopup('return', o.id_donhang)"
                    >
                      Trả hàng
                    </button>
                    <button
                      v-if="['Hủy đơn', 'Trả hàng'].includes(o.trangthai)"
                      class="btn btn-success"
                      @click="rebuy(o.id_donhang)"
                    >
                      Mua lại
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="pagination">
              <button 
                :disabled="currentPage === 1" 
                @click="changePage(currentPage - 1)"
              >
                ‹ Trước
              </button>

              <button 
                v-for="p in totalPages" 
                :key="p"
                :class="{ active: currentPage === p }"
                @click="changePage(p)"
              >
                {{ p }}
              </button>

              <button 
                :disabled="currentPage === totalPages" 
                @click="changePage(currentPage + 1)"
              >
                Sau ›
              </button>
            </div>
          </div>
        </section>
        <!-- =================== SECTION: VOUCHER =================== -->
        <section class="voucher-section" v-if="activeTab === 'voucher'">
          <h2 class="section-title-profile">Voucher của tôi</h2>

          <div class="card">
            <table class="order-table">
              <thead>
                <tr>
                  <th>Mã Voucher</th>
                  <th>Giảm giá</th>
                  <th>Mô tả</th>
                  <th>Ngày nhận</th>
                  <th>Ngày hết hạn</th>
                  <th>Trạng thái</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="v in vouchers" :key="v.id_voucher">
                  <td>#{{ v.ma_voucher }}</td>
                  <td>
                    <span v-if="v.loai_giam === 'VND'">
                      {{ formatMoney(v.gia_tri) }}đ
                    </span>

                    <span v-else-if="v.loai_giam === '%'">
                      {{ v.gia_tri }}%
                    </span>
                  </td>
                  <td>{{ v.mo_ta }}</td>
                  <td>{{ formatDate(v.ngay_nhan) }}</td>
                  <td>{{ formatDate(v.ngay_het_han) }}</td>
                  <td>
                    <span :class="voucherStatusClass(v.trang_thai)">
                      {{ formatVoucherStatus(v.trang_thai) }}
                    </span>
                  </td>
                </tr>

                <tr v-if="vouchers.length === 0">
                  <td colspan="5" style="text-align:center; padding:15px; color:#777;">
                    Bạn chưa nhận voucher nào.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </main>
  </div>
  <!-- POPUP LÝ DO HỦY / TRẢ -->
  <div v-if="showReasonPopup" class="order-popup-overlay">
    <div class="order-popup">

      <h3>{{ reasonType === 'cancel' ? 'Lý do hủy đơn' : 'Lý do trả hàng' }}</h3>

      <textarea 
        v-model="reasonText"
        placeholder="Nhập lý do..."
        style="width:100%; height:120px; padding:10px; border:1px solid #ccc; border-radius:6px;"
      ></textarea>

      <div class="popup-actions" style="margin-top:20px;">
        <button class="btn-secondary" @click="showReasonPopup = false">Hủy</button>
        <button class="btn btn-success" @click="submitReason">Xác nhận</button>
      </div>

    </div>
  </div>
  <!-- ========== POPUP CHI TIẾT ĐƠN ========== -->
  <div v-if="showOrderPopup" class="order-popup-overlay">
    <div class="order-popup">

      <h3>Chi tiết đơn hàng #{{ orderDetail?.order?.id_donhang }}</h3>

      <table class="popup-table">
        <thead>
          <tr>
            <th>Mã SP</th>
            <th>Hình ảnh</th>
            <th>Tên SP</th>
            <th>Giá</th>
            <th>Số lượng mua</th>
            <th>Thành tiền</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="it in orderDetail.items" :key="it.id_sanpham">
            <td>{{ it.id_sanpham }}</td>
            <td>
              <img :src="it.hinhAnhgoc" class="popup-img" />
            </td>
            <td>
              {{ it.tenSP }}
              <br />
              <small style="color: #666; font-size: 13px;">
                (Màu: {{ it.mauSac }} + Size: {{ it.sizeSP }})
              </small>
            </td>
            <td>{{ formatMoney(it.giaSP) }} VNĐ</td>
            <td>{{ it.soLuongMua }}</td>
            <td>{{ formatMoney(it.thanhtien) }} VNĐ</td>
            <td>
              <button 
                v-if="orderDetail.order.trangthai === 'Thành công'" 
                class="btn btn-success"
                @click="openRatingPopup(it)"
              >
                Đánh giá
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="popup-actions">
        <button class="btn btn-secondary" @click="closeOrderPopup">Đóng</button>
      </div>
    </div>
  </div>
  <!-- POPUP ĐÁNH GIÁ SẢN PHẨM -->
  <div v-if="showRatingPopup" class="order-popup-overlay">
    <div class="order-popup" style="max-width: 600px;">

      <h3>Đánh giá sản phẩm</h3>

      <!-- SẢN PHẨM -->
      <div style="display:flex; gap:15px; margin-bottom:15px;">
        <img :src="ratingItem.hinhAnhgoc" style="width:90px; height:90px; object-fit:cover; border-radius:6px;" />
        <div>
          <strong>{{ ratingItem.tenSP }}</strong><br>
          <small style="color:#666;">
            (Màu: {{ ratingItem.mauSac }} + Size: {{ ratingItem.sizeSP }})
          </small>
        </div>
      </div>

      <!-- CHỌN SAO -->
      <div class="rating-stars">
        <span
          v-for="n in 5"
          :key="n"
          class="star"
          :class="{ active: ratingStars >= n }"
          @click="ratingStars = n"
        >★</span>
      </div>

      <!-- COMMENT -->
      <textarea 
        v-model="ratingComment"
        placeholder="Viết đánh giá của bạn..."
        style="width:100%; height:120px; padding:10px; border:1px solid #ccc; border-radius:6px;"
      ></textarea>

      <div class="rating-actions">
        <button class="btn-cancel" @click="showRatingPopup = false">Hủy</button>
        <button class="btn-submit" @click="submitRating">Gửi đánh giá</button>
      </div>

    </div>
  </div>
  <footerWeb/>
  <!-- CUSTOM POPUP -->
  <div v-if="showPopup" class="custom-popup">
    <div class="popup-box">
      <p>{{ popupMessage }}</p>
      <button @click="showPopup = false">OK</button>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, computed } from "vue";
import HeaderWeb from "../../Header-web.vue";
import footerWeb from "../../footer-web.vue";

// 🔥 ĐƯỜNG DẪN ĐÚNG
const API_URL = "https://miraeshoes.shop/backend/api/Web/User.php";
const activeTab = ref("info");
const API_ORDER_ACTION = "https://miraeshoes.shop/backend/api/Web/OrderAction.php";
/* USER DATA */
const user = reactive({
  id_khachhang: "",
  tenKH: "",
  email: "",
  sodienthoai: "",
  ngaysinh: "",
});

/* LOAD USER */
onMounted(async () => {
  const saved = JSON.parse(localStorage.getItem("currentUser"));
  if (!saved) return;

  Object.assign(user, saved);

  if (!saved.id_khachhang) return;

  const res = await fetch(`${API_URL}?id=${saved.id_khachhang}`);
  const data = await res.json();

  if (data.status && data.data) {
    Object.assign(user, data.data);
    localStorage.setItem("currentUser", JSON.stringify(user));
  }
});

/* EDIT */
const isEditing = ref(false);
const editUser = reactive({
  tenKH: "",
  email: "",
  sodienthoai: "",
  ngaysinh: "",
  gioitinh: "",
});
const errors = reactive({
  tenKH: "",
  email: "",
  sodienthoai: "",
  ngaysinh: "",
});

/* VALIDATE */
const validate = () => {
  errors.tenKH = errors.email = errors.sodienthoai = errors.ngaysinh = "";
  let ok = true;

  if (!editUser.tenKH.trim()) ok = errors.tenKH = "Họ tên không được bỏ trống";
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(editUser.email)) ok = errors.email = "Email không hợp lệ";
  if (!/^[0-9]{9,12}$/.test(editUser.sodienthoai)) ok = errors.sodienthoai = "Số điện thoại không hợp lệ";
  if (!editUser.ngaysinh) ok = errors.ngaysinh = "Ngày sinh không hợp lệ";

  return !errors.tenKH && !errors.email && !errors.sodienthoai && !errors.ngaysinh;
};

/* START EDIT */
const startEdit = () => {
  Object.assign(editUser, user);
  isEditing.value = true;
};

/* SAVE */
const saveInfo = async () => {
  if (!validate()) return;

  try {
    const res = await fetch(`${API_URL}?id=${user.id_khachhang}`, {
      method: "POST", // 🔥 KHÔNG DÙNG PUT
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        _method: "PUT", // 🔥 GIẢ PUT
        tenKH: editUser.tenKH,
        email: editUser.email,
        sodienthoai: editUser.sodienthoai,
        ngaysinh: editUser.ngaysinh,
        gioitinh: editUser.gioitinh
      })
    });

    const data = await res.json();

    if (data.status) {
      Object.assign(user, editUser);

      // 🔥 cập nhật localStorage
      localStorage.setItem("currentUser", JSON.stringify(user));

      showCenterPopup("Cập nhật thông tin thành công!");
      isEditing.value = false;
    } else {
      showCenterPopup(data.message || "Cập nhật thất bại!");
    }

  } catch (err) {
    console.error(err);
    showCenterPopup("Lỗi kết nối server!");
  }
};


/* CANCEL */
const cancelEdit = () => {
  isEditing.value = false;
};

/* LOGOUT */
const logout = () => {
  localStorage.removeItem("currentUser");
  location.reload();
};

/* FORMAT DATE */
const formatDate = (d) => {
  if (!d) return "";
  return new Date(d).toLocaleDateString("vi-VN");
};

const showPopup = ref(false);
const popupMessage = ref("");

const showCenterPopup = (msg) => {
  popupMessage.value = msg;
  showPopup.value = true;
};
// PHÂN TRANG
const currentPage = ref(1);
const itemsPerPage = 5;

const paginatedOrders = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return orders.value.slice(start, start + itemsPerPage);
});

const totalPages = computed(() => {
  return Math.ceil(orders.value.length / itemsPerPage);
});

const changePage = (p) => {
  if (p >= 1 && p <= totalPages.value) {
    currentPage.value = p;
  }
};
// ======= API LẤY LỊCH SỬ ĐƠN HÀNG =======
const orders = ref([]);
const API_ORDER = "https://miraeshoes.shop/backend/api/Web/Lichsudonhang.php";

const loadOrders = async () => {
  if (!user.id_khachhang) return;

  const res = await fetch(`${API_ORDER}?user_id=${user.id_khachhang}`);
  const data = await res.json();

  if (data.status) {
    orders.value = data.data;
  }
};
const showReasonPopup = ref(false);
const reasonType = ref("");  // 'cancel' hoặc 'return'
const selectedOrderId = ref(null);
const reasonText = ref("");
const openReasonPopup = (type, id) => {
  reasonType.value = type;
  selectedOrderId.value = id;
  reasonText.value = "";
  showReasonPopup.value = true;
};
const submitReason = async () => {
  if (!reasonText.value.trim()) {
    showCenterPopup("Vui lòng nhập lý do!");
    return;
  }

  const action = reasonType.value === "cancel" ? "cancel" : "return";

  const res = await fetch(`${API_ORDER_ACTION}?id=${selectedOrderId.value}&action=${action}`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      lydo: reasonText.value
    })
  });

  const data = await res.json();

  if (data.status) {
    showCenterPopup(
      reasonType.value === "cancel"
      ? "Đã huỷ đơn hàng!"
      : "Đã gửi yêu cầu trả hàng!"
    );

    showReasonPopup.value = false;
    loadOrders();
  } else {
    showCenterPopup("Có lỗi xảy ra, vui lòng thử lại!");
  }
};

onMounted(() => {
  loadOrders();
  loadVoucher();
});

// ======= FORMAT TIỀN =======
const formatMoney = (n) => Number(n).toLocaleString("vi-VN");

// ======= MÀU TRẠNG THÁI =======
const statusClass = (st) => {
  st = st.toLowerCase();

  if (st.includes("hủy") || st.includes("trả")) return "status-danger"; // 🔥 đỏ
  if (st.includes("chờ")) return "status-warning";                     // cam
  if (st.includes("đang")) return "status-success";                    // xanh lá
  if (st.includes("thành công")) return "status-success";              // xanh lá

  return "status-default";
};
/* ================== HỦY ĐƠN ================== */
const cancelOrder = async (id) => {
  try {
    const res = await fetch(`${API_ORDER_ACTION}?id=${id}&action=cancel`);
    const data = await res.json();

    if (data.status) {
      showCenterPopup("Huỷ đơn hàng thành công!");
      loadOrders();
    } else {
      showCenterPopup(data.msg || "Không thể huỷ đơn hàng");
    }
  } catch (err) {
    showCenterPopup("Lỗi kết nối API huỷ đơn");
  }
};


/* ================== TRẢ HÀNG ================== */
const returnOrder = async (id) => {
  try {
    const res = await fetch(`${API_ORDER_ACTION}?id=${id}&action=return`);
    const data = await res.json();

    if (data.status) {
      showCenterPopup("Trả hàng thành công!");
      loadOrders();
    } else {
      showCenterPopup(data.msg || "Không thể trả hàng");
    }
  } catch (err) {
    showCenterPopup("Lỗi kết nối API trả hàng");
  }
};


/* ================== MUA LẠI ================== */
const rebuy = async (id) => {
  try {
    const res = await fetch(`${API_ORDER_ACTION}?id=${id}&action=rebuy`);
    const data = await res.json();

    if (data.status) {
      showCenterPopup(`Tạo đơn mới thành công! Mã: ${data.new_code}`);
      loadOrders();
    } else {
      showCenterPopup(data.msg || "Không thể mua lại đơn hàng");
    }
  } catch (err) {
    showCenterPopup("Lỗi kết nối API mua lại");
  }
};
const showOrderPopup = ref(false);
const orderDetail = reactive({
  order: null,
  items: []
});
/* ================== XEM CHI TIET DON HANG ================== */
const openOrderPopup = async (id) => {
  const res = await fetch(`https://miraeshoes.shop/backend/api/Web/Chitietdonhang.php?id=${id}`);
  const data = await res.json();

  if (data.status) {
    orderDetail.order = data.order;
    orderDetail.items = data.items;
    showOrderPopup.value = true;
  } else {
    showCenterPopup("Không tải được chi tiết đơn hàng!");
  }
};

const closeOrderPopup = () => {
  showOrderPopup.value = false;
};
/* ================== DANH GIA ================== */
const showRatingPopup = ref(false);
const ratingItem = reactive({});
const ratingStars = ref(0);
const ratingComment = ref("");

const openRatingPopup = (item) => {
  Object.assign(ratingItem, item);
  ratingStars.value = 0;
  ratingComment.value = "";
  showRatingPopup.value = true;
};

// Vue.js - gửi đánh giá
// Vue.js - gửi đánh giá
const submitRating = async () => {
  if (ratingStars.value === 0) {
    showCenterPopup("Vui lòng chọn số sao!");
    return;
  }
  if (!ratingComment.value.trim()) {
    showCenterPopup("Vui lòng nhập nội dung đánh giá!");
    return;
  }

  const res = await fetch("https://miraeshoes.shop/backend/api/Web/BinhLuan.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      id_khachhang: user.id_khachhang,
      id_sanpham: ratingItem.id_sanpham,
      sosao: ratingStars.value,
      noidung: ratingComment.value
    })
  });

  const data = await res.json();

  if (data.status) {
    showCenterPopup("Đánh giá thành công!");
    showRatingPopup.value = false;
  } else {
    showCenterPopup("Không thể gửi đánh giá!");
  }
};
const vouchers = ref([]);
const API_VOUCHER = "https://miraeshoes.shop/backend/api/Web/getVoucherUser.php";

const loadVoucher = async () => {
  if (!user.id_khachhang) return;

  const res = await fetch(`${API_VOUCHER}?user_id=${user.id_khachhang}`);
  const data = await res.json();

  if (data.status) {
    vouchers.value = data.data;
  }
};
const voucherStatusClass = (st) => {
  if (!st) return "status-default";

  const s = st.toLowerCase();

  if (s.includes("hieu") || s.includes("hiệu")) return "status-success";   // Xanh
  if (s.includes("het") || s.includes("hết")) return "status-danger";     // Đỏ
  if (s.includes("dung") || s.includes("dùng")) return "status-warning";  // Cam

  return "status-default";
};
const formatVoucherStatus = (st) => {
  if (!st) return "Không rõ";

  switch (st) {
    case "Hieu_luc":
      return "Hiệu lực";
    case "Het_han":
      return "Hết hạn";
    case "Da_dung":
      return "Đã dùng";
    case "Chua_dung":
      return "Chưa dùng";
    default:
      return st;
  }
};
</script>

<style scoped>
/* =================== GENERAL =================== */
.account-page {
  font-family: 'Roboto', sans-serif;
  background-color: #f7f7f7;
  color: #1a1a1a;
  min-height: 100vh;
  padding: 120px 0 60px;
}

.account-container {
  width: 90%;
  max-width: 1300px;
  margin: 0 auto;
}

.profile-email {
  font-size: 14px;
  color: #777;
  margin-top: 6px;
}

/* =================== MAIN LAYOUT =================== */
.profile-container {
  display: flex;
  flex-direction: row;   /* sidebar trái – content phải */
  gap: 30px;
  padding: 40px 0;
  align-items: flex-start;
}

/* THÔNG TIN CÁ NHÂN + ĐƠN HÀNG PHẢI ĐỨNG CHUNG 1 CỘT */
.profile-content,
.order-section {
  width: 100%;
}

/* Tách sidebar ra 1 cột riêng – phần còn lại là 1 cột */
.profile-container {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 30px;
}

/* Đưa đơn hàng xuống dưới */
.order-section {
  grid-column: 2; /* đứng dưới cột nội dung */
}

/* ======= SIDEBAR ======= */
.profile-sidebar {
  width: 280px;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  padding: 20px;
}

.profile-info h3 {
  font-size: 20px;
  color: #007bff;
  margin: 0 0 10px;
  border-bottom: 2px solid #007bff;
  padding-bottom: 10px;
}

.profile-menu a {
  display: block;
  text-decoration: none;
  color: #444;
  padding: 12px 10px;
  margin: 5px 0;
  border-radius: 4px;
  font-weight: 500;
  transition: 0.2s;
}

.profile-menu a:hover,
.profile-menu a.active {
  background-color: #e0f0ff;
  color: #007bff;
}

/* ======= MAIN CONTENT (bên phải) ======= */
.profile-content {
  flex: 1;
}

.section-title-profile {
  font-family: 'Montserrat', sans-serif;
  font-size: 28px;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 20px;
  border-bottom: 1px solid #ccc;
  padding-bottom: 10px;
}

.card {
  background-color: #fff;
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  margin-bottom: 30px;
}

.card h4 {
  font-size: 20px;
  color: #007bff;
  margin-bottom: 20px;
}

/* =================== INFO ROW =================== */
.info-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px dashed #eee;
}

.info-row span:first-child {
  font-weight: 500;
  color: #555;
}

.info-row span:last-child {
  font-weight: 600;
  color: #1a1a1a;
}

/* =================== BUTTONS =================== */
.edit-btn,
.save-btn,
.cancel-btn {
  padding: 8px 15px;
  border-radius: 4px;
  font-size: 14px;
  border: none;
  color: white;
  transition: background-color 0.2s;
}

.edit-btn,
.save-btn {
  background-color: #555;
}

.edit-btn:hover,
.save-btn:hover {
  background-color: #333;
}

.cancel-btn {
  background-color: #dc3545;
  margin-left: 10px;
}

.cancel-btn:hover {
  opacity: 0.8;
}

.edit-btn.primary {
  background-color: #007bff;
}

/* =================== ORDER TABLE =================== */
.order-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 15px;
}

.order-table th,
.order-table td {
  padding: 12px 10px;
  border-bottom: 1px solid #eee;
}

.order-table th {
  background-color: #f0f0f0;
  font-weight: 600;
  color: #333;
}

.action-cell {
  white-space: nowrap;
  text-align: right;
}
/* ======= STATUS ======= */
.status-danger {
  color: #d9534f; /* đỏ */
  font-weight: 600;
}

.status-warning {
  color: #f0ad4e; /* cam */
  font-weight: 600;
}

.status-success {
  color: #5cb85c; /* xanh lá */
  font-weight: 600;
}

.status-default {
  color: #555;
  font-weight: 500;
}
/* =================== ORDER SECTION ↓ DƯỚI THÔNG TIN CÁ NHÂN =================== */
.order-section {
  width: 100%;
  margin-top: 20px;
  display: block;
}
/* POPUP GIỮA MÀN HÌNH */
.custom-popup {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.popup-box {
  background: #fff;
  padding: 25px 30px;
  border-radius: 10px;
  text-align: center;
  width: 350px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  animation: fadeIn 0.2s ease-in-out;
}

.popup-box p {
  font-size: 16px;
  margin-bottom: 15px;
  color: #222;
  font-weight: 500;
}

.popup-box button {
  background: #007bff;
  color: #fff;
  border: none;
  padding: 8px 18px;
  font-size: 14px;
  border-radius: 5px;
  cursor: pointer;
}

.popup-box button:hover {
  background: #005fcc;
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}


/* =================== RESPONSIVE =================== */
@media (max-width: 992px) {
  .profile-container {
    flex-direction: column;
  }

  .profile-sidebar {
    width: 100%;
  }
}

@media (max-width: 576px) {
  .account-page {
    padding: 100px 15px 40px;
  }
}
/* =================== ACTION BUTTONS NEW STYLE =================== */
.action-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

/* Nút chung */
.action-btn-modern {
  padding: 7px 14px;
  border-radius: 6px;
  font-size: 13px;
  border: none;
  cursor: pointer;
  font-weight: 600;
  transition: 0.25s ease;
}
.pagination {
  margin-top: 20px;
  display: flex;
  justify-content: center;
  gap: 10px;
}

.pagination button {
  padding: 6px 14px;
  border-radius: 6px;
  border: 1px solid #ccc;
  background: white;
  cursor: pointer;
  font-weight: 500;
  transition: 0.2s;
}

.pagination button:hover {
  background: #007bff;
  color: white;
}

.pagination button.active {
  background: #007bff;
  color: white;
  border-color: #007bff;
}

.pagination button:disabled {
  background: #eee;
  color: #888;
  cursor: not-allowed;
}
/* POPUP OVERLAY */
.order-popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 99999;
}

/* POPUP BOX */
.order-popup {
  width: 900px;
  max-height: 80vh;
  background: #fff;
  padding: 25px;
  border-radius: 10px;
  overflow-y: auto;
  box-shadow: 0 4px 25px rgba(0, 0, 0, 0.3);
  z-index: 9999;
}


.order-popup h3 {
  margin-bottom: 20px;
  font-size: 22px;
  font-weight: bold;
}

/* TABLE */
.popup-table {
  width: 100%;
  border-collapse: collapse;
}

.popup-table th,
.popup-table td {
  border-bottom: 1px solid #eee;
  padding: 12px 8px;
  text-align: center;
}

.popup-table th {
  background: #f5f5f5;
  font-weight: 600;
}

/* IMG */
.popup-img {
  width: 70px;
  height: 70px;
  object-fit: cover;
  border-radius: 6px;
}

/* Bottom buttons */
.popup-actions {
  text-align: right;
  margin-top: 20px;
}

.btn-secondary {
  background: #666;
  color: white;
  padding: 8px 18px;
  border-radius: 6px;
  cursor: pointer;
}

.btn-secondary:hover {
  background: #444;
}
.rating-stars {
  display: flex;
  gap: 6px;
  font-size: 32px;
  cursor: pointer;
  user-select: none;
}

.rating-stars .star {
  color: #ccc;
  transition: 0.2s;
  cursor: pointer;
}

.rating-stars .star.active {
  color: gold;
}

.rating-stars .star:hover {
  color: gold;
  transform: scale(1.15);
}
/* ====== CUSTOM POPUP (thông báo) ====== */
.custom-popup {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999999;
}

.popup-box {
  background: #fff;
  padding: 25px 30px;
  border-radius: 10px;
  text-align: center;
  width: 350px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  animation: fadeIn 0.2s ease-in-out;
  z-index: 10001; /* Đảm bảo box thông báo nổi bật hơn */
}

/* ====== FORM ĐÁNH GIÁ – BUTTON HỦY VÀ GỬI ====== */
.rating-actions {
  margin-top: 25px;
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.btn-cancel,
.btn-submit {
  padding: 10px 22px;
  border-radius: 8px;
  border: none;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.25s ease;
  min-width: 130px;
}

/* Nút Hủy */
.btn-cancel {
  background: #555;
  color: #fff;
}

.btn-cancel:hover {
  background: #333;
}

/* Nút Gửi đánh giá */
.btn-submit {
  background: #28a745;
  color: white;
  box-shadow: 0 2px 6px rgba(40, 167, 69, 0.25);
}

.btn-submit:hover {
  background: #218838;
  box-shadow: 0 3px 8px rgba(40, 167, 69, 0.35);
}

</style>