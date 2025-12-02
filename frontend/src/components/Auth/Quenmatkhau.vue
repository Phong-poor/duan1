<template>
    <div class="forgot-container">
        <!-- LOGO MIRAE -->
        <img class="banner-img" src="../../assets/logone.png" alt="forgot password" />

        <h2 class="title">Quên mật khẩu</h2>

        <!-- BƯỚC 1: NHẬP EMAIL -->
        <div v-if="step === 1" class="step-box">
            <p class="desc">Nhập email để nhận mã OTP</p>

            <input type="email" v-model="email" placeholder="Nhập email" class="input-box" />

            <button class="main-btn" @click="sendOTP">Gửi mã OTP</button>
            <div class="back-login" @click="goLogin">
                ← Quay lại đăng nhập
            </div>
        </div>

        <!-- BƯỚC 2: NHẬP OTP -->
        <div v-if="step === 2" class="step-box">
            <p class="desc">Nhập mã OTP đã được gửi tới email của bạn</p>

            <input type="text" v-model="otp" placeholder="Nhập mã OTP" class="input-box" />

            <button class="main-btn" @click="verifyOTP">Xác nhận OTP</button>

            <div class="back-login" @click="step = 1">
                Gửi lại mã
            </div>
        </div>

        <!-- BƯỚC 3: ĐẶT MẬT KHẨU MỚI -->
        <div v-if="step === 3" class="step-box">
            <p class="desc">Mã OTP hợp lệ! Hãy đặt mật khẩu mới.</p>

            <input type="password" v-model="newPassword" placeholder="Mật khẩu mới" class="input-box" />

            <input type="password" v-model="confirmPassword" placeholder="Nhập lại mật khẩu mới" class="input-box" />

            <button class="main-btn" @click="resetPassword">Đặt lại mật khẩu</button>

            <div class="back-login" @click="goLogin">
                ← Quay lại đăng nhập
            </div>
        </div>

    </div>
</template>

<script>
export default {
    data() {
        return {
            step: 1,
            email: "",
            otp: "",
            newPassword: "",
            confirmPassword: ""
        };
    },

    methods: {
        goLogin() {
            this.$router.push({ name: "Dangnhap" });
        },
        sendOTP() {
            if (!this.email) return alert("Vui lòng nhập email");
            console.log("Gửi OTP:", this.email);
            this.step = 2;
        },

        verifyOTP() {
            if (this.otp.length < 4) return alert("OTP không hợp lệ");
            console.log("Xác OTP:", this.otp);
            this.step = 3;
        },

        resetPassword() {
            if (!this.newPassword) return alert("Vui lòng nhập mật khẩu mới");
            if (this.newPassword !== this.confirmPassword)
                return alert("Mật khẩu không trùng khớp!");

            alert("Đặt lại mật khẩu thành công!");

            this.$router.push({ name: "Dangnhap" });
        }
    }
};
</script>
<style
    scoped>
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

    /* LOGO MIRAE */
    .banner-img {
        width: 140px;
        height: 60px;
        object-fit: contain;
        /* Giữ nguyên tỉ lệ logo */
        margin-bottom: 20px;
        border-radius: 0;
        /* Không bo góc */
        border: none;
        /* Không viền */
        padding: 0;
        /* Không khoảng cách */
        background: transparent;
        /* Không nền */
        box-shadow: none;
        /* Không bóng */
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
</style>
