import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Alpine.js for mobile menu
document.addEventListener('alpine:init', () => {
    Alpine.data('sidebar', () => ({
        open: false,
        toggle() {
            this.open = !this.open;
        }
    }));
});

// Close mobile menu when clicking outside
document.addEventListener('click', (event) => {
    const sidebar = document.querySelector('[x-data*="sidebar"]');
    const mobileMenuButton = document.querySelector('[aria-label="Open sidebar"]');
    
    if (sidebar && !sidebar.contains(event.target) && 
        mobileMenuButton && !mobileMenuButton.contains(event.target)) {
        Alpine.store('sidebar', false);
    }
});