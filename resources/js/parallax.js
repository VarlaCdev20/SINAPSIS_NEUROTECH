/* ==========================================================
   🌠 PARALLAX.JS – Control de fondos dinámicos
   ========================================================== */

document.addEventListener('alpine:init', () => {
  Alpine.data('parallaxInit', () => ({
    init() {
      // Cargar imágenes al iniciar
      this.loadBackgroundImages();

      // Escuchar el scroll para efecto de desplazamiento
      window.addEventListener('scroll', this.handleScroll.bind(this), { passive: true });

      // Aplicar la posición inicial
      this.handleScroll();
    },

    /* =============================================
       CARGA DE IMÁGENES DE FONDO
    ============================================= */
    loadBackgroundImages() {
      document.querySelectorAll('.section-background-image').forEach(bg => {
        const src = bg.dataset.src;
        if (src) {
          bg.style.backgroundImage = `url('${src}')`;
          bg.classList.add('fade-in');
        }

        // Configuración de brillo y opacidad
        const brightness = bg.dataset.brightness || 0.5;
        const opacity = bg.dataset.opacity || 0.3;

        bg.style.filter = `brightness(${brightness})`;
        bg.style.opacity = opacity;
      });
    },

    /* =============================================
       EFECTO DE DESPLAZAMIENTO (PARALLAX)
    ============================================= */
    handleScroll() {
      const scrollY = window.scrollY || window.pageYOffset;

      document.querySelectorAll('.section-background-image').forEach(bg => {
        const speed = parseFloat(bg.dataset.speed || 0.1);
        const translateY = scrollY * speed;
        bg.style.transform = `translateY(${translateY}px)`;
      });
    },
  }));
});

/* ==========================================================
   ✅ Log de inicialización
   ========================================================== */
console.log('%cEfecto Parallax SINAPSIS activo 🌌', 'color: #D5A7D5; font-weight: bold;');
