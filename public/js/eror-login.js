document.addEventListener('DOMContentLoaded', function () {
  const toast = document.getElementById('loginErrorToast');
  const message = toast?.dataset.loginError?.trim();
  if (!toast || !message) return;

  toast.textContent = message;
  toast.classList.add('show');

  setTimeout(function () {
    toast.classList.remove('show');
  }, 3500);
});
