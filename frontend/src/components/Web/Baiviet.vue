<template>
  <div class="blog-wrapper">
    <HeaderWeb />

    <main class="container blog-page">
      
      <!-- Banner -->
      <img class="blog-banner" :src="post.banner" alt="Banner bài viết" />

      <!-- Tiêu đề -->
      <h1 class="blog-title">{{ post.title }}</h1>
      <p class="blog-meta">
        Đăng bởi <strong>{{ post.author }}</strong> • {{ post.date }}
      </p>

      <!-- Nội dung bài viết -->
      <section class="blog-content" v-html="post.content"></section>

      <!-- Tags -->
      <div class="tags">
        <span v-for="tag in post.tags" :key="tag" class="tag">{{ tag }}</span>
      </div>

      <!-- Bài viết liên quan -->
      <section class="related-posts">
        <h2>Bài viết nổi bật</h2>

        <div class="post-list">
          <article v-for="item in related" :key="item.id" class="post-card">
            <img :src="item.image" />
            <h3>{{ item.title }}</h3>
            <p>{{ item.short }}</p>
            <button class="btn-read" @click="openPost(item)">Đọc tiếp</button>
          </article>
        </div>
      </section>
    </main>

    <FooterWeb />
  </div>
</template>

<script setup>
import HeaderWeb from "../../Header-web.vue";
import FooterWeb from "../../footer-web.vue";

// ================== BÀI VIẾT CHÍNH ==================
const post = {
  title: "Nike Air Max – Biểu tượng của sự thoải mái & phong cách",
  author: "Minh Phong",
  date: "12/02/2025",
  banner: "https://images.unsplash.com/photo-1542291026-7eec264c27ff",

  content: `
    <p>
      Trong thế giới sneaker, <strong>Nike Air Max</strong> luôn giữ vị thế biểu tượng nhờ thiết kế độc đáo
      và công nghệ túi khí Air mang tính cách mạng. Trải qua hơn 30 năm, dòng Air Max đã đổi mới liên tục
      và trở thành lựa chọn của hàng triệu người yêu thích thời trang.
    </p>

    <img src="https://images.unsplash.com/photo-1519744792095-2f2205e87b6f"
        style="width:100%; border-radius:12px; margin:25px 0;" />

    <h2>1️⃣ Lịch sử hình thành và phát triển</h2>
    <p>
      Nike Air Max ra mắt lần đầu vào năm 1987 với thiết kế của <strong>Tinker Hatfield</strong>.
      Đôi Air Max 1 đầu tiên gây sốt toàn cầu nhờ khoang khí lộ ra bên ngoài – điều chưa từng có trước đó.
    </p>

    <ul>
      <li>✔ 1990 – Air Max 90 ra mắt, trở thành biểu tượng retro.</li>
      <li>✔ 1997 – Air Max 97 với thiết kế "đầu đạn bạc".</li>
      <li>✔ 2018 – Air Max 270 có đệm khí cao nhất 32mm.</li>
    </ul>

    <img src="https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f"
        style="width:100%; border-radius:12px; margin:25px 0;" />

    <h2>2️⃣ Công nghệ Air – Điều tạo nên sự khác biệt</h2>
    <p>
      Công nghệ đệm khí Air được thiết kế để hấp thụ lực và tạo độ đàn hồi, mang lại cảm giác nhẹ nhàng.
    </p>

    <ul>
      <li>🔹 Air Max 97 – Full Air dài toàn đế</li>
      <li>🔹 Air Max 270 – Chiều cao túi khí lớn nhất</li>
      <li>🔹 Air VaporMax – Đế khí tách rời hoàn toàn</li>
    </ul>

    <!-- 🔥 ẢNH ĐỒNG HỒ ĐÃ THU NHỎ ĐẸP -->
    <div style="text-align:center; margin:30px 0;">
      <img 
        src="https://images.unsplash.com/photo-1542044801-6e9c19bfda31"
        style="
          width:50%;
          max-width:380px;
          border-radius:14px;
          box-shadow:0 4px 15px rgba(0,0,0,0.15);
          display:block;
          margin:0 auto;
        "
      />
      <p style="font-size:14px; color:#666; margin-top:10px;">Một góc ảnh lifestyle đầy cảm hứng</p>
    </div>

    <h2>3️⃣ Thiết kế thời trang – Mạnh mẽ & hiện đại</h2>
    <p>
      Air Max được đánh giá cao nhờ form khỏe, đường nét sắc sảo, phù hợp xu hướng streetwear.
      Đặc biệt, phối màu cực kỳ đa dạng giúp Air Max dễ dàng kết hợp quần áo.
    </p>

    <blockquote>
      "Air Max không chỉ là một đôi giày – nó là tuyên ngôn phong cách."
    </blockquote>

    <h2>4️⃣ Hình ảnh thực tế</h2>

    <img src="https://images.unsplash.com/photo-1516826957135-700dedea698c"
        style="width:100%; border-radius:12px; margin:25px 0;" />

    <img src="https://images.unsplash.com/photo-1528701800489-20be9f4a5af0"
        style="width:100%; border-radius:12px; margin:25px 0;" />

    <img src="https://images.unsplash.com/photo-1503342452485-86b7f54527dd"
        style="width:100%; border-radius:12px; margin:25px 0;" />

    <img src="https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1"
        style="width:100%; border-radius:12px; margin:25px 0;" />

    <h2>5️⃣ Vì sao bạn nên sở hữu một đôi Air Max?</h2>

    <ul>
      <li>✔ Đi cực êm – đệm khí cực nhẹ</li>
      <li>✔ Bền – chất liệu cao cấp</li>
      <li>✔ Đẹp – thiết kế thời thượng</li>
      <li>✔ Dễ phối đồ – hợp mọi phong cách</li>
    </ul>

    <h2>📝 Kết luận</h2>
    <p>
      Nike Air Max không chỉ là một đôi giày – nó là câu chuyện của sự đổi mới,
      phong cách và trải nghiệm tuyệt vời. Một đôi giày mà bất kỳ sneakerhead nào
      cũng nên sở hữu ít nhất một lần trong đời.
    </p>

    <p><strong>— Hãy thử Air Max hôm nay và cảm nhận sự khác biệt!</strong></p>
  `,

  tags: ["Nike", "Air Max", "Sneaker", "Streetwear"],
};

