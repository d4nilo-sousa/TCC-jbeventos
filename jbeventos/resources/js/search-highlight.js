/**
 * resources/js/search-highlight.js
 * Lógica Híbrida Final:
 * 1. Eventos (AJAX JSON + Filtros + Paginação)
 * 2. Cursos (AJAX Simples + HTML Parsing)
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // =========================================================
    // VARIÁVEIS COMUNS E LÓGICA DE HIGHLIGHT
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
    // LÓGICA DE EVENTOS (JSON + FILTROS)
    // - Usa 'search-input', 'events-container', 'pagination-links'
    // =========================================================

    const eventSearchInput = document.getElementById('search-input');
    const eventsContainer = document.getElementById('events-container');
    const paginationLinks = document.getElementById('pagination-links');

    if (eventSearchInput && eventsContainer && paginationLinks) {
        
        const getSearchParams = (currentQuery) => {
            const urlParams = new URLSearchParams(window.location.search);
            const params = new URLSearchParams();

            Array.from(urlParams.entries()).forEach(([key, value]) => {
                if (key !== 'search' && key !== 'page') {
                    params.append(key, value);
                }
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

                if (!response.ok) {
                    throw new Error('Falha na resposta da rede: ' + response.status);
                }
                
                const data = await response.json();
                
                eventsContainer.innerHTML = data.eventsHtml;
                paginationLinks.innerHTML = data.paginationHtml;
                
            } catch (error) {
                console.error('Erro na pesquisa AJAX de Eventos:', error);
                eventsContainer.innerHTML = `
                    <div class="col-span-full text-center p-10 text-red-600 font-semibold">
                        Ocorreu um erro ao buscar os eventos (AJAX).
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
    // LÓGICA DE CURSOS (HTML Parsing)
    // - Procura pelos IDs da lógica antiga: 'coursesList', 'searchInput'
    // =========================================================
    
    // Usa a variável que você tinha no código anterior (searchInput no escopo global)
    // Se a página de Cursos não usa 'search-input', defina o ID correto aqui!
    const courseSearchInput = document.getElementById("searchInput"); // <-- VERIFIQUE ESTE ID!

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
                // Para cursos, você provavelmente não quer os filtros de eventos
                if (query) url += `?search=${encodeURIComponent(query)}`;

                fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
                    .then(res => res.text()) // Espera HTML/texto puro, não JSON
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, "text/html");
                        // Assume que o controller de Cursos retorna HTML que contém a div 'coursesList'
                        const newListContainer = doc.getElementById(listId);
                        
                        if (newListContainer) {
                             list.innerHTML = newListContainer.innerHTML;
                        } else {
                            // Se o controller de Cursos não retornar a div, tenta o método antigo
                            const newCards = doc.querySelectorAll(`#${listId} > *`);
                            list.innerHTML = "";
                            newCards.forEach(card => list.appendChild(card));
                        }
                        
                        // Highlight e mensagem de não encontrado
                        if (query) {
                            list.querySelectorAll(`.${cardClass}`).forEach(el => highlightText(el, query));
                        }
                        if (noMessage) noMessage.style.display = list.children.length > 0 ? "none" : "flex";
                    })
                    .catch(err => console.error('Erro na pesquisa AJAX de Cursos:', err));
            }

            courseSearchInput.addEventListener("keyup", debounce(() => {
                const query = courseSearchInput.value.trim();
                
                // Remove o highlight e limpa se a query estiver vazia
                if (!query) {
                    list.innerHTML = originalHTML; // Volta ao estado original
                    if (noMessage) noMessage.style.display = list.children.length > 0 ? "none" : "flex";
                }
                
                // Busca no backend
                fetchList(query);

            }, 150));
            
            // Tratamento de Submissão de formulário
            searchForm.addEventListener("submit", e => {
                 // Deixa o form submeter normalmente se a query não estiver vazia
                if (!courseSearchInput.value.trim()) {
                    e.preventDefault();
                    window.location.href = baseUrl; // Redireciona para a URL limpa se o campo estiver vazio
                }
            });
        }

        // Inicializa para cursos: 'coursesList' é o ID do container
        setupCourseSearch("coursesList", "course-title", "noCoursesMessage"); 
    }
});