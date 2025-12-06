/**
 * User Dropdown Menu - Global Handler
 * P-Mones - PT Pelabuhan Indonesia
 */

(function() {
    'use strict';
    
    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeUserDropdown();
    });
    
    function initializeUserDropdown() {
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userDropdownMenu = document.getElementById('userDropdownMenu');
        
        if (!userMenuBtn || !userDropdownMenu) {
            console.log('User dropdown elements not found on this page');
            return;
        }
        
        // Toggle dropdown on button click
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdownMenu.classList.toggle('show');
            
            // Add animation class
            if (userDropdownMenu.classList.contains('show')) {
                userMenuBtn.setAttribute('aria-expanded', 'true');
            } else {
                userMenuBtn.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userMenuBtn.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                userDropdownMenu.classList.remove('show');
                userMenuBtn.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Close dropdown when clicking menu items (except logout button)
        const dropdownItems = userDropdownMenu.querySelectorAll('.dropdown-item:not(.logout-btn)');
        dropdownItems.forEach(function(item) {
            item.addEventListener('click', function() {
                userDropdownMenu.classList.remove('show');
                userMenuBtn.setAttribute('aria-expanded', 'false');
            });
        });
        
        // Close dropdown on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && userDropdownMenu.classList.contains('show')) {
                userDropdownMenu.classList.remove('show');
                userMenuBtn.setAttribute('aria-expanded', 'false');
                userMenuBtn.focus(); // Return focus to button
            }
        });
        
        // Handle logout form submission
        const logoutForm = userDropdownMenu.querySelector('form');
        if (logoutForm) {
            logoutForm.addEventListener('submit', function(e) {
                // Optional: Add loading state
                const logoutBtn = this.querySelector('.logout-btn');
                if (logoutBtn) {
                    logoutBtn.innerHTML = '<i class="bi bi-hourglass-split"></i><span>Logging out...</span>';
                    logoutBtn.disabled = true;
                }
            });
        }
        
        console.log('✅ User dropdown initialized successfully');
    }
    
})();