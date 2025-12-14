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
        <router-link to="/Quanlylienhe" class="menu-item" active-class="active">
          <i class="fa-solid fa-message"></i> Liên hệ
        </router-link>
        <router-link to="/Quanlybaiviet" class="menu-item" active-class="active">
          <i class="fa-solid fa-newspaper"></i> Bài viết
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
          <h3 class="fw-bold">
            {{ tab === 'baiviet' ? 'Quản lý bài viết' : tab === 'danhmuc' ? 'Quản lý danh mục' : 'Quản lý tags' }}
          </h3>
          <button class="btn btn-primary me-2 back" @click="openForm">
            {{
                tab === 'baiviet'
                ? (isEditing ? 'Sửa bài viết' : 'Thêm bài viết')
                : tab === 'danhmuc'
                ? 'Thêm danh mục'
                : 'Thêm tag'
            }}
            </button>
        </div>

        <!-- 3 NÚT TAB + FILTER BÊN PHẢI -->
        <div class="d-flex justify-content-between mb-3">
            <!-- 3 BUTTONS -->
            <div class="d-flex gap-2">
                <button 
                class="btn"
                :class="tab === 'baiviet' ? 'btn-primary' : 'btn-outline-primary'"
                @click="tab = 'baiviet'"
                >
                Bài viết
                </button>
                <button 
                class="btn"
                :class="tab === 'danhmuc' ? 'btn-primary' : 'btn-outline-primary'"
                @click="tab = 'danhmuc'"
                >
                Danh mục
                </button>
                <button 
                class="btn"
                :class="tab === 'tags' ? 'btn-primary' : 'btn-outline-primary'"
                @click="tab = 'tags'"
                >
                Tags
                </button>
            </div>
            <!-- DROPDOWN FILTER (GIỮ NGUYÊN) -->
            <select v-model="filterDiscount" class="form-select filter-select" style="width:150px">
                <option value="">Tất cả</option>
                <option value="yes">Đang giảm giá</option>
                <option value="no">Chưa giảm giá</option>
            </select>
        </div>

        <!-- Product Table -->
        <table 
            class="table table-bordered text-center" 
            v-if="tab === 'baiviet' && !showForm"
        >
            <thead class="table-secondary">
                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Tiêu đề</th>
                    <th>Danh mục</th>
                    <th>Ngày tạo</th>
                    <th>Tác giả</th>
                    <th>Độ hot</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="bv in paginatedPosts" :key="bv.id_baiviet">
                    
                    <!-- ID -->
                    <td>{{ bv.id_baiviet }}</td>

                    <!-- Thumbnail -->
                    <td>
                        <img 
                            :src="'https://miraeshoes.shop/backend/uploads/Baiviet/' + bv.thumbnail" 
                            v-if="bv.thumbnail" 
                            class="thumb"
                            style="width:60px;height:60px;object-fit:cover;"
                        >
                    </td>
                    
                    <!-- Title -->
                    <td>{{ bv.title }}</td>

                    <!-- DANH MỤC -->
                    <td>{{ bv.tenDM || "Không có danh mục" }}</td>

                    <!-- Ngày tạo -->
                    <td>{{ bv.created_at }}</td>
                    <td>{{ bv.creator }}</td>
                    <td>
                      <span 
                        v-if="bv.hot == 1" 
                        class="badge bg-danger"
                      >
                        🔥 Tin hot
                      </span>

                      <span 
                        v-else 
                        class="badge bg-secondary"
                      >
                        Bình thường
                      </span>
                    </td>
                    <!-- Trạng thái -->
                    <td>
                        <span class="badge bg-success" v-if="bv.status === 'public'">Hiển thị</span>
                        <span class="badge bg-secondary" v-else>Ẩn</span>
                    </td>

                    <!-- Hành động -->
                    <td class="action-cell">
                        <button 
                            class="btn btn-warning btn-sm" 
                            @click="editBaiViet(bv)"
                        >
                            Sửa
                        </button>

                        <button 
                            class="btn btn-danger btn-sm ms-2" 
                            @click="deleteBaiViet(bv.id_baiviet)"
                        >
                            Xóa
                        </button>
                        <button
                          class="btn btn-sm ms-2"
                          :class="bv.hot == 1 ? 'btn-secondary' : 'btn-danger'"
                          @click="toggleHot(bv)"
                        >
                          {{ bv.hot == 1 ? '⬇️ Tin bth' : '🔥 Tin hot' }}
                        </button>
                    </td>

                </tr>
            </tbody>
        </table>
        <table class="table table-bordered text-center" v-if="tab === 'danhmuc' && !showForm">
            <thead class="table-secondary">
                <tr>
                    <th>ID</th>
                    <th>Tên danh mục</th>
                    <th>Slug</th>
                    <th>Mô tả</th>
                    <th>Hành động</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="dm in paginatedCategories" :key="dm.id_danhmuc">
                    <td>{{ dm.id_danhmuc }}</td>
                    <td>{{ dm.tenDM }}</td>
                    <td>{{ dm.slug }}</td>
                    <td>{{ dm.mota }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" @click="editDanhMuc(dm)">Sửa</button>
                        <button class="btn btn-danger btn-sm ms-2" @click="deleteDanhMuc(dm.id_danhmuc)">Xóa</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="table table-bordered text-center" v-if="tab === 'tags' && !showForm">
            <thead class="table-secondary">
                <tr>
                    <th>ID</th>
                    <th>Tag</th>
                    <th>Slug</th>
                    <th>Hành động</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="t in paginatedTags" :key="t.id_tag">
                    <td>{{ t.id_tag }}</td>
                    <td>{{ t.tag }}</td>
                    <td>{{ t.slug }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" @click="editTag(t)">Sửa</button>
                        <button class="btn btn-danger btn-sm ms-2" @click="deleteTag(t.id_tag)">Xóa</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Pagination -->
        <div class="d-flex justify-content-center my-3 gap-2" 
            v-if="tab === 'baiviet' && !showForm">

            <button class="btn btn-secondary btn-sm"
                    :disabled="pagePost === 1"
                    @click="pagePost--">Trước</button>

            <span>Trang {{ pagePost }} / {{ totalPagesPost }}</span>

            <button class="btn btn-secondary btn-sm"
                    :disabled="pagePost === totalPagesPost"
                    @click="pagePost++">Sau</button>
        </div>
        <div class="d-flex justify-content-center my-3 gap-2" 
            v-if="tab === 'danhmuc'">

            <button class="btn btn-secondary btn-sm"
                    :disabled="pageCate === 1"
                    @click="pageCate--">Trước</button>

            <span>Trang {{ pageCate }} / {{ totalPagesCate }}</span>

            <button class="btn btn-secondary btn-sm"
                    :disabled="pageCate === totalPagesCate"
                    @click="pageCate++">Sau</button>
        </div>
        <div class="d-flex justify-content-center my-3 gap-2" 
            v-if="tab === 'tags'">

            <button class="btn btn-secondary btn-sm"
                    :disabled="pageTag === 1"
                    @click="pageTag--">Trước</button>

            <span>Trang {{ pageTag }} / {{ totalPagesTag }}</span>

            <button class="btn btn-secondary btn-sm"
                    :disabled="pageTag === totalPagesTag"
                    @click="pageTag++">Sau</button>
        </div>
        <!-- FORM BÀI VIẾT -->
        <div class="card p-4 mt-4" v-if="showForm && tab === 'baiviet'">
            <h4 class="fw-bold mb-3">
                {{ isEditing ? "Sửa bài viết" : "Thêm bài viết" }}
            </h4>

            <!-- Thumbnail -->
            <div class="mb-3">
                <label class="fw-bold">Ảnh Thumbnail</label>
                <input type="file" class="form-control" @change="onThumbChange">

                <!-- preview -->
                <img 
                    v-if="thumbPreview" 
                    :src="thumbPreview" 
                    class="preview-img mt-2"
                    style="max-height:120px; border-radius:6px;"
                >
            </div>
            <!-- Thumbnail ALT -->
            <div class="mb-3">
                <label class="fw-bold">Thumbnail ALT (SEO)</label>
                <input 
                    v-model="form.thumbnail_alt" 
                    class="form-control" 
                    placeholder="Mô tả ảnh... (tự động lấy tên bài nếu bỏ trống)"
                >
            </div>

            <!-- Thumbnail TITLE -->
            <div class="mb-3">
                <label class="fw-bold">Thumbnail TITLE (SEO)</label>
                <input 
                    v-model="form.thumbnail_title" 
                    class="form-control" 
                    placeholder="Tiêu đề ảnh... (tự động lấy tên bài nếu bỏ trống)"
                >
            </div>
            <!-- Title -->
            <div class="mb-3">
                <label class="fw-bold">Tiêu đề</label>
                <input v-model="form.title" class="form-control" placeholder="Nhập tiêu đề...">
            </div>

            <!-- Slug -->
            <div class="mb-3">
                <label class="fw-bold">Slug (SEO URL)</label>
                <input v-model="form.slug" class="form-control" placeholder="Không nhập sẽ auto tạo">
            </div>

            <!-- Danh mục -->
            <div class="mb-3">
                <label class="fw-bold">Danh mục</label>
                <select v-model="form.id_danhmuc" class="form-select">
                    <option value="">-- Chọn danh mục --</option>
                    <option 
                        v-for="dm in DanhMucBaiViet" 
                        :key="dm.id_danhmuc"
                        :value="dm.id_danhmuc"
                    >
                        {{ dm.tenDM }}
                    </option>
                </select>
            </div>

            <!-- Content -->
            <div class="mb-3">
                <label class="fw-bold">Nội dung bài viết</label>
                <div id="quillEditor" class="quill-editor"></div>
            </div>

            <!-- SEO title -->
            <div class="mb-3">
                <label class="fw-bold">SEO Title</label>
                <input v-model="form.seo_title" class="form-control" placeholder="SEO title...">
            </div>

            <!-- SEO description -->
            <div class="mb-3">
                <label class="fw-bold">SEO Description</label>
                <textarea 
                    v-model="form.seo_description" 
                    rows="3" 
                    class="form-control"
                    placeholder="SEO mô tả..."
                ></textarea>
            </div>

            <!-- Tags -->
            <div class="mb-3">
                <label class="fw-bold">Tags</label>

                <div class="d-flex flex-wrap gap-2 mt-2">
                    <div 
                        v-for="t in TagsBaiViet" 
                        :key="t.id_tag" 
                        class="form-check"
                    >
                        <input 
                            type="checkbox" 
                            class="form-check-input"
                            :value="t.id_tag"
                            v-model="form.tags"
                        >
                        <label class="form-check-label">{{ t.tag }}</label>
                    </div>
                </div>
            </div>
            <!-- Status -->
            <div class="mb-3">
                <label class="fw-bold">Trạng thái</label>
                <select v-model="form.status" class="form-select">
                    <option value="public">Hiển thị</option>
                    <option value="draft">Ẩn</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary" @click="saveBaiViet">Lưu</button>
                <button class="btn btn-secondary" @click="closeForm">Hủy</button>
            </div>
        </div>
        <!-- FORM DANH MỤC -->
        <div class="card p-4 mt-4" v-if="showForm && tab === 'danhmuc'">
            <h4 class="fw-bold mb-3">
                {{ isEditing ? "Sửa danh mục" : "Thêm danh mục" }}
            </h4>

            <!-- Tên danh mục -->
            <div class="mb-3">
                <label class="fw-bold">Tên danh mục</label>
                <input 
                    v-model="cateForm.tenDM" 
                    class="form-control"
                    placeholder="Nhập tên danh mục..."
                    @input="cateForm.slug = slugify(cateForm.tenDM)"
                >
            </div>

            <!-- Slug -->
            <div class="mb-3">
                <label class="fw-bold">Slug</label>
                <input 
                    v-model="cateForm.slug" 
                    class="form-control"
                    placeholder="Slug sẽ được tạo tự động..."
                >
            </div>

            <!-- Mô tả -->
            <div class="mb-3">
                <label class="fw-bold">Mô tả</label>
                <textarea 
                    class="form-control"
                    rows="3"
                    placeholder="Nhập mô tả danh mục..."
                    v-model="cateForm.mota"
                ></textarea>
            </div>

            <!-- Buttons -->
            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary" @click="saveDanhMuc">
                {{ isEditing ? "Cập nhật" : "Thêm mới" }}
                </button>
                <button class="btn btn-secondary" @click="closeForm">Hủy</button>
            </div>
        </div>
        <!-- FORM THÊM TAG -->
        <div class="card p-4 mt-4" v-if="showForm && tab === 'tags'">
            <h4 class="fw-bold mb-3">
                {{ isEditing ? "Sửa tag" : "Thêm tag" }}
            </h4>

            <div class="mb-3">
                <label>Tên tag</label>
                <input type="text" class="form-control" v-model="tagForm.tag">
            </div>

            <div class="mb-3">
                <label>Slug</label>
                <input type="text" class="form-control" v-model="tagForm.slug">
            </div>

            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-primary" @click="saveTag">Lưu</button>
                <button class="btn btn-secondary" @click="closeForm">Hủy</button>
            </div>
        </div>
        </div>
      </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import logoImage from "../../assets/logo.png";
import HeaderAdmin from "../../Header-admin.vue";
import Quill from "quill";
import "quill/dist/quill.snow.css";

let quill = null;

/* ------------------ STATE ------------------ */
const tab = ref("baiviet");
const showForm = ref(false);
const isEditing = ref(false);

/* ---------- Pagination ---------- */
const perPage = 5;
const pagePost = ref(1);
const pageCate = ref(1);
const pageTag = ref(1);

/* ---------- Data ---------- */
const baiviet = ref([]);
const DanhMucBaiViet = ref([]);
const TagsBaiViet = ref([]);

/* ---------- Form Bài Viết ---------- */
const currentUser = JSON.parse(localStorage.getItem("currentUser"));
const form = ref({
  id_baiviet: "",
  title: "",
  slug: "",
  id_danhmuc: "",
  content: "",
  seo_title: "",
  seo_description: "",
  thumbnail: "",
  thumbnail_alt: "",
  thumbnail_title: "",
  tags: [],
  status: "public",
  hot: 0,
  creator: currentUser && currentUser.role === "admin" 
    ? currentUser.tenKH 
    : ""
});

/* ---------- Form Danh Mục ---------- */
const cateForm = ref({
  id_danhmuc: "",
  tenDM: "",
  slug: "",
  mota: "",
});

/* ---------- Form Tag ---------- */
const tagForm = ref({
  id_tag: "",
  tag: "",
  slug: "",
});

const thumbPreview = ref("");

/* ------------------ API ------------------ */
const API_DM = "https://miraeshoes.shop/backend/api/Admin/DanhMucBaiViet.php";
const API_TAG = "https://miraeshoes.shop/backend/api/Admin/TagsBaiViet.php";
const API_BAIVIET = "https://miraeshoes.shop/backend/api/Admin/BaiViet.php";

/* ------------------ LOAD API ------------------ */
async function loadDanhMuc() {
  const res = await fetch(API_DM);
  DanhMucBaiViet.value = await res.json();
}

async function loadTags() {
  const res = await fetch(API_TAG);
  TagsBaiViet.value = await res.json();
}

async function loadBaiViet() {
  const res = await fetch(API_BAIVIET);
  baiviet.value = await res.json();
}

/* ------------------ OPEN FORM ------------------ */
const openForm = () => {
  showForm.value = true;
  isEditing.value = false;

  if (tab.value === "baiviet") {
    form.value = {
      id_baiviet: "",
      title: "",
      slug: "",
      id_danhmuc: "",
      content: "",
      seo_title: "",
      seo_description: "",
      thumbnail: "",
      tags: [],
      status: "public",
      hot: 0,
      creator: currentUser && currentUser.role === "admin" 
        ? currentUser.tenKH 
        : ""
    };
    thumbPreview.value = "";
    initQuill();
  }

  if (tab.value === "danhmuc") {
    cateForm.value = { id: "", tenDM: "", slug: "", mota: "" };
  }

  if (tab.value === "tags") {
    tagForm.value = { id: "", tag: "", slug: "" };
  }
};

const closeForm = () => {
  showForm.value = false;
  if (quill) quill = null;
};

/* ------------------ CRUD BÀI VIẾT ------------------ */
async function saveBaiViet() {
    form.value.slug = slugify(form.value.title);
    form.value.content = quill.root.innerHTML;

    const method = isEditing.value ? "PUT" : "POST";

    await fetch(API_BAIVIET, {
        method,
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(form.value)
    });

    alert(isEditing.value ? "Cập nhật bài viết thành công!" : "Thêm bài viết thành công!");
    await loadBaiViet();
    closeForm();
}

function editBaiViet(bv) {
  showForm.value = true;
  isEditing.value = true;

  form.value = {
    id_baiviet: bv.id_baiviet,
    title: bv.title,
    slug: bv.slug,
    id_danhmuc: bv.id_danhmuc,
    content: bv.content,
    seo_title: bv.seo_title,
    seo_description: bv.seo_description,
    thumbnail: bv.thumbnail,
    tags: bv.tags ? bv.tags.map(t => t.id_tag) : [],
    status: bv.status,
    creator: bv.creator
  };

  thumbPreview.value =
    "https://miraeshoes.shop/backend/uploads/Baiviet/" + bv.thumbnail;

  initQuill(bv.content);
}

async function deleteBaiViet(id) {
  if (!confirm("Bạn có chắc muốn xóa bài viết này?")) return;

  await fetch(API_BAIVIET + "?id=" + id, { method: "DELETE" });

  alert("Xóa bài viết thành công!");
  await loadBaiViet();
}

/* ------------------ UPLOAD THUMBNAIL ------------------ */
const API_UPLOAD = "https://miraeshoes.shop/backend/api/Admin/UploadImage.php";
async function onThumbChange(e) {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append("file", file);

    const res = await fetch(API_UPLOAD, {
        method: "POST",
        body: formData
    });

    const data = await res.json();

    if (data.success) {
        form.value.thumbnail = data.fileName;
        thumbPreview.value = "https://miraeshoes.shop/backend/uploads/Baiviet/" + data.fileName;
    }
}

/* ------------------ CRUD DANH MỤC ------------------ */
async function saveDanhMuc() {
  cateForm.value.slug = slugify(cateForm.value.tenDM);

  const method = cateForm.value.id_danhmuc ? "PUT" : "POST";

  const res = await fetch(API_DM, {
    method,
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(cateForm.value),
  });

  const data = await res.json();

  if (!data.success) {
    alert(data.message || "Lỗi khi lưu danh mục");
    return;
  }

  alert("Lưu danh mục thành công!");
  await loadDanhMuc();
  closeForm();
}

async function deleteDanhMuc(id) {
  if (!confirm("Xóa danh mục này?")) return;
  await fetch(API_DM + "?id=" + id, { method: "DELETE" });

  await loadDanhMuc();
}

function editDanhMuc(dm) {
  showForm.value = true;
  isEditing.value = true;
  cateForm.value = { ...dm };
}

/* ------------------ CRUD TAG ------------------ */
async function saveTag() {
  tagForm.value.slug = slugify(tagForm.value.tag);

  const method = tagForm.value.id_tag ? "PUT" : "POST";

  await fetch(API_TAG, {
    method,
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(tagForm.value),
  });

  alert("Lưu tag thành công!");
  await loadTags();
  closeForm();
}

async function deleteTag(id) {
  if (!confirm("Xóa tag này?")) return;
  await fetch(API_TAG + "?id=" + id, { method: "DELETE" });

  await loadTags();
}

function editTag(t) {
  showForm.value = true;
  isEditing.value = true;
  tagForm.value = { ...t };
}

/* ------------------ HELPER ------------------ */
function slugify(str) {
  return str
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/(^-|-$)/g, "");
}

/* ------------------ PAGINATION ------------------ */
const filterDiscount = ref("");

const totalPagesPost = computed(() =>
  Math.max(1, Math.ceil(baiviet.value.length / perPage))
);

const paginatedPosts = computed(() => {
  const start = (pagePost.value - 1) * perPage;
  return baiviet.value.slice(start, start + perPage);
});

const totalPagesCate = computed(() =>
  Math.max(1, Math.ceil(DanhMucBaiViet.value.length / perPage))
);

const totalPagesTag = computed(() =>
  Math.max(1, Math.ceil(TagsBaiViet.value.length / perPage))
);

const paginatedCategories = computed(() => {
  const start = (pageCate.value - 1) * perPage;
  return DanhMucBaiViet.value.slice(start, start + perPage);
});

const paginatedTags = computed(() => {
  const start = (pageTag.value - 1) * perPage;
  return TagsBaiViet.value.slice(start, start + perPage);
});

/* ------------------ ON MOUNT ------------------ */
onMounted(() => {
  loadDanhMuc();
  loadTags();
  loadBaiViet();
});

/* ------------------ QUILL EDITOR ------------------ */
function initQuill(content = "") {
  setTimeout(() => {
    quill = new Quill("#quillEditor", {
      theme: "snow",
      modules: {
        toolbar: [
          ["bold", "italic", "underline"],
          [{ list: "ordered" }, { list: "bullet" }],
          ["link", "image"],
          [{ header: [1, 2, 3, false] }],
        ],
      },
    });

    quill.root.innerHTML = content;
  }, 50);
}
async function toggleHot(bv) {
  const res = await fetch(
    API_BAIVIET + "?action=toggle-hot",
    {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id_baiviet: bv.id_baiviet
      })
    }
  );

  const data = await res.json();

  if (!data.success) {
    alert(data.message);
    return;
  }

  alert(data.message);
  await loadBaiViet();
}
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
.quill-editor {
  background: white;
  min-height: 300px;
  max-height: 600px;
  overflow-y: auto;
  border: 1px solid #ced4da;
  border-radius: 6px;
}
</style>
