const favSlides = Array.from(document.querySelectorAll('.fav-slide'));
const favDots = document.getElementById('favDots');
let favIndex = 0;
let favTimer = null;

function renderFav(){
  if (!favSlides.length) return;
  favSlides.forEach((slide, i) => slide.classList.toggle('active', i === favIndex));
  if (favDots) {
    favDots.querySelectorAll('.dot').forEach((dot, i) => {
      dot.classList.toggle('active', i === favIndex);
    });
  }
}

function moveFav(step){
  if (!favSlides.length) return;
  favIndex = (favIndex + step + favSlides.length) % favSlides.length;
  renderFav();
  restartFavAuto();
}

function goFav(index){
  favIndex = index;
  renderFav();
  restartFavAuto();
}

function startFavAuto(){
  if (!favSlides.length || favSlides.length === 1) return;
  favTimer = setInterval(() => moveFav(1), 4000);
}

function restartFavAuto(){
  if (favTimer) clearInterval(favTimer);
  startFavAuto();
}

if (favSlides.length && favDots) {
  favSlides.forEach((_, i) => {
    const dot = document.createElement('button');
    dot.type = 'button';
    dot.className = 'dot' + (i === 0 ? ' active' : '');
    dot.addEventListener('click', () => goFav(i));
    favDots.appendChild(dot);
  });
}

renderFav();
startFavAuto();

