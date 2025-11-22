<template>
  <div class="order-page">
    <HeaderWeb />
    <main class="container">
      <div class="account-layout">
        <aside class="sidebar">
          <nav class="sidebar-nav">
            <a href="#" class="active">
              <i class="fas fa-shopping-bag"></i>
              Quản Lý Đơn Hàng
            </a>
            <a href="#">
              <i class="fas fa-user"></i>
              Thông Tin Chung
            </a>
          </nav>
        </aside>

        <section class="main-content">
          <h2 class="content-title">
            <i class="fas fa-history"></i>
            Lịch Sử Đơn Hàng
          </h2>

          <table class="order-history-table">
            <thead>
              <tr>
                <th>Mã đơn</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="order in pagedOrders"
                :key="order.id"
                :class="{ 'active-row': order.id === activeOrderId }"
                @click="selectOrder(order.id)"
              >
                <td>#{{ order.id }}</td>
                <td>{{ order.total }}</td>
                <td>
                  <span class="status-tag" :class="getStatusClass(order.status)">
                    {{ statusLabel(order.status) }}
                  </span>
                </td>
                <td>
                  <button
                    class="btn-action btn-detail"
                    type="button"
                    @click.stop="selectOrder(order.id)"
                  >
                    Xem chi tiết
                  </button>
                  <a
                    v-if="order.status !== 'cancelled'"
                    href="#"
                    class="btn-action btn-cancel"
                  >
                    Hủy đơn
                  </a>
                  <a v-else href="#" class="btn-action btn-buy-again">
                    Mua lại
                  </a>
                </td>
              </tr>
              <tr v-if="pagedOrders.length === 0">
                <td colspan="4" class="no-data">Không có đơn hàng.</td>
              </tr>
            </tbody>
          </table>

          <div class="pagination-controls" v-if="pageCount > 1">
            <button
              class="pagination-btn"
              :disabled="currentPage === 1"
              @click="prevPage"
            >
              ❮
            </button>
            <button
              v-for="page in pageCount"
              :key="page"
              class="pagination-btn"
              :class="{ active: page === currentPage }"
              @click="goToPage(page)"
            >
              {{ page }}
            </button>
            <button
              class="pagination-btn"
              :disabled="currentPage === pageCount"
              @click="nextPage"
            >
              ❯
            </button>
          </div>

          <div id="selectedOrderDetail" class="detail-area" v-if="activeOrder">
            <h4>Chi tiết đơn hàng #{{ activeOrder.id }}</h4>
            <table class="detail-table">
              <thead>
                <tr>
                  <th style="width: 50%;">Sản phẩm</th>
                  <th>SL</th>
                  <th>Giá</th>
                  <th>Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in activeOrder.products" :key="item.name">
                  <td>
                    <img
                      src="https://via.placeholder.com/40x40?text=P"
                      :alt="item.name"
                    />
                    {{ item.name }}
                  </td>
                  <td>{{ item.sl }}</td>
                  <td>{{ item.price }}</td>
                  <td>TBA</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="4" class="total-summary">
                    Tổng cộng: {{ activeOrder.total }}
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
          <div v-else id="selectedOrderDetail">
            <p class="placeholder-text">
              Vui lòng chọn đơn hàng để xem chi tiết.
            </p>
          </div>
        </section>
      </div>
    </main>
    <footerWeb/>
  </div>
</template>

