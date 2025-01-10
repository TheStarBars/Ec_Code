document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.querySelector("input[name='query']");
    const resultsContainer = document.getElementById('search_results'); // Conteneur des résultats
    const modal = document.getElementById("search_modal");

    searchInput.addEventListener("input", function() {
        const query = searchInput.value.trim();

        if (query.length >= 3) {  // Lance la recherche après 3 caractères
            console.log('Recherche lancée avec :', query);  // Log pour vérifier que la recherche est bien lancée
            fetch(`/search/books?query=${query}`)
                .then(response => {
                    console.log('Réponse reçue:', response);  // Log de la réponse
                    return response.json();
                })
                .then(data => {
                    console.log('Données reçues:', data);  // Log des données
                    resultsContainer.innerHTML = ''; // Réinitialiser les résultats
                    if (data.length > 0) {
                        data.forEach(book => {
                            const bookItem = document.createElement('div');
                            bookItem.classList.add('menu-item');

                            bookItem.innerHTML = `
                                <div class="menu-link flex justify-between gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <img alt="Cover" class="rounded-full size-9 shrink-0" src="${book.image || '/path/to/default-image.jpg'}" />
                                        <div class="flex flex-col">
                                            <a class="text-sm font-semibold text-gray-900 hover:text-primary-active mb-px" href="#">
                                                ${book.title}
                                            </a>
                                            <span class="text-2sm font-normal text-gray-500">
                                                ${book.description}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2.5">
                                        <div class="rating">
                                            ${renderStars(book.rating)}
                                        </div>
                                    </div>
                                </div>
                            `;
                            resultsContainer.appendChild(bookItem);
                        });
                    } else {
                        resultsContainer.innerHTML = '<p>Aucun livre trouvé.</p>';
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert("Une erreur s'est produite lors de la recherche.");
                });
        }
    });

    // Fonction pour générer les étoiles en fonction de la note
    function renderStars(rating) {
        let stars = '';
        const maxStars = 5;
        for (let i = 0; i < maxStars; i++) {
            stars += i < rating
                ? '<i class="rating-on ki-solid ki-star text-base leading-none"></i>'
                : '<i class="rating-off ki-outline ki-star text-base leading-none"></i>';
        }
        return stars;
    }
});
