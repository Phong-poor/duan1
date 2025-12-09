<template>
  <div class="app-wrapper d-flex">
    <!-- Sidebar -->
    <aside class="sidebar bg-dark text-white p-3">
      <img :src="logoImage" alt="Logo" class="logo-img">
      <ul class="sidebar-menu">
        <router-link to="/Dashboard" class="menu-item" active-class="active">
          <i class="fa-solid fa-chart-line"></i> Dashboard
        </router-link>
        <router-link to="/Quanlysanpham" class="menu-item" active-class="active">
          <i class="fa-solid fa-box"></i> Sản phẩm
        </router-link>
        <router-link to="/Quanlydanhmuc" class="menu-item" active-class="active">
          <i class="fa-solid fa-layer-group"></i> Danh mục
        </router-link>
        <router-link to="/Quanlythuonghieu" class="menu-item" active-class="active">
          <i class="fa-solid fa-bookmark"></i> Thương hiệu
        </router-link>
        <router-link to="/Quanlymausac" class="menu-item" active-class="active">
          <i class="fa-solid fa-palette"></i> Màu sắc
        </router-link>
        <router-link to="/Quanlysize" class="menu-item" active-class="active">
          <i class="fa-solid fa-maximize"></i> Size
        </router-link>
        <router-link to="/Quanlydonhang" class="menu-item" active-class="active">
          <i class="fa-solid fa-cart-shopping"></i> Đơn hàng
        </router-link>
        <router-link to="/Quanlybinhluan" class="menu-item" active-class="active">
          <i class="fa-solid fa-comment"></i> Đánh giá
        </router-link>
        <router-link to="/Quanlyvoucher" class="menu-item" active-class="active">
          <i class="fa-solid fa-ticket"></i> Voucher
        </router-link>
        <router-link to="/Quanlykhachhang" class="menu-item" active-class="active">
          <i class="fa-solid fa-users"></i> Khách hàng
        </router-link>
      </ul>
    </aside>

    <!-- Main Content -->
    <div class="main-content flex-grow-1">
      <header class="admin-header">
        <HeaderAdmin/>
      </header>

      <div class="content-section p-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="fw-bold">Quản lý sản phẩm</h3>
          <button class="btn btn-primary me-2 back" @click="scrollToForm">
            {{ isEditing ? 'Sửa sản phẩm' : 'Thêm sản phẩm' }}
          </button>
        </div>

        <!-- Search + Filter -->
        <div class="d-flex gap-2 mb-3">
          
          <!-- Search (tìm tên SP, danh mục, thương hiệu) -->
          <input
            v-model="search"
            type="text"
            class="form-control"
            placeholder="🔍 Tìm sản phẩm, danh mục hoặc thương hiệu..."
          />

          <!-- Dropdown giảm giá -->
          <select v-model="filterDiscount" class="form-select filter-select">
            <option value="">Tất cả</option>
            <option value="yes">Đang giảm giá</option>
            <option value="no">Chưa giảm giá</option>
          </select>

        </div>

        <!-- Product Table -->
        <table class="table table-bordered text-center">
          <thead class="table-secondary">
            <tr>
              <th>Mã SP</th>
              <th>Hình ảnh</th>
              <th>Tên SP</th>
              <th>Danh mục</th>
              <th>Thương hiệu</th>
              <th>Giá</th>
              <th>Giảm giá</th>
              <th>Bắt đầu</th>
              <th>Kết thúc</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="sp in paginatedProducts" :key="sp.id">
              <td>{{ sp.maSP }}</td>
              <td>
                <img :src="sp.image" class="thumb" v-if="sp.image" />
              </td>
              <td>{{ sp.name }}</td>
              <td>{{ sp.category }}</td>
              <td>{{ sp.brand }}</td>
              <td>{{ formatCurrency(sp.price) }}</td>
              <td>
                <span v-if="sp.giamgiaSP && sp.giamgiaSP > 0">
                  {{ formatCurrency(sp.giamgiaSP) }}
                </span>
                <span v-else>
                  Không giảm
                </span>
              </td>
              <td>{{ sp.giamgia_start || '' }}</td>
              <td>{{ sp.giamgia_end || '' }}</td>
              <td class="action-cell">
                <button class="btn btn-warning btn-sm" @click="startEdit(sp)">Sửa</button>
                <button class="btn btn-danger btn-sm ms-2" @click="deleteProduct(sp.id)">Xóa</button>
                <button class="btn btn-info btn-sm ms-2" @click="openDiscountPopup(sp)">Giảm giá</button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-center my-3 gap-2">
          <button class="btn btn-secondary btn-sm" :disabled="page === 1" @click="page--">Trước</button>
          <span>Trang {{ page }} / {{ totalPages }}</span>
          <button class="btn btn-secondary btn-sm" :disabled="page === totalPages" @click="page++">Sau</button>
        </div>
        <!-- POPUP GIẢM GIÁ -->
        <div v-if="showDiscount" class="popup-overlay">
          <div class="popup-box">
            <h5 class="fw-bold mb-3">Giảm giá sản phẩm</h5>

            <div class="mb-3">
              <label>% giảm</label>
              <input v-model.number="discountForm.percent" type="number" class="form-control" />
            </div>

            <div class="mb-3">
              <label>Ngày bắt đầu</label>
              <input v-model="discountForm.start" type="datetime-local" class="form-control" />
            </div>

            <div class="mb-3">
              <label>Ngày kết thúc</label>
              <input v-model="discountForm.end" type="datetime-local" class="form-control" />
            </div>

            <div class="d-flex justify-content-end gap-2">
              <button class="btn btn-secondary" @click="showDiscount=false">Hủy</button>
              <button class="btn btn-primary" @click="applyDiscount">Lưu</button>
            </div>
          </div>
        </div>

        <!-- Add / Edit Product Form -->
        <div class="card p-4 mt-4" id="add-form">
          <h4 class="fw-bold mb-3">
            {{ isEditing ? 'Sửa sản phẩm' : 'Thêm sản phẩm' }}
          </h4>

          <!-- Ảnh chính -->
          <div class="mb-3">
            <label class="form-label">Ảnh chính</label>

            <!-- Chỉ cho chọn file khi THÊM -->
            <div v-if="!isEditing">
              <input type="file" class="form-control" @change="onMainImageChange" />
            </div>

            <img
              v-if="mainImagePreview"
              :src="mainImagePreview"
              class="preview-img mt-2"
            />
          </div>

          <!-- Tên SP -->
          <div class="mb-3">
            <label class="form-label">Tên sản phẩm</label>
            <input v-model="productForm.name" type="text" class="form-control" />
          </div>

          <!-- Danh mục -->
          <div class="mb-3">
            <label class="form-label">Danh mục</label>
            <select v-model="productForm.category" class="form-select">
              <option disabled value="">-- Chọn danh mục --</option>
              <option v-for="dm in categories" :key="dm.id_danhmuc" :value="dm.id_danhmuc">
                {{ dm.tenDM }}
              </option>
            </select>
          </div>

          <!-- Thương hiệu -->
          <div class="mb-3">
            <label class="form-label">Thương hiệu</label>
            <select v-model="productForm.thuonghieu" class="form-select">
              <option disabled value="">-- Chọn thương hiệu --</option>
              <option v-for="th in brands" :key="th.id_thuonghieu" :value="th.id_thuonghieu">
                {{ th.tenTH }}
              </option>
            </select>
          </div>

          <!-- Giá -->
          <div class="mb-3">
            <label class="form-label">Giá</label>
            <input v-model.number="productForm.price" type="number" class="form-control" />
          </div>

          <!-- Ảnh phụ -->
          <div class="mb-3">
            <label class="form-label">Ảnh phụ</label>
            <input
              type="file"
              class="form-control"
              multiple
              @change="onExtraImagesChange"
            />

            <div class="d-flex flex-wrap mt-3 gap-2">
              <div
                v-for="(img, index) in productForm.extraImageUrls"
                :key="index"
                class="position-relative"
              >
                <img :src="img.preview" class="preview-img" />
                <button
                  class="btn btn-danger btn-sm delete-img-btn"
                  type="button"
                  @click="removeExtraImage(index)"
                >
                  ✖
                </button>
              </div>
            </div>
          </div>

          <!-- BIẾN THỂ -->
          <div class="variant-box border p-3">
            <h5 class="fw-semibold">Biến thể (màu + size + số lượng + ảnh)</h5>

            <div
              v-for="(v, index) in variants"
              :key="index"
              class="border p-3 rounded mt-2"
            >
              <div class="row g-2">

                <!-- Màu -->
                <div class="col-md-3">
                  <label>Màu sắc</label>
                  <select v-model="v.color" class="form-select">
                    <option disabled value="">-- Chọn màu --</option>
                    <option v-for="c in colors" :key="c.id_mausac" :value="c.id_mausac">
                      {{ c.mausac }}
                    </option>
                  </select>
                </div>

                <!-- Size -->
                <div class="col-md-3">
                  <label>Size</label>
                  <select v-model="v.size" class="form-select">
                    <option disabled value="">-- Chọn size --</option>
                    <option v-for="s in sizes" :key="s.id_size" :value="s.id_size">
                      {{ s.size }}
                    </option>
                  </select>
                </div>

                <!-- SL -->
                <div class="col-md-2">
                  <label>Số lượng</label>
                  <input v-model.number="v.quantity" type="number" class="form-control" />
                </div>

                <!-- Ảnh biến thể -->
                <div class="col-md-3">
                  <label>Ảnh biến thể</label>
                  <input
                    type="file"
                    class="form-control"
                    @change="onVariantImageChange($event, index)"
                  />
                  <img
                    v-if="v.preview || v.imageUrl"
                    :src="v.preview || (backendBase + v.imageUrl)"
                    class="preview-img mt-2"
                  />
                </div>

                <!-- Xóa -->
                <div class="col-md-1 d-flex align-items-end">
                  <button
                    class="btn btn-danger btn-sm"
                    type="button"
                    @click="removeVariant(index)"
                  >
                    ✖
                  </button>
                </div>

              </div>
            </div>

            <button
              class="btn btn-success btn-sm mt-3"
              type="button"
              @click="addVariant"
            >
              + Thêm biến thể
            </button>
          </div>

          <button class="btn btn-primary mt-3" type="button" @click="submitProduct">
            {{ isEditing ? 'Lưu thay đổi' : 'Lưu sản phẩm' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import logoImage from "../../assets/logo.png";
import HeaderAdmin from "../../Header-admin.vue";

/* --------- CONST --------- */
const backendBase = "http://localhost/duan1/backend/";

/* --------- STATE --------- */
const search = ref("");
const filterDiscount = ref("");

const categories = ref([]);
const brands = ref([]);
const colors = ref([]);
const sizes = ref([]);
const products = ref([]);

const isEditing = ref(false);
const editingId = ref(null);

/* ảnh chính: path lưu DB + preview hiển thị */
const productForm = ref({
  name: "",
  category: "",
  thuonghieu: "",
  price: "",
  imageUrl: "",          // đường dẫn lưu DB: "uploads/Product/xxx.jpg"
  extraImageUrls: []     // [{ preview, real }]
});
const mainImagePreview = ref("");        // src hiển thị ảnh chính

/* biến thể: thêm field preview để show */
const variants = ref([
  { color: "", size: "", quantity: 0, imageUrl: "", preview: "" }
]);
/* ---- Format datetime-local từ DB ---- */
const formatDateForInput = (dateString) => {
  if (!dateString) return "";

  const d = new Date(dateString);
  if (isNaN(d.getTime())) return "";

  const year = d.getFullYear();
  const month = String(d.getMonth() + 1).padStart(2, "0");
  const day = String(d.getDate()).padStart(2, "0");
  const hour = String(d.getHours()).padStart(2, "0");
  const minute = String(d.getMinutes()).padStart(2, "0");

  return `${year}-${month}-${day}T${hour}:${minute}`;
};
/* ----- GIẢM GIÁ ----- */
const showDiscount = ref(false);

const discountForm = ref({
  id_sanpham: null,
  originalPrice: 0,
  percent: 0,
  start: "",
  end: ""
});
const openDiscountPopup = (sp) => {
  showDiscount.value = true;

  discountForm.value = {
    id_sanpham: sp.id,
    originalPrice: sp.price,
    percent: sp.giamgiaSP 
      ? Math.round(((sp.price - sp.giamgiaSP) / sp.price) * 100)
      : 0,
    start: formatDateForInput(sp.giamgia_start),
    end: formatDateForInput(sp.giamgia_end)
  };
};

const applyDiscount = async () => {
  const payload = {
    id_sanpham: discountForm.value.id_sanpham,
    percent: discountForm.value.percent,
    giaSP: discountForm.value.originalPrice,
    giamgia_start: discountForm.value.start,
    giamgia_end: discountForm.value.end
  };

  const res = await fetch("http://localhost/duan1/backend/api/Admin/ApplyDiscount.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
  });

  const data = await res.json();

  if (data.status === "success") {
    alert("Cập nhật giảm giá thành công!");
    showDiscount.value = false;
    loadProducts();
  }
};


/* --------- LOAD OPTIONS --------- */
const loadOptions = async () => {
  const [dm, th, ms, sz] = await Promise.all([
    fetch("http://localhost/duan1/backend/api/Admin/GetCategory.php"),
    fetch("http://localhost/duan1/backend/api/Admin/GetBrand.php"),
    fetch("http://localhost/duan1/backend/api/Admin/GetColor.php"),
    fetch("http://localhost/duan1/backend/api/Admin/GetSize.php")
  ]);

  categories.value = await dm.json();
  brands.value = await th.json();
  colors.value = await ms.json();
  sizes.value = await sz.json();
};

/* ----- ẢNH CHÍNH (chỉ dùng khi THÊM) ----- */
const onMainImageChange = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  productForm.value.imageUrl = "uploads/Product/" + file.name;      // lưu DB
  mainImagePreview.value = URL.createObjectURL(file);               // hiển thị
};

