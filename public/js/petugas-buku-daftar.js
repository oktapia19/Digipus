setTimeout(() => {
  document.querySelectorAll('.auto-dismiss-alert').forEach((el) => {
    el.style.transition = 'opacity .3s ease';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 300);
  });
}, 4000);

