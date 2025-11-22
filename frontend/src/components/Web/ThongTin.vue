<template>
  <HeaderWeb/> 
  <div class="account-page">
    <main class="account-container">
      <div class="profile-container">
        <aside class="profile-sidebar">
          <div class="profile-info">
            <h3>Xin Chào, Ngọc Anh!</h3>
            <p class="profile-email">ngocanh@example.com</p>
          </div>
          <div class="profile-menu">
            <a href="#" class="active">Thông tin chung</a>
            <a href="#">Quản lý đơn hàng</a>
            <a href="#">Sản phẩm yêu thích</a>
            <a href="#" class="logout-link">Đăng Xuất</a>
          </div>
        </aside>

        <section class="profile-content">
          <h2 class="section-title-profile">Thông Tin Cá Nhân</h2>

          <div class="card">
            <h4>Chi Tiết Tài Khoản</h4>
            <div class="info-row">
              <span>Họ và Tên:</span>
              <span>Trần Ngọc Anh</span>
            </div>
            <div class="info-row">
              <span>Email:</span>
              <span>ngocanh@example.com</span>
            </div>
            <div class="info-row">
              <span>Số Điện Thoại:</span>
              <span>0987 654 321</span>
            </div>
            <div class="info-row">
              <span>Ngày Sinh:</span>
              <span>25/10/1999</span>
            </div>
            <a href="#" class="edit-btn">Chỉnh sửa thông tin</a>
          </div>

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
                    <a href="#" class="action-btn-custom cancel-btn">Hủy Đơn</a>
                  </td>
                </tr>
                <tr>
                  <td>#KH10240</td>
                  <td>15/10/2025</td>
                  <td>3,500,000 VNĐ</td>
                  <td><span class="status-paid">Thành công</span></td>
                  <td class="action-cell">
                    <router-link to="/ChiTietDonHang" class="action-link">Xem</router-link>
                    <a href="#" class="action-btn-custom cancel-btn">Trả hàng</a>
                    
                  </td>
                </tr>
                <tr>
                  <td>#KH10221</td>
                  <td>28/09/2025</td>
                  <td>7,200,000 VNĐ</td>
                  <td><span class="status-paid">Thành công</span></td>
                  <td class="action-cell">
                    <router-link to="/ChiTietDonHang" class="action-link">Xem</router-link>
                    <a href="#" class="action-btn-custom cancel-btn">Trả hàng</a>
                  </td>
                </tr>
                <tr>
                  <td>#KH10200</td>
                  <td>10/09/2025</td>
                  <td>2,990,000 VNĐ</td>
                  <td class="status-cancelled">Đã hủy</td>
                  <td class="action-cell">
                    <router-link to="/ChiTietDonHang" class="action-link">Xem</router-link>
                    <a href="#" class="action-btn-custom reorder-btn">Mua lại</a>
                  </td>
                </tr>
              </tbody>
            </table>
            <a href="#" class="edit-btn primary">Xem tất cả đơn hàng</a>
          </div>
        </section>
      </div>
    </main>
  </div>
  <footerWeb/>
</template>
<script setup>
import HeaderWeb from '../../Header-web.vue';
import footerWeb from '../../footer-web.vue';
</script>
<style scoped>
.account-page {
  font-family: 'Roboto', sans-serif;
  background-color: #f7f7f7;
  color: #1a1a1a;
  min-height: 100vh;
  padding: 120px 0 60px; /* chừa khoảng header global */
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

.profile-container {
  display: flex;
  gap: 30px;
  padding: 40px 0;
}

.profile-sidebar {
  width: 280px;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  padding: 20px;
  align-self: flex-start;
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
  transition: background-color 0.2s, color 0.2s;
  font-weight: 500;
}

.profile-menu a:hover,
.profile-menu a.active {
  background-color: #e0f0ff;
  color: #007bff;
}

.logout-link {
  color: #007bff;
  font-weight: 700;
}

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
  margin: 0 0 20px;
}

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

.edit-btn {
  background-color: #555;
  color: white;
  padding: 8px 15px;
  border-radius: 4px;
  text-decoration: none;
  font-size: 14px;
  display: inline-block;
  margin-top: 15px;
  transition: background-color 0.2s;
}

.edit-btn:hover {
  background-color: #333;
}

.edit-btn.primary {
  background-color: #007bff;
}

.order-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
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
  text-decoration: none;
  margin-left: 5px;
  display: inline-block;
  transition: opacity 0.2s;
}

.cancel-btn {
  background-color: #dc3545;
  color: white;
}

.cancel-btn:hover,
.reorder-btn:hover {
  opacity: 0.8;
}

.reorder-btn {
  background-color: #007bff;
  color: white;
}

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