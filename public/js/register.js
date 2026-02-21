const text = "Membuka Jendela Dunia Melalui Literasi";
let i = 0;

function typeEffect(){
  if(i < text.length){
    document.getElementById("typing").innerHTML += text.charAt(i);
    i++;
    setTimeout(typeEffect, 60);
  }
}

document.addEventListener("DOMContentLoaded", typeEffect);
