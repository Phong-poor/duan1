<template>
  <div class="container" :class="{ 'active': isRegisterActive }">

    <!-- FORM LOGIN -->
    <div class="form-box login">
      <form @submit.prevent="handleLogin" autocomplete="off">
        <h1>Đăng Nhập</h1>

        <div class="input-box">
          <input type="email" placeholder="Nhập email" v-model="loginEmail" @focus="showEmailList = true"
            @input="filterEmails" @blur="hideEmailList">
          <ul v-if="showEmailList" class="email-dropdown">
            <li v-for="acc in filteredEmails" @mousedown.prevent="selectEmail(acc)">
              {{ acc.email }}
            </li>
          </ul>
          <i class="fa-solid fa-user"></i>
        </div>

        <div class="input-box">
          <input :type="showLoginPassword ? 'text' : 'password'" v-model="loginPassword" placeholder="Password" required
            autocomplete="new-password">
          <!-- ĐÃ SỬA -->
          <i class="fa-solid" :class="showLoginPassword ? 'fa-eye' : 'fa-eye-slash'"
            @click="showLoginPassword = !showLoginPassword"></i>
        </div>

        <div class="row-between">
          <label class="remember-me">
            <input type="checkbox" v-model="rememberPassword" />
            Ghi nhớ mật khẩu
          </label>
          <a class="forgot-link" href="Quenmatkhau">Quên mật khẩu?</a>
        </div>

        <button type="submit" class="btn">Đăng Nhập</button>

        <p>Hoặc đăng nhập bằng</p>
        <div class="social-icons">
          <!-- GOOGLE LOGIN
          <a @click.prevent="handleGoogleLogin" class="google-btn">
            <img :src="googleIconPath" alt="Google">
          </a> -->
          <!-- thay thế phần google btn -->
          <div id="google-btn-container"></div>

          <!-- FACEBOOK (chưa dùng) 
          <a href="#" class="facebook-btn">
            <img :src="facebookIconPath" alt="Facebook">
          </a> -->
        </div>
      </form>
    </div>

    <!-- FORM REGISTER -->
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
          <input :type="showRegPassword ? 'text' : 'password'" v-model="regPassword" placeholder="Nhập mật khẩu"
            required autocomplete="new-password">
          <!-- ĐÃ SỬA -->
          <i class="fa-solid" :class="showRegPassword ? 'fa-eye' : 'fa-eye-slash'"
            @click="showRegPassword = !showRegPassword"></i>
        </div>

        <div class="input-box">
          <input :type="showRegPasswordConfirm ? 'text' : 'password'" v-model="regPasswordConfirm"
            placeholder="Xác nhận mật khẩu" required autocomplete="new-password">
          <!-- ĐÃ SỬA -->
          <i class="fa-solid" :class="showRegPasswordConfirm ? 'fa-eye' : 'fa-eye-slash'"
            @click="showRegPasswordConfirm = !showRegPasswordConfirm"></i>
        </div>
        <button class="btn">Đăng Kí</button>

        <p>Hoặc đăng kí bằng</p>
        <div class="social-icons">
          <a href="#"><img :src="googleIconPath" alt="Google"></a>
          <a href="#"><img :src="facebookIconPath" alt="Facebook"></a>
        </div>
      </form>
    </div>

    <!-- TOGGLE -->
    <div class="toggle-box">
      <img :src="toggleImagePath" alt="Login Banner" class="toggle-image">

      <div class="toggle-panel toggle-left">
        <h1>Hello, Welcome!</h1>
        <p>Bạn chưa có tài khoản?</p>
        <button class="btn register-btn" @click="isRegisterActive = true">Đăng Kí</button>
      </div>

      <div class="toggle-panel toggle-right">
        <h1>Welcome Home!</h1>
        <p>Bạn đã có tài khoản?</p>
        <button class="btn login-btn" @click="isRegisterActive = false">Đăng Nhập</button>
      </div>
    </div>

    <!-- TOAST -->
    <div id="toast" class="toast">{{ toastMessage }}</div>

    <!-- POPUP -->
    <div v-if="popup.show" class="popup-overlay">
      <div class="popup-box">
        <div class="popup-icon"></div>
        <h3>{{ popup.title }}</h3>
        <p>{{ popup.message }}</p>
        <button class="popup-btn" @click="popup.show = false">OK</button>
      </div>
    </div>

   
    <div id="toast" class="toast">{{ toastMessage }}</div>

  </div>
</template>


<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import toggleImg from '../../assets/images.jpg';
import googleIcon from '../../assets/Google__G__logo.svg.jpg';
import facebookIcon from '../../assets/Facebook_Logo_(2019).jpg';

const router = useRouter();

/* ========== STATE ========== */
const isRegisterActive = ref(false);