/* ----- ẢNH PHỤ ----- */
const onExtraImagesChange = (e) => {
  const files = Array.from(e.target.files);

  files.forEach(file => {
    productForm.value.extraImageUrls.push({
      preview: URL.createObjectURL(file),           // hiển thị
      real: "uploads/Product/" + file.name          // lưu DB
    });
  });

  e.target.value = "";
};

const removeExtraImage = (index) => {
  productForm.value.extraImageUrls.splice(index, 1);
};

/* ----- ẢNH BIẾN THỂ ----- */
const onVariantImageChange = (e, index) => {
  const file = e.target.files[0];
  if (!file) return;
  variants.value[index].imageUrl = "uploads/Product/" + file.name;       // DB
  variants.value[index].preview  = URL.createObjectURL(file);            // hiển thị
};

/* ----- THÊM / XOÁ BIẾN THỂ ----- */
const addVariant = () => {
  variants.value.push({
    color: "",
    size: "",
    quantity: 0,
    imageUrl: "",
    preview: ""
  });
};

const removeVariant = (index) => {
  variants.value.splice(index, 1);
};

/* --------- LOAD PRODUCTS (list) --------- */
const loadProducts = async () => {
  const res = await fetch(
    "http://localhost/duan1/backend/api/Admin/GetProducts.php"
  );
  const data = await res.json();

  products.value = (data.products || []).map((p) => ({
    id: p.id,
    maSP: p.maSP,
    name: p.tenSP,
    image: p.hinhAnhGoc?.startsWith("http")
      ? p.hinhAnhGoc
      : (p.hinhAnhGoc ? backendBase + p.hinhAnhGoc : ""),
    category: p.category,
    brand: p.brand,
    price: p.giaSP,
    giamgiaSP: p.giamgiaSP,
    giamgia_start: p.giamgia_start,
    giamgia_end: p.giamgia_end
  }));
};

