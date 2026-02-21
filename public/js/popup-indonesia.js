(() => {
  if (window.popupIndonesiaAlert && window.popupIndonesiaConfirm) return;

  const STYLE_ID = 'popup-indonesia-style';

  const pasangStyle = () => {
    if (document.getElementById(STYLE_ID)) return;
    const style = document.createElement('style');
    style.id = STYLE_ID;
    style.textContent = `
      .popup-id-overlay {
        position: fixed;
        inset: 0;
        z-index: 100000;
        background: rgba(19, 12, 30, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
      }
      .popup-id-card {
        width: min(460px, 92vw);
        background: #fff;
        border: 1px solid #e7dbfa;
        border-radius: 16px;
        box-shadow: 0 22px 54px rgba(20, 12, 35, 0.25);
        padding: 20px;
      }
      .popup-id-title {
        margin: 0 0 8px;
        color: #3f2470;
        font-size: 20px;
        font-weight: 700;
      }
      .popup-id-message {
        margin: 0;
        color: #3a3550;
        font-size: 15px;
        line-height: 1.55;
        white-space: pre-wrap;
      }
      .popup-id-actions {
        margin-top: 18px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
      }
      .popup-id-btn {
        border-radius: 10px;
        padding: 10px 14px;
        font-weight: 600;
        cursor: pointer;
        font-size: 14px;
      }
      .popup-id-btn-batal {
        border: 1px solid #d8d6e0;
        background: #fff;
        color: #4f4a61;
      }
      .popup-id-btn-oke {
        border: 1px solid #6b33b8;
        background: #6b33b8;
        color: #fff;
      }
    `;
    document.head.appendChild(style);
  };

  const popupDialog = (pesan, { title = 'Informasi', confirm = false } = {}) => new Promise((resolve) => {
    pasangStyle();

    const overlay = document.createElement('div');
    overlay.className = 'popup-id-overlay';

    const card = document.createElement('div');
    card.className = 'popup-id-card';
    card.setAttribute('role', 'dialog');
    card.setAttribute('aria-modal', 'true');
    card.setAttribute('aria-label', title);

    const judul = document.createElement('h3');
    judul.className = 'popup-id-title';
    judul.textContent = title;

    const isi = document.createElement('p');
    isi.className = 'popup-id-message';
    isi.textContent = String(pesan ?? '');

    const actions = document.createElement('div');
    actions.className = 'popup-id-actions';

    if (confirm) {
      const tombolBatal = document.createElement('button');
      tombolBatal.type = 'button';
      tombolBatal.className = 'popup-id-btn popup-id-btn-batal';
      tombolBatal.textContent = 'Batal';
      tombolBatal.addEventListener('click', () => tutup(false));
      actions.appendChild(tombolBatal);
    }

    const tombolOke = document.createElement('button');
    tombolOke.type = 'button';
    tombolOke.className = 'popup-id-btn popup-id-btn-oke';
    tombolOke.textContent = 'OK';
    tombolOke.addEventListener('click', () => tutup(true));
    actions.appendChild(tombolOke);

    card.appendChild(judul);
    card.appendChild(isi);
    card.appendChild(actions);
    overlay.appendChild(card);
    document.body.appendChild(overlay);
    tombolOke.focus();

    const keyListener = (event) => {
      if (event.key === 'Escape') {
        tutup(false);
      }
      if (!confirm && event.key === 'Enter') {
        tutup(true);
      }
    };

    const tutup = (value) => {
      window.removeEventListener('keydown', keyListener);
      overlay.remove();
      resolve(value);
    };

    overlay.addEventListener('click', (event) => {
      if (event.target === overlay) {
        tutup(false);
      }
    });
    window.addEventListener('keydown', keyListener);
  });

  window.popupIndonesiaAlert = (pesan, title = 'Informasi') => popupDialog(pesan, { title, confirm: false });
  window.popupIndonesiaConfirm = (pesan, title = 'Konfirmasi') => popupDialog(pesan, { title, confirm: true });

  document.addEventListener('submit', async (event) => {
    const form = event.target.closest('form[data-popup-confirm]');
    if (!form) return;

    event.preventDefault();
    const pesan = form.getAttribute('data-popup-message') || 'Yakin melanjutkan aksi ini?';
    const title = form.getAttribute('data-popup-title') || 'Konfirmasi';
    const lanjut = await window.popupIndonesiaConfirm(pesan, title);
    if (lanjut) form.submit();
  });
})();
