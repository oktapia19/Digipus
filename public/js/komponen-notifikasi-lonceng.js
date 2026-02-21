(() => {
  const popupConfirm = (pesan, judul = 'Konfirmasi') => (
    window.popupIndonesiaConfirm
      ? window.popupIndonesiaConfirm(pesan, judul)
      : Promise.resolve(window.confirm(pesan))
  );

  const wrap = document.querySelector('.app-notif-wrap');
  const btn = document.getElementById('appNotifBtn');
  const dd = document.getElementById('appNotifDropdown');
  const list = dd?.querySelector('.app-notif-list');
  const clearBtn = document.getElementById('appNotifClearAllBtn');
  const csrf = wrap?.dataset.csrfToken || '';
  if (!btn || !dd || !csrf) return;

  const updateBadge = (count) => {
    const current = btn.querySelector('.app-notif-badge');
    if (count > 0) {
      if (current) {
        current.textContent = count > 99 ? '99+' : String(count);
      } else {
        const badge = document.createElement('span');
        badge.className = 'app-notif-badge';
        badge.textContent = count > 99 ? '99+' : String(count);
        btn.appendChild(badge);
      }
    } else if (current) {
      current.remove();
    }
  };

  const unreadCountFromDom = () => dd.querySelectorAll('.app-notif-item.unread').length;

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    dd.style.display = dd.style.display === 'block' ? 'none' : 'block';
  });

  document.addEventListener('click', (e) => {
    if (!dd.contains(e.target) && !btn.contains(e.target)) dd.style.display = 'none';
  });

  dd.addEventListener('click', (e) => {
    const itemBtn = e.target.closest('.app-notif-open');
    if (!itemBtn) return;
    e.preventDefault();

    const row = itemBtn.closest('.app-notif-item');
    fetch(itemBtn.dataset.readUrl, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
    }).then((r) => r.json())
      .then((data) => {
        row?.classList.remove('unread');
        updateBadge(typeof data.unread_count === 'number' ? data.unread_count : unreadCountFromDom());
      })
      .catch(() => {});
  });

  clearBtn?.addEventListener('click', async (e) => {
    e.preventDefault();
    const lanjut = await popupConfirm('Hapus semua notifikasi?');
    if (!lanjut) return;

    fetch(clearBtn.dataset.url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
    }).then((r) => r.json())
      .then(() => {
        if (list) {
          list.innerHTML = '<div class="app-notif-empty">Belum ada notifikasi.</div>';
        }
        updateBadge(0);
      })
      .catch(() => {});
  });
})();
