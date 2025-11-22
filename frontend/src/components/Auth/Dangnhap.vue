<template>
  <div class="container" :class="{ 'active': isRegisterActive }">
    <div class="form-box login">
      <form @submit.prevent="handleLogin">
        <h1>Đăng Nhập</h1>

        <div class="input-box">
          <input type="email" v-model="loginEmail" placeholder="email" required>
          <i class="fa-solid fa-user"></i>
        </div>

        <div class="input-box">
          <input type="password" v-model="loginPassword" placeholder="Password" required>
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
          <a href="#"><img :src="githubIconPath" alt="GitHub"></a>
        </div>
      </form>
    </div>

    <div class="form-box register">
      <form @submit.prevent="handleRegister">
        <h1>Đăng Kí</h1>
        <div class="input-box">
          <input type="text" v-model="regUser" placeholder="Username" required>
          <i class="fa-solid fa-envelope"></i>
        </div>
        <div class="input-box">
          <input type="email" v-model="regEmail" placeholder="email" required>
          <i class="fa-solid fa-user"></i>
        </div>
        <div class="input-box">
          <input type="password" v-model="regPassword" placeholder="Password" required>
          <i class="fa-solid fa-lock"></i>
        </div>
        <button class="btn">Đăng Kí</button>

        <p>Hoặc đăng kí bằng</p>
        <div class="social-icons">
          <a href="#"><img :src="googleIconPath" alt="Google"></a>
          <a href="#"><img :src="facebookIconPath" alt="Facebook"></a>
          <a href="#"><img :src="githubIconPath" alt="GitHub"></a>
        </div>
      </form>
    </div>

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
import githubIcon from '../../assets/github1.jpg';

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
      regPassword: "",

      toggleImagePath: toggleImg,
      googleIconPath: googleIcon,
      facebookIconPath: facebookIcon,
      githubIconPath: githubIcon,
    };
  },

  methods: {
    showRegister() {
      this.isRegisterActive = true;
    },
    showLogin() {
      this.isRegisterActive = false;
    },

    //  ĐĂNG NHẬP
    handleLogin() {
      if (!this.loginEmail || !this.loginPassword) {
        alert("Vui lòng nhập đủ thông tin!");
        return;
      }

      // Sau khi login hợp lệ → chuyển trang Home
      this.$router.push("/trang-chu");
    },

    //  ĐĂNG KÝ
    handleRegister() {
      alert("Đăng ký thành công!");
      this.showLogin();
    },

    //  CHUYỂN TRANG QUÊN MẬT KHẨU
    goForgotPassword() {
      this.$router.push("/quen-mat-khau");
    }
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
}

/* Khung bao toàn bộ login/register */
.container {
  position: relative;
  width: 850px;
  height: 550px;
  background: #fff;
  margin: 20px;
  border-radius: 30px;
  box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
  overflow: hidden;
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
  transition-delay: 0s;
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