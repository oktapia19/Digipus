let borrowAlertTimer;
  function showBorrowLimitAlert(message) {
    const alertBox = document.getElementById('borrowLimitAlert');
    if (!alertBox) return;

    alertBox.textContent = message || 'Kembalikan buku terlebih dahulu';
    alertBox.classList.add('show');

    clearTimeout(borrowAlertTimer);
    borrowAlertTimer = setTimeout(() => {
      alertBox.classList.remove('show');
    }, 4000);
  }

