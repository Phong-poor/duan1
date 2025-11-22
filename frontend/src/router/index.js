import { createRouter, createWebHistory } from 'vue-router'
import Dangnhap from '../Auth/Dangnhap.vue'
import Quenmatkhau from '../Auth/Quenmatkhau.vue'
import TrangChu from '../trangchu.vue'

const routes = [
  { path: '/', component: Dangnhap },
  { path: '/dang-nhap', component: Dangnhap },
  { path: '/quen-mat-khau', component: Quenmatkhau },
  { path: '/trang-chu', component: TrangChu },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
