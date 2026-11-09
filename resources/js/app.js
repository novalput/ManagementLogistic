import './bootstrap';

import Alpine from 'alpinejs';
import { Html5Qrcode } from "html5-qrcode";

window.Alpine = Alpine;
window.Html5Qrcode = Html5Qrcode;

Alpine.start();

// Sidebar and Greeting Logic
window.openSidebar = function () {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');

    sidebar.classList.remove('-translate-x-full');
    sidebar.classList.add('translate-x-0');

    backdrop.classList.remove('hidden');
    // Small delay to allow display:block to apply before opacity transition
    setTimeout(() => {
        backdrop.classList.remove('opacity-0');
    }, 10);
}

window.closeSidebar = function () {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');

    sidebar.classList.remove('translate-x-0');
    sidebar.classList.add('-translate-x-full');

    backdrop.classList.add('opacity-0');
    // Wait for transition to finish before hiding
    setTimeout(() => {
        backdrop.classList.add('hidden');
    }, 300);
}

// Close sidebar when clicking a link on mobile
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('#sidebar nav a, #sidebar .user-profile-section a, #sidebar .user-profile-section button').forEach(link => {
        link.addEventListener('click', (e) => {
            // Stop propagation to prevent ghost clicks on elements behind the sidebar
            e.stopPropagation();

            if (window.innerWidth < 768) { // md breakpoint
                // Add a delay to allow the navigation to start before closing
                // This helps prevent ghost clicks and visual glitches
                setTimeout(() => {
                    closeSidebar();
                }, 400);
            }
        });
    });

    updateGreeting();
    setInterval(updateGreeting, 60000);
});

function getTokyoHour() {
    const now = new Date();
    const tokyoTime = new Date(
        now.toLocaleString('en-US', { timeZone: 'Asia/Tokyo' })
    );
    return tokyoTime.getHours();
}

function updateGreeting() {
    // Only update greeting on dashboard page
    if (!window.location.pathname.includes('/dashboard') && window.location.pathname !== '/') {
        return;
    }

    const hour = getTokyoHour();
    let greeting = '';

    if (hour >= 5 && hour < 11) {
        greeting = 'Good Morning';
    } else if (hour >= 11 && hour < 15) {
        greeting = 'Good Afternoon';
    } else if (hour >= 15 && hour < 18) {
        greeting = 'Good Evening';
    } else {
        greeting = 'Good Night';
    }

    const greetingElement = document.getElementById('greeting-text');
    if (greetingElement) {
        greetingElement.textContent = greeting;
    }
}
