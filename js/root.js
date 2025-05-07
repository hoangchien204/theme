var menuHumberger = document.querySelectorAll(".menu-humberger");
var menu = document.querySelectorAll(".menu");
menuHumberger[0].addEventListener("click", function () {
  if (menuHumberger[0].classList.contains("fa-times")) {
    menuHumberger[0].classList.replace("fa-times", "fa-bars");
  } else {
    menuHumberger[0].classList.replace("fa-bars", "fa-times");
    }
    menu[0].classList.toggle("show");
});
