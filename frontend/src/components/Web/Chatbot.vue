<template>
  <div v-if="isOpen" class="chatbot-overlay" @click="closeModal">
    <div class="chatbot-container" @click.stop>

      <!-- Header -->
      <div class="chatbot-header">
        <div class="header-content">
          <div class="bot-avatar">🤖</div>
          <div>
            <h3>Trợ lý AI</h3>
            <p class="status">Đang hoạt động</p>
          </div>
        </div>
        <button @click="closeModal" class="close-btn">✕</button>
      </div>

      <!-- Messages -->
      <div class="chatbot-messages" ref="messagesContainer">
        <div v-for="(msg, index) in messages" :key="index" 
             :class="['message', msg.type]">
          <div class="message-content">

            <!-- Render AI text -->
            <div v-html="formatMessage(msg.text)"></div>

            <!-- Render product cards -->
            <div v-if="msg.products && msg.products.length" class="products-grid">
              <div v-for="product in msg.products" :key="product.id" class="product-card">

                <img :src="product.image" :alt="product.name" />

                <div class="product-info">
                  <h4>{{ product.name }}</h4>
                  <p class="price">{{ formatPrice(product.price) }} VNĐ</p>

                  <!-- FIXED LINK + CLICK HANDLER -->
                  <a
                    :href="`/ChiTiet?id=${product.id}`"
                    class="view-btn"
                    :data-id="product.id"
                    @click.prevent="handleProductClick(product.id)"
                  >
                    Xem chi tiết →
                  </a>
                </div>
              </div>
            </div>
          </div>

          <span class="message-time">{{ msg.time }}</span>
        </div>

        <!-- Typing -->
        <div v-if="isLoading" class="message bot">
          <div class="message-content">
            <div class="typing-indicator">
              <span></span><span></span><span></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Input box -->
      <div class="chatbot-input">
        <input 
          v-model="userInput"
          @keypress.enter="handleSendMessage"
          :disabled="isLoading"
          type="text"
          placeholder="Nhập tin nhắn... (VD: Tôi cần giày Nike size 42 màu đen)"
        />
        <button @click="handleSendMessage" :disabled="isLoading || !userInput.trim()">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
          </svg>
        </button>
      </div>

    </div>
  </div>

  <!-- Floating Button -->
  <button v-if="!isOpen" @click="openModal" class="chatbot-float-btn">
    💬
  </button>
</template>


<script setup>
import { ref, nextTick } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const isOpen = ref(false);
const userInput = ref("");
const messages = ref([]);
const isLoading = ref(false);
const messagesContainer = ref(null);

// ==========================
// MỞ CHATBOT
// ==========================
const openModal = () => {
  isOpen.value = true;
  if (messages.value.length === 0) {
    addBotMessage(
      "Xin chào! 👋 Tôi có thể giúp bạn tìm giày phù hợp. Hãy cho tôi biết bạn đang tìm loại giày nào nhé!"
    );
  }
};

const closeModal = () => {
  isOpen.value = false;
};

// ==========================
// THÊM TIN NHẮN BOT
// ==========================
const addBotMessage = (text, products = null) => {
  messages.value.push({
    type: "bot",
    text,
    time: getCurrentTime(),
    products,
  });
  scrollToBottom();
};

// ==========================
// THÊM TIN NHẮN USER
// ==========================
const addUserMessage = (text) => {
  messages.value.push({
    type: "user",
    text,
    time: getCurrentTime(),
  });
  scrollToBottom();
};

// ==========================
// XỬ LÝ GỬI TIN
// ==========================
const handleSendMessage = async () => {
  if (!userInput.value.trim() || isLoading.value) return;

  const message = userInput.value.trim();
  addUserMessage(message);
  userInput.value = "";
  isLoading.value = true;

  try {
    const response = await fetch(
      "http://localhost/duan1/backend/api/Web/Chatbot.php",
      {
        method: "POST",
        headers: { "Content-Type": "application/json; charset=UTF-8" },
        body: JSON.stringify({ message }),
      }
    );

    const text = await response.text();
    let data = JSON.parse(text);

    if (data.error) {
      addBotMessage("❌ Lỗi: " + data.error);
    } else {
      addBotMessage(data.message, data.products);
    }
  } catch (err) {
    addBotMessage("❌ Không thể kết nối đến server");
  } finally {
    isLoading.value = false;
  }
};

