document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const searchForm = searchInput.closest("form");
    const eventsList = document.getElementById("eventsList");
    const baseUrl = eventsList.dataset.url;
    const originalHTML = eventsList.innerHTML;
    const noEventsMessage = document.getElementById("noEventsMessage");
    let timer = null;

    function normalize(str) {
        return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    function highlightText(element, query) {
        if (!query) return;
        const text = element.textContent;
        const normalizedText = normalize(text);
        const normalizedQuery = normalize(query);
        const regex = new RegExp(`(${normalizedQuery})`, "gi");

        // Cria array de partes para evitar problemas com purged CSS
        const parts = text.split(regex);
        element.innerHTML = parts
            .map(part => regex.test(normalize(part))
                ? `<span class="bg-yellow-200">${part}</span>`
                : part
            )
            .join("");
    }

    function fetchEvents(query) {
        let url = baseUrl;
        if (query) url += `?search=${encodeURIComponent(query)}`;

        fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, "text/html");
                const newCards = doc.querySelectorAll("#eventsList > *");
                eventsList.innerHTML = "";
                newCards.forEach(card => eventsList.appendChild(card));
                if (query) {
                    eventsList.querySelectorAll(".event-title").forEach(title => highlightText(title, query));
                }
                noEventsMessage.style.display = eventsList.children.length > 0 ? "none" : "flex";
            })
            .catch(err => console.error(err));
    }

    searchInput.addEventListener("keyup", () => {
        clearTimeout(timer);
        const query = searchInput.value.trim();

        if (query) {
            eventsList.querySelectorAll(".event-title").forEach(title => {
                title.innerHTML = title.textContent; // reseta
                highlightText(title, query);
            });
        }

        timer = setTimeout(() => {
            if (query) fetchEvents(query);
            else {
                eventsList.innerHTML = originalHTML;
                noEventsMessage.style.display = eventsList.children.length > 0 ? "none" : "flex";
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
                eventsList.innerHTML = originalHTML;
                noEventsMessage.style.display = eventsList.children.length > 0 ? "none" : "flex";
            }
        }, 100);
    });

    searchInput.addEventListener("focus", () => {
        const query = searchInput.value.trim();
        if (query) fetchEvents(query);
    });
});
