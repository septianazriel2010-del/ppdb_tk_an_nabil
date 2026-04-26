// Toggle Class Active
const navbarNav = document.querySelector('.navbar-nav')
// Ketika menu di klik
document.querySelector('#menu').
onclick = () => {
navbarNav.classList.toggle('active')
};
// Klik di luar sidebar untuk menghilangkan nav nya
const menu = document.querySelector('#menu');

document.addEventListener('click', function(e) {
    if(!menu.contains(e.target) && !navbarNav.contains(e.target)) {
        navbarNav.classList.remove('active')
    }
});

// ========== SCROLL ANIMATION TRIGGER ==========
function isInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.bottom >= 0
    );
}

function triggerScrollAnimations() {
    const elements = document.querySelectorAll('[data-scroll-animate]');
    elements.forEach(el => {
        if (isInViewport(el) && !el.classList.contains('animated')) {
            const animType = el.getAttribute('data-scroll-animate');
            el.classList.add(animType, 'animated');
        }
    });
}

// Trigger on scroll
window.addEventListener('scroll', triggerScrollAnimations, { passive: true });

// Trigger on load juga
window.addEventListener('load', triggerScrollAnimations);

// Cek juga langsung saat DOM ready
document.addEventListener('DOMContentLoaded', triggerScrollAnimations);