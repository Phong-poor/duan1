<template>
  <div>
    <HeaderWeb />

    <div class="support-wrapper">
      <div class="container">
        <h1 class="title">Trung tâm hỗ trợ khách hàng</h1>
        <p class="subtitle">
          Đội ngũ MIRAE luôn trực tiếp để giải đáp mọi thắc mắc của bạn về sản phẩm,
          đơn hàng và chính sách.
        </p>

        <div class="content">
          <!-- LEFT -->
          <div class="left-box">
            <div class="info-block">
              <h3>Thông tin liên hệ chính thức</h3>
              <p>Trụ sở chính:</p>
              <p>57/3 Bùi Minh Trực, P.5, TP. Buôn Ma Thuột</p>
              <p>Hotline: 1900 88 66 92</p>
              <p>Email hỗ trợ:</p>
              <p class="email">support@miraeofficial.com</p>
              <p>Giờ làm việc:</p>
              <p>Thứ 2 - Thứ 7: 08:00 - 20:00 (trừ ngày lễ)</p>
            </div>

            <iframe
              class="map"
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3834.014135673787!2d108.04493!3d12.68849!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3171f80c24d2f0a1%3A0x6ddc4c0db8f2e1c6!2zNTcvMyBCw7RpIE1pbmggVHLGsOG7o2MsIFBoxrDhu51uZyA1LCBCdW9uIE1hIFRow7RuZywgR2lhIHBow6F0IE1hcg!5e0!3m2!1svi!2s!4v1732222000000!5m2!1svi!2s"
              allowfullscreen
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
          </div>

          <!-- RIGHT -->
          <div class="right-box">
            <div class="note">
              ✨ <b>Về Mirae</b><br />
              Mirae là đội ngũ chuyên cung cấp các dòng giày sneaker chính hãng 100%…
            </div>

            <form class="form">
              <div class="row">
                <div class="col">
                  <label>Họ và tên</label>
                  <input />
                </div>
                <div class="col">
                  <label>Email</label>
                  <input />
                </div>
              </div>

              <label>Chọn chủ đề liên hệ</label>
              <!-- DROPDOWN ĐẸP -->
              <div class="select-wrapper">
                <select>
                  <option value="" disabled selected>-- Chọn chủ đề liên hệ --</option>
                  <option value="order">Vấn đề về đơn hàng</option>
                  <option value="warranty">Bảo hành & đổi trả</option>
                  <option value="product">Tư vấn sản phẩm / size</option>
                  <option value="payment">Thanh toán & vận chuyển</option>
                  <option value="other">Khác...</option>
                </select>
              </div>

              <label>Nội dung chi tiết</label>
              <textarea rows="4"></textarea>

              <button class="submit-btn">📨 Gửi yêu cầu</button>
            </form>

            <div class="section-title">BÀI VIẾT HỖ TRỢ VÀ CHÍNH SÁCH</div>

            <div class="faq">
              <details>
                <summary>[TIN TỨC] Cẩm nang kiểm tra hàng và hoàn trả 100%</summary>
                <p>Khi nhận hàng, hãy:</p>
                <ul>
                  <li>Kiểm tra hộp, tem niêm phong còn nguyên vẹn.</li>
                  <li>Đối chiếu size, mã, màu với đơn hàng.</li>
                  <li>Kiểm tra nhanh keo, đường chỉ, chất liệu và form giày.</li>
                </ul>
              </details>

              <details>
                <summary>[HƯỚNG DẪN] Chính sách đổi trả & bảo hành</summary>
                <p>Đổi trả trong <b>7 ngày</b> nếu:</p>
                <ul>
                  <li>Sai size, sai mẫu, sai màu.</li>
                  <li>Lỗi sản xuất: bung keo, lệch form, tróc da, đứt chỉ…</li>
                  <li>Giày chưa sử dụng, còn hộp và phụ kiện đầy đủ.</li>
                </ul>
              </details>

              <details>
                <summary>[TƯ VẤN] Cách chọn size giày chính xác nhất</summary>
                <ul>
                  <li>Đo chiều dài bàn chân, lấy số lớn hơn giữa hai chân.</li>
                  <li>Đối chiếu bảng size theo centimet.</li>
                  <li>Chân bè hoặc mu cao nên tăng 0.5–1 size.</li>
                </ul>
              </details>

              <details>
                <summary>[THANH TOÁN] Các phương thức thanh toán</summary>
                <ul>
                  <li><b>COD</b> – thanh toán khi nhận hàng.</li>
                  <li><b>Chuyển khoản ngân hàng</b>.</li>
                  <li><b>Ví điện tử</b> (Momo, ZaloPay…).</li>
                  <li><b>Thẻ ATM / Visa / Mastercard</b>.</li>
                </ul>
                <p>Mọi thắc mắc: Hotline <b>1900 88 66 92</b>.</p>
              </details>
            </div>
          </div>
        </div>
      </div>
    </div>

    <footerWeb />
  </div>
