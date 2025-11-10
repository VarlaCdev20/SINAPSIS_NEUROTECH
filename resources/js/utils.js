/* ==========================================================
   🧠 UTILS.JS – Funciones globales de apoyo (SINAPSIS)
   ========================================================== */

/* ==========================================================
   🔸 SCROLL Y NAVEGACIÓN SUAVE
   ========================================================== */

/**
 * Desplaza suavemente hacia un elemento del DOM.
 * @param {string} targetId - ID del elemento destino.
 * @param {number} offset - Desplazamiento opcional (para compensar header fijo).
 */
export function smoothScrollTo(targetId, offset = 80) {
  const target = document.getElementById(targetId);
  if (target) {
    window.scrollTo({
      top: target.offsetTop - offset,
      behavior: 'smooth',
    });
  }
}

/**
 * Asigna el comportamiento de scroll suave a todos los enlaces con href="#".
 */
export function enableSmoothScroll() {
  const links = document.querySelectorAll('a[href^="#"]');
  links.forEach(link => {
    link.addEventListener('click', e => {
      const targetId = link.getAttribute('href').substring(1);
      const target = document.getElementById(targetId);
      if (target) {
        e.preventDefault();
        smoothScrollTo(targetId);
      }
    });
  });
}

/* ==========================================================
   🔸 EFECTOS VISUALES GLOBALES
   ========================================================== */

/**
 * Agrega o quita clases de brillo (glow) a un elemento a intervalos.
 * @param {HTMLElement} el - Elemento objetivo.
 * @param {string} className - Clase de brillo (por defecto dorado).
 * @param {number} interval - Intervalo en milisegundos.
 */
export function toggleGlow(el, className = 'text-glow-gold', interval = 3000) {
  if (!el) return;
  setInterval(() => el.classList.toggle(className), interval);
}

/**
 * Cambia el aspecto visual del header al hacer scroll (transparente → sólido).
 */
export function updateHeaderOnScroll() {
  const header = document.querySelector('header');
  if (!header) return;

  window.addEventListener('scroll', () => {
    if (window.scrollY > 100) {
      header.classList.add('bg-base-dark/95', 'shadow-2xl', 'backdrop-blur-xl');
    } else {
      header.classList.remove('bg-base-dark/95', 'shadow-2xl', 'backdrop-blur-xl');
    }
  });
}

/**
 * Aplica animación de entrada (fade-in) a los elementos con clase .animate-fadeIn
 */
export function enableFadeInObserver() {
  const fadeElements = document.querySelectorAll('.animate-fadeIn');
  const fadeObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('opacity-100', 'translate-y-0');
        fadeObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.2 });
  fadeElements.forEach(el => fadeObserver.observe(el));
}

/* ==========================================================
   🔸 VALIDACIONES RÁPIDAS / UTILITARIOS
   ========================================================== */

/**
 * Verifica si un correo tiene formato válido.
 * @param {string} email
 * @returns {boolean}
 */
export function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

/**
 * Verifica si una cadena contiene solo letras (permite tildes y espacios).
 * @param {string} str
 * @returns {boolean}
 */
export function isAlpha(str) {
  return /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]+$/.test(str);
}

/**
 * Verifica si una cadena contiene solo números.
 * @param {string} str
 * @returns {boolean}
 */
export function isNumeric(str) {
  return /^\d+$/.test(str);
}

/* ==========================================================
   🔸 LOGS CON ESTILO
   ========================================================== */

/**
 * Muestra mensajes estilizados en la consola.
 * @param {string} message - Texto del mensaje.
 * @param {string} color - Color personalizado (por defecto dorado).
 */
export function logStyled(message, color = '#FFD700') {
  console.log(`%c${message}`, `color: ${color}; font-weight: bold;`);
}

/* ==========================================================
   🌟 INICIALIZADOR GLOBAL (opcional)
   ========================================================== */

/**
 * Inicializa las utilidades globales de SINAPSIS.
 * Puede llamarse directamente desde app.js
 */
export function initUtils() {
  enableSmoothScroll();
  updateHeaderOnScroll();
  enableFadeInObserver();
  const title = document.querySelector('.hero-title');
  toggleGlow(title);
  logStyled('Utilidades globales SINAPSIS activadas ⚙️', '#D5A7D5');
}

/* ==========================================================
   ✅ Log de carga del módulo
   ========================================================== */
logStyled('Módulo utils.js cargado 🧩', '#9276C7');
