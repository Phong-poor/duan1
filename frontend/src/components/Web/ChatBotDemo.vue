<template>
  <div class="chat-wrapper">
    <!-- NÚT BẬT CHAT -->
    <div class="chat-btn" @click="toggleChat">💬</div>

    <!-- KHUNG CHAT -->
    <div v-if="open" class="chat-box">

      <!-- HEADER -->
      <div class="chat-header">
        <strong>Mirae AI</strong>
        <button @click="open = false">×</button>
      </div>

      <!-- BODY -->
      <div class="chat-body" ref="msgBox">

        <!-- Bot đang nhập -->
        <div v-if="typing" class="msg bot typing">
          Mirae AI đang nhập…
        </div>

        <!-- Tin nhắn -->
        <template v-for="(msg, i) in messages" :key="i">
          
          <!-- Tin text -->
          <div v-if="msg.type === 'text'" :class="['msg', msg.from]">
            {{ msg.text }}
          </div>

          <!-- CARD SẢN PHẨM -->
          <div v-if="msg.type === 'cards'" class="card-list">
            <div v-for="sp in msg.items" :key="sp.id_sanpham" class="product-card">

              <img :src="'http://localhost/DUAN1/backend/' + sp.hinhAnhgoc" />

              <div class="info">
                <h4>{{ sp.tenSP }}</h4>

                <p class="price">
                  {{ format(sp.giaSauGiam) }} đ
                  <span v-if="sp.giaSP > sp.giaSauGiam" class="old">
                    {{ format(sp.giaSP) }} đ
                  </span>
                </p>

                <router-link :to="'/chitiet/' + sp.id_sanpham">
                  <button class="btn-detail">Xem chi tiết</button>
                </router-link>
              </div>

            </div>
          </div>
        </template>

      </div>

      <!-- INPUT -->
      <div class="chat-input">
        <input
          v-model="input"
          @keyup.enter="send"
          placeholder="Nhập tin nhắn..."
        />
        <button @click="send">➤</button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, nextTick, onMounted } from "vue";
import axios from "axios";

const open = ref(false);
const input = ref("");
const messages = ref([]);
const products = ref([]);
const typing = ref(false);

// Format tiền
function format(x) {
  return Number(x).toLocaleString("vi-VN");
}

// Load sản phẩm từ backend
async function loadProducts() {
  const res = await axios.get("http://localhost/DUAN1/backend/api/Web/SanPham.php");
  products.value = res.data.data;
}

// AI phân tích câu hỏi
async function askAI(question) {
  typing.value = true;

  const API_KEY = "AIzaSyB7C62jRTc1KqhutCtkPd9Uvg77KEh46oI"; // ⚠ Đổi key của bạn vào đây
  const MODEL = "gemini-1.5-flash";

  const prompt = `
Phân tích người dùng nói: "${question}"

CHỈ TRẢ JSON dạng:

{
  "keyword": "",
  "brand": "",
  "category": "",
  "min": 0,
  "max": 0
}

RULE:
- "giày thể thao", "giày chạy", "sport" → category = "Giày thời trang"
- giá dưới xxx → max
- giá trên xxx → min
- Adidas, Nike, Puma → brand
- không chắc → để rỗng
`;

  try {
    const res = await axios.post(
      `https://generativelanguage.googleapis.com/v1beta/models/${MODEL}:generateContent?key=${API_KEY}`,
      {
        contents: [
          { parts: [{ text: prompt }] }
        ]
      }
    );

    typing.value = false;

    return res.data.candidates[0].content.parts[0].text;

  } catch (err) {
    typing.value = false;
    console.error("Gemini lỗi:", err);
    return `{"keyword":"${question}"}`;
  }
}

// Mapping thông minh
function smartMapping(f) {
  const q = f.keyword.toLowerCase();

  if (q.includes("thể thao") || q.includes("sport") || q.includes("running")) {
    f.category = "Giày thời trang";
  }

  return f;
}

// Lọc sản phẩm
function filterProducts(f) {
  return products.value.filter(p => {

    let ok = true;

    if (f.keyword && !p.tenSP.toLowerCase().includes(f.keyword.toLowerCase())) ok = false;
    if (f.brand && p.tenTH.toLowerCase() !== f.brand.toLowerCase()) ok = false;
    if (f.category && p.tenDM.toLowerCase() !== f.category.toLowerCase()) ok = false;
    if (f.min && p.giaSauGiam < f.min) ok = false;
    if (f.max && p.giaSauGiam > f.max) ok = false;

    return ok;
  });
}

