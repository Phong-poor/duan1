<template>
  <footer class="footer">
    <div class="container">
      <div class="footer-container">
        
        <!-- COLUMN 1 -->
        <div class="footer-column">
          <h4>Giới thiệu về công ty</h4>
          <ul>
            <li><a href="#">Chính sách bảo mật</a></li>
            <li><a href="#">Quy chế hoạt động</a></li>
            <li><a href="#">Kiểm tra hóa đơn điện tử</a></li>
            <li><a href="#">Tra cứu thông tin bảo hành</a></li>
            <li><a href="#">Câu hỏi thường gặp mua hàng</a></li>
          </ul>

          <div class="certification-group" style="margin-top:20px;">
            <img src="./assets/bct.png" alt="Bo Cong Thuong">
          </div>
        </div>

        <!-- COLUMN 2 -->
        <div class="footer-column">
          <h4>Tin tuyển dụng</h4>
          <ul>
            <li><a href="#">Tin khuyến mãi</a></li>
            <li><a href="#">Hướng dẫn mua online</a></li>
            <li><a href="#">Hướng dẫn mua trả góp</a></li>
            <li><a href="#">Chính sách trả góp</a></li>
          </ul>

          <h4 style="margin-top:25px;">Chứng nhận:</h4>
          <div class="certification-group">
            <img src="./assets/IO.jpg" alt="Chứng nhận ISO">
          </div>
        </div>

        <!-- COLUMN 3 -->
        <div class="footer-column">
          <h4>Hệ thống cửa hàng</h4>
          <ul>
            <li><a href="#">Chính sách đổi trả</a></li>
            <li><a href="#">Hệ thống bảo hành</a></li>
            <li><a href="#">Giới thiệu máy đổi trả</a></li>
          </ul>
        </div>

        <!-- COLUMN 4 -->
        <div class="footer-column">
          <h4>Tư vấn mua hàng (Miễn phí)</h4>
          <div class="hotline-section">
            <span class="hotline">1800 6601</span>
            <span class="hotline-label">(Nhánh 1)</span>
          </div>

          <h4 style="margin-top:0;">Hỗ trợ kỹ thuật</h4>
          <div class="hotline-section">
            <span class="hotline">1800 6601</span>
            <span class="hotline-label">(Nhánh 2)</span>
            <span class="hotline-label" style="font-size:11px;margin-bottom:0;">
              Góp ý khiếu nại (8h00-22h00)
            </span>
            <span class="hotline">1800 6616</span>
          </div>

          <h4 style="margin-top:15px;">Hỗ trợ thanh toán:</h4>
          <div class="payment-group">
            <img src="./assets/visa.png" alt="Visa">
          </div>
        </div>

        <!-- COLUMN 5 -->
        <div class="footer-column">
          <h4>Website cùng FPT Retail:</h4>

          <div class="partner-section">
            <div class="partner-logo">
              F.Studio 
              <span class="partner-desc">Cửa hàng ủy quyền bởi Apple:</span>
            </div>
          </div>

          <div class="partner-section">
            <div class="partner-logo">
              F.Care 
              <span class="partner-desc">Trung tâm bảo hành ủy quyền của Apple:</span>
            </div>
          </div>

          <div class="partner-section">
            <img src="./assets/logone.png" alt="FPT Polytechnic" style="max-height:40px;margin-top:5px;">
          </div>
        </div>

      </div>
    </div>

    <div class="footer-bottom-copy">
      @ 2007 - 2023 Công Ty Cổ Phần Bán Lẻ Kỹ Thuật Số FPT / 
      Địa chỉ: 261-263 Khánh Hội, P5, Q4, TP.Hồ Chí Minh /
      GPDĐKKD số 0311609355 do Sở KHDT TP.HCM cấp ngày 08/03/2012. 
      GP số 47/GP-TTĐT do Sở TTTT TP.HCM cấp ngày 02/07/2018. 
      Điện thoại <a href="tel:02873023456">(028) 7302 3456</a>. 
      Email: <a href="mailto:fptshop@fpt.com.vn">fptshop@fpt.com.vn</a>. 
      Chịu trách nhiệm nội dung: Nguyễn Trịnh Nhật Linh.
    </div>
  </footer>
</template>

<script>
import logoImage from './assets/logone.png';
</script>

<style scoped>
/* --- FOOTER MAIN STYLING --- */
.footer {
  background-color: #262626;
  color: #b0b0b0;
  padding: 30px 0 0;
  font-family: 'Roboto', sans-serif;
  line-height: 1.6;
}

.footer-container {
  max-width: 1200px;
  width: 90%;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 15px;
  padding-bottom: 20px;
  border-bottom: 1px solid red;
}

/* COLUMN */
.footer-column {
  flex: 1 1 180px;
  padding: 0 10px;
  min-width: 150px;
}

.footer-column h4 {
  font-weight: 500;
  font-size: 14px;
  color: #fff;
  margin-bottom: 15px;
  text-transform: uppercase;
}

.footer-column ul {
  padding: 0;
  margin: 0;
  list-style: none;
}

.footer-column ul li {
  margin-bottom: 5px;
  font-size: 13px;
}

.footer-column ul li a {
  color: #b0b0b0;
  text-decoration: none;
  transition: color 0.2s;
}
.footer-column ul li a:hover {
  color: #fff;
}

/* HOTLINE */
.hotline { color:#ff0000; font-weight:700; font-size:15px; }
.hotline-label { font-size:12px; color:#b0b0b0; }

/* CERTIFICATIONS */
.certification-group {
  display:flex; align-items:center; gap:8px; margin-top:10px;
}
.certification-group img { max-height:35px; }

/* PAYMENT */
.payment-group img { max-height:25px; margin-right:5px; }

/* PARTNERS */
.partner-section { margin-bottom:15px; }
.partner-logo { font-size:15px; font-weight:700; color:#fff; margin-top:8px; }
.partner-desc { font-size:11px; color:#888; }

/* COPYRIGHT */
.footer-bottom-copy {
  background:#262626;
  color:#888;
  font-size:11px;
  padding:15px 0;
  text-align:center;
  width:90%;
  max-width:1200px;
  margin:0 auto;
}

.footer-bottom-copy a { color:#888; }
.footer-bottom-copy a:hover { color:#ff0000; }

/* Responsive */
@media (max-width:768px) {
  .footer-container {
    flex-direction: column;
    text-align: left;
  }
}
</style>
