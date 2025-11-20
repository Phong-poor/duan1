<template>
  <div class="app-wrapper d-flex">
    <!-- Sidebar -->
    <aside class="sidebar bg-dark text-white p-3">
      <img :src="logoImage" alt="Logo" class="logo-img">
      <ul class="sidebar-menu">
        <router-link to="/" class="menu-item" active-class="active">
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
        <router-link to="Quanlydonhang" class="menu-item" active-class="active">
          <i class="fa-solid fa-cart-shopping"></i> Đơn hàng
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
          <button class="btn btn-primary me-2 back" @click="scrollToForm">Thêm sản phẩm</button>
        </div>

        <!-- Search -->
        <input
          v-model="search"
          type="text"
          class="form-control mb-3"
          placeholder="🔍 Tìm sản phẩm..."
        />

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
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="sp in paginatedProducts" :key="sp.id">
              <td>{{ sp.id }}</td>
              <td><img :src="sp.image" class="thumb" /></td>
              <td>{{ sp.name }}</td>
              <td>{{ sp.category }}</td>
              <td>{{ sp.brand }}</td>
              <td>{{ sp.price }} đ</td>
              <td>{{ sp.discount ?? 0 }}%</td>
              <td>
                <button class="btn btn-warning btn-sm" @click="scrollToForm">Sửa</button>
                <button class="btn btn-danger btn-sm ms-2">Xóa</button>
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

        <!-- Add Product Form -->
        <div class="card p-4 mt-4" id="add-form">
          <h4 class="fw-bold mb-3">Thêm sản phẩm</h4>

          <!-- Ảnh chính -->
          <div class="mb-3">
            <label class="form-label">Hình ảnh chính</label>
            <input
              type="file"
              class="form-control"
              :class="{ 'is-invalid': errors.mainImage }"
              @change="onMainImageChange"
            />
            <div v-if="errors.mainImage" class="text-danger small mt-1">
              {{ errors.mainImage }}
            </div>
          </div>

          <!-- Tên sản phẩm -->
          <div class="mb-3">
            <label class="form-label">Tên sản phẩm</label>
            <input
              v-model="productForm.name"
              type="text"
              class="form-control"
              :class="{ 'is-invalid': errors.name }"
            />
            <div v-if="errors.name" class="text-danger small mt-1">
              {{ errors.name }}
            </div>
          </div>

          <!-- Danh mục -->
          <div class="mb-3">
            <label class="form-label">Danh mục</label>
            <select
              v-model="productForm.category"
              class="form-select"
              :class="{ 'is-invalid': errors.category }"
            >
              <option disabled value="">-- Chọn danh mục --</option>
              <option v-for="dm in categories" :key="dm.id" :value="dm.id">
                {{ dm.ten }}
              </option>
            </select>
            <div v-if="errors.category" class="text-danger small mt-1">
              {{ errors.category }}
            </div>
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
            <div v-if="errors.thuonghieu" class="text-danger small mt-1">
              {{ errors.thuonghieu }}
            </div>
          </div>

          <!-- Giá -->
          <div class="mb-3">
            <label class="form-label">Giá</label>
            <input
              v-model.number="productForm.price"
              type="number"
              class="form-control"
              :class="{ 'is-invalid': errors.price }"
            />
            <div v-if="errors.price" class="text-danger small mt-1">
              {{ errors.price }}
            </div>
          </div>

          <!-- Ảnh phụ -->
          <div class="mb-3">
            <label class="form-label">Ảnh phụ (nhiều ảnh, không bắt buộc)</label>
            <input type="file" multiple class="form-control" @change="onExtraImagesChange" />

            <!-- Preview ảnh phụ -->
            <div class="d-flex flex-wrap mt-3 gap-2">
              <div
                v-for="(img, index) in extraImagesPreview"
                :key="index"
                class="position-relative"
              >
                <img :src="img" class="preview-img" />
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
          <div class="variant-box border p-3 mb-3">
            <h5 class="fw-semibold">Biến thể (màu + size + số lượng)</h5>

            <div
              v-for="(v, index) in variants"
              :key="index"
              class="border p-3 rounded mt-2"
            >
              <div class="row g-2">
                <div class="col-md-4">
                  <label class="form-label">Màu sắc</label>
                  <select
                    v-model="v.color"
                    class="form-select"
                    :class="{ 'is-invalid': errors['variant_' + index] }"
                  >
                    <option disabled value="">-- Chọn màu --</option>
                    <option v-for="c in colors" :key="c.id" :value="c.id">
                      {{ c.ten }}
                    </option>
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label">Size</label>
                  <select
                    v-model="v.size"
                    class="form-select"
                    :class="{ 'is-invalid': errors['variant_' + index] }"
                  >
                    <option disabled value="">-- Chọn size --</option>
                    <option v-for="s in sizes" :key="s.id" :value="s.id">
                      {{ s.size }}
                    </option>
                  </select>
                </div>

                <div class="col-md-3">
                  <label class="form-label">Số lượng</label>
                  <input
                    v-model.number="v.quantity"
                    type="number"
                    class="form-control"
                    :class="{ 'is-invalid': errors['variant_' + index] }"
                  />
                </div>

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

              <!-- lỗi cho từng biến thể -->
              <div v-if="errors['variant_' + index]" class="text-danger small mt-1">
                {{ errors['variant_' + index] }}
              </div>
            </div>

            <button class="btn btn-success btn-sm mt-2" type="button" @click="addVariant">
              ➕ Thêm biến thể
            </button>
          </div>

          <button class="btn btn-primary" type="button" @click="saveProduct">
            Lưu sản phẩm
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

