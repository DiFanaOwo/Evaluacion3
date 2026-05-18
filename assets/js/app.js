/* ============================================
   INGENIOSOS - FUNCIONALIDADES PRINCIPALES
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {
    initializeApp();
});

// INICIALIZACIÓN GENERAL
function initializeApp() {
    setupNavigation();
    setupCategoryCards();
    setupSearch();
    setupCartIcon();
    setupFavoritesIcon();
    setupUserIcon();
    addParticleEffects();
}

// ============================================
// NAVEGACIÓN
// ============================================
function setupNavigation() {
    const navBtns = document.querySelectorAll('.nav-btn');
    
    navBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            navBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Animación de clic
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 100);
        });
    });
}

// ============================================
// TARJETAS DE CATEGORÍAS
// ============================================
function setupCategoryCards() {
    const categoryCards = document.querySelectorAll('.category-card');
    
    categoryCards.forEach((card, index) => {
        // Hover effects
        card.addEventListener('mouseenter', function() {
            this.style.zIndex = 10;
            createRipple(this);
        });

        card.addEventListener('mouseleave', function() {
            this.style.zIndex = 'auto';
        });

        // Click action
        card.addEventListener('click', function() {
            if (this.classList.contains('see-all-card')) {
                handleViewAll();
            } else {
                const categoryTitle = this.querySelector('.category-title').textContent;
                handleCategoryClick(categoryTitle);
            }
        });
    });
}

function createRipple(element) {
    const ripple = document.createElement('div');
    ripple.style.position = 'absolute';
    ripple.style.width = '20px';
    ripple.style.height = '20px';
    ripple.style.background = 'rgba(255, 255, 255, 0.5)';
    ripple.style.borderRadius = '50%';
    ripple.style.pointerEvents = 'none';
    ripple.style.animation = 'rippleAnimation 0.6s ease-out';
    
    element.style.position = 'relative';
    element.appendChild(ripple);
    
    setTimeout(() => ripple.remove(), 600);
}

function handleCategoryClick(category) {
    console.log(`Abriendo categoría: ${category}`);
    // Aquí irá la lógica para filtrar productos por categoría
    showNotification(`Explorando: ${category}`);
}

function handleViewAll() {
    console.log('Viendo todas las categorías');
    showNotification('Cargando todas las categorías...');
}

// ============================================
// SEARCH
// ============================================
function setupSearch() {
    const searchBox = document.querySelector('.search-box');
    let searchTimeout;

    searchBox.addEventListener('focus', function() {
        this.style.boxShadow = '0 12px 28px rgba(0, 201, 215, 0.3)';
    });

    searchBox.addEventListener('blur', function() {
        this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
    });

    searchBox.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            submitSearch(this.value);
        }
    });
}

function submitSearch(query) {
    if (!query || !query.trim()) {
        showNotification('Ingresa un término para buscar.');
        return;
    }

    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.submit();
        return;
    }

    console.log(`Buscando: ${query}`);
    showNotification(`Buscando: "${query}"`);
}

// ============================================
// ICONOS DEL HEADER
// ============================================
function setupCartIcon() {
    const cartBtn = document.querySelector('.icon-btn[title="Carrito"]');
    
    if (cartBtn) {
        cartBtn.addEventListener('click', () => {
            showNotification('Abriendo carrito de compras...');
            animateCartIcon(cartBtn);
        });
    }
}

function setupFavoritesIcon() {
    const favBtn = document.querySelector('.icon-btn[title="Favoritos"]');
    
    if (favBtn) {
        favBtn.addEventListener('click', () => {
            showNotification('Favoritos cargados');
            animatePulse(favBtn);
        });
    }
}

function setupUserIcon() {
    const userBtn = document.querySelector('.icon-btn[title="Mi Perfil"]');
    
    if (userBtn) {
        userBtn.addEventListener('click', () => {
            showNotification('Abriendo perfil...');
            animateUser(userBtn);
        });
    }
}

function animateCartIcon(element) {
    element.style.animation = 'none';
    setTimeout(() => {
        element.style.animation = 'cartBounce 0.6s ease';
    }, 10);
}

function animatePulse(element) {
    element.style.animation = 'none';
    setTimeout(() => {
        element.style.animation = 'pulse 0.6s ease';
    }, 10);
}

function animateUser(element) {
    element.style.animation = 'none';
    setTimeout(() => {
        element.style.animation = 'userBounce 0.6s ease';
    }, 10);
}

// ============================================
// NOTIFICACIONES
// ============================================
function showNotification(message) {
    // Crear contenedor de notificación
    let notifContainer = document.getElementById('notif-container');
    
    if (!notifContainer) {
        notifContainer = document.createElement('div');
        notifContainer.id = 'notif-container';
        notifContainer.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            pointer-events: none;
        `;
        document.body.appendChild(notifContainer);
    }

    const notif = document.createElement('div');
    notif.style.cssText = `
        background: linear-gradient(135deg, #00C9D7, #5FE3E8);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 20px;
        margin-bottom: 10px;
        box-shadow: 0 8px 24px rgba(0, 201, 215, 0.3);
        animation: slideInRight 0.4s ease;
        font-weight: 600;
        max-width: 300px;
        word-break: break-word;
    `;
    
    notif.textContent = message;
    notifContainer.appendChild(notif);

    setTimeout(() => {
        notif.style.animation = 'slideOutRight 0.4s ease';
        setTimeout(() => notif.remove(), 400);
    }, 3000);
}

// ============================================
// EFECTOS DE PARTÍCULAS
// ============================================
function addParticleEffects() {
    // Agregar animaciones CSS dinámicamente
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        @keyframes cartBounce {
            0% { transform: scale(1); }
            25% { transform: scale(1.1) rotate(-5deg); }
            50% { transform: scale(1); }
            75% { transform: scale(1.1) rotate(5deg); }
            100% { transform: scale(1); }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }

        @keyframes userBounce {
            0% { transform: scale(1); }
            50% { transform: scale(1.2) translateY(-5px); }
            100% { transform: scale(1); }
        }

        @keyframes rippleAnimation {
            from {
                transform: scale(0);
                opacity: 1;
            }
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        .icon-btn {
            animation-fill-mode: forwards !important;
        }

        /* Efecto Hover mejorado */
        .category-card:active {
            transform: scale(0.98) !important;
        }
    `;
    
    document.head.appendChild(style);
}

// ============================================
// UTILIDADES
// ============================================
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}



// Log de inicialización
console.log('%c✨ Ingeniosos Cargado', 'font-size: 20px; color: #00C9D7; font-weight: bold;');