/* --------- LOAD PRODUCT DETAIL (edit) --------- */
const startEdit = async (sp) => {
  isEditing.value = true;
  editingId.value = sp.id;
  scrollToForm();

  try {
    const res = await fetch(
      "http://localhost/duan1/backend/api/Admin/GetProductDetail.php?id=" + sp.id
    );
    const data = await res.json();

    if (data.status !== "success") {
      alert(data.message || "Không load được chi tiết sản phẩm");
      return;
    }

    const p = data.product;

    // Ảnh chính: lưu path DB + set preview từ backendBase
    productForm.value.imageUrl = p.hinhAnhGoc || "";
    mainImagePreview.value = p.hinhAnhGoc
      ? (p.hinhAnhGoc.startsWith("http")
          ? p.hinhAnhGoc
          : backendBase + p.hinhAnhGoc)
      : "";

    // Ảnh phụ: chuẩn hóa về [{preview, real}]
    productForm.value.extraImageUrls = (data.extraImages || []).map(img => ({
      preview: backendBase + img.url,      // hiển thị
      real: img.url                        // lưu DB
    }));

    // Thông tin cơ bản
    productForm.value.name       = p.tenSP;
    productForm.value.category   = p.id_danhmuc;
    productForm.value.thuonghieu = p.id_thuonghieu;
    productForm.value.price      = p.giaSP;

    // Biến thể
    variants.value = (data.variants || []).map((v) => ({
      color: v.color,
      size: v.size,
      quantity: v.quantity,
      imageUrl: v.imageUrl || "",
      preview: v.imageUrl ? (backendBase + v.imageUrl) : ""
    }));

    if (variants.value.length === 0) {
      variants.value.push({
        color: "",
        size: "",
        quantity: 0,
        imageUrl: "",
        preview: ""
      });
    }

  } catch (err) {
    console.error(err);
    alert("Lỗi load chi tiết sản phẩm");
  }
};
/* --------- XÓA SẢN PHẨM --------- */
const deleteProduct = async (id) => {
  if (!confirm("Bạn có chắc muốn xóa sản phẩm này không?")) return;

  const res = await fetch("http://localhost/duan1/backend/api/Admin/DeleteProduct.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id_sanpham: id })
  });

  const data = await res.json();

  if (data.status === "success") {
    alert("Xóa thành công!");
    loadProducts();
  } else {
    alert("Lỗi: " + data.message);
  }
};
/* PAGINATION */
const page = ref(1);
const perPage = 5;

