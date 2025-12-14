<template>
  <HeaderWeb />

  <div class="post-detail-page">
    <!-- CHỈ RENDER KHI CÓ DATA -->
    <main v-if="post" class="container post-detail">

      <!-- ===== BREADCRUMB ===== -->
      <nav class="breadcrumb">
        <router-link to="/">Trang chủ</router-link>
        <span>/</span>
        <router-link :to="`/tintuc/${post.slug_danhmuc}`">
          {{ post.tenDM }}
        </router-link>
        <span>/</span>
        <span>{{ post.title }}</span>
      </nav>

      <!-- ===== TITLE ===== -->
      <h1 class="post-title">{{ post.title }}</h1>

      <!-- ===== META ===== -->
      <div class="post-meta">
        <span>
          <i class="far fa-calendar-alt"></i>
          {{ post.created_at }}
        </span>
        <span>
          <i class="fas fa-folder"></i>
          {{ post.tenDM }}
        </span>
      </div>

      <!-- ===== THUMBNAIL ===== -->
      <img
        v-if="post.thumbnail"
        class="post-thumbnail"
        :src="`https://miraeshoes.shop/backend/uploads/Baiviet/${post.thumbnail}`"
        :alt="post.title"
      />

      <!-- ===== CONTENT ===== -->
      <article class="post-content" v-html="post.content"></article>

      <!-- ===== TAGS ===== -->
      <div v-if="post.tags?.length" class="post-tags">
        <span v-for="t in post.tags" :key="t.id_tag">
          #{{ t.tag }}
        </span>
      </div>

    </main>

    <!-- LOADING -->
    <div v-else class="loading">
      ⏳ Đang tải bài viết...
    </div>
  </div>

  <footerWeb />
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'

import HeaderWeb from '../../Header-web.vue'
import footerWeb from '../../footer-web.vue'

const route = useRoute()
const post = ref(null)

/* ================= SEO HELPERS ================= */
function setMeta(name, content) {
  let meta = document.querySelector(`meta[name="${name}"]`)
  if (!meta) {
    meta = document.createElement('meta')
    meta.setAttribute('name', name)
    document.head.appendChild(meta)
  }
  meta.setAttribute('content', content)
}

function setProperty(property, content) {
  let meta = document.querySelector(`meta[property="${property}"]`)
  if (!meta) {
    meta = document.createElement('meta')
    meta.setAttribute('property', property)
    document.head.appendChild(meta)
  }
  meta.setAttribute('content', content)
}

function setCanonical(url) {
  let link = document.querySelector('link[rel="canonical"]')
  if (!link) {
    link = document.createElement('link')
    link.setAttribute('rel', 'canonical')
    document.head.appendChild(link)
  }
  link.setAttribute('href', url)
}

function setJsonLD(data) {
  let script = document.querySelector('#jsonld-article')
  if (!script) {
    script = document.createElement('script')
    script.type = 'application/ld+json'
    script.id = 'jsonld-article'
    document.head.appendChild(script)
  }
  script.textContent = JSON.stringify(data)
}

/* ================= LOAD POST ================= */
async function loadPost() {
  const slugBaiViet = route.params.slugBaiViet
  if (!slugBaiViet) return

  const res = await fetch(
    `https://miraeshoes.shop/backend/api/Web/BaiVietChiTiet.php?slug=${slugBaiViet}`
  )
  const json = await res.json()
  post.value = json

  /* ===== SEO BASIC ===== */
  document.title = json.seo_title || json.title
  setMeta('description', json.seo_description || json.title)

  /* ===== CANONICAL ===== */
  const canonicalUrl =
  window.location.origin +
  `/tintuc/${json.slug_danhmuc}/${json.slug}`
  setCanonical(canonicalUrl)

  /* ===== OPEN GRAPH ===== */
  setProperty('og:type', 'article')
  setProperty('og:title', json.seo_title || json.title)
  setProperty('og:description', json.seo_description || json.title)
  setProperty('og:url', canonicalUrl)
  setProperty(
    'og:image',
    `https://miraeshoes.shop/backend/uploads/Baiviet/${json.thumbnail}`
  )

  /* ===== JSON-LD ARTICLE ===== */
  setJsonLD({
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": json.title,
    "description": json.seo_description,
    "image": `https://miraeshoes.shop/backend/uploads/Baiviet/${json.thumbnail}`,
    "author": {
      "@type": "Person",
      "name": json.creator || "MIRAE"
    },
    "datePublished": json.created_at,
    "dateModified": json.updated_at,
    "mainEntityOfPage": canonicalUrl
  })
}

onMounted(loadPost)

watch(
  () => route.params.slugBaiViet,
  () => loadPost()
)
</script>

<style scoped>
.post-detail {
  padding-top: 110px;
  max-width: 900px;
}

.loading {
  padding: 140px 0;
  text-align: center;
  font-size: 18px;
  color: #777;
}

.breadcrumb {
  font-size: 14px;
  margin-bottom: 15px;
  color: #777;
}
.breadcrumb a {
  color: #007bff;
  text-decoration: none;
}

.post-title {
  font-size: 34px;
  font-weight: 700;
  margin-bottom: 10px;
}

.post-meta {
  display: flex;
  gap: 20px;
  font-size: 14px;
  color: #666;
  margin-bottom: 25px;
}

.post-thumbnail {
  width: 100%;
  border-radius: 12px;
  margin-bottom: 25px;
}

.post-content {
  font-size: 17px;
  line-height: 1.75;
}
.post-content img {
  max-width: 100%;
  border-radius: 10px;
  margin: 20px 0;
}

.post-tags {
  margin-top: 30px;
}
.post-tags span {
  background: #f1f1f1;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 13px;
  margin-right: 8px;
}
</style>