<script setup>
import { computed, ref, watchEffect }from 'vue';
import HeaderWeb from '../../Header-web.vue';
import footerWeb from '../../footer-web.vue';
const allOrders = [
  {
    id: 'KH00245',
    total: '4,299.86 $',
    status: 'cancelled',
    date: '01/11/2025',
    products: [
      { name: 'Gaming PC X MSI i5', sl: 1, price: '3,999.95 $' },
      { name: 'Màn hình MSI', sl: 1, price: '299.91 $' },
    ],
  },
  {
    id: 'KH00240',
    total: '3,999.95 $',
    status: 'pending',
    date: '15/10/2025',
    products: [{ name: 'Nike Air Max 97 (Size 40)', sl: 1, price: '3,999.95 $' }],
  },
  {
    id: 'KH00231',
    total: '699.92 $',
    status: 'completed',
    date: '28/09/2025',
    products: [
      { name: 'Webcam HD', sl: 1, price: '399.99 $' },
      { name: 'Thẻ nhớ 128GB', sl: 1, price: '299.93 $' },
    ],
  },
  {
    id: 'KH00206',
    total: '699.92 $',
    status: 'pending',
    date: '10/08/2025',
    products: [
      { name: 'Sản phẩm A', sl: 1, price: '300.00 $' },
      { name: 'Sản phẩm B', sl: 1, price: '399.92 $' },
    ],
  },
  {
    id: 'KH00200',
    total: '1,500.00 $',
    status: 'completed',
    date: '05/08/2025',
    products: [{ name: 'Giày Adidas Samba', sl: 1, price: '1,500.00 $' }],
  },
  {
    id: 'KH00195',
    total: '990.00 $',
    status: 'pending',
    date: '20/07/2025',
    products: [{ name: 'Áo Polo Nike', sl: 2, price: '495.00 $' }],
  },
  {
    id: 'KH00188',
    total: '2,200.00 $',
    status: 'completed',
    date: '10/07/2025',
    products: [{ name: 'Giày Converse', sl: 1, price: '2,200.00 $' }],
  },
  {
    id: 'KH00170',
    total: '500.00 $',
    status: 'cancelled',
    date: '01/07/2025',
    products: [{ name: 'Dây giày', sl: 4, price: '125.00 $' }],
  },
];

const ITEMS_PER_PAGE = 5;

const currentPage = ref(1);
const activeOrderId = ref(null);

const pageCount = computed(() => Math.ceil(allOrders.length / ITEMS_PER_PAGE));

const pagedOrders = computed(() => {
  const start = (currentPage.value - 1) * ITEMS_PER_PAGE;
  return allOrders.slice(start, start + ITEMS_PER_PAGE);
});

const activeOrder = computed(() =>
  allOrders.find((order) => order.id === activeOrderId.value) || null,
);

watchEffect(() => {
  if (!pagedOrders.value.length) {
    activeOrderId.value = null;
    return;
  }
  if (!activeOrderId.value || !pagedOrders.value.some((o) => o.id === activeOrderId.value)) {
    activeOrderId.value = pagedOrders.value[0].id;
  }
});

const selectOrder = (orderId) => {
  activeOrderId.value = orderId;
};

const goToPage = (page) => {
  if (page >= 1 && page <= pageCount.value) {
    currentPage.value = page;
  }
};

const prevPage = () => {
  goToPage(currentPage.value - 1);
};

const nextPage = () => {
  goToPage(currentPage.value + 1);
};

const getStatusClass = (status) => {
  switch (status) {
    case 'cancelled':
      return 'status-cancelled';
    case 'pending':
      return 'status-pending';
    case 'completed':
      return 'status-completed';
    default:
      return '';
  }
};

const statusLabel = (status) => {
  switch (status) {
    case 'cancelled':
      return 'Đã hủy';
    case 'pending':
      return 'Đang xử lý';
    case 'completed':
      return 'Hoàn tất';
    default:
      return status;
  }
};
</script>

<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');

.order-page {
  font-family: 'Roboto', sans-serif;
  background-color: #f7f7f7;
  min-height: 100vh;
  color: #333;
}

.container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 140px 20px 60px;
}

.account-layout {
  display: flex;
  gap: 30px;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}

.sidebar {
  flex: 0 0 250px;
  background-color: #fcfcfc;
  border-right: 1px solid #eee;
  border-top-left-radius: 10px;
  border-bottom-left-radius: 10px;
}

.sidebar-nav {
  padding: 20px 0;
}

.sidebar-nav a {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  text-decoration: none;
  color: #555;
  font-size: 15px;
  transition: background-color 0.2s, color 0.2s;
}

