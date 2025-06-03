document.addEventListener('DOMContentLoaded', function() {
    console.log("AdminPro script.js loaded"); // For debugging

    // --- Sidebar Navigation Dropdowns ---
    const sidebarDropdownToggles = document.querySelectorAll('.sidebar .nav-item.dropdown-toggle');
    sidebarDropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdownMenu = this.nextElementSibling; // Assumes .dropdown-menu is the immediate next sibling
            const parentLi = this.closest('.nav-dropdown'); // The <li> containing toggle and menu

            if (dropdownMenu) {
                // Optional: Close other open submenus in the sidebar
                // document.querySelectorAll('.sidebar .dropdown-menu.dropdown-open').forEach(openMenu => {
                //     if (openMenu !== dropdownMenu) {
                //         openMenu.classList.remove('dropdown-open');
                //         const otherToggle = openMenu.previousElementSibling;
                //         if (otherToggle) {
                //             otherToggle.classList.remove('active');
                //             otherToggle.setAttribute('aria-expanded', 'false');
                //             const otherChevron = otherToggle.querySelector('.dropdown-icon');
                //             if (otherChevron) {
                //                 otherChevron.classList.remove('fa-chevron-up'); // Or your 'open' icon class
                //                 otherChevron.classList.add('fa-chevron-down'); // Or your 'closed' icon class
                //             }
                //         }
                //     }
                // });

                dropdownMenu.classList.toggle('dropdown-open'); // Your CSS uses .dropdown-open
                this.classList.toggle('active', dropdownMenu.classList.contains('dropdown-open'));
                this.setAttribute('aria-expanded', dropdownMenu.classList.contains('dropdown-open'));
                if (parentLi) {
                    parentLi.classList.toggle('active', dropdownMenu.classList.contains('dropdown-open'));
                }

                const chevron = this.querySelector('.dropdown-icon');
                if (chevron) {
                    // Ensure classes are exclusive if they represent state
                    if (dropdownMenu.classList.contains('dropdown-open')) {
                        chevron.classList.remove('fa-chevron-down'); // Or your default icon for closed
                        chevron.classList.add('fa-chevron-up');   // Or your icon for open
                    } else {
                        chevron.classList.remove('fa-chevron-up');
                        chevron.classList.add('fa-chevron-down');
                    }
                }
            }
        });
    });

    // --- Mobile Sidebar Toggle ---
    const mobileToggleBtns = document.querySelectorAll('.mobile-toggle-btn'); // Assuming ID is #mobileSidebarToggle as per navbar
    const adminSidebar = document.getElementById('adminSidebar'); // Assuming sidebar has id="adminSidebar"
    const body = document.body;
    const sidebarBackdrop = document.getElementById('sidebarBackdrop'); // Assuming backdrop has id="sidebarBackdrop"

    if (adminSidebar) {
        mobileToggleBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                adminSidebar.classList.toggle('show'); // Your CSS: .sidebar.show
                body.classList.toggle('sidebar-open');
                if (sidebarBackdrop) {
                    sidebarBackdrop.classList.toggle('show', adminSidebar.classList.contains('show'));
                }
            });
        });

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', function() {
                adminSidebar.classList.remove('show');
                body.classList.remove('sidebar-open');
                this.classList.remove('show');
            });
        }

        // Close sidebar when clicking outside on mobile (more robust)
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 992 && adminSidebar.classList.contains('show')) {
                const isClickInsideSidebar = adminSidebar.contains(e.target);
                let isClickOnToggler = false;
                mobileToggleBtns.forEach(btn => {
                    if (btn.contains(e.target)) {
                        isClickOnToggler = true;
                    }
                });

                if (!isClickInsideSidebar && !isClickOnToggler) {
                    adminSidebar.classList.remove('show');
                    body.classList.remove('sidebar-open');
                    if (sidebarBackdrop) sidebarBackdrop.classList.remove('show');
                }
            }
        });
    }


    // --- Navbar Dropdowns ---
    // (Notifications, Messages, Profile)
    const navbarDropdownToggles = document.querySelectorAll('.navbar-items .dropdown-toggle');
    navbarDropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const parentDropdown = this.closest('.dropdown'); // The .navbar-item.dropdown
            const dropdownContent = parentDropdown.querySelector('.dropdown-content');

            // Close other open navbar dropdowns
            document.querySelectorAll('.navbar-items .dropdown-content.show').forEach(openContent => {
                if (openContent !== dropdownContent) {
                    openContent.classList.remove('show');
                    openContent.closest('.dropdown').querySelector('.dropdown-toggle').classList.remove('active');
                     openContent.closest('.dropdown').classList.remove('show'); // Also from parent .dropdown
                }
            });

            if (dropdownContent) {
                dropdownContent.classList.toggle('show'); // Your CSS uses .dropdown-content.show
                this.classList.toggle('active', dropdownContent.classList.contains('show'));
                parentDropdown.classList.toggle('show', dropdownContent.classList.contains('show')); // For wrapper .dropdown.show
            }
        });
    });

    // Close navbar dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        let clickedInsideDropdown = false;
        document.querySelectorAll('.navbar-items .dropdown').forEach(dd => {
            if (dd.contains(e.target)) {
                clickedInsideDropdown = true;
            }
        });

        if (!clickedInsideDropdown) {
            document.querySelectorAll('.navbar-items .dropdown-content.show').forEach(openContent => {
                openContent.classList.remove('show');
                openContent.closest('.dropdown').querySelector('.dropdown-toggle').classList.remove('active');
                openContent.closest('.dropdown').classList.remove('show');
            });
        }
    });

    // --- Basic Tab Navigation ---
    // (Used for example in roles/permissions if you build that, or user profile tabs)
    // Assumes a structure like: <ul class="nav nav-tabs"><li class="nav-item"><a class="nav-link tab" data-tab="tab1">...</a></li></ul>
    // And <div class="tab-pane" id="tab1-tab">...</div>
    const tabs = document.querySelectorAll('.nav-tabs .nav-link.tab, .nav-pills .nav-link[data-toggle="tab"]'); // Handle Bootstrap-like tabs too
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const targetSelector = this.dataset.target || this.getAttribute('href'); // Use data-target or href
            if (!targetSelector || !targetSelector.startsWith('#')) return;

            const targetPane = document.querySelector(targetSelector);

            // Deactivate other tabs in the same group
            this.closest('.nav').querySelectorAll('.nav-link.active').forEach(activeLink => {
                activeLink.classList.remove('active');
                activeLink.setAttribute('aria-selected', 'false');
            });
             this.closest('.nav').parentElement.querySelectorAll('.tab-pane.active').forEach(activePane => {
                activePane.classList.remove('active', 'show');
            });


            // Activate clicked tab and its pane
            this.classList.add('active');
            this.setAttribute('aria-selected', 'true');
            if (targetPane) {
                targetPane.classList.add('active', 'show');
            }
        });
    });

    // --- Client-side "Mark all as read" for Notifications/Messages (visual only) ---
    // Real implementation requires AJAX
    const markAllReadLinks = document.querySelectorAll('.mark-all-read');
    markAllReadLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdownContent = this.closest('.dropdown-content');
            if (!dropdownContent) return;
            const dropdownBody = dropdownContent.querySelector('.dropdown-body');
            if (!dropdownBody) return;

            dropdownBody.querySelectorAll('.notification-item.unread, .message-item.unread').forEach(item => {
                item.classList.remove('unread');
            });

            // Update main badge for this dropdown
            const mainToggle = dropdownContent.closest('.dropdown').querySelector('.dropdown-toggle');
            if (mainToggle) {
                const badge = mainToggle.querySelector('.badge');
                if (badge) {
                    badge.textContent = '0';
                    badge.style.display = 'none'; // Or remove it
                }
            }
            // Optionally close dropdown:
            // dropdownContent.classList.remove('show');
            // if(mainToggle) mainToggle.classList.remove('active');
            // if(mainToggle) mainToggle.closest('.dropdown').classList.remove('show');

            // Placeholder for AJAX call
            const markReadUrl = this.dataset.markReadUrl; // e.g., data-mark-read-url="{{ route('notifications.markallread') }}"
            if (markReadUrl) {
                console.log("AJAX call to: " + markReadUrl);
                // fetch(markReadUrl, { method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', ... }})
                // .then(...)
            }
        });
    });


    // --- Client-side Table Search ---
    const searchInputs = document.querySelectorAll('.search-input'); // General search input
    searchInputs.forEach(input => {
        // Prevent form submission if inside a form (navbar search might not be)
        if (input.closest('form')) {
            input.closest('form').addEventListener('submit', function(e){
                if(input === document.activeElement) e.preventDefault(); // Prevent submit if search input is focused
            });
        }

        input.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase().trim();
            let tableToSearch = null;

            // Try to find table in common locations relative to search input
            if (this.dataset.table) { // If search input has data-table="#myTableId"
                tableToSearch = document.querySelector(this.dataset.table);
            } else if (this.closest('.card-header') && this.closest('.card-header').nextElementSibling && this.closest('.card-header').nextElementSibling.querySelector('table')) {
                tableToSearch = this.closest('.card-header').nextElementSibling.querySelector('table'); // Table in card-body after card-header
            } else if (this.closest('.card') && this.closest('.card').querySelector('table')) {
                tableToSearch = this.closest('.card').querySelector('table'); // Table anywhere in card
            } else {
                tableToSearch = document.querySelector('.main-content table'); // Fallback to first table in main content
            }


            if (tableToSearch) {
                const rows = tableToSearch.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const textContent = row.textContent.toLowerCase();
                    row.style.display = textContent.includes(searchValue) ? '' : 'none';
                });
            }
        });
    });


    // --- Activate Sidebar Nav Item based on Current URL ---
    try {
        const currentPath = window.location.pathname + window.location.search; // Include query string for more exact match
        const exactMatchLink = document.querySelector(`.sidebar .nav-menu a[href="${currentPath}"]`);

        if (exactMatchLink) {
            activateSidebarLink(exactMatchLink);
        } else {
            // Fallback: Try to find the closest match if no exact match (e.g. for edit pages)
            const currentPathBase = window.location.pathname;
            let bestMatch = null;
            let bestMatchLength = 0;

            document.querySelectorAll('.sidebar .nav-menu a.nav-item, .sidebar .nav-menu a.dropdown-item').forEach(link => {
                const linkPath = new URL(link.href).pathname;
                if (currentPathBase.startsWith(linkPath) && linkPath.length > bestMatchLength) {
                    // Prioritize longer paths if multiple startWith matches
                    if (linkPath !== "/" || currentPathBase === "/") { // Avoid matching all to "/" unless it is the actual root
                       bestMatch = link;
                       bestMatchLength = linkPath.length;
                    }
                }
            });
            if (bestMatch) {
                activateSidebarLink(bestMatch);
            }
        }
    } catch (e) {
        console.error("Error activating sidebar link:", e);
    }

    function activateSidebarLink(linkElement) {
        if (!linkElement) return;

        // Remove active from all other items first
        document.querySelectorAll('.sidebar .nav-item.active, .sidebar .dropdown-item.active').forEach(activeEl => {
            activeEl.classList.remove('active');
        });
        document.querySelectorAll('.sidebar .nav-dropdown.active').forEach(activeEl => {
             activeEl.classList.remove('active');
        });


        linkElement.classList.add('active');
        const parentDropdownMenu = linkElement.closest('.dropdown-menu');

        if (parentDropdownMenu) { // It's a sub-item
            parentDropdownMenu.classList.add('dropdown-open');
            const toggle = parentDropdownMenu.previousElementSibling; // The .nav-item.dropdown-toggle
            if (toggle) {
                toggle.classList.add('active');
                toggle.setAttribute('aria-expanded', 'true');
                const chevron = toggle.querySelector('.dropdown-icon');
                if (chevron) {
                    chevron.classList.remove('fa-chevron-down');
                    chevron.classList.add('fa-chevron-up'); // Your open icon class
                }
            }
            const parentLiDropdown = parentDropdownMenu.closest('.nav-dropdown');
            if(parentLiDropdown) parentLiDropdown.classList.add('active');

        } else if (linkElement.classList.contains('dropdown-toggle')) { // It's a top-level dropdown toggle itself
            const dropdownMenu = linkElement.nextElementSibling;
            // if (dropdownMenu) dropdownMenu.classList.add('dropdown-open'); // Only open if it's meant to be active directly
            linkElement.setAttribute('aria-expanded', 'true');
            const chevron = linkElement.querySelector('.dropdown-icon');
            if (chevron) {
                 chevron.classList.remove('fa-chevron-down');
                 chevron.classList.add('fa-chevron-up');
            }
            const parentLiDropdown = linkElement.closest('.nav-dropdown');
            if(parentLiDropdown) parentLiDropdown.classList.add('active');
        }
    }


    // --- Initialize plugins on dynamically added content (if any via AJAX/JS repeaters) ---
    // This is a placeholder. Specific plugins like Select2/Datepicker need re-init after new DOM elements are added.
    // The repeater JS already handles datepicker for new items.
    function initializeDynamicPlugins(parentElement) {
        // Example for Select2 on new elements if you use it in repeaters:
        // $(parentElement).find('.select2-dynamic').each(function() {
        //     if (!$(this).data('select2')) { // Check if not already initialized
        //         $(this).select2({ theme: 'bootstrap4', width: '100%' });
        //     }
        // });
    }

    // --- Global AJAX Setup for CSRF token --- (If using jQuery for AJAX elsewhere)
    // $.ajaxSetup({
    //     headers: {
    //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    //     }
    // });

    // --- Initialize Bootstrap components if you were using them (but your JS seems to handle toggles) ---
    // Example: Initialize tooltips
    // const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')) // Bootstrap 5
    // const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    //   return new bootstrap.Tooltip(tooltipTriggerEl)
    // })

    // Example: Initialize Bootstrap 4 Modals, Popovers, Tooltips, Alerts (if not handled by data attributes)
    // This is only if your custom CSS *doesn't* replace Bootstrap's JS needs.
    // Your current JS handles custom dropdowns. Bootstrap modals would still need their JS.
    // $('[data-toggle="tooltip"]').tooltip();
    // $('[data-toggle="popover"]').popover();
    // $('.modal').modal(); // This would auto-show modals without trigger. Better to trigger via JS: $('#myModal').modal('show');
    // $('.alert').alert(); // For programmatic dismissal, data-dismiss handles it usually.

});
