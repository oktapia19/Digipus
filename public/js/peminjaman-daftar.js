const popupCadangan = (pesan, { title = 'Informasi', confirm = false } = {}) => new Promise((resolve) => {
  const overlay = document.createElement('div');
  overlay.style.cssText = 'position:fixed;inset:0;background:rgba(20,12,35,.45);display:flex;align-items:center;justify-content:center;z-index:99999;padding:16px;';

  const box = document.createElement('div');
  box.style.cssText = 'width:min(460px,92vw);background:#fff;border-radius:16px;box-shadow:0 18px 50px rgba(0,0,0,.25);padding:20px;';
  box.innerHTML = `
    <div style="font-weight:700;color:#3f2470;font-size:20px;margin-bottom:8px;">${title}</div>
    <div style="color:#3a3550;font-size:15px;line-height:1.5;white-space:pre-wrap;">${pesan}</div>
    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
      ${confirm ? '<button id="dlgCancel" style="padding:10px 14px;border:1px solid #ddd;border-radius:10px;background:#fff;cursor:pointer;font-weight:600;">Batal</button>' : ''}
      <button id="dlgOk" style="padding:10px 14px;border:none;border-radius:10px;background:#6B33B8;color:#fff;cursor:pointer;font-weight:600;">OK</button>
    </div>
  `;

  overlay.appendChild(box);
  document.body.appendChild(overlay);

  const close = (value) => {
    overlay.remove();
    resolve(value);
  };

  box.querySelector('#dlgOk')?.addEventListener('click', () => close(true));
  box.querySelector('#dlgCancel')?.addEventListener('click', () => close(false));
  overlay.addEventListener('click', (event) => {
    if (event.target === overlay) close(false);
  });
});

const popupAlert = (pesan, judul = 'Informasi') => (
  window.popupIndonesiaAlert
    ? window.popupIndonesiaAlert(pesan, judul)
    : popupCadangan(pesan, { title: judul, confirm: false })
);

const popupConfirm = (pesan, judul = 'Konfirmasi') => (
  window.popupIndonesiaConfirm
    ? window.popupIndonesiaConfirm(pesan, judul)
    : popupCadangan(pesan, { title: judul, confirm: true })
);

function lihatBukti(id){
  fetch(`/api/peminjaman/${id}`)
    .then(r => r.json())
    .then(data => {
      document.getElementById('modalBukti').dataset.peminjamanId = id;
      document.getElementById('kodeBuktiDisplay').textContent = data.kode;

      const detail = `
        <div class="modal-row"><span class="modal-label">Peminjam:</span><span class="modal-value">${data.user.nama_lengkap || data.user.email}</span></div>
        <div class="modal-row"><span class="modal-label">Buku:</span><span class="modal-value">${data.book.judul}</span></div>
        <div class="modal-row"><span class="modal-label">Durasi:</span><span class="modal-value">${data.durasi} ${data.durasi_satuan || 'hari'}</span></div>
        <div class="modal-row"><span class="modal-label">Tanggal Pinjam:</span><span class="modal-value">${data.tanggal_pinjam}</span></div>
        <div class="modal-row"><span class="modal-label">Jatuh Tempo:</span><span class="modal-value">${data.tanggal_kembali}</span></div>
      `;
      document.getElementById('buktiDetail').innerHTML = detail;
      document.getElementById('modalBukti').classList.add('active');
    })
    .catch(e => popupAlert('Gagal memuat bukti: ' + e.message, 'Gagal'));
}

function tutupBuktiModal(){
  document.getElementById('modalBukti').classList.remove('active');
}

function printBuktiModal(){
  const id = document.getElementById('modalBukti').dataset.peminjamanId;
  if(!id){
    popupAlert('Data peminjaman belum tersedia', 'Perhatian');
    return;
  }

  fetch(`/peminjaman/${id}/receipt-pdf`, { credentials: 'same-origin' })
    .then(response => {
      if (!response.ok) {
        throw new Error('Gagal membuat file PDF');
      }
      return response.blob();
    })
    .then(blob => {
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = `bukti-peminjaman-${id}.pdf`;
      document.body.appendChild(link);
      link.click();
      link.remove();
      window.URL.revokeObjectURL(url);
    })
    .catch(e => popupAlert(e.message || 'Gagal mengunduh PDF', 'Gagal'));
}

function salinKodeBukti(){
  const kode = document.getElementById('kodeBuktiDisplay').textContent;
  navigator.clipboard.writeText(kode).then(() => {
    popupAlert('Kode "' + kode + '" berhasil disalin!', 'Berhasil');
  }).catch(() => {
    popupAlert('Gagal menyalin kode', 'Gagal');
  });
}

async function kembalikanBuku(id){
  const lanjut = await popupConfirm('Kembalikan buku ini?');
  if (!lanjut) return;

  const form = new FormData();
  form.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

  fetch(`/peminjaman/${id}/return`, { method: 'POST', body: form })
    .then(r => r.json())
    .then(async (data) => {
      await popupAlert(data.message || 'Buku berhasil dikembalikan!', 'Berhasil');
      location.reload();
    })
    .catch(e => popupAlert('Gagal: ' + e.message, 'Gagal'));
}

function openReviewModal(id, judul){
  document.getElementById('reviewPeminjamanId').value = id;
  document.getElementById('reviewBookTitle').textContent = judul;
  document.getElementById('modalUlasan').classList.add('active');
}

function closeReviewModal(){
  document.getElementById('modalUlasan').classList.remove('active');
}