/* ------------ STATE ------------ */

const search = ref("");

// danh mục & thương hiệu từ API
const categories = ref([]);
const brands = ref([]);

// danh sách sản phẩm (hiển thị trên bảng)
const products = ref([]);

// form thêm sản phẩm
const productForm = ref({
  name: "",
  category: "",
  thuonghieu: "",
  price: "",
});

// ảnh
const mainImage = ref(null);
const extraImages = ref([]);
const extraImagesPreview = ref([]);

// biến thể
const variants = ref([{ color: "", size: "", quantity: 0 }]);
const errors = ref({});
// màu & size (tạm thời static, sau này có API thì đổi)
const colors = ref([
  { id: 1, ten: "Đỏ" },
  { id: 2, ten: "Đen" },
  { id: 3, ten: "Xanh" },
]);

const sizes = ref([
  { id: 1, size: "36" },
  { id: 2, size: "37" },
  { id: 3, size: "38" },
  { id: 4, size: "39" },
  { id: 5, size: "40" },
  { id: 6, size: "41" },
  { id: 7, size: "42" },
]);

/* ------------ LOAD STATIC OPTIONS (DM + TH) ------------ */

const loadStaticOptions = async () => {
  try {
    const dm = await fetch("http://localhost/duan1/backend/api/Admin/GetCategory.php");
    categories.value = await dm.json();

    const th = await fetch("http://localhost/duan1/backend/api/Admin/GetThuongHieu.php");
    brands.value = await th.json();
  } catch (err) {
    console.error("Lỗi load dữ liệu danh mục / thương hiệu:", err);
  }
};

/* ------------ LOAD PRODUCT LIST (product.json) ------------ */

const loadProducts = async () => {
  try {
    const res = await fetch("http://localhost/duan1/backend/api/Admin/GetProducts.php");
    const data = await res.json();

    products.value = data.products.map(p => ({
      id: p.id,
      name: p.tenSP,
      image: p.hinhAnhGoc,
      category: p.category,
      brand: p.brand,
      price: p.giaSP,
      discount: 0
    }));
  } catch (err) {
    console.error("Lỗi load sản phẩm:", err);
  }
};


/* ------------ PAGINATION ------------ */

const page = ref(1);
const perPage = 5;

const filteredProducts = computed(() =>
  products.value.filter((p) =>
    (p.name || "").toLowerCase().includes(search.value.toLowerCase())
  )
);

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredProducts.value.length / perPage))
);

