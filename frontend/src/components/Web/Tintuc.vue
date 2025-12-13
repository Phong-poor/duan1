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
          <div class="featured-wrapper">
            <transition name="fade-slide" mode="out-in">
              <article 
                v-if="hotPosts[currentSlide]" 
                :key="hotPosts[currentSlide].id_baiviet"
                class="featured-post"
              >
                <img
                  :src="'http://localhost/duan1/backend/uploads/Baiviet/' + hotPosts[currentSlide].thumbnail"
                />
                <div class="featured-post-info">
                  <span class="category">🔥 Tin hot</span>
                  <h2>{{ hotPosts[currentSlide].title }}</h2>
                  <p v-html="hotPosts[currentSlide].seo_description"></p>
                  <router-link
                    class="read-more"
                    :to="`/tintuc/${hotPosts[currentSlide].slug_danhmuc}/${hotPosts[currentSlide].slug}`"
                  >
                    Đọc thêm →
                  </router-link>
                  <p class="post-meta">
                    <i class="far fa-calendar-alt"></i>
                    {{ hotPosts[currentSlide].created_at }}
                  </p>
                </div>
              </article>
            </transition>

            <!-- NÚT TRÁI -->
            <button class="slide-btn prev" @click="prevSlide">
              <i class="fas fa-chevron-left"></i>
            </button>

            <!-- NÚT PHẢI -->
            <button class="slide-btn next" @click="nextSlide">
              <i class="fas fa-chevron-right"></i>
            </button>

          </div>
          <!-- ARTICLES GRID -->
          <div class="post-grid">
            <router-link
              v-for="it in filteredNews"
              :key="it.id_baiviet"
              :to="`/tintuc/${it.slug_danhmuc}/${it.slug}`"
              class="post-card"
            >
              <article
                class="post-card"
                v-for="it in filteredNews"
                :key="it.id_baiviet"
              >
                <img
                  :src="'http://localhost/duan1/backend/uploads/Baiviet/' + it.thumbnail"
                />
                <div class="post-card-info">
                  <span class="category">Tin tức</span>
                  <h3>{{ it.title }}</h3>
                  <p class="excerpt" v-html="it.seo_description"></p>
                  <p class="post-meta">
                    <i class="far fa-calendar-alt"></i> {{ it.created_at }}
                  </p>
                </div>
              </article>
            </router-link>
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
            <h4><i class="fas fa-tags"></i> Danh mục</h4>
            <ul>
              <li
                v-for="c in categories"
                :key="c.id_danhmuc"
              >
                <a
                  href="#"
                  @click.prevent="selectCategory(c.slug)"
                  :class="{ active: selectedCategory === c.slug }"
                >
                  <i class="fas fa-angle-right"></i>
                  {{ c.tenDM }} ({{ c.total }})
                </a>
              </li>
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
  </div>
  <footerWeb />
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import HeaderWeb from '../../Header-web.vue'
import footerWeb from '../../footer-web.vue'

/* ================= STATE ================= */
const news = ref([])
const recent = ref([])
const search = ref('')
const page = ref(1)
const perPage = 4
const currentSlide = ref(0)

let slideTimer = null

/* ================= LOAD DATA ================= */
onMounted(async () => {
  const res = await fetch('http://localhost/duan1/backend/api/Web/BaiViet.php')
  const json = await res.json()

  // 🔥 API trả { hot: [], normal: [] }
  const hot = Array.isArray(json.hot) ? json.hot : []
  const normal = Array.isArray(json.normal) ? json.normal : []

  // gộp lại cho pagination + search
  news.value = [...hot, ...normal]

  // sidebar recent
  recent.value = normal.slice(0, 5).map(p => ({
    title: p.title
  }))

  slideTimer = setInterval(() => {
    if (hotPosts.value.length === 0) return
    currentSlide.value =
      (currentSlide.value + 1) % hotPosts.value.length
  }, 5000)
})


