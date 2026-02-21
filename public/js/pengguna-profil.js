const editModal = document.getElementById('editProfileModal');

function openEditModal() {
  editModal?.classList.add('active');
}

function closeEditModal() {
  editModal?.classList.remove('active');
}

editModal?.addEventListener('click', (e) => {
  if (e.target === editModal) closeEditModal();
});

if (editModal?.dataset.autoOpen === '1') {
  openEditModal();
}

setTimeout(() => {
  document.querySelectorAll('.auto-dismiss-alert').forEach((el) => {
    el.style.transition = 'opacity .3s ease';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 300);
  });
}, 4000);
