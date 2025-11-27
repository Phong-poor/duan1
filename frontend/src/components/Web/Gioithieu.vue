<template>
  <HeaderWeb/>
  <div class="about-page bg-light">

    <main class="container py-5">
      <h1 class="text-center fw-bold mb-5 section-main-title">
        MIRAE: THỜI TRANG CỦA TƯƠNG LAI
      </h1>

      <!-- BLOCK 1 -->
      <section class="row align-items-center mb-5">
        <div class="col-md-6">
          <h2 class="section-title">Câu chuyện của MIRAE</h2>
          <p class="mt-3">
            <b>MIRAE</b> được hình thành từ khát vọng tạo ra những đôi giày không chỉ đẹp, bền,
            mà còn mang trong mình một linh hồn – một triết lý sống hướng đến tương lai.
          </p>
          <p>
            Triết lý <b>"Future Movement"</b> đưa sự đổi mới, công nghệ và sáng tạo vào từng chi tiết.
          </p>
          <p>
            MIRAE đã phát triển thành thương hiệu được yêu thích
            bởi cộng đồng trẻ đam mê thể thao và thời trang tương lai.
          </p>
        </div>

        <div class="col-md-6">
          <div class="img-box block1-img"></div>
        </div>
      </section>

      <!-- BLOCK 2 -->
      <section class="row align-items-center mb-5 flex-md-row-reverse">
        <div class="col-md-6">
          <h2 class="section-title">Công Nghệ Đột Phá</h2>
          <p class="mt-3">
            Đế giày <b>FusionFoam™</b> ứng dụng bọt khí phân tầng cho khả năng hấp thụ lực tối ưu.
          </p>
          <p>
            Chất liệu <b>AeroMesh</b> giúp thoáng khí tối đa nhưng vẫn giữ form ôm chân.
          </p>
          <p>
            Công nghệ MIRAE luôn được thử nghiệm kỹ càng trước khi sản xuất.
          </p>
        </div>

        <div class="col-md-6">
          <div class="img-box block2-img"></div>
        </div>
      </section>

      <hr class="my-5"/>

      <!-- TEAM -->
      <section class="text-center mb-5">
        <h2 class="section-title text-center">Đội Ngũ Sáng Tạo</h2>
        <p class="text-muted">
          Những con người đứng sau những sản phẩm mang dấu ấn tương lai của MIRAE.
        </p>

        <div class="row justify-content-center mt-4 g-4">

          <div class="col-md-3 team-card">
            <div class="team-img team1-img"></div>
            <h5 class="fw-bold mt-3">John Wick</h5>
            <p>Trưởng nhóm thiết kế cấp cao.</p>
          </div>

          <div class="col-md-3 team-card">
            <div class="team-img team2-img"></div>
            <h5 class="fw-bold mt-3">Mr. Beats</h5>
            <p>Giám đốc phát triển vật liệu.</p>
          </div>

          <div class="col-md-3 team-card">
            <div class="team-img team3-img"></div>
            <h5 class="fw-bold mt-3">Mark</h5>
            <p>Giám đốc sáng tạo.</p>
          </div>

        </div>
      </section>

      <hr class="my-5"/>

      <!-- BLOCK 3 -->
      <section class="row align-items-center mb-5">
        <div class="col-md-6">
          <h2 class="section-title">Cam Kết Bền Vững</h2>
          <p class="mt-3">
            MIRAE cam kết sử dụng vật liệu thân thiện môi trường như sợi tái chế và cao su tự nhiên.
          </p>
          <p>
            Hơn 70% sản phẩm đã ứng dụng các vật liệu bền vững.
          </p>
        </div>

        <div class="col-md-6">
          <div class="img-box block3-img"></div>
        </div>
      </section>

      <!-- BLOCK 4 -->
      <section class="row align-items-center mb-5 flex-md-row-reverse">
        <div class="col-md-6">
          <h2 class="section-title">Cộng Đồng & Văn Hóa MIRAE</h2>
          <p class="mt-3">
            MIRAE là cộng đồng yêu thích phong cách sống năng động, sáng tạo.
          </p>
          <p>
            Các sự kiện như “Future Walk” hay workshop thiết kế thu hút hàng nghìn thành viên hằng năm.
          </p>
        </div>

        <div class="col-md-6">
          <div class="img-box block4-img"></div>
        </div>
      </section>

      <!-- HIGHLIGHT -->
      <div class="highlight-box text-center p-4 mt-4">
        <h3 class="fw-bold">
          "Gia nhập cộng đồng MIRAE và bước vào tương lai cùng chúng tôi"
        </h3>
        <p class="mt-2">Khám phá bộ sưu tập mới nhất ngay hôm nay.</p>
        <a href="/SanPham" class="btn btn-dark px-4 py-2 mt-3">Khám Phá Ngay</a>
      </div>

    </main>
    <footerWeb/>
  </div>
</template>

<script setup>
import HeaderWeb from '../../Header-web.vue';
import footerWeb from '../../footer-web.vue';
</script>

<style scoped>
.about-page {
  margin-top: 50px;
}

/* SECTION TITLES */
.section-title {
  font-size: 1.8rem;
  font-weight: 700;
  border-left: 5px solid #ff6600;
  padding-left: 10px;
}
.section-main-title {
  font-size: 2.4rem;
  font-weight: 800;
}

/* IMAGE BLOCKS */
.img-box {
  width: 100%;
  height: 280px;
  background-size: cover;
  background-position: center;
  border-radius: 12px;
}

.block1-img {
  background-image: url('https://www.jordan1.vn/wp-content/uploads/2023/09/jordan-stadium-90-white-teal-fb2__5__e7e929ed24ec463fb9a3f80607fcee08.png');
}

.block2-img {
  background-image: url('https://cf.shopee.vn/file/9a13e9d23c33ce8e605b79e3b134b566');
}

.block3-img {
  background-image: url('https://cdn.storims.com/api/v2/image/resize?path=https://storage.googleapis.com/storims_cdn/storims/uploads/f494d6f689de4546907fc7814ab512b1.jpeg');
}

.block4-img {
  background-image: url('https://bizweb.dktcdn.net/100/467/909/products/mot-so-hinh-anh-cua-nike-air-for-1.jpg?v=1722598855297');
}

/* TEAM SECTION */
.team-card {
  background: #fff;
  border-radius: 18px;
  padding: 25px;
  transition: 0.3s;
  text-align: center;
}
.team-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 22px rgba(0, 0, 0, 0.15);
}

.team-img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background-size: cover;
  background-position: center;
  margin: auto;
  border: 3px solid #ddd;
}

/* TEAM IMAGES */
.team1-img {
  background-image: url('https://cdn-img.thethao247.vn/upload/thanhtung/2019/10/17/adidas-football-lionel-messi-15-years-nemeziz.jpg');
}

.team2-img {
  background-image: url('https://kenh14cdn.com/2016/cristiano-ronaldo-cr7-football-player-real-madrid-jersey-king-nike-shoes-1478667893107.jpg');
}

/* GIÁM ĐỐC – ĐÃ CẬP NHẬT ẢNH */
.team3-img {
  background-image: url('https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=500&q=80');
}

/* HIGHLIGHT BOX */
.highlight-box {
  background: #fff4e6;
  border-left: 5px solid #ff6600;
  border-radius: 12px;
}
</style>