</template>

<script setup>
import HeaderWeb from '../../Header-web.vue'
import footerWeb from '../../footer-web.vue'
</script>

<style scoped>
/* GENERAL */
.support-wrapper {
  font-family: Arial, sans-serif;
  background: #f8f8f8;
  padding: 120px 0 60px; /* chừa chỗ cho header fixed */
}

.container {
  max-width: 1180px;
  margin: 0 auto;
}

/* TITLE */
.title {
  text-align: center;
  font-size: 26px;
  font-weight: 700;
}

.subtitle {
  text-align: center;
  margin-top: 6px;
  color: #777;
  font-size: 15px;
}

/* CONTENT LAYOUT */
.content {
  margin-top: 30px;
  display: flex;
  gap: 24px;
}

/* LEFT */
.left-box {
  flex: 0 0 32%;
}

.info-block {
  background: #fff;
  padding: 18px;
  border-radius: 6px;
  border: 1px solid #e3e3e3;
  font-size: 14px;
}

.email {
  color: #0a73ff;
  margin-bottom: 8px;
}

.map {
  width: 100%;
  height: 250px;
  margin-top: 20px;
  border-radius: 6px;
  border: 1px solid #ddd;
}

/* RIGHT */
.right-box {
  flex: 1;
}

.note {
  background: #e8f2ff;
  border: 1px solid #c9dbff;
  padding: 15px;
  font-size: 14px;
  border-radius: 6px;
}

.form {
  margin-top: 20px;
  background: #fff;
  padding: 18px;
  border-radius: 6px;
  border: 1px solid #ddd;
}

.row {
  display: flex;
  gap: 16px;
}

.col {
  flex: 1;
}

label {
  font-size: 14px;
  display: block;
  margin-top: 10px;
}

input,
textarea {
  width: 100%;
  margin-top: 4px;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 14px;
}

/* ===== DROPDOWN ĐẸP ===== */
.select-wrapper {
  position: relative;
  margin-top: 4px;
}

.select-wrapper select {
  width: 100%;
  padding: 10px 40px 10px 12px; /* chừa chỗ cho icon mũi tên */
  border: 1px solid #ccc;
  border-radius: 6px;
  background: #fff;
  font-size: 14px;
  color: #333;
  cursor: pointer;
  appearance: none;        /* ẩn arrow mặc định */
  -webkit-appearance: none;
  -moz-appearance: none;
  transition: border-color 0.15s ease, box-shadow 0.15s ease,
    background-color 0.15s ease;
}

.select-wrapper::after {
  content: "▾";
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 12px;
  color: #666;
  pointer-events: none; /* tránh chặn click vào select */
}

.select-wrapper select:focus {
  outline: none;
  border-color: #0a8dff;
  box-shadow: 0 0 0 3px rgba(10, 141, 255, 0.15);
}

.select-wrapper select option {
  background: #fff;
  color: #333;
}

/* BUTTON */
.submit-btn {
  margin-top: 16px;
  background: #0a8dff;
  color: #fff;
  border: none;
  padding: 10px 18px;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
}

/* SECTION TITLE */
.section-title {
  margin-top: 26px;
  background: #fff;
  display: inline-block;
  padding: 8px 12px;
  border-radius: 4px;
  border: 1px solid #d0d0d0;
  font-weight: 700;
  font-size: 14px;
}

/* FAQ */
.faq {
  margin-top: 16px;
}

details {
  background: #fff;
  padding: 12px 14px;
  border-radius: 6px;
  border: 1px solid #ddd;
  margin-bottom: 10px;
  cursor: pointer;
}

summary {
  font-size: 14px;
  font-weight: 600;
}

details p,
details ul {
  font-size: 13px;
  margin-top: 8px;
}

details ul {
  padding-left: 18px;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .support-wrapper {
    padding: 110px 16px 50px;
  }
  .content {
    gap: 18px;
  }
}

@media (max-width: 768px) {
  .content {
    flex-direction: column;
  }

  .left-box,
  .right-box {
    flex: 1 1 100%;
  }

  .row {
    flex-direction: column;
  }

  .title {
    font-size: 22px;
  }

  .subtitle {
    font-size: 14px;
  }
}

@media (max-width: 480px) {
  .title {
    font-size: 20px;
  }

  .subtitle {
    font-size: 13px;
  }

  .submit-btn {
    width: 100%;
    text-align: center;
  }

  .map {
    height: 220px;
  }
}
</style>
