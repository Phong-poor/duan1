<template>
  <div class="news-page">
    <HeaderWeb />

    <main class="container blog-page">
      <h1 class="section-title">
        <i class="fas fa-newspaper"></i>
        TIN TỨC & CÂU CHUYỆN SNEAKER
      </h1>

      <div class="blog-content">

        <!-- LEFT CONTENT -->
        <div class="main-posts">

          <!-- FEATURED POST -->
          <article class="featured-post">
            <img
              src="../../assets/banner-slide-3.jpg"alt="Jordan 1 Hero"
            />
            <div class="featured-post-info">
              <span class="category">Jordan</span>
              <h2>Lịch Sử Của Jordan 1: Tại Sao Nó Vẫn Là Huyền Thoại?</h2>
              <p>
                Một góc nhìn khác, mở ra thế giới rộng lớn về lịch sử và ý nghĩa
                đằng sau đôi giày mang tính biểu tượng...
              </p>
              <a class="read-more">Đọc thêm →</a>
              <p class="post-meta">
                <i class="far fa-calendar-alt"></i> 03/11/2023 |
                <i class="far fa-user"></i> Fashion / Sneaker |
                <i class="far fa-comment"></i> 5 bình luận
              </p>
            </div>
          </article>

          <!-- ARTICLES GRID -->
          <div class="post-grid">
            <article
              class="post-card"
              v-for="(it, i) in filteredNews"
              :key="i"
            >

              <img
              src="../../assets/banner-slide-2.jpg"alt="Jordan 1 Hero"
            />

              <div class="post-card-info">
                <span class="category">Tin tức</span>
                <h3>{{ it.title }}</h3>
                <p class="excerpt">{{ it.desc }}</p>
                <p class="post-meta">
                  <i class="far fa-calendar-alt"></i> {{ it.date }}
                </p>
              </div>
            </article>
          </div>

          <!-- PAGINATION -->
          <div class="pagination">
            <button :disabled="page === 1" @click="prevPage">
              <i class="fas fa-chevron-left"></i>
            </button>

            <button
              v-for="p in totalPages"
              :key="p"
              :class="{ active: p === page }"
              @click="goPage(p)"
            >
              {{ p }}
            </button>

            <button :disabled="page === totalPages" @click="nextPage">
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>

        </div>

        <!-- SIDEBAR -->
        <div class="sidebar">

          <!-- SEARCH -->
          <div class="sidebar-widget">
            <h4><i class="fas fa-search"></i> Tìm kiếm bài viết</h4>
            <input v-model="search" type="text" placeholder="Nhập từ khóa..." />
            <button @click="onSearch">Tìm kiếm</button>
          </div>

          <!-- CATEGORIES -->
          <div class="sidebar-widget">
            <h4><i class="fas fa-tags"></i> Danh mục phổ biến</h4>
            <ul>
              <li><a href="#"><i class="fas fa-angle-right"></i> Tin tức phát hành</a></li>
              <li><a href="#"><i class="fas fa-angle-right"></i> Đánh giá sản phẩm</a></li>
              <li><a href="#"><i class="fas fa-angle-right"></i> Hướng dẫn phối đồ</a></li>
              <li><a href="#"><i class="fas fa-angle-right"></i> Lịch sử thương hiệu</a></li>
            </ul>
          </div>

          <!-- RECENT -->
          <div class="sidebar-widget">
            <h4><i class="fas fa-bolt"></i> Bài viết gần đây</h4>
            <ul>
              <li v-for="(r, idx) in recent" :key="idx">
                <a href="#">{{ r.title }}</a>
              </li>
            </ul>
          </div>

          <!-- AD -->
          <div class="sidebar-widget ad">
            <img src="https://i.imgur.com/3ZQ3Z9K.jpg" alt="Banner quảng cáo" />
          </div>

        </div>
      </div>
    </main>

    <footerWeb />
  </div>
</template>

<script setup>
import HeaderWeb from '../../Header-web.vue'
import footerWeb from '../../footer-web.vue'

import { ref, computed } from 'vue'
import bannerSlide1 from "../../assets/banner-slide-2.jpg";



const search = ref('')
const page = ref(1)
const perPage = 4

