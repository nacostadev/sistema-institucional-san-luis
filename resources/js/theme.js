/**
 * Gestor de Tema (Modo Claro / Modo Oscuro) - Instituto San Luis
 */

const THEME_KEY = 'sanluis-theme';

export function getPreferredTheme() {
    const stored = localStorage.getItem(THEME_KEY);
    if (stored === 'dark' || stored === 'light') {
        return stored;
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

export function setTheme(theme) {
    document.documentElement.setAttribute('data-bs-theme', theme);
    localStorage.setItem(THEME_KEY, theme);
    updateToggleButtons(theme);

    // Disparar evento para componentes que requieran reaccionar
    window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
}

export function toggleTheme() {
    const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    setTheme(next);
}

function updateToggleButtons(theme) {
    const toggles = document.querySelectorAll('[data-theme-toggle]');
    toggles.forEach(btn => {
        const icon = btn.querySelector('.theme-icon');
        const text = btn.querySelector('.theme-text');

        if (theme === 'dark') {
            btn.setAttribute('title', 'Cambiar a modo claro');
            btn.setAttribute('aria-label', 'Cambiar a modo claro');
            if (icon) {
                icon.className = 'bx bx-sun theme-icon';
            }
            if (text) {
                text.textContent = 'Modo claro';
            }
        } else {
            btn.setAttribute('title', 'Cambiar a modo oscuro');
            btn.setAttribute('aria-label', 'Cambiar a modo oscuro');
            if (icon) {
                icon.className = 'bx bx-moon theme-icon';
            }
            if (text) {
                text.textContent = 'Modo oscuro';
            }
        }
    });
}

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    const activeTheme = getPreferredTheme();
    setTheme(activeTheme);

    document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            toggleTheme();
        });
    });

    // Detectar cambios en la preferencia del sistema operativo si no se ha elegido manualmente
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (!localStorage.getItem(THEME_KEY)) {
            setTheme(e.matches ? 'dark' : 'light');
        }
    });
});

window.toggleTheme = toggleTheme;
window.setTheme = setTheme;