// Gửi tin nhắn
async function send() {
  if (!input.value.trim()) return;

  const text = input.value;
  messages.value.push({ type: "text", from: "user", text });

  input.value = "";

  let aiJson = await askAI(text);

  let filter = null;
  try {
    filter = JSON.parse(aiJson);
  } catch {
    filter = { keyword: text, brand: "", category: "", min: 0, max: 0 };
  }

  filter = smartMapping(filter);

  const result = filterProducts(filter);

  if (result.length === 0) {
    messages.value.push({
      type: "text",
      from: "bot",
      text: "Hiện shop không có sản phẩm phù hợp rồi ạ ❤️"
    });
  } else {
    messages.value.push({
      type: "cards",
      from: "bot",
      items: result.slice(0, 4)
    });
  }

  nextTick(scrollBottom);
}

// Bot chào khách
function toggleChat() {
  open.value = !open.value;

  if (open.value && messages.value.length === 0) {
    messages.value.push({
      type: "text",
      from: "bot",
      text: "Xin chào! 👋 Mirae AI có thể giúp bạn tìm giày theo giá, thương hiệu hoặc nhu cầu!"
    });
  }
}

// Scroll xuống cuối
function scrollBottom() {
  const el = document.querySelector(".chat-body");
  if (el) el.scrollTop = el.scrollHeight;
}

onMounted(loadProducts);
</script>

<style>
/* FIX KHÔNG BỊ CHE */
.chat-wrapper {
  position: fixed !important;
  bottom: 25px !important;
  right: 25px !important;
  z-index: 99999999 !important;
}

/* Nút chat */
.chat-btn {
  width: 65px;
  height: 65px;
  background: linear-gradient(135deg, #ff7b00, #ff4800);
  border-radius: 50%;
  box-shadow: 0 5px 20px rgba(255, 105, 0, 0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 30px;
  color: white;
  cursor: pointer;
  transition: 0.2s;
}

.chat-btn:hover {
  transform: scale(1.15);
}

/* Khung chat */
.chat-box {
  width: 360px;
  height: 520px;
  background: white;
  border-radius: 16px;
  box-shadow: 0 0 20px rgba(0,0,0,0.18);
  display: flex;
  flex-direction: column;
  animation: fadeIn 0.25s ease-out;
}

@keyframes fadeIn {
  from { transform: translateY(20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.chat-header {
  background: linear-gradient(135deg, #ff7b00, #ff4f00);
  padding: 12px;
  color: white;
  font-weight: bold;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.chat-body {
  flex: 1;
  padding: 12px;
  overflow-y: auto;
  background: #f7f7f7;
}

/* Bong bóng chat */
.msg {
  max-width: 80%;
  padding: 10px 13px;
  border-radius: 12px;
  margin-bottom: 12px;
  font-size: 15px;
  line-height: 1.4;
}

.user {
  background: #007bff;
  color: white;
  margin-left: auto;
}

.bot {
  background: white;
  color: #333;
}

.typing {
  font-style: italic;
  background: #eee;
}

/* Ô nhập */
.chat-input {
  padding: 10px;
  display: flex;
  background: white;
  border-top: 1px solid #ddd;
}

.chat-input input {
  flex: 1;
  padding: 9px;
  border-radius: 10px;
  border: none;
  background: #f1f1f1;
}

.chat-input button {
  width: 48px;
  margin-left: 8px;
  border: none;
  border-radius: 10px;
  font-size: 20px;
  background: linear-gradient(135deg, #ff7b00, #ff4f00);
  color: white;
}

/* CARD SẢN PHẨM */
.card-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.product-card {
  background: white;
  border-radius: 12px;
  display: flex;
  gap: 12px;
  padding: 10px;
  border: 1px solid #eee;
}

.product-card img {
  width: 80px;
  height: 80px;
  border-radius: 10px;
  object-fit: cover;
}

.price {
  color: #ff4f00;
  font-weight: bold;
}

.old {
  text-decoration: line-through;
  color: #888;
}

.btn-detail {
  margin-top: 5px;
  width: 100%;
  padding: 6px;
  border-radius: 8px;
  border: none;
  background: linear-gradient(135deg, #ff7b00, #ff4f00);
  color: white;
}
</style>
