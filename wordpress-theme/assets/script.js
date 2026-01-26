// Toggle Menu Function
function toggleMenu() {
    const menu = document.getElementById('mainMenu');
    if (menu) {
        menu.classList.toggle('active');
    }
}

// Banner Carousel Auto-play
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.banner-slide');
    if (slides.length > 0) {
        let currentSlide = 0;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                if (i === index) {
                    slide.classList.add('active');
                }
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }

        // Auto-play every 5 seconds
        setInterval(nextSlide, 5000);
    }

    // Mobile Accordion for Both Sidebars
    const mobileToggles = document.querySelectorAll('.column-header.mobile-toggle');
    mobileToggles.forEach(function(mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            const content = this.nextElementSibling;
            this.classList.toggle('collapsed');
            if (content) {
                content.classList.toggle('mobile-collapsed');
            }
        });
    });
});
