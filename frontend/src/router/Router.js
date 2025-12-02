import { createWebHistory, createRouter } from 'vue-router';

import Quanlysanpham from '../components/Admin/Quanlysanpham.vue';
import Quanlydanhmuc from '../components/Admin/Quanlydanhmuc.vue';
import Quanlythuonghieu from '../components/Admin/Quanlythuonghieu.vue';
import Quanlykhachhang from '../components/Admin/Quanlykhachhang.vue';
import Quanlydonhang from '../components/Admin/Quanlydonhang.vue';
import Quanlymausac from '../components/Admin/Quanlymausac.vue';
import Quanlysize from '../components/Admin/Quanlysize.vue';
import Dashboard from '../components/Admin/Dashboard.vue';

import TrangChu from '../components/Web/TrangChu.vue';
import Sanpham from '../components/Web/Sanpham.vue';
import ChiTiet from '../components/Web/ChiTiet.vue';
import ThongTin from '../components/Web/ThongTin.vue';
import Gioithieu from '../components/Web/Gioithieu.vue';
import Dangnhap from '../components/Auth/Dangnhap.vue'; 
import Quenmatkhau from '../components/Auth/Quenmatkhau.vue';

import tintuc from '../components/Web/Tintuc.vue';
import Thanhtoangiohang from '../components/Web/Thanhtoangiohang.vue';
import LienHe from '../components/Web/LienHe.vue';
import Baiviet from '../components/Web/Baiviet.vue';


const routes = [
    { path: '/Quanlysanpham', name: 'Quanlysanpham', component: Quanlysanpham },
    { path: '/ThongTin', name: 'ThongTin', component: ThongTin },
    { path: '/Quanlydanhmuc', name: 'Quanlydanhmuc', component: Quanlydanhmuc },
    { path: '/Quanlythuonghieu', name: 'Quanlythuonghieu', component: Quanlythuonghieu },
    { path: '/Quanlymausac', name: 'Quanlymausac', component: Quanlymausac },
    { path: '/Quanlysize', name: 'Quanlysize', component: Quanlysize },
    { path: '/Quanlykhachhang', name: 'Quanlykhachhang', component: Quanlykhachhang },
    { path: '/Quanlydonhang', name: 'Quanlydonhang', component: Quanlydonhang },
    { path: '/Dashboard', name: 'Dashboard', component: Dashboard },

    { path: '/', name: 'TrangChu', component: TrangChu },
    { path: '/Sanpham', name: 'Sanpham', component: Sanpham },
    { path: '/ChiTietDonHang', name: 'ChiTietDonHang', component: ChiTietDonHang },
    { path: '/ChiTiet', name: 'ChiTiet', component: ChiTiet },
    { path: '/Gioithieu', name: 'Gioithieu', component: Gioithieu },
    { path: '/Dangnhap', name: 'Dangnhap', component: Dangnhap },
    { path: '/Quenmatkhau', name: 'Quenmatkhau', component: Quenmatkhau },
    { path: '/tintuc', name: 'tintuc', component: tintuc },
    { path: '/Thanhtoangiohang', name: 'Thanhtoangiohang', component: Thanhtoangiohang },
    { path: '/LienHe', name: 'LienHe', component: LienHe },
    { path: '/Baiviet', name: 'Baiviet', component: Baiviet },

];


const router = createRouter({
    history: createWebHistory(),
    routes
})
router.beforeEach((to, from, next) => {
  const currentUser = localStorage.getItem('currentUser');
  
  if (to.meta.isAuth) {
    if (!currentUser) {
      // chưa login -> về trang login
      return next({ name: 'Dangnhap' });
    }

    const user = JSON.parse(currentUser);
    if (!user.role) {
      // user không có quyền hợp lệ
      return next({ name: 'Dangnhap' });
    }
  }

  // nếu mọi thứ ok thì cho đi tiếp
  next();
});



export default router