import {createWebHistory, createRouter} from 'vue-router'

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
import ChiTietDonHang from '../components/Web/ChiTietDonHang.vue';
import ChiTiet from '../components/Web/ChiTiet.vue';
import ThongTin from '../components/Web/ThongTin.vue';
import Gioithieu from '../components/Web/Gioithieu.vue';
import Dangnhap from '../components/Auth/Dangnhap.vue'; 
import Quenmatkhau from '../components/Auth/Quenmatkhau.vue';
// import tintuc from '../components/Web/tintuc.vue';
import Thanhtoangiohang from '../components/Web/Thanhtoangiohang.vue';


const routes = [
    { path: '/Quanlysanpham', name: 'Quanlysanpham', component: Quanlysanpham, meta: { isAuth: false } },
    { path: '/ThongTin', name: 'ThongTin', component: ThongTin, meta: { isAuth: false } },
    { path: '/Quanlydanhmuc', name: 'Quanlydanhmuc', component: Quanlydanhmuc, meta: { isAuth: false } },
    { path: '/Quanlythuonghieu', name: 'Quanlythuonghieu', component: Quanlythuonghieu, meta: { isAuth: false } },
    { path: '/Quanlymausac', name: 'Quanlymausac', component: Quanlymausac, meta: { isAuth: false } },
    { path: '/Quanlysize', name: 'Quanlysize', component: Quanlysize, meta: { isAuth: false } },
    { path: '/Quanlykhachhang', name: 'Quanlykhachhang', component: Quanlykhachhang, meta: { isAuth: false } },
    { path: '/Quanlydonhang', name: 'Quanlydonhang', component: Quanlydonhang, meta: { isAuth: false } },
    { path: '/Dashboard', name: 'Dashboard', component: Dashboard, meta: { isAuth: false } },
    { path: '/', name: 'TrangChu', component: TrangChu, meta: { isAuth: false } },
    { path: '/Sanpham', name: 'Sanpham', component: Sanpham, meta: { isAuth: false } },
    { path: '/ChiTietDonHang', name: 'ChiTietDonHang', component: ChiTietDonHang, meta: { isAuth: false } },
    { path: '/ChiTiet', name: 'ChiTiet', component: ChiTiet, meta: { isAuth: false } },
    { path: '/Gioithieu', name: 'Gioithieu', component: Gioithieu, meta: { isAuth: false } },
    { path: '/Dangnhap', name: 'Dangnhap', component: Dangnhap, meta: { isAuth: false } },
    { path: '/Quenmatkhau', name: 'Quenmatkhau', component: Quenmatkhau, meta: { isAuth: false } },
    // { path: '/tintuc', name: 'tintuc', component: tintuc, meta: { isAuth: false } },  
    { path: '/Thanhtoangiohang', name: 'Thanhtoangiohang', component: Thanhtoangiohang, meta: { isAuth: false } }, 

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