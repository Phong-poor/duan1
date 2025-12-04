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
            <a class="active">Thông tin chung</a>
            <a>Quản lý đơn hàng</a>
            
      
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
<section class="order-section">

  <h2 class="section-title-profile">Đơn Hàng Gần Đây</h2>

  <div class="card">
    <table class="order-table">
      <thead>
        <tr>
          <th>Mã Đơn</th>
          <th>Ngày Đặt</th>
          <th>Tổng Tiền</th>
          <th>Trạng Thái</th>
          <th class="action-cell">Hành Động</th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td>#KH10245</td>
          <td>01/11/2025</td>
          <td>4,800,000 VNĐ</td>
          <td><span class="status-shipping">Đang xử lý</span></td>
          <td class="action-cell">
            <router-link to="/ChiTietDonHang" class="action-link">Xem</router-link>
            <button class="action-btn-custom cancel-btn">Hủy Đơn</button>
          </td>
        </tr>

        <tr>
          <td>#KH10240</td>
          <td>15/10/2025</td>
          <td>3,500,000 VNĐ</td>
          <td><span class="status-paid">Thành công</span></td>
          <td class="action-cell">
            <router-link to="/ChiTietDonHang" class="action-link">Xem</router-link>
            <button class="action-btn-custom cancel-btn">Trả hàng</button>
          </td>
        </tr>

        <tr>
          <td>#KH10221</td>
          <td>28/09/2025</td>
          <td>7,200,000 VNĐ</td>
          <td><span class="status-paid">Thành công</span></td>
          <td class="action-cell">
            <router-link to="/ChiTietDonHang" class="action-link">Xem</router-link>
            <button class="action-btn-custom cancel-btn">Trả hàng</button>
          </td>
        </tr>

        <tr>
          <td>#KH10200</td>
          <td>10/09/2025</td>
          <td>2,990,000 VNĐ</td>
          <td class="status-cancelled">Đã hủy</td>
          <td class="action-cell">
            <router-link to="/ChiTietDonHang" class="action-link">Xem</router-link>
            <button class="action-btn-custom reorder-btn">Mua lại</button>
          </td>
        </tr>
      </tbody>
    </table>

    <button class="edit-btn primary" style="width:100%; margin-top:15px;">
      Xem tất cả đơn hàng
    </button>
  </div>

</section>


      </div>
    </main>
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
import { reactive, ref, onMounted } from "vue";
import HeaderWeb from "../../Header-web.vue";
import footerWeb from "../../footer-web.vue";

// 🔥 ĐƯỜNG DẪN ĐÚNG
const API_URL = "http://localhost/duan1/backend/api/Web/user.php";

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
  margin-top: 15px;
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

.action-link {
  color: #007bff;
  margin-right: 5px;
  text-decoration: none;
}

.action-link:hover {
  text-decoration: underline;
}

.action-btn-custom {
  padding: 6px 10px;
  border-radius: 4px;
  font-size: 13px;
  margin-left: 5px;
}

.cancel-btn {
  background-color: #dc3545;
}

.reorder-btn {
  background-color: #007bff;
}

/* ======= STATUS ======= */
.status-shipping {
  color: orange;
  font-weight: 500;
}

.status-paid {
  color: green;
  font-weight: 500;
}

.status-cancelled {
  color: #777;
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
</style>

