<template>
  <div class="container" :class="{ 'active': isRegisterActive }">
    <!-- Form login -->
    <div class="form-box login">
      <form @submit.prevent="handleLogin" autocomplete="off">
        <h1>Đăng Nhập</h1>

        <div class="input-box">
          <input type="email" v-model="loginEmail" placeholder="Email" required autocomplete="off">
          <i class="fa-solid fa-user"></i>
        </div>

        <div class="input-box">
          <input type="password" v-model="loginPassword" placeholder="Password" required autocomplete="new-password">
          <i class="fa-solid fa-lock"></i>
        </div>

        <div class="forgot-link">
          <a @click.prevent="goForgotPassword">Quên mật khẩu?</a>
        </div>

        <button type="submit" class="btn">Đăng Nhập</button>

        <p>Hoặc đăng nhập bằng</p>
        <div class="social-icons">
          <a href="#"><img :src="googleIconPath" alt="Google"></a>
          <a href="#"><img :src="facebookIconPath" alt="Facebook"></a>
        </div>
      </form>
    </div>
    <!-- Form register -->
    <div class="form-box register">
      <form @submit.prevent="handleRegister" autocomplete="off">
        <h1>Đăng Kí</h1>

        <div class="input-box">
          <input type="text" v-model="regUser" placeholder="Tên của bạn" required autocomplete="off">
          <i class="fa-solid fa-user"></i>
        </div>

        <div class="input-box">
          <input type="email" v-model="regEmail" placeholder="Email" required autocomplete="off">
          <i class="fa-solid fa-envelope"></i>
        </div>

        <div class="input-box">
          <input type="tel" v-model="regPhone" placeholder="Số điện thoại" required autocomplete="off">
          <i class="fa-solid fa-phone"></i>
        </div>

        <div class="input-box">
          <select v-model="regGender" required>
            <option value="" disabled selected>Chọn giới tính</option>
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
            <option value="Khác">Khác</option>
          </select>
          <i class="fa-solid fa-venus-mars"></i>
        </div>

        <div class="input-box">
          <input type="date" v-model="regBirth" required>
          <i class="fa-solid fa-calendar"></i>
        </div>

        <div class="input-box">
          <input type="password" v-model="regPassword" placeholder="Nhập mật khẩu" required autocomplete="new-password">
          <i class="fa-solid fa-lock"></i>
        </div>

        <div class="input-box">
          <input type="password" v-model="regPasswordConfirm" placeholder="Xác nhận mật khẩu" required
            autocomplete="new-password">
          <i class="fa-solid fa-lock"></i>
        </div>
        <button class="btn">Đăng Kí</button>

        <p>Hoặc đăng kí bằng</p>
        <div class="social-icons">
          <a href="#"><img :src="googleIconPath" alt="Google"></a>
          <a href="#"><img :src="facebookIconPath" alt="Facebook"></a>
        </div>
      </form>
    </div>
    <!-- Toggle panel -->
    <div class="toggle-box">
      <img :src="toggleImagePath" alt="Login Banner" class="toggle-image">

      <div class="toggle-panel toggle-left">
        <h1>Hello, Welcome!</h1>
        <p>Bạn chưa có tài khoản?</p>
        <button class="btn register-btn" @click="showRegister">Đăng Kí</button>
      </div>

      <div class="toggle-panel toggle-right">
        <h1>Welcome Home!</h1>
        <p>Bạn đã có tài khoản?</p>
        <button class="btn login-btn" @click="showLogin">Đăng Nhập</button>
      </div>
    </div>
  </div>
</template>

<script>
import toggleImg from '../../assets/images.jpg';
import googleIcon from '../../assets/Google__G__logo.svg.jpg';
import facebookIcon from '../../assets/Facebook_Logo_(2019).jpg';

