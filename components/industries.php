<?php
/**
 * YTech Panels - Industries We Serve Component
 * Auto-playing carousel showcasing industries served with images and names.
 */

$industries = [
    ['name' => 'Manufacturing', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQCFITKCYjrUS582sjWA97CG9o3AfPlFHa5in3J6ulVHA&s=10'],
    ['name' => 'Food Processing', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ3p11rBHCa7JY_Sempr_QbfTbf9TvkK9W4cRCIDeatzJsbI4hAN83Pf4-D&s=10'],
    ['name' => 'Pharmaceuticals', 'image' => 'https://zenithbci.co.in/wp-content/uploads/2025/05/qa-and-qc-in-pharmaceutical-industry.jpg'],
    ['name' => 'Textile Industries', 'image' => 'https://media.fortuneindia.com/fortune-india/2025-09-09/3ok9sa86/Textile_MG_9261%20copy.jpg?w=640&auto=format,compress&q=80'],
    ['name' => 'Chemical Plants', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT5YtFr5UGdbITRQKNAn6WJqPey6YQcAqNfhi38ulwD9Q&s=10'],
    ['name' => 'Water Treatment Plants', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcThDw0WRYaEwtyocJkKfSIMLsYGF2e5PALFjZWOGDIcuA&s=10'],
    ['name' => 'Commercial Buildings', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRDhKzbSGBTx9xIdJZKmNKQUv09s41YuiGXI006V16xqA&s=10'],
    ['name' => 'Power Plants', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRnpLs-qm1pb0N6ailMiJ_wLJL818iTLgfCqbOLHOiMow&s=10'],
    ['name' => 'Infrastructure Projects', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQlURc-Jx57I9mENiW74gaWOWZz-imQtMkpcanY3CTDLw&s=10'],
    ['name' => 'Engineering Companies', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTNxVWgZm18-ROyFs4uaITBy_eHJkjyQ3uWjCf-Wml9gw&s=10'],
    ['name' => 'OEM Manufacturers', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSZHJ_B2ryR9hUzXB0XE5I83atl2DrUjD0NAfSIyxONoQ&s=10'],
    ['name' => 'Solar', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS6yKWKjAr4CoPtmjV44fLYiXfHo-x4In8ywNPHToRTfg&s=10']
];
?>
<section class="industries-section" id="industries-we-serve">
    <div class="container">

        <div class="industries-header">
            <span class="industries-tagline">Our Expertise</span>
            <h2 class="industries-title">Industries We Serve</h2>
            <div class="industries-underline"></div>
        </div>

        <div class="industries-carousel" id="industries-carousel">
            <button class="industries-btn industries-btn-prev" id="industries-prev" aria-label="Previous">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </button>

            <div class="industries-viewport">
                <div class="industries-track" id="industries-track">
                    <?php foreach ($industries as $industry): ?>
                        <div class="industries-card">
                            <div class="industries-card-inner">
                                <div class="industries-image">
                                    <img src="<?php echo htmlspecialchars($industry['image']); ?>" alt="<?php echo htmlspecialchars($industry['name']); ?>" loading="lazy">
                                </div>
                                <h3 class="industries-name"><?php echo htmlspecialchars($industry['name']); ?></h3>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button class="industries-btn industries-btn-next" id="industries-next" aria-label="Next">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </button>
        </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('industries-track');
    const prevBtn = document.getElementById('industries-prev');
    const nextBtn = document.getElementById('industries-next');
    if (!track) return;

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

    function goToSlide(index) {
        currentSlide = index;
        const maxSlide = totalSlides - 1;
        if (currentSlide > maxSlide) currentSlide = 0;
        if (currentSlide < 0) currentSlide = maxSlide;

        const offset = -(currentSlide * (100 / cardsPerView));
        track.style.transform = 'translateX(' + offset + '%)';
    }

    function nextSlide() { goToSlide(currentSlide + 1); }
    function prevSlide() { goToSlide(currentSlide - 1); }

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

    function resetAuto() { startAuto(); }

    function handleResize() {
        const newCardsPerView = getCardsPerView();
        if (newCardsPerView !== cardsPerView) {
            cardsPerView = newCardsPerView;
            totalSlides = Math.ceil(totalCards / cardsPerView);
            goToSlide(0);
        }
    }

    if (prevBtn) prevBtn.addEventListener('click', function() { prevSlide(); resetAuto(); });
    if (nextBtn) nextBtn.addEventListener('click', function() { nextSlide(); resetAuto(); });

    const carousel = document.getElementById('industries-carousel');
    carousel.addEventListener('mouseenter', stopAuto);
    carousel.addEventListener('mouseleave', startAuto);

    let startX = 0;
    carousel.addEventListener('touchstart', function(e) {
        startX = e.changedTouches[0].screenX;
        stopAuto();
    }, { passive: true });

    carousel.addEventListener('touchend', function(e) {
        const diff = startX - e.changedTouches[0].screenX;
        if (Math.abs(diff) > 50) {
            if (diff > 0) nextSlide(); else prevSlide();
        }
        startAuto();
    }, { passive: true });

    window.addEventListener('resize', handleResize);

    startAuto();
});
</script>