onUnmounted(() => {
  if (slideTimer) clearInterval(slideTimer)
})

/* ================= COMPUTED ================= */
const hotPosts = computed(() =>
  news.value.filter(p => Number(p.hot) === 1)
)

const normalPosts = computed(() =>
  news.value.filter(p => Number(p.hot) === 0)
)

const filtered = computed(() => {
  let list = normalPosts.value

  // lọc theo danh mục
  if (selectedCategory.value) {
    list = list.filter(
      p => p.slug_danhmuc === selectedCategory.value
    )
  }

  // lọc theo từ khóa
  if (search.value) {
    const s = search.value.toLowerCase()
    list = list.filter(p =>
      p.title.toLowerCase().includes(s)
    )
  }

  return list
})

function resetSlideTimer() {
  if (slideTimer) clearInterval(slideTimer)

  slideTimer = setInterval(() => {
    if (hotPosts.value.length === 0) return
    currentSlide.value =
      (currentSlide.value + 1) % hotPosts.value.length
  }, 5000)
}
function prevSlide() {
  if (hotPosts.value.length === 0) return

  currentSlide.value =
    (currentSlide.value - 1 + hotPosts.value.length) % hotPosts.value.length

  resetSlideTimer() // ⭐ BONUS
}

function nextSlide() {
  if (hotPosts.value.length === 0) return

  currentSlide.value =
    (currentSlide.value + 1) % hotPosts.value.length

  resetSlideTimer() // ⭐ BONUS
}
const totalPages = computed(() =>
  Math.max(1, Math.ceil(filtered.value.length / perPage))
)

const filteredNews = computed(() => {
  const start = (page.value - 1) * perPage
  return filtered.value.slice(start, start + perPage)
})

/* ================= WATCH ================= */
watch(search, () => {
  page.value = 1
})

/* ================= METHODS ================= */
function onSearch() {
  page.value = 1
  selectedCategory.value = null
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
const categories = ref([])
const selectedCategory = ref(null)

const loadCategories = async () => {
  const res = await fetch(
    'http://localhost/duan1/backend/api/Web/DanhMucBaiViet.php'
  )
  categories.value = await res.json()
}

onMounted(async () => {
  // load bài viết (code cũ của bạn)
  const res = await fetch('http://localhost/duan1/backend/api/Web/BaiViet.php')
  const json = await res.json()

  const hot = Array.isArray(json.hot) ? json.hot : []
  const normal = Array.isArray(json.normal) ? json.normal : []

  news.value = [...hot, ...normal]

  recent.value = normal.slice(0, 5).map(p => ({ title: p.title }))

  loadCategories()

  slideTimer = setInterval(() => {
    if (hotPosts.value.length === 0) return
    currentSlide.value =
      (currentSlide.value + 1) % hotPosts.value.length
  }, 5000)
})
function selectCategory(slug) {
  selectedCategory.value = slug
  page.value = 1
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
  color: black;
  text-decoration: none;
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
/* ===== HOT SLIDER ANIMATION ===== */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.6s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateX(40px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateX(-40px);
}
/* ===== FEATURED SLIDER CONTROLS ===== */
.featured-wrapper {
  position: relative;
}

/* Nút trái phải */
.slide-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 42px;
  height: 42px;
  border-radius: 50%;
  border: none;
  background: rgba(0,0,0,0.45);
  color: #fff;
  cursor: pointer;
  z-index: 5;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.25s ease;
  opacity: 0;
}

/* Hover mới hiện */
.featured-wrapper:hover .slide-btn {
  opacity: 1;
}

.slide-btn:hover {
  background: rgba(0,0,0,0.7);
  transform: translateY(-50%) scale(1.08);
}

/* vị trí */
.slide-btn.prev {
  left: 14px;
}

.slide-btn.next {
  right: 14px;
}
.sidebar-widget ul li a.active {
  color: #007bff;
  font-weight: 600;
}

</style>
