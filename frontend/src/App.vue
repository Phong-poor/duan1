<script setup>
import Trangchu from './trangchu.vue';
import thongTin from './ThongTin.vue';
import chitietdonhang from './ChiTietDonHang.vue';
import sanpham from './SanPham.vue';
import chitiet from './ChiTiet.vue';
</script>
<template>

  <main>
    <!-- <Trangchu /> -->
    <!-- <thongTin/> -->
    <!-- <chitietdonhang/> -->
     <!-- <sanpham/> -->
      <chitiet/>
  </main>
  <FooterComponent  />
</template>
<style scoped>
.footer {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
}
</style>