.sidebar-nav a i {
  margin-right: 10px;
}

.sidebar-nav a:hover {
  background-color: #e6f0ff;
  color: #007bff;
}

.sidebar-nav a.active {
  background-color: #007bff;
  color: #fff;
  font-weight: 500;
}

.sidebar-nav a.active i {
  color: #fff;
}

.main-content {
  flex-grow: 1;
  padding: 30px;
}

.content-title {
  font-family: 'Montserrat', sans-serif;
  font-size: 22px;
  font-weight: 700;
  color: #1a1a1a;
  border-bottom: 2px solid #ddd;
  padding-bottom: 5px;
  margin-bottom: 25px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.order-history-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
  margin-bottom: 30px;
  box-shadow: 0 1px 5px rgba(0, 0, 0, 0.05);
}

.order-history-table th {
  padding: 12px 10px;
  text-align: left;
  background-color: #f2f2f2;
  font-weight: 600;
  color: #444;
  text-transform: uppercase;
}

.order-history-table td {
  padding: 10px 10px;
  text-align: left;
  background-color: #fff;
  border-bottom: 1px solid #eee;
  font-weight: 500;
  color: #1a1a1a;
}

.order-history-table tr.active-row td {
  background-color: #e6f0ff;
}

.status-tag {
  font-size: 13px;
  font-weight: 500;
  padding: 4px 8px;
  border-radius: 4px;
  display: inline-block;
}

.status-cancelled {
  color: #f44336;
  background-color: #ffebee;
}

.status-pending {
  color: #ff9800;
  background-color: #fff3e0;
}

.status-completed {
  color: #4caf50;
  background-color: #e8f5e9;
}

.btn-action {
  padding: 6px 10px;
  border-radius: 4px;
  font-size: 13px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.2s;
  margin-right: 5px;
  text-decoration: none;
  display: inline-block;
  text-align: center;
  border: none;
}

.btn-detail {
  background-color: #007bff;
  color: #fff;
}

.btn-buy-again {
  background-color: #4caf50;
  color: #fff;
}

.btn-cancel {
  background-color: #f44336;
  color: #fff;
}

.btn-detail:hover {
  background-color: #0056b3;
}

.btn-buy-again:hover {
  background-color: #388e3c;
}

.btn-cancel:hover {
  background-color: #d32f2f;
}

.pagination-controls {
  text-align: center;
  margin-top: 20px;
}

.pagination-btn {
  background-color: #fff;
  border: 1px solid #ddd;
  color: #555;
  padding: 8px 12px;
  margin: 0 4px;
  cursor: pointer;
  border-radius: 4px;
  transition: background-color 0.2s, color 0.2s;
  font-size: 14px;
}

.pagination-btn:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

.pagination-btn.active,
.pagination-btn:not(:disabled):hover {
  background-color: #007bff;
  color: #fff;
  border-color: #007bff;
}

.detail-area {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  border: 1px solid #ddd;
  margin-top: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.detail-area h4 {
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 15px;
  color: #1a1a1a;
}

.detail-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

.detail-table th {
  background-color: #f9f9f9;
  padding: 10px;
  text-align: left;
  color: #555;
  text-transform: uppercase;
}

.detail-table td {
  padding: 10px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.detail-table td img {
  width: 40px;
  height: 40px;
  object-fit: contain;
  margin-right: 10px;
}

.total-summary {
  font-weight: 700;
  padding-top: 10px;
  text-align: right;
  font-size: 16px;
  color: #e53935;
}

.placeholder-text {
  text-align: center;
  color: #777;
  margin: 50px 0;
}

.no-data {
  text-align: center;
  color: #777;
  font-style: italic;
}

@media (max-width: 768px) {
  .container {
    padding-top: 110px;
  }

  .account-layout {
    flex-direction: column;
  }

  .sidebar {
    border-right: none;
    border-bottom: 1px solid #eee;
  }

  .order-history-table {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
  }

  .main-content {
    padding: 20px 15px;
  }
}
</style>