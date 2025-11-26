<template>
  <HeaderWeb  />
  <div class="bg-light">

    <!-- GIỎ HÀNG -->
    <div class="container py-5">

      <h3 class="fw-bold mb-4">GIỎ HÀNG</h3>

      <div class="row g-4">

        <!-- LEFT CART LIST -->
        <div class="col-lg-8">
          <div class="bg-white p-3 rounded shadow-sm">

            <!-- HEADER -->
            <div class="row fw-semibold border-bottom pb-2 small text-uppercase text-center">

              <div class="col-2">Hình ảnh</div>
              <div class="col-4">Sản phẩm</div>
              <div class="col-2">Số lượng</div>
              <div class="col-2">Thành tiền</div>
              <div class="col-2">Đơn giá</div>

            </div>


            <!-- PRODUCT 1 -->
            <div class="row align-items-center py-3 border-bottom text-center">

              <!-- HÌNH ẢNH -->
              <div class="col-2 d-flex justify-content-center">
                <img src="https://via.placeholder.com/100" class="img-fluid rounded" />
              </div>

              <!-- SẢN PHẨM (CỘT 2) -->
              <div class="col-4 text-start">
                <div class="fw-semibold">Nike Air Max 97</div>
                <div class="text-secondary small">(Màu đỏ + size 42)</div>
              </div>

              <!-- SỐ LƯỢNG -->
              <div class="col-2">
                <input type="number" class="form-control text-center" value="1" />
              </div>

              <!-- THÀNH TIỀN -->
              <div class="col-2 fw-bold">3,700,000</div>

              <!-- ĐƠN GIÁ (CỘT CUỐI) -->
              <div class="col-2">3,700,000</div>

            </div>


            <!-- PRODUCT 2 -->
            <div class="row align-items-center py-3 border-bottom text-center">

              <div class="col-2 d-flex justify-content-center">
                <img src="https://via.placeholder.com/100" class="img-fluid rounded" />
              </div>

              <div class="col-4 text-start">
                <div class="fw-semibold">Adidas Ultraboost 21</div>
                <div class="text-secondary small">(Màu xám + size 40)</div>
              </div>

              <div class="col-2">
                <input type="number" class="form-control text-center" value="1" />
              </div>

              <div class="col-2 fw-bold">3,999,000</div>

              <div class="col-2">3,999,000</div>

            </div>


          </div>
        </div>

        <!-- RIGHT SUMMARY -->
        <div class="col-lg-4">
          <div class="bg-white p-3 rounded shadow-sm">
            <h5 class="fw-semibold mb-3">Tóm Tắt Đơn Hàng</h5>

            <label class="small mb-1">Mã giảm giá:</label>
            <div class="input-group mb-3">
              <input type="text" class="form-control" placeholder="Nhập mã" />
              <button class="btn btn-dark">Áp dụng</button>
            </div>

            <div class="d-flex justify-content-between small py-1">
              <span>Tạm tính sản phẩm:</span>
              <span>7,699,000 VNĐ</span>
            </div>

            <div class="d-flex justify-content-between small py-1">
              <span>Phí vận chuyển:</span>
              <span>30,000 VNĐ</span>
            </div>

            <div class="d-flex justify-content-between small text-danger py-1">
              <span>Giảm giá:</span>
              <span>-10,000 VNĐ</span>
            </div>

            <hr />

            <div class="d-flex justify-content-between fw-bold">
              <span>Thanh toán:</span>
              <span class="text-danger">7,720,000 VNĐ</span>
            </div>

            <button class="btn btn-dark w-100 mt-3">Thanh toán</button>
          </div>
        </div>

      </div>

      <!-- CHECKOUT FORM -->
      <h3 class="fw-bold mt-5 mb-3">HOÀN TẤT ĐƠN HÀNG</h3>

      <div class="bg-white p-4 rounded shadow-sm">

        <!-- SHIPPING -->
        <h5 class="fw-semibold mb-3">1. Thông Tin Giao Hàng</h5>

        <div class="row g-3">
          <div class="col-md-6">
            <input type="text" class="form-control" placeholder="Họ và tên" />
          </div>
          <div class="col-md-6">
            <input type="text" class="form-control" placeholder="Số điện thoại" />
          </div>

          <div class="col-md-6">
            <input type="email" class="form-control" placeholder="Email" />
          </div>

          <div class="col-md-6">
            <input type="text" class="form-control" placeholder="Tỉnh / Thành phố" />
          </div>
        </div>

        <input type="text" class="form-control mt-3" placeholder="Địa chỉ cụ thể" />
        <textarea class="form-control mt-3" rows="4" placeholder="Ghi chú (nếu có)"></textarea>

        <!-- PAYMENT -->
        <h5 class="fw-semibold mt-4 mb-3">2. Phương Thức Thanh Toán</h5>

        <div class="list-group">
          <label class="list-group-item d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="pay" />
            Thanh toán khi nhận hàng (COD)
          </label>

          <label class="list-group-item d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="pay" />
            Chuyển khoản ngân hàng
          </label>

          <label class="list-group-item d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="pay" />
            Ví điện tử (Momo/ZaloPay)
          </label>
        </div>

        <button class="btn btn-dark px-4 mt-4">Đặt hàng</button>
      </div>

    </div>

  </div>
  <footerWeb />
</template>

<script setup>
import HeaderWeb from '../../Header-web.vue';
import footerWeb from '../../footer-web.vue';
</script>

<style scoped>
.bg-light{
  margin-top: 50px;
}
</style>
