document.addEventListener('DOMContentLoaded', function() {

    // =========================================================
    // FUNÇÕES COMUNS E DE HIGHLIGHT
    // =========================================================
    const debounce = (func, delay) => {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => func(...args), delay);
        };
    };

    function normalize(str) {
        return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    function highlightText(element, query) {
        if (!query) return;
        const text = element.textContent;
        const normalizedQuery = normalize(query);
        const regex = new RegExp(`(${normalizedQuery})`, "gi");

        const parts = text.split(regex);
        element.innerHTML = parts
            .map(part => regex.test(normalize(part))
                ? `<span class="bg-yellow-200">${part}</span>`
                : part
            )
            .join("");
    }

    // =========================================================
    // 🔍 LÓGICA DE PESQUISA GLOBAL (explore.index)
    // =========================================================
    const exploreSearchInput = document.getElementById('explore-search-input');
    const exploreResultsContainer = document.getElementById('results-container');
    const activeTabInput = document.getElementById('active-tab-input');

    if (exploreSearchInput && exploreResultsContainer) {

        const performExploreSearch = async (query) => {
            const tab = activeTabInput ? activeTabInput.value : 'all';
            const url = `/explore?search=${encodeURIComponent(query)}&tab=${encodeURIComponent(tab)}`;

            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) throw new Error(`Erro HTTP ${response.status}`);

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newResults = doc.getElementById('results-container');

                if (newResults) {
                    exploreResultsContainer.innerHTML = newResults.innerHTML;

                    // Aplica highlight nas partes relevantes
                    if (query) {
                        exploreResultsContainer.querySelectorAll('.searchable').forEach(el => highlightText(el, query));
                    }
                }

            } catch (error) {
                console.error('Erro na busca AJAX do Explore:', error);
                exploreResultsContainer.innerHTML = `
                    <div class="text-center py-10 text-red-600 font-semibold">
                        Ocorreu um erro ao buscar os resultados. Tente novamente.
                    </div>
                `;
            }
        };

        exploreSearchInput.addEventListener('input', debounce((e) => {
            performExploreSearch(e.target.value.trim());
        }, 400));
    }

    // =========================================================
    // 🔎 LÓGICA DE EVENTOS (JSON + PAGINAÇÃO)
    // =========================================================
    const eventSearchInput = document.getElementById('search-input');
    const eventsContainer = document.getElementById('events-container');
    const paginationLinks = document.getElementById('pagination-links');

    if (eventSearchInput && eventsContainer && paginationLinks) {
        const getSearchParams = (currentQuery) => {
            const urlParams = new URLSearchParams(window.location.search);
            const params = new URLSearchParams();
            Array.from(urlParams.entries()).forEach(([key, value]) => {
                if (key !== 'search' && key !== 'page') params.append(key, value);
            });
            params.append('search', currentQuery);
            return params.toString();
        };

        const performEventSearch = async (query) => {
            const queryString = getSearchParams(query);
            const url = `/events?${queryString}`;

            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) throw new Error('Falha na rede: ' + response.status);
                const data = await response.json();

                eventsContainer.innerHTML = data.eventsHtml;
                paginationLinks.innerHTML = data.paginationHtml;

                if (query) {
                    eventsContainer.querySelectorAll('.event-name-searchable').forEach(el => highlightText(el, query));
                }

            } catch (error) {
                console.error('Erro na pesquisa AJAX de Eventos:', error);
                eventsContainer.innerHTML = `
                    <div class="col-span-full text-center p-10 text-red-600 font-semibold">
                        Ocorreu um erro ao buscar os eventos. Por favor, tente recarregar a página.
                    </div>
                `;
                paginationLinks.innerHTML = '';
            }
        };

        eventSearchInput.addEventListener('input', debounce((e) => {
            performEventSearch(e.target.value);
        }, 300));
    }

    // =========================================================
    // 📘 LÓGICA DE CURSOS (HTML Parsing)
    // =========================================================
    const courseSearchInput = document.getElementById("searchInput");

    if (courseSearchInput) {
        const searchForm = courseSearchInput.closest("form");

        function setupCourseSearch(listId, cardClass, noMessageId) {
            const list = document.getElementById(listId);
            if (!list) return;

            const baseUrl = list.dataset.url || searchForm.getAttribute('action') || window.location.pathname;
            const originalHTML = list.innerHTML;
            const noMessage = document.getElementById(noMessageId);

            function fetchList(query) {
                let url = baseUrl;
                if (query) url += `?search=${encodeURIComponent(query)}`;

                fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
                    .then(res => res.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, "text/html");
                        const newListContainer = doc.getElementById(listId);

                        if (newListContainer) {
                            list.innerHTML = newListContainer.innerHTML;
                        } else {
                            const newCards = doc.querySelectorAll(`#${listId} > *`);
                            list.innerHTML = "";
                            newCards.forEach(card => list.appendChild(card));
                        }

                        if (query) {
                            list.querySelectorAll(`.${cardClass}`).forEach(el => highlightText(el, query));
                        }
                        if (noMessage) noMessage.style.display = list.children.length > 0 ? "none" : "flex";
                    })
                    .catch(err => console.error('Erro na pesquisa AJAX de Cursos:', err));
            }

            courseSearchInput.addEventListener("keyup", debounce(() => {
                const query = courseSearchInput.value.trim();
                if (!query) {
                    list.innerHTML = originalHTML;
                    if (noMessage) noMessage.style.display = list.children.length > 0 ? "none" : "flex";
                }
                fetchList(query);
            }, 150));

            searchForm.addEventListener("submit", e => {
                if (!courseSearchInput.value.trim()) {
                    e.preventDefault();
                    window.location.href = baseUrl;
                }
            });
        }

        setupCourseSearch("coursesList", "course-title", "noCoursesMessage");
    }
});
