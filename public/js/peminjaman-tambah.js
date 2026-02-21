// Hitung dan tampilkan tanggal jatuh tempo otomatis
  function pad(n){return n<10? '0'+n: n}
  function hitungTanggalKembali(){
    const durasiInput = document.getElementById('durasi');
    const satuan = document.getElementById('durasi_satuan').value;
    const max = satuan === 'jam' ? 24 : 12;
    durasiInput.max = max;
    if (parseInt(durasiInput.value || '1', 10) > max) {
      durasiInput.value = max;
    }
    const durasi = parseInt(durasiInput.value || 1, 10);
    const hari = new Date();
    const addDays = satuan === 'jam' ? 1 : durasi;
    const tanggalKembali = new Date(hari.getFullYear(), hari.getMonth(), hari.getDate() + addDays);
    const iso = tanggalKembali.getFullYear() + '-' + pad(tanggalKembali.getMonth() + 1) + '-' + pad(tanggalKembali.getDate());
    document.getElementById('tanggal_kembali').value = iso;
  }
  document.addEventListener('DOMContentLoaded', hitungTanggalKembali);
  document.getElementById('durasi').addEventListener('input', hitungTanggalKembali);
  document.getElementById('durasi_satuan').addEventListener('change', hitungTanggalKembali);

