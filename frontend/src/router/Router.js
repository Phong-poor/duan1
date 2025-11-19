import {createWebHistory, createRouter} from 'vue-router'

import Quanlysanpham from '../components/Admin/Quanlysanpham.vue';
import Quanlydanhmuc from '../components/Admin/Quanlydanhmuc.vue';
import Quanlythuonghieu from '../components/Admin/Quanlythuonghieu.vue';
import Quanlykhachhang from '../components/Admin/Quanlykhachhang.vue';
import Quanlydonhang from '../components/Admin/Quanlydonhang.vue';
const routes = [
    { path: '/', name: 'Quanlysanpham', component: Quanlysanpham, meta: { isAuth: false } },
    { path: '/Quanlydanhmuc', name: 'Quanlydanhmuc', component: Quanlydanhmuc, meta: { isAuth: false } },
    { path: '/Quanlythuonghieu', name: 'Quanlythuonghieu', component: Quanlythuonghieu, meta: { isAuth: false } },
    { path: '/Quanlykhachhang', name: 'Quanlykhachhang', component: Quanlykhachhang, meta: { isAuth: false } },
    { path: '/Quanlydonhang', name: 'Quanlydonhang', component: Quanlydonhang, meta: { isAuth: false } },
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