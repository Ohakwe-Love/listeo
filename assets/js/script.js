// responsive header
const headerMenuBtn = document.querySelector(".small-screen-menu");
const closeMenuBtn = document.querySelector(".close-small-screen-nav");
const smallScreenNav = document.querySelector(".small-screen-nav");

if (headerMenuBtn && closeMenuBtn && smallScreenNav) {
    headerMenuBtn.addEventListener("click", () => {
        smallScreenNav.style.left = "0";
    });

    closeMenuBtn.addEventListener("click", () => {
        smallScreenNav.style.left = "-1000px";
    });
}

// header scroll event
window.addEventListener('scroll', (event)=>{
    const height = window.scrollY;
    const header = document.querySelector("header");

    if (height > 400){
        header.style.position = "fixed";
        header.style.top = '0';
        header.style.zIndex = "999";
        header.style.boxShadow = '0 10px 15px rgba(25,25,25,0.1)';
    } else {
        header.style.position = "relative";
        header.style.boxShadow = 'none';
    }
})


// hero typewriting functionality
const heroTypewriter = document.querySelector('.hero-typewriter');
const texts = ["Comfort Your Priority.", "Celebrations Effortless.", "Every Bite Delicious.", "Moments Last Forever.", "Adventures Memorable.", "Fitness Fun."];
let textIndex = 0;
let heroCharIndex = 0;
let clearTexts = false;

function typeWriter() {
    const currentText = texts[textIndex];

    if (!clearTexts) {
        heroTypewriter.textContent = currentText.substring(0, heroCharIndex + 1);
        heroCharIndex++;

        if (heroCharIndex === currentText.length) {
            clearTexts = true;
            setTimeout(typeWriter, 2000);
        } else {
            setTimeout(typeWriter, 100);
        }
    } else {
        heroTypewriter.textContent = currentText.substring(0, heroCharIndex - 1);
        heroCharIndex--;

        if (heroCharIndex === 0) {
            clearTexts = false;
            textIndex = (textIndex + 1) % texts.length;
            setTimeout(typeWriter, 500);
        } else {
            setTimeout(typeWriter, 50);
        }
    }
}
if (heroTypewriter) {
    typeWriter();
}

// listeo hero-slides 
const slides = document.querySelectorAll(".background-slide");
let currentSlide = 0;
const totalSlides = slides.length;

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.classList.toggle("active", i === index);
    });
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    showSlide(currentSlide);
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    showSlide(currentSlide);
}

const heroNext = document.querySelector(".hero-next");
const heroPrev = document.querySelector(".hero-prev");

if (heroNext && heroPrev && totalSlides > 0) {
    heroNext.addEventListener("click", nextSlide);
    heroPrev.addEventListener("click", prevSlide);
    setInterval(nextSlide, 7000);
}

// back to top
const topLimit = 800;
window.addEventListener("scroll", ()=>{
    const scroll = window.scrollY;
    const back_to_top = document.querySelector(".back_to_top");

    if (!back_to_top) {
        return;
    }

    if (scroll >= topLimit) {
        back_to_top.style.visibility = 'visible';
    } else {
        back_to_top.style.visibility = 'hidden';
    }
})