const paginatedProducts = computed(() => {
  const start = (page.value - 1) * perPage;
  return filteredProducts.value.slice(start, start + perPage);
});

/* ------------ IMAGE HANDLER ------------ */

const onMainImageChange = (e) => {
  mainImage.value = e.target.files[0] || null;
};

const onExtraImagesChange = (e) => {
  const files = Array.from(e.target.files);
  files.forEach((file) => {
    extraImages.value.push(file);
    const previewURL = URL.createObjectURL(file);
    extraImagesPreview.value.push(previewURL);
  });
};

const removeExtraImage = (index) => {
  extraImages.value.splice(index, 1);
  extraImagesPreview.value.splice(index, 1);
};

/* ------------ VARIANT HANDLER ------------ */

const addVariant = () => {
  variants.value.push({ color: "", size: "", quantity: 0 });
};

const removeVariant = (index) => {
  variants.value.splice(index, 1);
};
/* ------------ VALIDATE FORM ------------ */

const validateForm = () => {
  errors.value = {};

  if (!productForm.value.name.trim()) {
    errors.value.name = "Tên sản phẩm không được để trống";
  }

  if (!productForm.value.category) {
    errors.value.category = "Vui lòng chọn danh mục";
  }

  if (!productForm.value.thuonghieu) {
    errors.value.thuonghieu = "Vui lòng chọn thương hiệu";
  }

  if (!productForm.value.price || productForm.value.price <= 0) {
    errors.value.price = "Giá sản phẩm phải lớn hơn 0";
  }

  if (!mainImage.value) {
    errors.value.mainImage = "Vui lòng chọn ảnh chính";
  }

  // VALIDATE BIẾN THỂ
  variants.value.forEach((v, index) => {
    if (!v.color || !v.size || !v.quantity || v.quantity <= 0) {
      errors.value[`variant_${index}`] = "Biến thể phải đầy đủ màu - size - số lượng";
    }
  });

  return Object.keys(errors.value).length === 0;
};
/* ------------ SAVE PRODUCT (CALL API) ------------ */

const saveProduct = async () => {
  try {
    const fd = new FormData();

    fd.append("tenSP", productForm.value.name);
    fd.append("maSP", "SP_" + Date.now());
    fd.append("giaSP", productForm.value.price || 0);
    fd.append("mota", "");
    fd.append("id_danhmuc", productForm.value.category);
    fd.append("id_thuonghieu", productForm.value.thuonghieu);

    if (mainImage.value) {
      fd.append("mainImage", mainImage.value);
    }

    extraImages.value.forEach((img) => {
      fd.append("extraImages[]", img);
    });

    fd.append("variants", JSON.stringify(variants.value));

    const res = await fetch(
      "http://localhost/duan1/backend/api/Admin/ProductController.php",
      {
        method: "POST",
        body: fd,
      }
    );

    const data = await res.json();
    if (data.status === "success") {
      alert("Thêm sản phẩm thành công!");
      await loadProducts();
      resetForm();
    } else {
      alert("Lỗi thêm sản phẩm: " + (data.message || ""));
    }
  } catch (err) {
    console.error("Lỗi saveProduct:", err);
    alert("Có lỗi khi thêm sản phẩm, kiểm tra console.");
  }
};

/* ------------ RESET FORM ------------ */

const resetForm = () => {
  productForm.value = {
    name: "",
    category: "",
    thuonghieu: "",
    price: "",
  };
  mainImage.value = null;
  extraImages.value = [];
  extraImagesPreview.value = [];
  variants.value = [{ color: "", size: "", quantity: 0 }];
};

/* ------------ SCROLL ------------ */

const scrollToForm = () => {
  const form = document.getElementById("add-form");
  if (form) form.scrollIntoView({ behavior: "smooth", block: "start" });
};

/* ------------ INIT ------------ */

loadStaticOptions();
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

.brand-title {
  font-size: 18px;
  margin-top: 10px;
  font-weight: 600;
  color: #ffffffd9;
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

.hover-item:hover {
  background: #444;
  cursor: pointer;
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

.variant-box h5 {
  margin-bottom: 10px;
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
</style>
