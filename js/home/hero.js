var slide = document.querySelectorAll(".slide");
var index = 0;

// next
function nextSlide() {
    slide[index].classList.remove("active-slide");
    index = (index + 1) % slide.length;
    slide[index].classList.add("active-slide");
}
function prevSlide() {
    slide[index].classList.remove("active-slide");
    index = (index - 1 + slide.length) % slide.length;
    slide[index].classList.add("active-slide");
}