/* LOGIN */
const loginEmail = ref('');
const loginPassword = ref('');
const rememberPassword = ref(false);
const showLoginPassword = ref(false);

/* REGISTER */
const regUser = ref('');
const regEmail = ref('');
const regPassword = ref('');
const regPasswordConfirm = ref('');
const showRegPassword = ref(false);
const showRegPasswordConfirm = ref(false);

/* ICONS & IMAGES */
const toggleImagePath = toggleImg;
const googleIconPath = googleIcon;
const facebookIconPath = facebookIcon;

/* TOAST */
const toastMessage = ref('');

/* POPUP */
const popup = reactive({
  show: false,
  title: '',
  message: ''
});

/* ========== LIFECYCLE ========== */
onMounted(() => {
  const savedLogin = JSON.parse(localStorage.getItem("savedLogin"));
  if (savedLogin) {
    loginEmail.value = savedLogin.email;
    loginPassword.value = savedLogin.password;
    rememberPassword.value = true;
  }
});

/* ========== FUNCTIONS ========== */
const showPopup = (title, message) => {
  popup.title = title;
  popup.message = message;
  popup.show = true;
};

const showToast = (msg, type = "success") => {
  toastMessage.value = msg;
  const toast = document.getElementById("toast");
  toast.style.background =
    type === "error"
      ? "linear-gradient(135deg, #e74c3c, #c0392b)"
      : "linear-gradient(135deg, #4CAF50, #2ecc71)";
  toast.classList.add("show");
  setTimeout(() => toast.classList.remove("show"), 2500);
};

/* ===== LOGIN ===== */
const handleLogin = async () => {
  if (!loginEmail.value || !loginPassword.value) {
    showPopup("Thông báo", "Vui lòng nhập đầy đủ thông tin!");
    return;
  }

  try {
    const res = await fetch("https://miraeshoes.shop/backend/api/Auth/login.php", {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email: loginEmail.value, password: loginPassword.value })
    });

    const text = await res.text();
    let data;
    try { data = JSON.parse(text); } catch { showPopup("Lỗi", "Server trả dữ liệu sai!"); return; }

    if (data.status === "success") {
      showPopup("Thành công", data.msg || "Đăng nhập thành công!");
      localStorage.setItem("currentUser", JSON.stringify(data.user || {}));

      if (rememberPassword.value) {
        let savedList = JSON.parse(localStorage.getItem("savedAccounts")) || [];

        // Xóa email cũ để tránh bị trùng
        savedList = savedList.filter(acc => acc.email !== loginEmail.value);

        // Lưu mật khẩu mới
        savedList.push({
          email: loginEmail.value,
          password: loginPassword.value
        });

        localStorage.setItem("savedAccounts", JSON.stringify(savedList));
      }

      setTimeout(() => {
        if (data.user?.role === "admin") router.push("/Dashboard");
        else router.push("/");
      }, 800);
      return;
    }

    showPopup("Lỗi đăng nhập", data.msg || "Email hoặc mật khẩu sai!");
  } catch (error) {
    showPopup("Lỗi", "Không thể kết nối server!");
  }
};

const showEmailList = ref(false);
const emailList = ref([]);
const filteredEmails = ref([]);

onMounted(() => {
  emailList.value = JSON.parse(localStorage.getItem("savedAccounts")) || [];
  filteredEmails.value = emailList.value;
});

const filterEmails = () => {
  filteredEmails.value = emailList.value.filter(acc =>
    acc.email.toLowerCase().includes(loginEmail.value.toLowerCase())
  );
};

const selectEmail = (acc) => {
  loginEmail.value = acc.email;
  loginPassword.value = acc.password;
  showEmailList.value = false;
};

const hideEmailList = () => {
  setTimeout(() => showEmailList.value = false, 200);
};


// ===== GOOGLE LOGIN =====

const GOOGLE_CLIENT_ID = "715472750510-l92si2vfelssksmk16r32n7cc0f22g8j.apps.googleusercontent.com";

const loadGoogleSDK = () => {
  return new Promise((resolve, reject) => {
    if (window.google && window.google.accounts) return resolve();

    const script = document.createElement("script");
    script.src = "https://accounts.google.com/gsi/client";
    script.async = true;
    script.defer = true;

    script.onload = resolve;
    script.onerror = () => reject("Google SDK load failed");

    document.head.appendChild(script);
  });
};

const initGoogleButton = async () => {
  try {
    await loadGoogleSDK();

    window.google.accounts.id.initialize({
      client_id: GOOGLE_CLIENT_ID,
      callback: handleGoogleCallback,
      ux_mode: "popup"
    });

    const el = document.getElementById("google-btn-container");
    if (el) {
      el.innerHTML = "";
      window.google.accounts.id.renderButton(el, {
        type: "standard",
        theme: "outline",
        size: "large",
        text: "continue_with",
        shape: "rectangular",
      });
    }
  } catch (err) {
    console.error(err);
    showPopup("Lỗi", "Không thể tải Google Login");
  }
};