export default {
  data() {
    return {
      isRegisterActive: false,

      // login
      loginEmail: "",
      loginPassword: "",

      // register
      regUser: "",
      regEmail: "",
      regPhone: "",
      regGender: "",
      regBirth: "",
      regPassword: "",
      regPasswordConfirm: "",

      toggleImagePath: toggleImg,
      googleIconPath: googleIcon,
      facebookIconPath: facebookIcon,
    };
  },

  methods: {
    showRegister() { this.isRegisterActive = true; },
    showLogin() { this.isRegisterActive = false; },

    // ===== PHẦN ĐĂNG NHẬP =====
    async handleLogin() {
      if (!this.loginEmail || !this.loginPassword) {
        alert("Vui lòng nhập đủ thông tin!");
        return;
      }
      try {
        const res = await fetch("http://localhost/duan1/backend/api/Auth/login.php", {
          method: "POST",
          credentials: "include",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            email: this.loginEmail,
            password: this.loginPassword
          })
        });

        const text = await res.text();
        let data;

        try {
          data = JSON.parse(text);
        } catch (e) {
          console.warn("Server trả dữ liệu không phải JSON:", text);
          alert("Lỗi server! Vui lòng thử lại.");
          return;
        }

        if (data.status === "success" && data.user) {
            localStorage.setItem("currentUser", JSON.stringify(data.user));
            alert(data.msg);

            const role = (data.user.role || "").trim().toLowerCase();
            if (role === "admin") {
                this.$router.push("/Dashboard");
            } else {
                this.$router.push("/");
            }
        } else {
          // Server trả lỗi hoặc user null
          alert(data.msg || "Email hoặc mật khẩu không đúng!");
        }

      } catch (error) {
        console.error("Lỗi kết nối server:", error);
        alert("Lỗi kết nối server! Vui lòng thử lại.");
      }
    },

    // ===== PHẦN ĐĂNG KÍ =====
    async handleRegister() {
      if (!this.regUser || !this.regEmail || !this.regPhone || !this.regGender ||
          !this.regBirth || !this.regPassword || !this.regPasswordConfirm) {
        alert("Vui lòng nhập đầy đủ thông tin!");
        return;
      }

      // Validate email, phone, password
      const emailPattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
      if (!emailPattern.test(this.regEmail)) {
        alert("Email phải kết thúc bằng @gmail.com!");
        return;
      }

      const phonePattern = /^\d{10}$/;
      if (!phonePattern.test(this.regPhone)) {
        alert("Số điện thoại phải đủ 10 số!");
        return;
      }

      if (this.regPassword.length < 6) {
        alert("Mật khẩu phải ít nhất 6 ký tự!");
        return;
      }

      if (this.regPassword !== this.regPasswordConfirm) {
        alert("Mật khẩu xác nhận không khớp!");
        return;
      }

      const payload = {
        tenKH: this.regUser,
        email: this.regEmail,
        phone: this.regPhone,
        gender: this.regGender,
        ngaysinh: this.regBirth,
        password: this.regPassword,
        confirm_password: this.regPasswordConfirm
      };

      try {
        const res = await fetch("http://localhost/duan1/backend/api/Auth/register.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (data.status === "success") {
          alert("Đăng ký thành công! Vui lòng đăng nhập.");
          this.showLogin();
        } else {
          alert(data.msg || "Đăng ký thất bại!");
        }

      } catch (error) {
        console.error(error);
        alert("Lỗi kết nối server! Vui lòng thử lại.");
      }
    },
  }
};
</script>
<style scoped>
/* Import Font Awesome & Google Font Poppins */
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css");
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap");

/* Reset chung cho giao diện */
* {
  font-family: "Poppins", sans-serif;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  text-decoration: none;
  list-style: none;
}

