const popupAlert = (pesan, judul = 'Informasi') => (
  window.popupIndonesiaAlert
    ? window.popupIndonesiaAlert(pesan, judul)
    : Promise.resolve(window.alert(pesan))
);

function copyKode() {
  const kode = document.getElementById('kodeDisplay')?.textContent || '';
  navigator.clipboard.writeText(kode).then(() => {
    popupAlert('Kode berhasil disalin!', 'Berhasil');
  }).catch(() => {
    const input = document.createElement('input');
    input.value = kode;
    document.body.appendChild(input);
    input.select();
    document.execCommand('copy');
    document.body.removeChild(input);
    popupAlert('Kode berhasil disalin!', 'Berhasil');
  });
}

if (new URLSearchParams(window.location.search).get('print') === '1') {
  window.addEventListener('load', () => {
    setTimeout(() => window.print(), 250);
  });
}
