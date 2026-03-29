// ── UI Interactions ─────────────────────────────────────────────
// User menu dropdown and global search functionality

(function() {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', initUIInteractions);

    function initUIInteractions() {
        initUserMenu();
        initGlobalSearch();
    }

    // User Menu Dropdown
    function initUserMenu() {
        const userMenuBtn = document.getElementById('user-menu-btn');
        const userMenuDropdown = document.getElementById('user-menu-dropdown');

        if (!userMenuBtn || !userMenuDropdown) return;

        userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userMenuDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', () => {
            userMenuDropdown.classList.add('hidden');
        });

        userMenuDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    // Global Search AJAX
    function initGlobalSearch() {
        // Desktop search
        const searchInput = document.getElementById('global-search-input');
        const searchResults = document.getElementById('global-search-results');
        const searchWrap = document.getElementById('global-search-wrap');

        // Mobile search
        const searchInputMobile = document.getElementById('global-search-input-mobile');
        const searchResultsMobile = document.getElementById('global-search-results-mobile');
        const searchWrapMobile = document.getElementById('global-search-wrap-mobile');

        // Initialize desktop search
        if (searchInput && searchResults) {
            setupSearch(searchInput, searchResults, searchWrap);
        }

        // Initialize mobile search
        if (searchInputMobile && searchResultsMobile) {
            setupSearch(searchInputMobile, searchResultsMobile, searchWrapMobile);
        }
    }

    // Setup search for a given input/results pair
    function setupSearch(searchInput, searchResults, searchWrap) {
        let searchTimeout;

        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            const query = searchInput.value.trim();

            if (query.length < 2) {
                searchResults.classList.add('hidden');
                searchResults.innerHTML = '';
                return;
            }

            // Show loading state
            searchResults.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">Buscando...</div>';
            searchResults.classList.remove('hidden');

            searchTimeout = setTimeout(() => {
                performSearch(query, searchResults);
            }, 300);
        });

        // Close on click outside
        document.addEventListener('click', (e) => {
            if (searchWrap && !searchWrap.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                searchResults.classList.add('hidden');
                searchInput.blur();
            }
        });
    }

    // Perform search request
    function performSearch(query, resultsContainer) {
        // Get the search route from a meta tag or use a default
        const searchUrl = document.querySelector('meta[name="search-route"]')?.content || '/buscar';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        fetch(`${searchUrl}?q=${encodeURIComponent(query)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (!data || data.length === 0) {
                resultsContainer.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">Sin resultados</div>';
            } else {
                let html = '';
                data.forEach(item => {
                    const icon = item.icon || '📦';
                    const url = item.url || '#';
                    html += `<a href="${url}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition border-b border-gray-50 last:border-0">
                        <span class="text-lg">${icon}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${item.title || ''}</p>
                            <p class="text-xs text-gray-500 truncate">${item.subtitle || ''}</p>
                        </div>
                    </a>`;
                });
                resultsContainer.innerHTML = html;
            }
            resultsContainer.classList.remove('hidden');
        })
        .catch(() => {
            resultsContainer.innerHTML = '<div class="px-4 py-3 text-sm text-red-500">Error al buscar</div>';
        });
    }

})();
