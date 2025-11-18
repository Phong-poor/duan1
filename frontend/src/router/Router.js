import {createWebHistory, createRouter} from 'vue-router'

import Quanlysanpham from '../components/Admin/Quanlysanpham.vue';
import Quanlydanhmuc from '../components/Admin/Quanlydanhmuc.vue';
const routes = [
    { path: '/', name: 'Quanlysanpham', component: Quanlysanpham, meta: { isAuth: false } },
    { path: '/Quanlydanhmuc', name: 'Quanlydanhmuc', component: Quanlydanhmuc, meta: { isAuth: false } },
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