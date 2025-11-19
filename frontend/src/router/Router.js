import {createWebHistory, createRouter} from 'vue-router'
import trangChu from '../components/Web/trangchu.vue';
import sanPham from '../components/Web/Sanpham.vue';
import chiTiet from '../components/Web/ChiTiet.vue';
import chiTietDonHang from '../components/Web/ChiTietDonHang.vue';
import thongTin from '../components/Web/ThongTin.vue';

// import Quanlysanpham from '../components/Admin/Quanlysanpham.vue';
// import Quanlydanhmuc from '../components/Admin/Quanlydanhmuc.vue';
const routes = [
    // { path: '/', name: 'Quanlysanpham', component: Quanlysanpham, meta: { isAuth: false } },
    // { path: '/Quanlydanhmuc', name: 'Quanlydanhmuc', component: Quanlydanhmuc, meta: { isAuth: false } },
    { path: '/trangchu', name: 'trangChu', component: trangChu, meta: { isAuth: false } },
    { path: '/Sanpham', name: 'sanPham', component: sanPham, meta: { isAuth: false } },
    { path: '/ChiTiet', name: 'chiTiet', component: chiTiet, meta: { isAuth: false } },
    { path: '/ChiTietDonHang', name: 'chiTietDonHang', component: chiTietDonHang, meta: { isAuth: false } },
    { path: '/Thongtin', name: 'thongTin', component: thongTin, meta: { isAuth: false } },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})
router.beforeEach((to, from, next) => {
  const currentUser = localStorage.getItem('currentUser');
  
  if (to.meta.isAuth) {
    if (!currentUser) {
      // chưa login -> về trang login
      return next({ name: 'Login' });
    }

    const user = JSON.parse(currentUser);
    if (!user.role) {
      // user không có quyền hợp lệ
      return next({ name: 'Login' });
    }
  }

  // nếu mọi thứ ok thì cho đi tiếp
  next();
});

export default router