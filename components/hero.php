<!-- ==========================================
     HERO SLIDER COMPONENT
     ========================================== -->
<div class="hero-slider-container" id="hero-slider">
    <!-- Slides -->
    <ul class="hero-slides-wrapper">
        <li class="hero-slide active">
            <img src="assets/images/hero/slide-1.png" alt="YTech Panel Slider Image 1" loading="eager">
        </li>
        <li class="hero-slide">
            <img src="assets/images/hero/slide-2.png" alt="YTech Panel Slider Image 2" loading="lazy">
        </li>
        <li class="hero-slide">
            <img src="assets/images/hero/slide-3.png" alt="YTech Panel Slider Image 3" loading="lazy">
        </li>
    </ul>

    <!-- Left & Right Arrow Controls -->
    <button class="hero-arrow hero-arrow-left" id="hero-prev" aria-label="Previous Slide">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </button>
    <button class="hero-arrow hero-arrow-right" id="hero-next" aria-label="Next Slide">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
    </button>

</div>

<!-- ==========================================
     HERO SLIDER JAVASCRIPT LOGIC
     ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sliderContainer = document.getElementById('hero-slider');
    if (!sliderContainer) return;

    const slides = sliderContainer.querySelectorAll('.hero-slide');
    const prevBtn = document.getElementById('hero-prev');
    const nextBtn = document.getElementById('hero-next');
    
    let currentIndex = 0;
    const slideIntervalTime = 5000; // 5 seconds
    let slideTimer;

    // Show slide by index
    function showSlide(index) {
        // Handle index boundaries
        if (index >= slides.length) {
            currentIndex = 0;
        } else if (index < 0) {
            currentIndex = slides.length - 1;
        } else {
            currentIndex = index;
        }

        // Toggle active states on slides
        slides.forEach((slide, i) => {
            if (i === currentIndex) {
                slide.classList.add('active');
            } else {
                slide.classList.remove('active');
            }
        });
    }

    // Auto Advance Slides
    function startAutoSlide() {
        stopAutoSlide(); // Ensure no multiple intervals run
        slideTimer = setInterval(() => {
            showSlide(currentIndex + 1);
        }, slideIntervalTime);
    }

    function stopAutoSlide() {
        if (slideTimer) {
            clearInterval(slideTimer);
        }
    }

    // Button event listeners
    nextBtn.addEventListener('click', () => {
        showSlide(currentIndex + 1);
        startAutoSlide(); // Reset timer on click
    });

    prevBtn.addEventListener('click', () => {
        showSlide(currentIndex - 1);
        startAutoSlide(); // Reset timer on click
    });


    // Pause on hover
    sliderContainer.addEventListener('mouseenter', stopAutoSlide);
    sliderContainer.addEventListener('mouseleave', startAutoSlide);

    // Initialize
    startAutoSlide();
});
</script>