const news = ref([
  { img: 'https://pos.nvncdn.com/be5dfe-25943/art/artCT/20210131_wrxwc5KJjvZeWXlUfn4vUVkI.jpg', title: 'Nike Review', desc: 'Air Max Day 2026: Những Phiên Bản Đáng Chờ Đợi', date: '07/11/2023' },
  { img: 'https://pos.nvncdn.com/3c8244-211061/pc/20241111_8mYgtbqm.gif?v=1731308362', title: 'Jordan Story', desc: 'Câu Chuyện Đằng Sau Air Jordan', date: '08/11/2023' },
  { img: 'https://images.unsplash.com/photo-1528701800489-20beebc1c1d5?q=80&w=1200&auto=format&fit=crop', title: 'Adidas New Drop', desc: 'UltraBoost 24 Chính Thức Ra Mắt', date: '05/11/2023' },
  { img: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=1200&auto=format&fit=crop', title: 'Puma Trend', desc: 'BST Puma 2025 Cực Cháy', date: '09/11/2023' },
  { img: 'https://images.unsplash.com/photo-1606813903189-2d4c7d2b4f27?q=80&w=1200&auto=format&fit=crop', title: 'Retro Drop', desc: 'Phong cách retro quay trở lại', date: '12/10/2023' },
  { img: 'https://images.unsplash.com/photo-1512412046878-87e2f1b7f4f6?q=80&w=1200&auto=format&fit=crop', title: 'Sneaker Care', desc: 'Cách vệ sinh giày trắng đúng cách', date: '20/10/2023' },
  { img: 'https://images.unsplash.com/photo-1611851250534-43cc9893a5b6?q=80&w=1200&auto=format&fit=crop', title: 'Limited Release', desc: 'Những phiên bản giới hạn 2024', date: '02/09/2023' },
  { img: 'https://images.unsplash.com/photo-1519744792095-2f2205e87b6f?q=80&w=1200&auto=format&fit=crop', title: 'Street Style', desc: 'Phối đồ sneaker cho mùa hè', date: '15/08/2023' }
])

const recent = [
  { img: 'https://i.imgur.com/qUVq3uL.jpg', title: '1000+ mẫu giày hot', cat: 'Tin tức / Sneaker' },
  { img: 'https://i.imgur.com/6X2bH6s.jpg', title: 'Top phối đồ 2024', cat: 'Phong cách' },
  { img: 'https://i.imgur.com/3ZQ3Z9K.jpg', title: 'Bảo quản giày đúng cách', cat: 'Tips' }
]

const filtered = computed(() => {
  if (!search.value) return news.value
  const s = search.value.toLowerCase()
  return news.value.filter(n =>
    n.title.toLowerCase().includes(s) ||
    n.desc.toLowerCase().includes(s)
  )
})

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filtered.value.length / perPage))
)

const filteredNews = computed(() => {
  const start = (page.value - 1) * perPage
  return filtered.value.slice(start, start + perPage)
})

function onSearch() {
  page.value = 1
}
function prevPage() {
  if (page.value > 1) page.value--
}
function nextPage() {
  if (page.value < totalPages.value) page.value++
}
function goPage(p) {
  page.value = p
}
</script>

<style scoped>
/* Shared layout */
.blog-page {
padding-top: 100px;
}

.section-title {
  font-family: 'Montserrat', sans-serif;
  text-align: center;
  font-size: 40px;
  margin-bottom: 35px;
}

.blog-content {
  display: flex;
  gap: 30px;
  height: calc(100vh - 150px);
  overflow: hidden;
}

/* Independent scroll columns */
.main-posts,
.sidebar {
  height: 100%;
  overflow-y: auto;
  padding-right: 10px;
}

/* Scrollbar */
.main-posts::-webkit-scrollbar,
.sidebar::-webkit-scrollbar {
  width: 7px;
}
.main-posts::-webkit-scrollbar-track,
.sidebar::-webkit-scrollbar-track {
  background: transparent;
}
.main-posts::-webkit-scrollbar-thumb,
.sidebar::-webkit-scrollbar-thumb {
  background: #cfcfcf;
  border-radius: 10px;
}
.main-posts:hover::-webkit-scrollbar-thumb,
.sidebar:hover::-webkit-scrollbar-thumb {
  background: #b3b3b3;
}

/* Sidebar styling */
.sidebar-widget {
  background: #ffffff;
  padding: 20px 22px;
  border-radius: 14px;
  box-shadow: 0 4px 14px rgba(0,0,0,0.06);
  margin-bottom: 25px;
  border: 1px solid #f1f1f1;
}

.sidebar-widget h4 {
  font-size: 17px;
  font-weight: 700;
  color: #222;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.sidebar-widget h4 i {
  color: #007bff;
}

.sidebar-widget input {
  width: 100%;
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #ccc;
  margin-bottom: 10px;
}

.sidebar-widget button {
  width: 100%;
  padding: 10px;
  background: #007bff;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
}

.sidebar-widget button:hover {
  background: #005fcc;
}

.sidebar-widget ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar-widget ul li {
  margin-bottom: 10px;
}

.sidebar-widget ul li a {
  text-decoration: none;
  color: #333;
  font-size: 15px;
  font-weight: 500;
  display: flex;
  gap: 6px;
}

.sidebar-widget ul li a:hover {
  color: #007bff;
}

/* Ad */
.sidebar-widget.ad {
  padding: 0;
  box-shadow: none;
  border: none;
}
.sidebar-widget.ad img {
  width: 100%;
  border-radius: 12px;
}

/* Featured Post */
.featured-post {
  display: flex;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  margin-bottom: 35px;
  overflow: hidden;
}

.featured-post img {
  width: 50%;
  height: 380px;
  object-fit: cover;
}

.featured-post-info {
  width: 50%;
  padding: 28px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.featured-post-info h2 {
  font-size: 28px;
  margin: 10px 0;
  font-family: 'Montserrat';
}

/* Posts grid */
.post-grid {
  display: grid;
  grid-template-columns: repeat(2,1fr);
  gap: 26px;
}

.post-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  overflow: hidden;
}

.post-card img {
  width: 100%;
  height: 220px;
  object-fit: cover;
}

.post-card-info {
  padding: 18px;
}

.post-card-info h3 {
  font-size: 20px;
  font-weight: 600;
  margin: 8px 0;
  font-family: 'Montserrat';
}

/* Pagination small professional */
.pagination {
  display: flex;
  gap: 8px;
  margin: 28px 0;
  justify-content: center;
}

.pagination button {
  width: 32px;
  height: 32px;
  padding: 0;
  border-radius: 8px;
  border: 1px solid #d0d0d0;
  background: #fff;
  cursor: pointer;
  font-size: 14px;
  transition: 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.pagination button:hover:not(.active):not(:disabled) {
  background: #f5f5f5;
}

.pagination .active {
  background: #007bff;
  color: #fff;
  border-color: #007bff;
}
</style>