const filteredProducts = computed(() =>
  products.value.filter((p) => {

    // tìm theo: Tên SP + Danh mục + Thương hiệu
    const searchText = search.value.toLowerCase();
    const matchSearch =
      p.name.toLowerCase().includes(searchText) ||
      p.category.toLowerCase().includes(searchText) ||
      p.brand.toLowerCase().includes(searchText);

    // lọc giảm giá
    const matchDiscount =
      filterDiscount.value === ""
        ? true
        : filterDiscount.value === "yes"
        ? p.giamgiaSP > 0
        : p.giamgiaSP == 0 || p.giamgiaSP == null;

    return matchSearch && matchDiscount;
  })
);

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredProducts.value.length / perPage))
);

const paginatedProducts = computed(() => {
  const start = (page.value - 1) * perPage;
  return filteredProducts.value.slice(start, start + perPage);
});

/* --------- SUBMIT PRODUCT (Add + Edit) --------- */
const submitProduct = async () => {
  const payload = {
    tenSP: productForm.value.name,
    giaSP: productForm.value.price,
    id_danhmuc: productForm.value.category,
    id_thuonghieu: productForm.value.thuonghieu,
    imageUrl: productForm.value.imageUrl,
    // gửi về backend chỉ path thật, không gửi preview
    extraImages: productForm.value.extraImageUrls.map(i => i.real),
    variants: variants.value.map(v => ({
      color: v.color,
      size: v.size,
      quantity: v.quantity,
      imageUrl: v.imageUrl
    }))
  };

  const url = isEditing.value
    ? "http://localhost/duan1/backend/api/Admin/UpdateProduct.php"
    : "http://localhost/duan1/backend/api/Admin/AddProduct.php";

  if (isEditing.value) {
    payload.id_sanpham = editingId.value;
  }

  const res = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
  });

  const data = await res.json();

  if (data.status === "success") {
    alert(isEditing.value ? "Cập nhật sản phẩm thành công!" : "Thêm sản phẩm thành công!");
    resetForm();
    loadProducts();
  } else {
    alert("Lỗi: " + (data.message || ""));
  }
};

