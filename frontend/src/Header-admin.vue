<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const currentUser = ref(null);

// Khi load trang admin → kiểm tra user
onMounted(() => {
  const user = localStorage.getItem("currentUser");
  if (user) {
    currentUser.value = JSON.parse(user);
  }

  // Nếu chưa đăng nhập → đá về trang login
  if (!currentUser.value) {
    router.push("/Dangnhap");
  }

  // Nếu không phải admin → cấm truy cập
  if (currentUser.value?.role !== "admin") {
    router.push("/");
  }
});

// 🔴 Logout cho trang Admin
const logout = () => {
  localStorage.removeItem("currentUser");
  currentUser.value = null;
  window.location.href = "/Dangnhap";
};
</script>
<template>
  <header class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">


    <div class="flex-grow-1 text-center fw-semibold fs-5 welcome">
      Xin chào, {{ currentUser?.tenKH || "Admin" }}
    </div>

    <div>
      <router-link to="/" class="btn btn-primary me-2 back">Trở về trang web</router-link>
      <button class="btn btn-danger" @click="logout">Đăng xuất</button>
    </div>
  </header>
</template>

<style scoped>
.welcome{
    color: white;
}
header{
    background-color: rgb(85, 85, 85);
}
</style>