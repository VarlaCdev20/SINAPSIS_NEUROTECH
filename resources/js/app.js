/* ==========================================================
   ⚡ APP.JS – Archivo principal del frontend (SINAPSIS)
   ========================================================== */

/* Importación de estilos base (Tailwind + CSS propios) */
import '../css/app.css';

/* Activar Alpine.js (interactividad ligera) */
import Alpine from 'alpinejs';
import { initUtils } from './utils';

/* ✅ Evita que Alpine se ejecute dos veces */
if (!window.Alpine) {
    window.Alpine = Alpine;
    Alpine.start();
}

/* Importación de módulos personalizados */
import './carousel';
import './parallax';
import './forms';

/* Activa los efectos y helpers globales */
initUtils();

/* ==========================================================
   🧠 CONFIGURACIÓN GLOBAL – Eventos y helpers
   ========================================================== */

/* --- Animación suave al hacer scroll --- */
document.addEventListener('DOMContentLoaded', () => {
    const scrollLinks = document.querySelectorAll('a[href^="#"]');
    scrollLinks.forEach(link => {
        link.addEventListener('click', e => {
            const targetId = link.getAttribute('href').substring(1);
            const target = document.getElementById(targetId);
            if (target) {
                e.preventDefault();
                window.scrollTo({
                    top: target.offsetTop - 80, // evita superposición con header
                    behavior: 'smooth',
                });
            }
        });
    });
});

/* --- Efecto de aparición progresiva (fade-in) --- */
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

/* ========================================================== */

/* --- Brillo del header al hacer scroll --- */
window.addEventListener('scroll', () => {
    const header = document.querySelector('header');
    if (!header) return;
    if (window.scrollY > 100) {
        header.classList.add('bg-base-dark/95', 'shadow-2xl');
    } else {
        header.classList.remove('bg-base-dark/95', 'shadow-2xl');
    }
});

/* --- Parpadeo sutil del título principal --- */
const title = document.querySelector('.hero-title');
if (title) {
    setInterval(() => {
        title.classList.toggle('text-glow-gold');
    }, 3000);
}

/* ==========================================================
   ✅ LOG DE INICIO
   ========================================================== */
console.log('%cSINAPSIS Frontend Inicializado 🧠✨', 'color: #FFD700; font-weight: bold;');
