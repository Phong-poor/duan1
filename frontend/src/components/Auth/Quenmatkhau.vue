<template>
  <div class="forgot-container">

    <!-- LOGO -->
    <img class="banner-img" src="../../assets/logone.png" alt="forgot password" />
    <h2 class="title">Quên mật khẩu</h2>

    <!-- BƯỚC 1: NHẬP EMAIL -->
    <div v-if="step === 1" class="step-box">
      <p class="desc">Nhập email để nhận mã OTP</p>

      <input type="email" v-model="email" placeholder="Nhập email" class="input-box" />

      <button class="main-btn" @click="sendOTP">Gửi mã OTP</button>
      <div class="back-login" @click="goLogin">← Quay lại đăng nhập</div>
    </div>

    <!-- BƯỚC 2: NHẬP OTP -->
    <div v-if="step === 2" class="step-box">
      <p class="desc">Nhập mã OTP đã gửi tới email của bạn</p>

      <input type="text" v-model="otp" placeholder="Nhập mã OTP" class="input-box" />

      <button class="main-btn" @click="verifyOTP">Xác nhận OTP</button>

      <div class="back-login" @click="resendOTP">Gửi lại mã OTP</div>
      <div class="back-login" @click="goLogin">← Quay lại đăng nhập</div>
    </div>

    <!-- BƯỚC 3: ĐẶT MẬT KHẨU MỚI -->
    <div v-if="step === 3" class="step-box">
      <p class="desc">Hãy đặt mật khẩu mới.</p>

      <input type="password" v-model="newPassword" placeholder="Mật khẩu mới" class="input-box" />
      <input type="password" v-model="confirmPassword" placeholder="Nhập lại mật khẩu" class="input-box" />

      <button class="main-btn" @click="resetPassword">Đặt lại mật khẩu</button>

      <div class="back-login" @click="goLogin">← Quay lại đăng nhập</div>
    </div>

    <!-- POPUP -->
    <div v-if="popup.show" class="popup-overlay">
      <div class="popup-box">
        <h3>{{ popup.title }}</h3>
        <p>{{ popup.message }}</p>
        <button class="popup-btn" @click="popup.show = false">OK</button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

// STATE
const step = ref(1);
const email = ref("");
const otp = ref("");
const newPassword = ref("");
const confirmPassword = ref("");

// POPUP
const popup = ref({
  show: false,
  title: "",
  message: ""
});

const showPopup = (title, message) => {
  popup.value.title = title;
  popup.value.message = message;
  popup.value.show = true;
};

// Điều hướng
const goLogin = () => router.push("/Dangnhap");


// =============================
// 📌 GỬI OTP
// =============================
const sendOTP = async () => {
  if (!email.value)
    return showPopup("Thông báo", "Vui lòng nhập email");

  const res = await fetch("https://miraeshoes.shop/backend/api/Auth/forgotPassword.php?action=sendOTP", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email: email.value })
  });

  const data = await res.json();
  showPopup("Thông báo", data.msg);

  if (data.status === "success") step.value = 2;
};


// =============================
// 📌 XÁC THỰC OTP
// =============================
const verifyOTP = async () => {
  if (otp.value.length < 4)
    return showPopup("Lỗi", "OTP không hợp lệ");

  const res = await fetch("https://miraeshoes.shop/backend/api/Auth/forgotPassword.php?action=verifyOTP", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email: email.value, otp: otp.value })
  });

  const data = await res.json();
  showPopup("Thông báo", data.msg);

  if (data.status === "success") step.value = 3;
};


// =============================
// 📌 GỬI LẠI OTP
// =============================
const resendOTP = async () => {
  const res = await fetch("https://miraeshoes.shop/backend/api/Auth/forgotPassword.php?action=resendOTP", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email: email.value })
  });

  const data = await res.json();
  showPopup("Thông báo", data.msg);
};


// =============================
// 📌 ĐỔI MẬT KHẨU
// =============================
const resetPassword = async () => {
  if (!newPassword.value)
    return showPopup("Lỗi", "Vui lòng nhập mật khẩu mới");

  if (newPassword.value !== confirmPassword.value)
    return showPopup("Lỗi", "Mật khẩu không trùng khớp");

  const res = await fetch("https://miraeshoes.shop/backend/api/Auth/forgotPassword.php?action=resetPassword", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      email: email.value,
      password: newPassword.value,
      confirm_password: confirmPassword.value
    })
  });

  const data = await res.json();
  showPopup("Thông báo", data.msg);

  if (data.status === "success") router.push("/Dangnhap");
};
</script>

<style scoped>
/* GIỮ NGUYÊN CSS CỦA BẠN */

.forgot-container {
  width: 380px;
  padding: 32px 26px;
  margin: 60px auto;
  background: #ffffff;
  border-radius: 22px;
  box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.08);
  text-align: center;
  border: 1px solid #f0f0f0;
}

.banner-img {
  width: 140px;
  height: 60px;
  object-fit: contain;
  margin-bottom: 20px;
}

.title {
  margin-bottom: 5px;
  font-size: 27px;
  font-weight: 700;
  color: #222;
}

.desc {
  font-size: 15px;
  color: #666;
  margin-bottom: 14px;
}

.step-box {
  margin-top: 20px;
}

.input-box {
  width: 95%;
  padding: 14px;
  margin-top: 12px;
  border-radius: 14px;
  border: 1.5px solid #d2d2d2;
  background: #fafafa;
  font-size: 15px;
  transition: 0.25s;
}

.input-box:focus {
  border-color: #4A6CF7;
  background: #fff;
  box-shadow: 0px 0px 8px rgba(74, 108, 247, 0.35);
}

.main-btn {
  width: 100%;
  padding: 14px;
  margin-top: 20px;
  background: linear-gradient(135deg, #4A6CF7, #5a7bff);
  color: white;
  border: none;
  border-radius: 15px;
  font-size: 17px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.25s;
}

.main-btn:hover {
  opacity: 0.95;
  transform: translateY(-2px);
}

.back-login {
  margin-top: 15px;
  color: #4A6CF7;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: 0.2s;
}

.back-login:hover {
  opacity: 0.75;
}


/* POPUP */
.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.45);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.popup-box {
  background: #fff;
  width: 350px;
  padding: 25px;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 5px 20px rgba(0,0,0,0.25);
}

.popup-btn {
  width: 100%;
  padding: 12px;
  margin-top: 15px;
  background: #4A6CF7;
  border: none;
  color: white;
  font-size: 16px;
  border-radius: 10px;
  cursor: pointer;
}

.popup-btn:hover {
  background: #3f5ed9;
}
</style>
