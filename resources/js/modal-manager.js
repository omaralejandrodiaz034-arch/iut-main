/**
 * Modal Manager — Gestión global de modales
 *
 * Uso en HTML:
 *
 * BOTÓN DISPARADOR:
 *   <button data-modal-target="mi-modal">Abrir</button>
 *
 * ESTRUCTURA DEL MODAL:
 *   <div id="mi-modal" class="modal-overlay" aria-hidden="true">
 *     <div class="modal-content">
 *       <button class="close-modal">✕</button>
 *       <!-- contenido -->
 *     </div>
 *   </div>
 *
 * USO PROGRAMÁTICO:
 *   import { openModal, closeModal } from './modal-manager';
 *   openModal('mi-modal');
 *   closeModal('mi-modal');
 *   // O directamente desde la ventana global:
 *   window.openModal('mi-modal');
 */

/**
 * Abre un modal por su ID.
 * @param {string} id - El ID del elemento modal.
 */
function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) {
        console.warn(`[ModalManager] No se encontró el modal con id="${id}"`);
        return;
    }
    modal.setAttribute('aria-hidden', 'false');
    modal.classList.remove('hidden', 'pointer-events-none');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
    // Foco al primer elemento interactivo dentro del modal
    const focusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    if (focusable) {
        setTimeout(() => focusable.focus(), 50);
    }
}

/**
 * Cierra un modal por su ID.
 * @param {string} id - El ID del elemento modal.
 */
function closeModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.setAttribute('aria-hidden', 'true');
    modal.classList.add('hidden');
    modal.classList.remove('flex', 'pointer-events-none');
    // Restaurar scroll solo si no hay otro modal abierto
    const anyOpen = document.querySelector('.modal-overlay:not([aria-hidden="true"]):not(.hidden)');
    if (!anyOpen) {
        document.body.classList.remove('overflow-hidden');
    }
}

/**
 * Cierra todos los modales abiertos.
 */
function closeAllModals() {
    document.querySelectorAll('.modal-overlay[aria-hidden="false"]').forEach(modal => {
        closeModal(modal.id);
    });
}

// Inicialización automática
document.addEventListener('DOMContentLoaded', () => {
    // 1. Delegación de eventos en botones con data-modal-target
    document.addEventListener('click', (e) => {
        // Abrir modal
        const trigger = e.target.closest('[data-modal-target]');
        if (trigger) {
            e.preventDefault();
            openModal(trigger.getAttribute('data-modal-target'));
            return;
        }

        // Cerrar via botón .close-modal
        const closeBtn = e.target.closest('.close-modal');
        if (closeBtn) {
            const modal = closeBtn.closest('.modal-overlay');
            if (modal) closeModal(modal.id);
            return;
        }

        // Cerrar al hacer clic en el overlay (fuera del .modal-content)
        if (e.target.classList.contains('modal-overlay')) {
            closeModal(e.target.id);
        }
    });

    // 2. Cerrar con tecla Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('.modal-overlay[aria-hidden="false"]');
            if (openModals.length > 0) {
                closeModal(openModals[openModals.length - 1].id);
            }
        }
    });
});

// Exponer globalmente para uso desde Blade / scripts inline
window.openModal = openModal;
window.closeModal = closeModal;
window.closeAllModals = closeAllModals;

export { openModal, closeModal, closeAllModals };