/* RESET */
const resetForm = () => {
  productForm.value = {
    name: "",
    category: "",
    thuonghieu: "",
    price: "",
    imageUrl: "",
    extraImageUrls: []
  };
  mainImagePreview.value = "";
  variants.value = [{ color: "", size: "", quantity: 0, imageUrl: "", preview: "" }];
  isEditing.value = false;
  editingId.value = null;
};

const scrollToForm = () => {
  document.getElementById("add-form")?.scrollIntoView({
    behavior: "smooth",
    block: "start"
  });
};

const formatCurrency = (val) =>
  new Intl.NumberFormat("vi-VN").format(val || 0) + " đ";

/* INIT */
loadOptions();
loadProducts();
</script>

<style scoped>
.logo-img {
  width: 120px;
  height: auto;
  margin-left: 25px;
}

.sidebar {
  width: 240px;
  background: linear-gradient(180deg, #1b1c1f, #111);
  color: white;
  position: fixed;
  left: 0;
  top: 0;
  bottom: 0;
  overflow-y: auto;
}

.sidebar-menu {
  padding: 0;
  display: flex;
  flex-direction: column;
}

.menu-item {
  padding: 12px 20px;
  margin: 6px 12px;
  border-radius: 8px;
  font-size: 15px;
  display: flex;
  align-items: center;
  gap: 12px;
  color: white;
  text-decoration: none;
  transition: 0.25s ease;
}

.menu-item:hover {
  background: #2c2d31;
  transform: translateX(5px);
}

.menu-item.active {
  background: #0d6efd;
  color: white;
  box-shadow: 0 4px 10px rgba(13, 110, 253, 0.4);
  transform: translateX(5px);
}

.main-content {
  margin-left: 240px !important;
  margin-top: 70px;
  overflow-y: visible;
  height: auto;
}

.thumb {
  width: 60px;
  height: 60px;
  object-fit: cover;
}

header.admin-header {
  position: fixed;
  top: 0;
  left: 240px;
  right: 0;
  z-index: 999;
}

.content-section {
  padding-top: 80px;
}

.variant-box {
  background: #f8f9fa;
  border-radius: 6px;
}

.preview-img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 6px;
  border: 1px solid #ddd;
}

.delete-img-btn {
  position: absolute;
  top: -6px;
  right: -6px;
  border-radius: 50%;
  padding: 2px 6px;
  font-size: 12px;
}
.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 99999;
}

.popup-box {
  background: white;
  padding: 25px;
  width: 400px;
  border-radius: 8px;
}
table th {
  white-space: nowrap;
  text-align: center;
  vertical-align: middle;
}
.action-cell {
  white-space: nowrap;
}
.action-cell button {
  display: inline-block;
  vertical-align: middle;
}
table td:not(.no-resize) {
  font-size: 13px;
  vertical-align: middle;
}
.filter-select {
  width: 160px;
}
</style>
