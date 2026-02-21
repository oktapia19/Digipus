const addKategoriModal = document.getElementById('addKategoriModal');
const editKategoriModal = document.getElementById('editKategoriModal');
const editKategoriForm = document.getElementById('editKategoriForm');
const editKategoriNameInput = document.getElementById('edit-kategori-name');
const editKategoriIdInput = document.getElementById('edit-kategori-id');
const kategoriKonfigurasi = document.getElementById('kategoriKonfigurasi');
const baseUrl = kategoriKonfigurasi?.dataset.baseUrl || '';

function openAddModal() {
  addKategoriModal?.classList.add('active');
}

function closeAddModal() {
  addKategoriModal?.classList.remove('active');
}

function openEditModal(id, name) {
  if (!editKategoriForm || !baseUrl) return;
  editKategoriForm.action = `${baseUrl}/${id}`;
  if (editKategoriNameInput) editKategoriNameInput.value = name;
  if (editKategoriIdInput) editKategoriIdInput.value = id;
  editKategoriModal?.classList.add('active');
}

function closeEditModal() {
  editKategoriModal?.classList.remove('active');
}

addKategoriModal?.addEventListener('click', (e) => {
  if (e.target === addKategoriModal) closeAddModal();
});

editKategoriModal?.addEventListener('click', (e) => {
  if (e.target === editKategoriModal) closeEditModal();
});

const modalAwal = kategoriKonfigurasi?.dataset.modalAwal;
if (modalAwal === 'edit') {
  openEditModal(kategoriKonfigurasi.dataset.kategoriId, kategoriKonfigurasi.dataset.kategoriNama || '');
} else if (modalAwal === 'tambah') {
  openAddModal();
}

setTimeout(() => {
  document.querySelectorAll('.auto-dismiss-alert').forEach((el) => {
    el.style.transition = 'opacity .3s ease';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 300);
  });
}, 4000);
