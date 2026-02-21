const multi = document.getElementById('multiSelect');
const list  = document.getElementById('optionsList');
const box   = document.getElementById('kategoriInputs');

multi.addEventListener('click', () => list.style.display = 'block');

document.addEventListener('click', e => {
  if(!multi.contains(e.target) && !list.contains(e.target)){
    list.style.display = 'none';
  }
});

list.querySelectorAll('.option').forEach(opt => {
  opt.addEventListener('click', () => {
    const id = opt.dataset.id;
    const name = opt.textContent.trim();

    if(multi.querySelector(`[data-id="${id}"]`)) return;

    const ph = multi.querySelector('.placeholder');
    if(ph) ph.remove();

    const tag = document.createElement('span');
    tag.className = 'tag';
    tag.dataset.id = id;
    tag.innerHTML = `${name} <i class="fa-solid fa-xmark"></i>`;
    multi.appendChild(tag);

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'kategori[]';
    input.value = id;
    input.dataset.id = id;
    box.appendChild(input);

    list.style.display = 'none';
  });
});

multi.addEventListener('click', e => {
  if(e.target.classList.contains('fa-xmark')){
    const tag = e.target.parentElement;
    const id = tag.dataset.id;
    tag.remove();
    box.querySelector(`input[data-id="${id}"]`)?.remove();

    if(!multi.querySelector('.tag')){
      multi.innerHTML = `<span class="placeholder">Pilih kategori</span>`;
    }
  }
});

/* PREVIEW COVER */
const input = document.getElementById('coverInput');
const img = document.getElementById('previewImage');
const ph = document.getElementById('placeholder');

input.addEventListener('change', () => {
  if(input.files[0]){
    const r = new FileReader();
    r.onload = e => {
      img.src = e.target.result;
      img.style.display = 'block';
      ph.style.display = 'none';
    }
    r.readAsDataURL(input.files[0]);
  }
});