/* Nền gradient + căn giữa container */
body {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(90deg, #e2e2e2, #c9d6ff);
  margin: 0;
  /* tránh padding/margin mặc định */
}
/* Khung bao toàn bộ login/register */
.container {
  position: relative;
  width: 1000px;
  max-width: 95%;
  height: 850px;
  background: #fff;
  border-radius: 30px;
  box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
  overflow: hidden;

  /* Căn giữa màn hình */
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 auto;
}

/* Tiêu đề */
.container h1 {
  font-size: 36px;
  margin: -10px;
}

/* Mô tả nhỏ */
.container p {
  font-size: 14.5px;
  margin: 15px 0;
}

/* Form chiếm toàn khung form-box */
form {
  width: 100%;
}

/* Khung login/register nằm chồng lên nhau (absolute) */
.form-box {
  position: absolute;
  right: 0;
  width: 50%;
  height: 100%;
  background: #fff;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  color: #333;
  text-align: center;
  padding: 40px;
  z-index: 1;
  transition: 0.5s ease-in-out;
}

/* Khi active → form login trượt sang trái */
.container.active .form-box.login {
  right: 50%;
  transition-delay: 0s;
}

/* Khi active → form register trượt vào vị trí */
.container.active .form-box.register {
  right: 50%;
  transition-delay: 0.2s;
}

/* Chỉnh select giống input-box */
.input-box select {
  width: 100%;
  padding: 13px 50px 13px 20px;
  background: #eee;
  border-radius: 8px;
  border: none;
  outline: none;
  font-size: 16px;
  color: #333;
  font-weight: 500;
  -webkit-appearance: none;
  /* loại bỏ style mặc định trên Chrome/Safari */
  -moz-appearance: none;
  /* Firefox */
  appearance: none;
  cursor: pointer;
}

/* Placeholder màu xám */
.input-box select option[value=""][disabled] {
  color: #888;
}

/* Icon bên phải vẫn đúng vị trí */
.input-box i {
  position: absolute;
  right: 20px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
  color: #333;
}

.input-box i.fa-calendar {
  color: #7494ec;
}

/* Ẩn register khi chưa active */
.form-box.register {
  visibility: hidden;
  transition-delay: 0s;
}

/* Hiện register khi active */
.container.active .form-box.register {
  visibility: visible;
}

/* Hộp input */
.input-box {
  position: relative;
  margin: 30px 0;
}

/* Ô input */
.input-box input {
  width: 100%;
  padding: 13px 50px 13px 20px;
  background: #eee;
  border-radius: 8px;
  border: none;
  outline: none;
  font-size: 16px;
  color: #333;
  font-weight: 500;
}

/* Placeholder */
.input-box input::placeholder {
  color: #888;
  font-weight: 400;
}

/* Icon trong input */
.input-box i {
  position: absolute;
  right: 20px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
}

/* Link quên mật khẩu */
.forgot-link {
  margin: -15px 0 15px;
}

.forgot-link a {
  font-size: 14.5px;
  color: #333;
}

/* Nút bấm */
.btn {
  width: 100%;
  height: 48px;
  background: #7494ec;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  border: none;
  outline: none;
  cursor: pointer;
  font-size: 16px;
  color: #fff;
  font-weight: 600;
}

/* Hàng icon mạng xã hội */
.social-icons {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

/* Ô icon */
.social-icons a {
  display: inline-flex;
  justify-content: center;
  align-items: center;
  width: 40px;
  height: 40px;
  border: 1px solid #ccc;
  margin: 0 8px;
  border-radius: 8px;
  background-color: #fff;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

/* Ảnh icon */
.social-icons a img {
  width: 24px;
  height: 24px;
  object-fit: contain;
}

/* Toggle-box: hộp chứa ảnh + panel */
.toggle-box {
  position: absolute;
  left: 0;
  width: 50%;
  height: 100%;
  background: transparent;
  border-radius: 30px 0 0 30px;
  z-index: 3;
  transition: 0.8s ease-in-out;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
}

/* Khi active → toggle-box chạy sang phải */
.container.active .toggle-box {
  left: 50%;
  border-radius: 0 30px 30px 0;
}

/* Ảnh nền chuyển động mượt */
.toggle-image {
  position: absolute;
  width: 100%;
  height: 100%;
  object-fit: cover;
  top: 0;
  left: 0;
  z-index: 1;
  transition: 0.6s ease-in-out;
}

/* Khi active ảnh đổi vị trí */
.container.active .toggle-image {
  left: 0;
}

/* Panel chứa text + nút */
.toggle-panel {
  position: absolute;
  width: 100%;
  height: 100%;
  color: #fff;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  padding: 40px;
  background: rgba(0, 0, 0, 0.45);
  /* làm tối ảnh */
  z-index: 2;
}

/* Panel trái (khi chưa active) */
.toggle-panel.toggle-left {
  opacity: 1;
  transform: translateX(0);
  transition: opacity 0.3s ease-in-out 0.2s, transform 0.3s ease-in-out 0.2s;
}

/* Khi active panel trái biến mất */
.container.active .toggle-panel.toggle-left {
  opacity: 0;
  transform: translateX(-100%);
  transition-delay: 0s;
}

/* Panel phải (hiện lên khi active) */
.toggle-panel.toggle-right {
  opacity: 0;
  transform: translateX(100%);
  transition: opacity 0.3s ease-in-out 0s, transform 0.3s ease-in-out 0s;
}

/* Panel phải vào vị trí khi active */
.container.active .toggle-panel.toggle-right {
  opacity: 1;
  transform: translateX(0);
  transition-delay: 0.2s;
}

/* Style tiêu đề panel */
.toggle-panel h1 {
  font-size: 36px;
  margin-bottom: 10px;
}

/* Mô tả panel */
.toggle-panel p {
  font-size: 14.5px;
  margin-bottom: 20px;
}

/* Nút trên panel */
.toggle-panel .btn {
  width: 160px;
  height: 46px;
  background: transparent;
  border: 2px solid #fff;
  box-shadow: none;
  font-weight: 500;
}
</style>