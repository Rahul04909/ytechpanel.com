<?php
/**
 * YTech Panels - Industries We Serve Component
 * Auto-playing carousel showcasing industries served with icons and names.
 */

$industries = [
    [
        'name' => 'Manufacturing',
        'icon' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="32" r="20" stroke="currentColor" stroke-width="2.5"/><circle cx="32" cy="32" r="8" stroke="currentColor" stroke-width="2.5"/><path d="M32 12V8M32 56v-4M12 32H8M56 32h-4M18.3 18.3l-2.8-2.8M48.5 48.5l-2.8-2.8M45.7 18.3l2.8-2.8M15.5 48.5l2.8-2.8" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>'
    ],
    [
        'name' => 'Food Processing',
        'icon' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 52c0-8 4-16 12-16s12 8 12 16" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/><path d="M26 28c0-6 2-8 6-8s6 2 6 8" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/><path d="M32 20V8M38 14l-6-6-6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 36h-2a4 4 0 010-8h2M50 36h2a4 4 0 000-8h-2" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>'
    ],
    [
        'name' => 'Pharmaceuticals',
        'icon' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="20" y="8" width="24" height="48" rx="12" stroke="currentColor" stroke-width="2.5"/><line x1="20" y1="24" x2="44" y2="24" stroke="currentColor" stroke-width="2.5"/><line x1="20" y1="40" x2="44" y2="40" stroke="currentColor" stroke-width="2.5"/><circle cx="32" cy="32" r="6" stroke="currentColor" stroke-width="2.5"/></svg>'
    ],
    [
        'name' => 'Textile Industries',
        'icon' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 52L32 12l20 40" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/><path d="M32 12v40" stroke="currentColor" stroke-width="2.5"/><path d="M12 52h40" stroke="currentColor" stroke-width="2.5"/><path d="M20 36l12-16 12 16" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>'
    ],
    [
        'name' => 'Chemical Plants',
        'icon' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M24 8v16l-8 28a4 4 0 004 4h24a4 4 0 004-4l-8-28V8" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/><path d="M24 8h16" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/><path d="M20 36h24" stroke="currentColor" stroke-width="2"/><circle cx="32" cy="20" r="3" stroke="currentColor" stroke-width="2"/></svg>'
    ],
    [
        'name' => 'Water Treatment Plants',
        'icon' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M44 40c0 8.8-5.4 16-12 16s-12-7.2-12-16c0-8.8 12-28 12-28s12 19.2 12 28z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/><path d="M28 38l4 4 8-8" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
    ],
    [
        'name' => 'Commercial Buildings',
        'icon' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="16" y="12" width="14" height="40" stroke="currentColor" stroke-width="2.5"/><rect x="34" y="24" width="14" height="28" stroke="currentColor" stroke-width="2.5"/><line x1="16" y1="28" x2="30" y2="28" stroke="currentColor" stroke-width="1.5"/><line x1="16" y1="36" x2="30" y2="36" stroke="currentColor" stroke-width="1.5"/><line x1="34" y1="36" x2="48" y2="36" stroke="currentColor" stroke-width="1.5"/><line x1="34" y1="44" x2="48" y2="44" stroke="currentColor" stroke-width="1.5"/></svg>'
    ],
    [
        'name' => 'Power Plants',
        'icon' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M28 8L20 32h10l-2 24 16-32H34l4-16z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/></svg>'
    ],
    [
        'name' => 'Infrastructure Projects',
        'icon' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 52l10-16h6l4-12h4l4 12h6l10 16" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/><line x1="32" y1="24" x2="32" y2="52" stroke="currentColor" stroke-width="2.5"/></svg>'
    ],
    [
        'name' => 'Engineering Companies',
        'icon' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="32" r="22" stroke="currentColor" stroke-width="2.5"/><path d="M32 10v44M10 32h44" stroke="currentColor" stroke-width="2.5"/><circle cx="32" cy="32" r="6" stroke="currentColor" stroke-width="2.5"/><path d="M22 22l20 20M42 22l-20 20" stroke="currentColor" stroke-width="1.5"/></svg>'
    ],
    [
        'name' => 'OEM Manufacturers',
        'icon' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="10" y="16" width="44" height="32" rx="4" stroke="currentColor" stroke-width="2.5"/><rect x="18" y="24" width="12" height="12" stroke="currentColor" stroke-width="2"/><rect x="36" y="28" width="10" height="14" stroke="currentColor" stroke-width="2"/><line x1="18" y1="46" x2="46" y2="46" stroke="currentColor" stroke-width="1.5"/></svg>'
    ],
    [
        'name' => 'Solar',
        'icon' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="32" r="10" stroke="currentColor" stroke-width="2.5"/><path d="M32 4v8M32 52v8M8 32h8M48 32h8M13.5 13.5l5.6 5.6M44.9 44.9l5.6 5.6M50.5 13.5l-5.6 5.6M19.1 44.9l-5.6 5.6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>'
    ]
];
?>
<section class="industries-section" id="industries-we-serve">
    <div class="container">

        <!-- Section Header -->
        <div class="industries-header">
            <span class="industries-tagline">Our Expertise</span>
            <h2 class="industries-title">Industries We Serve</h2>
            <div class="industries-underline"></div>
        </div>

        <!-- Carousel -->
        <div class="industries-carousel" id="industries-carousel">
            <div class="industries-track" id="industries-track">
                <?php foreach ($industries as $industry): ?>
                    <div class="industries-card">
                        <div class="industries-card-inner">
                            <div class="industries-icon">
                                <?php echo $industry['icon']; ?>
                            </div>
                            <h3 class="industries-name"><?php echo htmlspecialchars($industry['name']); ?></h3>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Navigation Dots -->
            <div class="industries-dots" id="industries-dots"></div>
        </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('industries-track');
    const dotsContainer = document.getElementById('industries-dots');
    if (!track || !dotsContainer) return;

    const cards = track.querySelectorAll('.industries-card');
    const totalCards = cards.length;

    let currentSlide = 0;
    let cardsPerView = getCardsPerView();
    let totalSlides = Math.ceil(totalCards / cardsPerView);
    let autoTimer;
    const autoInterval = 3500;

    function getCardsPerView() {
        if (window.innerWidth <= 480) return 1;
        if (window.innerWidth <= 768) return 2;
        if (window.innerWidth <= 1024) return 3;
        return 4;
    }

    function buildDots() {
        dotsContainer.innerHTML = '';
        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('button');
            dot.className = 'industries-dot' + (i === 0 ? ' active' : '');
            dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
            dot.addEventListener('click', function() {
                goToSlide(i);
                resetAuto();
            });
            dotsContainer.appendChild(dot);
        }
    }

    function goToSlide(index) {
        currentSlide = index;
        const maxSlide = totalSlides - 1;
        if (currentSlide > maxSlide) currentSlide = 0;
        if (currentSlide < 0) currentSlide = maxSlide;

        const offset = -(currentSlide * (100 / cardsPerView));
        track.style.transform = 'translateX(' + offset + '%)';

        const dots = dotsContainer.querySelectorAll('.industries-dot');
        dots.forEach(function(d, i) {
            d.classList.toggle('active', i === currentSlide);
        });
    }

    function nextSlide() {
        goToSlide(currentSlide + 1);
    }

    function startAuto() {
        stopAuto();
        autoTimer = setInterval(nextSlide, autoInterval);
    }

    function stopAuto() {
        if (autoTimer) {
            clearInterval(autoTimer);
            autoTimer = null;
        }
    }

    function resetAuto() {
        startAuto();
    }

    function handleResize() {
        const newCardsPerView = getCardsPerView();
        if (newCardsPerView !== cardsPerView) {
            cardsPerView = newCardsPerView;
            totalSlides = Math.ceil(totalCards / cardsPerView);
            buildDots();
            goToSlide(0);
        }
    }

    // Build initial dots
    buildDots();

    // Auto-play
    startAuto();

    // Pause on hover
    const carousel = document.getElementById('industries-carousel');
    carousel.addEventListener('mouseenter', stopAuto);
    carousel.addEventListener('mouseleave', startAuto);

    // Touch/swipe support
    let startX = 0;
    let endX = 0;

    carousel.addEventListener('touchstart', function(e) {
        startX = e.changedTouches[0].screenX;
        stopAuto();
    }, { passive: true });

    carousel.addEventListener('touchend', function(e) {
        endX = e.changedTouches[0].screenX;
        const diff = startX - endX;
        if (Math.abs(diff) > 50) {
            if (diff > 0) {
                nextSlide();
            } else {
                goToSlide(currentSlide - 1);
            }
        }
        startAuto();
    }, { passive: true });

    // Recalculate on resize
    window.addEventListener('resize', handleResize);
});
</script>
