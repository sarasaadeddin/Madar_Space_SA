const toggle = document.getElementById("menu-toggle");
  const navLinks = document.querySelector(".nav-links");

  toggle.addEventListener("click", () => {
    navLinks.classList.toggle("active");
  });


let search = document.getElementById("search");
let cards = document.querySelectorAll(".card");

search.addEventListener("keyup", function(){

let value = search.value.toLowerCase();

cards.forEach(function(card){

let text = card.innerText.toLowerCase();

if(text.includes(value)){
card.style.display="block";
}else{
card.style.display="none";
}

});

});