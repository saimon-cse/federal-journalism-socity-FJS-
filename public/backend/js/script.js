// backend/js/script.js
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar navigation dropdowns
    const sidebarDropdownToggles = document.querySelectorAll('.nav-dropdown .dropdown-toggle');

    sidebarDropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();

            // Get the dropdown menu
            const dropdownMenu = this.nextElementSibling;

            // Toggle visibility of dropdown menu
            dropdownMenu.classList.toggle('dropdown-open');

            // Toggle chevron icon
            const chevron = this.querySelector('.dropdown-icon');
            if (chevron) {
                chevron.classList.toggle('fa-chevron-down');
                chevron.classList.toggle('fa-chevron-up');
            }
        });
    });

    // Mobile sidebar toggle - both in navbar and floating button
    const mobileToggleBtns = document.querySelectorAll('.mobile-toggle, .mobile-toggle-btn');
    const sidebar = document.querySelector('.sidebar');

    if (sidebar) {
        mobileToggleBtns.forEach(btn => {
            if (btn) {
                btn.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 992) {
                if (!e.target.closest('.sidebar') &&
                    !e.target.closest('.mobile-toggle') &&
                    !e.target.closest('.mobile-toggle-btn')) {
                    if (sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                }
            }
        });
    }

    // Navbar dropdown toggle
    const navbarDropdownToggles = document.querySelectorAll('.navbar .dropdown-toggle');

    navbarDropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Get parent dropdown
            const dropdown = this.closest('.dropdown');

            // Close any other open dropdowns first
            document.querySelectorAll('.navbar .dropdown').forEach(otherDropdown => {
                if (otherDropdown !== dropdown && otherDropdown.classList.contains('show')) {
                    otherDropdown.classList.remove('show');
                }
            });

            // Toggle the clicked dropdown
            dropdown.classList.toggle('show');
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.navbar .dropdown')) {
            document.querySelectorAll('.navbar .dropdown').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });

    // Tab navigation (for role/permissions page)
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            tabs.forEach(t => t.classList.remove('active'));

            // Add active class to clicked tab
            this.classList.add('active');

            // Hide all tab contents
            tabContents.forEach(content => content.classList.add('hidden'));

            // Show the relevant tab content
            const tabId = this.getAttribute('data-tab');
            const tabContent = document.getElementById(tabId + '-tab');
            if (tabContent) {
                tabContent.classList.remove('hidden');
            }
        });
    });

    // Mark notifications/messages as read
    const markAllReadLinks = document.querySelectorAll('.mark-all-read');

    markAllReadLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const dropdownContent = this.closest('.dropdown-content');
            if (!dropdownContent) return;

            const dropdownBody = dropdownContent.querySelector('.dropdown-body');
            if (!dropdownBody) return;

            const unreadItems = dropdownBody.querySelectorAll('.unread');

            // Mark all as read
            unreadItems.forEach(item => {
                item.classList.remove('unread');
            });

            // Update badge count
            const dropdown = this.closest('.dropdown');
            if (dropdown) {
                const badge = dropdown.querySelector('.badge');
                if (badge) {
                    badge.style.display = 'none';
                }
            }
        });
    });

    // Handle notification click
    const notificationItems = document.querySelectorAll('.notification-item');

    notificationItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // If it's a link with href="#", prevent default to avoid jumping to top
            if (this.getAttribute('href') === '#') {
                e.preventDefault();
            }

            // Remove unread class
            this.classList.remove('unread');

            // Update badge count
            updateBadgeCount('notification');
        });
    });

    // Handle message click
    const messageItems = document.querySelectorAll('.message-item');

    messageItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // If it's a link with href="#", prevent default to avoid jumping to top
            if (this.getAttribute('href') === '#') {
                e.preventDefault();
            }

            // Remove unread class
            this.classList.remove('unread');

            // Update badge count
            updateBadgeCount('message');
        });
    });

    // Update notification and message badges
    function updateBadgeCount(type) {
        if (type === 'notification' || type === 'all') {
            const notificationDropdown = document.querySelector('.dropdown-notifications');
            if (notificationDropdown) {
                const unreadNotifications = notificationDropdown.querySelectorAll('.notification-item.unread');
                const dropdown = notificationDropdown.closest('.dropdown');
                if (dropdown) {
                    const badge = dropdown.querySelector('.badge');
                    if (badge) {
                        if (unreadNotifications.length > 0) {
                            badge.textContent = unreadNotifications.length;
                            badge.style.display = '';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                }
            }
        }

        if (type === 'message' || type === 'all') {
            const messageDropdown = document.querySelector('.dropdown-messages');
            if (messageDropdown) {
                const unreadMessages = messageDropdown.querySelectorAll('.message-item.unread');
                const dropdown = messageDropdown.closest('.dropdown');
                if (dropdown) {
                    const badge = dropdown.querySelector('.badge');
                    if (badge) {
                        if (unreadMessages.length > 0) {
                            badge.textContent = unreadMessages.length;
                            badge.style.display = '';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                }
            }
        }
    }

    // Update all badges on page load
    updateBadgeCount('all');

    // Search functionality
    const searchInputs = document.querySelectorAll('.search-input');

    searchInputs.forEach(input => {
        input.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();

            // Look for table in closest card body or main content if not found
            let container = this.closest('.card-body');
            if (!container) {
                container = document.querySelector('.main-content');
            }

            if (container) {
                const table = container.querySelector('table');
                if (table) {
                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchValue) ? '' : 'none';
                    });
                }
            }
        });
    });

    // Prevent navbar search from submitting a form
    const navbarSearch = document.querySelector('.navbar-search .search-input');
    if (navbarSearch) {
        navbarSearch.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });
    }

    // Add active class to current nav item based on URL
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-menu a');

    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            // Add active class to the link
            link.classList.add('active');

            // If it's inside a dropdown, open the dropdown
            const dropdown = link.closest('.dropdown-menu');
            if (dropdown) {
                dropdown.classList.add('dropdown-open');

                // Find the toggle button and update its icon
                const toggle = dropdown.previousElementSibling;
                if (toggle) {
                    toggle.classList.add('active');
                    const chevron = toggle.querySelector('.dropdown-icon');
                    if (chevron) {
                        chevron.classList.remove('fa-chevron-down');
                        chevron.classList.add('fa-chevron-up');
                    }
                }
            }
        }
    });
});



