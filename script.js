// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
  const hamburgerBtn = document.getElementById('hamburgerBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  
  if (hamburgerBtn && mobileMenu) {
    hamburgerBtn.addEventListener('click', function() {
      const isOpen = mobileMenu.style.display !== 'none';
      mobileMenu.style.display = isOpen ? 'none' : 'block';
      hamburgerBtn.setAttribute('aria-expanded', !isOpen);
    });
  }

  // Set active nav link based on current page
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';
  const navLinks = document.querySelectorAll('.ma-nav-links a, .ma-mobile-menu a');
  
  navLinks.forEach(link => {
    const href = link.getAttribute('href');
    if (href === currentPage || (currentPage === '' && href === 'index.html')) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });

  // Hero Slider
  const heroSlider = document.getElementById('heroSlider');
  const sliderDots = document.getElementById('sliderDots');
  
  if (heroSlider && sliderDots) {
    const slides = heroSlider.querySelectorAll('.ma-slide');
    const totalSlides = slides.length;
    let currentIndex = 0;
    let autoSlideInterval;

    // Set first slide as active
    slides[0].classList.add('active');

    // Create dots
    slides.forEach((_, index) => {
      const dot = document.createElement('button');
      dot.className = 'ma-slider-dot';
      dot.setAttribute('aria-label', `Go to slide ${index + 1}`);
      if (index === 0) dot.classList.add('active');
      
      dot.addEventListener('click', () => {
        goToSlide(index);
        resetAutoSlide();
      });
      
      sliderDots.appendChild(dot);
    });

    const dots = sliderDots.querySelectorAll('.ma-slider-dot');

    function goToSlide(index) {
      currentIndex = index;
      
      slides.forEach((slide, i) => {
        slide.classList.toggle('active', i === currentIndex);
      });
      
      dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === currentIndex);
      });
    }

    function nextSlide() {
      currentIndex = (currentIndex + 1) % totalSlides;
      goToSlide(currentIndex);
    }

    function resetAutoSlide() {
      clearInterval(autoSlideInterval);
      autoSlideInterval = setInterval(nextSlide, 4000);
    }

    // Start auto slide
    resetAutoSlide();

    // Pause on hover
    heroSlider.addEventListener('mouseenter', () => {
      clearInterval(autoSlideInterval);
    });
    
    heroSlider.addEventListener('mouseleave', () => {
      resetAutoSlide();
    });
  }
});
