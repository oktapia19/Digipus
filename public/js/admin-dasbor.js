function setMinTanggalPengembalian(){
  const el = document.getElementById('tanggalPengembalian');
  if (!el) return;
  const today = new Date();
  const pad = (n)=> (n<10 ? '0'+n : n);
  const iso = today.getFullYear() + '-' + pad(today.getMonth()+1) + '-' + pad(today.getDate());
  el.min = iso;
  if (!el.value) el.value = iso;
}

function popupDialog(message, { title = 'Informasi', confirm = false } = {}) {
  return new Promise((resolve) => {
    const overlay = document.createElement('div');
    overlay.style.cssText = 'position:fixed;inset:0;background:rgba(20,12,35,.45);display:flex;align-items:center;justify-content:center;z-index:99999;padding:16px;';

    const box = document.createElement('div');
    box.style.cssText = 'width:min(460px,92vw);background:#fff;border-radius:16px;box-shadow:0 18px 50px rgba(0,0,0,.25);padding:20px;';
    box.innerHTML = `
      <div style="font-weight:700;color:#3f2470;font-size:20px;margin-bottom:8px;">${title}</div>
      <div style="color:#3a3550;font-size:15px;line-height:1.5;white-space:pre-wrap;">${message}</div>
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
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) close(false);
    });
  });
}

const popupAlert = (message, title = 'Informasi') => popupDialog(message, { title, confirm: false });
const popupConfirm = (message, title = 'Konfirmasi') => popupDialog(message, { title, confirm: true });

function bukaBukti(id){
  fetch(`/api/peminjaman/${id}`)
    .then(r => r.json())
    .then(data => {
      const status = data.status === 'pending'
        ? '<span style="color:#f57c00;font-weight:600">Menunggu Konfirmasi</span>'
        : (data.status === 'confirmed'
          ? '<span style="color:#2e7d32;font-weight:600">Dikonfirmasi</span>'
          : '<span style="color:#999;font-weight:600">'+data.status+'</span>');

      const content = `
        <div style="display:grid;grid-template-columns:140px 1fr;gap:12px;padding:12px 0;border-bottom:1px solid #f0f0f0"><span style="font-weight:600;color:#555">Kode:</span><span>${data.kode}</span></div>
        <div style="display:grid;grid-template-columns:140px 1fr;gap:12px;padding:12px 0;border-bottom:1px solid #f0f0f0"><span style="font-weight:600;color:#555">Peminjam:</span><div><div>${data.user.nama_lengkap || data.user.email}</div><small style="color:#999">${data.no_telepon}</small></div></div>
        <div style="display:grid;grid-template-columns:140px 1fr;gap:12px;padding:12px 0;border-bottom:1px solid #f0f0f0"><span style="font-weight:600;color:#555">Alamat:</span><span>${data.alamat}</span></div>
        <div style="display:grid;grid-template-columns:140px 1fr;gap:12px;padding:12px 0;border-bottom:1px solid #f0f0f0"><span style="font-weight:600;color:#555">Buku:</span><span>${data.book.judul}</span></div>
        <div style="display:grid;grid-template-columns:140px 1fr;gap:12px;padding:12px 0;border-bottom:1px solid #f0f0f0"><span style="font-weight:600;color:#555">Durasi:</span><span>${data.durasi} ${data.durasi_satuan || 'hari'}</span></div>
        <div style="display:grid;grid-template-columns:140px 1fr;gap:12px;padding:12px 0;border-bottom:1px solid #f0f0f0"><span style="font-weight:600;color:#555">Tanggal Pinjam:</span><span>${data.tanggal_pinjam}</span></div>
        <div style="display:grid;grid-template-columns:140px 1fr;gap:12px;padding:12px 0;border-bottom:1px solid #f0f0f0"><span style="font-weight:600;color:#555">Jatuh Tempo:</span><span>${data.tanggal_kembali}</span></div>
        <div style="display:grid;grid-template-columns:140px 1fr;gap:12px;padding:12px 0"><span style="font-weight:600;color:#555">Status:</span>${status}</div>
      `;
      document.getElementById('modalContent').innerHTML = content;
      document.getElementById('modalBukti').style.display = 'flex';
    })
    .catch(e => popupAlert('Gagal memuat detail: ' + e.message, 'Gagal'));
}

function tutupBukti(){
  document.getElementById('modalBukti').style.display = 'none';
}

function bukaBuktiModal(id){
  fetch(`/api/peminjaman/${id}`)
    .then(r => r.json())
    .then(data => {
      document.getElementById('kodeDisplay').textContent = data.kode;
      const detail = `
        <div style="display:grid;grid-template-columns:140px 1fr;gap:12px;padding:12px 0;border-bottom:1px solid #f0f0f0"><span style="font-weight:600;color:#555">Peminjam:</span><div><div>${data.user.nama_lengkap || data.user.email}</div><small style="color:#999">${data.no_telepon || 'N/A'}</small></div></div>
        <div style="display:grid;grid-template-columns:140px 1fr;gap:12px;padding:12px 0;border-bottom:1px solid #f0f0f0"><span style="font-weight:600;color:#555">Buku:</span><span>${data.book.judul}</span></div>
        <div style="display:grid;grid-template-columns:140px 1fr;gap:12px;padding:12px 0;border-bottom:1px solid #f0f0f0"><span style="font-weight:600;color:#555">Durasi:</span><span>${data.durasi} ${data.durasi_satuan || 'hari'}</span></div>
        <div style="display:grid;grid-template-columns:140px 1fr;gap:12px;padding:12px 0;border-bottom:1px solid #f0f0f0"><span style="font-weight:600;color:#555">Tanggal Pinjam:</span><span>${data.tanggal_pinjam}</span></div>
        <div style="display:grid;grid-template-columns:140px 1fr;gap:12px;padding:12px 0"><span style="font-weight:600;color:#555">Jatuh Tempo:</span><span>${data.tanggal_kembali}</span></div>
      `;
      document.getElementById('buktiDetail').innerHTML = detail;
      document.getElementById('modalBuktiReceipt').style.display = 'flex';
    })
    .catch(e => popupAlert('Gagal memuat bukti: ' + e.message, 'Gagal'));
}

function tutupBuktiReceipt(){
  document.getElementById('modalBuktiReceipt').style.display = 'none';
}

function salinKode(){
  const kode = document.getElementById('kodeDisplay').textContent;
  navigator.clipboard.writeText(kode).then(() => {
    popupAlert('Kode "' + kode + '" berhasil disalin.', 'Berhasil');
  }).catch(() => {
    popupAlert('Gagal menyalin kode', 'Gagal');
  });
}

async function konfirmasiLangsung(id){
  const ok = await popupConfirm('Setujui peminjaman ini?');
  if(!ok) return;

  const form = new FormData();
  form.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
  fetch(`/admin/peminjaman/${id}/confirm`, {method:'POST', body:form})
    .then(async () => {
      await popupAlert('Peminjaman dikonfirmasi!', 'Berhasil');
      location.reload();
    })
    .catch(e => popupAlert('Gagal: ' + e.message, 'Gagal'));
}

async function batalkanLangsung(id){
  const ok = await popupConfirm('Batalkan peminjaman ini?');
  if(!ok) return;

  const form = new FormData();
  form.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
  fetch(`/admin/peminjaman/${id}/reject`, {method:'POST', body:form})
    .then(async () => {
      await popupAlert('Peminjaman dibatalkan!', 'Berhasil');
      location.reload();
    })
    .catch(e => popupAlert('Gagal: ' + e.message, 'Gagal'));
}

function openModalPengembalianBuku(){
  document.getElementById('kodeVerifikasi').value = '';
  document.getElementById('kondisiBuku').value = 'baik';
  setMinTanggalPengembalian();
  document.getElementById('formVerifikasiReturn').action = '/admin/peminjaman/verify-by-code';
  document.getElementById('modalVerifikasiReturn').style.display = 'flex';
  document.getElementById('kodeVerifikasi').focus();
}

function openVerifikasiReturn(id){
  document.getElementById('kodeVerifikasi').value = '';
  document.getElementById('kondisiBuku').value = 'baik';
  setMinTanggalPengembalian();
  document.getElementById('formVerifikasiReturn').action = `/admin/peminjaman/${id}/return-confirm`;
  document.getElementById('modalVerifikasiReturn').style.display = 'flex';
  document.getElementById('kodeVerifikasi').focus();
}

function closeVerifikasiReturn(){
  document.getElementById('modalVerifikasiReturn').style.display = 'none';
}

document.getElementById('formVerifikasiReturn')?.addEventListener('submit', async function(e){
  e.preventDefault();
  const form = new FormData(this);
  const action = this.action;
  const kode = form.get('kode_verifikasi')?.trim() || '';

  if(!kode){
    await popupAlert('Silakan masukkan kode!', 'Perhatian');
    return;
  }

  try {
    const r = await fetch(action, {method:'POST', body:form});
    const data = await r.json();
    if(data.success){
      await popupAlert(data.message || 'Pengembalian buku dikonfirmasi!', 'Berhasil');
      location.reload();
    } else {
      await popupAlert(data.message || 'Gagal verifikasi pengembalian.', 'Gagal');
    }
  } catch (e2) {
    await popupAlert('Error: ' + e2.message, 'Gagal');
  }
});
