const text = "Membuka Jendela Dunia Melalui Literasi";
let i = 0;
const speed = 60;

function typeEffect(){
  if(i < text.length){
    document.getElementById("typing").innerHTML += text.charAt(i);
    i++;
    setTimeout(typeEffect, speed);
  }
}

document.addEventListener("DOMContentLoaded", typeEffect);
