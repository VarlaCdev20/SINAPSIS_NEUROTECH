// ===========================================================
// 🎠 CAROUSEL SINAPSIS - Alpine.js (fade + autoplay + teclado)
// ===========================================================
document.addEventListener('alpine:init', () => {
    Alpine.data('carousel', (config = {}) => ({
        currentIndex: 0,
        images: Array.isArray(config.images) ? config.images : [],
        autoplayInterval: null,

        _len() {
            return this.images.length;
        },

        _getSrc(i) {
            const it = this.images[i];
            return typeof it === 'string' ? it : (it?.src || '');
        },

        next() {
            if (!this._len()) return;
            this.currentIndex = (this.currentIndex + 1) % this._len();
            this.resetAutoplay();
        },

        prev() {
            if (!this._len()) return;
            this.currentIndex = (this.currentIndex - 1 + this._len()) % this._len();
            this.resetAutoplay();
        },

        goToSlide(index) {
            if (!this._len()) return;
            if (index < 0 || index >= this._len()) return;
            this.currentIndex = index;
            this.resetAutoplay();
        },

        resetAutoplay() {
            if (this.autoplayInterval) clearInterval(this.autoplayInterval);
            this.autoplayInterval = setInterval(() => this.next(), 6000);
        },

        init() {
            if (!this._len()) {
                console.warn('⚠️ [Carousel] No hay imágenes cargadas.');
                return;
            }

            // Pre-carga de imágenes para evitar “parpadeos”
            this.images.forEach((_, i) => {
                const img = new Image();
                img.src = this._getSrc(i);
            });

            // Arranca autoplay
            this.resetAutoplay();

            // Pausa al pasar el mouse
            this.$root.addEventListener('mouseenter', () => {
                if (this.autoplayInterval) clearInterval(this.autoplayInterval);
            });

            // Reanuda al salir
            this.$root.addEventListener('mouseleave', () => this.resetAutoplay());

            // Navegación con teclado
            window.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') this.prev();
                if (e.key === 'ArrowRight') this.next();
            });
        }
    }));
});