// ==========================
// CLICK "XEM CHI TIẾT"
// ==========================
const handleProductClick = (id) => {
  router.push(`/ChiTiet?id=${id}`);
  isOpen.value = false;
};

// ==========================
// FORMAT MARKDOWN LINK
// ==========================
const formatMessage = (text) => {
  return text.replace(
    /\[([^\]]+)\]\(\/ChiTiet\?id=(\d+)\)/g,
    `<a href="/ChiTiet?id=$2" class="product-link" data-id="$2">$1</a>`
  );
};

// ==========================
// TIỆN ÍCH
// ==========================
const formatPrice = (price) => new Intl.NumberFormat("vi-VN").format(price);

const getCurrentTime = () => {
  const d = new Date();
  return `${String(d.getHours()).padStart(2, "0")}:${String(
    d.getMinutes()
  ).padStart(2, "0")}`;
};

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop =
        messagesContainer.value.scrollHeight;
    }
  });
};
</script>


<style scoped>
.chatbot-overlay {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: transparent;
  display: flex;
  justify-content: flex-end;
  align-items: flex-end;
  width: auto;
  height: auto;
  padding: 0;
  z-index: 9999;
}


.chatbot-container {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 500px;
  height: 600px;
  display: flex;
  flex-direction: column;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  overflow: hidden;
}

.chatbot-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-content {
  display: flex;
  align-items: center;
  gap: 12px;
}

.bot-avatar {
  font-size: 32px;
  width: 50px;
  height: 50px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.chatbot-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
}

.status {
  margin: 4px 0 0;
  font-size: 12px;
  opacity: 0.9;
}

.close-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  font-size: 24px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  cursor: pointer;
  transition: background 0.3s;
}

.close-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

.chatbot-messages {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
  background: #f5f5f5;
}

.message {
  margin-bottom: 16px;
  display: flex;
  flex-direction: column;
}

.message.user {
  align-items: flex-end;
}

.message.bot {
  align-items: flex-start;
}

.message-content {
  max-width: 75%;
  padding: 12px 16px;
  border-radius: 12px;
  word-wrap: break-word;
}

.message.user .message-content {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.message.bot .message-content {
  background: white;
  color: #333;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.message-time {
  font-size: 11px;
  color: #999;
  margin-top: 4px;
}

.products-grid {
  margin-top: 12px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.product-card {
  background: #f9f9f9;
  border-radius: 8px;
  padding: 12px;
  display: flex;
  gap: 12px;
  border: 1px solid #e0e0e0;
}

.product-card img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 6px;
}

.product-info {
  flex: 1;
}

.product-info h4 {
  margin: 0 0 8px;
  font-size: 14px;
  color: #333;
}

.price {
  color: #e53e3e;
  font-weight: 600;
  margin: 4px 0;
}

.view-btn {
  display: inline-block;
  margin-top: 8px;
  color: #667eea;
  font-size: 13px;
  text-decoration: none;
  font-weight: 500;
}

.view-btn:hover {
  text-decoration: underline;
}

.product-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.product-link:hover {
  text-decoration: underline;
}

.typing-indicator {
  display: flex;
  gap: 4px;
}

.typing-indicator span {
  width: 8px;
  height: 8px;
  background: #999;
  border-radius: 50%;
  animation: typing 1.4s infinite;
}

.typing-indicator span:nth-child(2) {
  animation-delay: 0.2s;
}

.typing-indicator span:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes typing {
  0%, 60%, 100% {
    transform: translateY(0);
  }
  30% {
    transform: translateY(-10px);
  }
}

.chatbot-input {
  padding: 16px 20px;
  background: white;
  border-top: 1px solid #e0e0e0;
  display: flex;
  gap: 12px;
}

.chatbot-input input {
  flex: 1;
  padding: 12px 16px;
  border: 1px solid #e0e0e0;
  border-radius: 24px;
  font-size: 14px;
  outline: none;
  transition: border-color 0.3s;
}

.chatbot-input input:focus {
  border-color: #667eea;
}

.chatbot-input button {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.2s;
}

.chatbot-input button:hover:not(:disabled) {
  transform: scale(1.05);
}

.chatbot-input button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.chatbot-float-btn {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  font-size: 28px;
  border: none;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
  transition: transform 0.3s;
  z-index: 9998;
}

.chatbot-float-btn:hover {
  transform: scale(1.1);
}

@media (max-width: 768px) {
  .chatbot-container {
    height: 100vh;
    max-width: 100%;
    border-radius: 0;
  }
}
</style>