// ================== BÀI VIẾT LIÊN QUAN ==================
const related = [
  {
    id: 1,
    title: "TOP 5 đôi sneaker đáng mua nhất 2025",
    image: "https://images.unsplash.com/photo-1528701800489-20be9f4a5af0",
    short: "Những đôi giày đang thống trị năm 2025...",
  },
  {
    id: 2,
    title: "Lịch sử dòng giày Nike Air Max",
    image: "https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f",
    short: "Hành trình phát triển từ Air Max 1 đến Air Max 720...",
  },
  {
    id: 3,
    title: "Cách phối đồ với sneaker trắng",
    image: "https://images.unsplash.com/photo-1519744693578-465d56ca2685",
    short: "Hướng dẫn phối đồ chuẩn trend với sneaker trắng...",
  },
];

// ================== CLICK BÀI VIẾT ==================
const openPost = (item) => {
  alert("Bạn có thể điều hướng sang bài viết: " + item.title);
};
</script>

<style scoped>
.blog-wrapper {
  background: #f5f5f5;
  padding-top: 100px;
  font-family: "Roboto", sans-serif;
}

.blog-page {
  padding-bottom: 50px;
}

.blog-banner {
  width: 100%;
  height: 350px;
  object-fit: cover;
  border-radius: 12px;
}

.blog-title {
  font-size: 34px;
  font-weight: 700;
  margin: 20px 0 10px;
}

.blog-meta {
  color: #777;
  font-size: 14px;
  margin-bottom: 20px;
}

.blog-content {
  background: white;
  padding: 25px;
  border-radius: 10px;
  line-height: 1.8;
  font-size: 16px;
  margin-bottom: 20px;
}

.blog-content h2 {
  margin: 18px 0 10px;
  font-size: 22px;
}

blockquote {
  padding: 15px;
  border-left: 4px solid #007bff;
  background: #eef5ff;
  margin: 15px 0;
  font-style: italic;
}

.tags {
  margin-bottom: 30px;
}

.tag {
  background: #007bff;
  padding: 6px 12px;
  border-radius: 20px;
  color: white;
  margin-right: 8px;
  font-size: 13px;
}

.related-posts h2 {
  font-size: 26px;
  margin-bottom: 15px;
}

.post-list {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

.post-card {
  width: 30%;
  background: white;
  border-radius: 10px;
  padding: 15px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  text-align: center;
}

.post-card img {
  width: 100%;
  height: 170px;
  object-fit: cover;
  border-radius: 10px;
}

.post-card h3 {
  font-size: 17px;
  margin: 10px 0;
  font-weight: 600;
}

.post-card p {
  font-size: 14px;
  color: #444;
  height: 45px;
  overflow: hidden;
}

.btn-read {
  margin-top: 10px;
  background: #007bff;
  color: white;
  padding: 8px 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

@media (max-width: 768px) {
  .post-card {
    width: 100%;
  }
}
</style>