const handleGoogleCallback = async (response) => {
  if (!response || !response.credential) {
    return showPopup("Lỗi", "Không lấy được token từ Google");
  }

  try {
    const res = await fetch("https://miraeshoes.shop/backend/api/Auth/google_login.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ credential: response.credential })
    });

    const text = await res.text();
    let data;

    try { data = JSON.parse(text); }
    catch {
      console.error("Server trả HTML:", text);
      return showPopup("Lỗi", "không hợp lệ");
    }

    if (data.status === "success") {
      localStorage.setItem("currentUser", JSON.stringify(data.user));
      showPopup("Thành công", "Đăng nhập Google thành công!");
      setTimeout(() => router.push("/"), 500);
      return;
    }

    showPopup("Lỗi", data.msg || "Google login thất bại!");
  } catch (e) {
    showPopup("Lỗi", "Không thể kết nối server");
  }
};

// chạy khi mounted
onMounted(() => {
  initGoogleButton();
});



/* ===== REGISTER ===== */
const handleRegister = async () => {
  if (!regUser.value || !regEmail.value || !regPassword.value || !regPasswordConfirm.value) {
    showPopup("Cảnh báo", "Vui lòng nhập đầy đủ thông tin!");
    return;
  }

  const emailPattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
  if (!emailPattern.test(regEmail.value)) {
    showPopup("Email không hợp lệ", "Email phải kết thúc bằng @gmail.com!");
    return;
  }

  if (regPassword.value.length < 6) {
    showPopup("Mật khẩu yếu", "Mật khẩu phải ít nhất 6 ký tự!");
    return;
  }

  if (regPassword.value !== regPasswordConfirm.value) {
    showPopup("Sai mật khẩu", "Mật khẩu xác nhận không khớp!");
    return;
  }

  try {
    const res = await fetch("https://miraeshoes.shop/backend/api/Auth/Register.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        tenKH: regUser.value,
        email: regEmail.value,
        password: regPassword.value,
        confirm_password: regPasswordConfirm.value
      })
    });

    const data = await res.json();

    if (data.status === "success") {
      showPopup("Thành công", data.msg);
      isRegisterActive.value = false;
    } else {
      showPopup("Lỗi đăng ký", data.msg);
    }

  } catch (err) {
    console.error(err);
    showPopup("Lỗi", "Không thể kết nối server");
  }
};
</script>

<style scoped>
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

.input-box i.fa-eye,
.input-box i.fa-eye-slash {
  cursor: pointer;
  right: 18px;
  font-size: 20px;
  color: #555;
  transition: 0.2s ease;
}




/* --- FIX GIAO DIỆN REMEMBER + QUÊN MẬT KHẨU --- */

.row-between {
  width: 100%;
  margin-top: 10px;
  margin-bottom: 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.remember-me {
  font-size: 15px;
  margin-bottom: 30px;
  color: #333;
  display: flex;
  align-items: center;
  gap: 10px;
}

.remember-me input[type="checkbox"] {
  transform: scale(1.1);
  cursor: pointer;
}

.forgot-link {
  font-size: 14px;
  color: #555;
  cursor: pointer;
  text-decoration: none;
}

.forgot-link:hover {
  text-decoration: underline;
}



/* ================= POPUP ================= */
.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.45);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  animation: fadeIn 0.25s ease;
}

.popup-box {
  background: #fff;
  width: 380px;
  padding: 25px 30px;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 5px 25px rgba(0, 0, 0, 0.25);
  animation: popupShow 0.25s ease;
}

.popup-box h2 {
  margin-bottom: 10px;
  font-size: 26px;
  color: #333;
  font-weight: 600;
}

.popup-box p {
  font-size: 16px;
  color: #555;
  margin-bottom: 20px;
}

.popup-btn {
  width: 100%;
  padding: 12px 0;
  background: #7494ec;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
}

.popup-btn:hover {
  background: #5a7ae0;
}

/* ANIMATION */
@keyframes fadeIn {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

@keyframes popupShow {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }

  to {
    transform: translateY(0);
    opacity: 1;
  }
}

:deep(i[class^="fa-"]),
:deep(i[class*=" fa-"]) {
  font-family: "Font Awesome 6 Free" !important;
  font-weight: 900 !important;
  display: inline-block;
}

.email-dropdown {
  position: absolute;
  top: 46px;
  left: 0;
  width: 100%;
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 6px;
  max-height: 160px;
  overflow-y: auto;
  z-index: 20;
}

.email-dropdown li {
  padding: 8px 10px;
  cursor: pointer;
}

.email-dropdown li:hover {
  background: #f0f0f0;
}

/*css đăng nhập gg */
.social-icons {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-top: 15px;
}
</style>