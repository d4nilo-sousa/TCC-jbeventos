document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const searchForm = searchInput.closest("form");
    let timer = null;

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

    function setupSearch(listId, cardClass, noMessageId) {
        const list = document.getElementById(listId);
        if (!list) return;

        const baseUrl = list.dataset.url;
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
                    const newCards = doc.querySelectorAll(`#${listId} > *`);
                    list.innerHTML = "";
                    newCards.forEach(card => list.appendChild(card));

                    if (query) {
                        list.querySelectorAll(`.${cardClass}`).forEach(el => highlightText(el, query));
                    }

                    if (noMessage) noMessage.style.display = list.children.length > 0 ? "none" : "flex";
                })
                .catch(err => console.error(err));
        }

        searchInput.addEventListener("keyup", () => {
            clearTimeout(timer);
            const query = searchInput.value.trim();

            if (query) {
                list.querySelectorAll(`.${cardClass}`).forEach(el => {
                    el.innerHTML = el.textContent; // reset
                    highlightText(el, query);
                });
            }

            timer = setTimeout(() => {
                if (query) fetchList(query);
                else {
                    list.innerHTML = originalHTML;
                    if (noMessage) noMessage.style.display = list.children.length > 0 ? "none" : "flex";
                }
            }, 150);
        });

        searchForm.addEventListener("submit", e => {
            if (!searchInput.value.trim()) {
                e.preventDefault();
                window.location.href = baseUrl;
            }
        });

        searchInput.addEventListener("blur", () => {
            setTimeout(() => {
                if (document.activeElement !== searchInput) {
                    list.innerHTML = originalHTML;
                    if (noMessage) noMessage.style.display = list.children.length > 0 ? "none" : "flex";
                }
            }, 100);
        });

        searchInput.addEventListener("focus", () => {
            const query = searchInput.value.trim();
            if (query) fetchList(query);
        });
    }

    // Inicializa para cursos e eventos
    setupSearch("coursesList", "course-title", "noCoursesMessage");
    setupSearch("eventsList", "event-title", "noEventsMessage");
});
