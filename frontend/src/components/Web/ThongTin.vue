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
            
      
          </div>
        </aside>

        <!-- MAIN CONTENT -->
        <section class="profile-content">
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
                      @click="cancelOrder(o.id_donhang)"
                    >
                      Huỷ đơn
                    </button>
                    <!-- Nút Trả hàng -->
                    <button
                      v-if="o.trangthai === 'Thành công'"
                      class="btn btn-danger"
                      @click="returnOrder(o.id_donhang)"
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
      </div>
    </main>
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
                class="btn btn-success">
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
const API_URL = "http://localhost/duan1/backend/api/Web/user.php";
const activeTab = ref("info");
const API_ORDER_ACTION = "http://localhost/duan1/backend/api/Web/OrderAction.php";
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

  const res = await fetch(`${API_URL}?id=${user.id_khachhang}`, {
    method: "PUT",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(editUser),
  });

  const data = await res.json();

  if (data.status) {

    // Cập nhật giao diện
    Object.assign(user, editUser);

    // 🔥 Lưu lại vào localStorage
    localStorage.setItem("currentUser", JSON.stringify(user));

   showCenterPopup(`Cập nhật thành công! Xin chào ${user.tenKH}!`);

    isEditing.value = false;

  } else {
    alert("Cập nhật thất bại!");
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
const API_ORDER = "http://localhost/duan1/backend/api/Web/Lichsudonhang.php";

const loadOrders = async () => {
  if (!user.id_khachhang) return;

  const res = await fetch(`${API_ORDER}?user_id=${user.id_khachhang}`);
  const data = await res.json();

  if (data.status) {
    orders.value = data.data;
  }
};

onMounted(() => {
  loadOrders();
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
  const res = await fetch(`http://localhost/duan1/backend/api/Web/Chitietdonhang.php?id=${id}`);
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
  box-shadow: 0 4px 25px rgba(0,0,0,0.3);
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

</style>