/**
 * Script pour gérer l'ouverture et la soumission du modal de lecture de livres.
 * - Affichage des données dans le modal.
 * - Envoi des modifications via AJAX.
 */
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.querySelector('#book_reading_modal'); // Sélectionner le modal
    const form = document.querySelector('#book-reading-form'); // Sélectionner le formulaire dans le modal
    const modalBackdrop = document.querySelector('.modal-backdrop'); // Sélectionner l'overlay de la page
    const body = document.querySelector('body'); // Le body pour désactiver le défilement en arrière-plan

    /**
     * Ouvrir le modal et remplir les données dans le formulaire.
     * Les éléments déclencheurs doivent avoir l'attribut `data-modal-toggle`.
     */
    document.querySelectorAll('[data-modal-toggle="#book_reading_modal"]').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            // Récupérer les données depuis les attributs `data-*` du lien cliqué
            const readBookId = this.dataset.readbook;
            const bookId = this.dataset.book;
            const description = this.dataset.description;
            const rating = this.dataset.rating;
            const checked = this.dataset.checked === 'checked';

            modal.querySelector('#readbook').value = readBookId || '';
            modal.querySelector('#book').value = bookId || '';
            modal.querySelector('#description').value = description || '';
            modal.querySelector('#rating').value = rating || '1.0';
            modal.querySelector('#check').checked = checked;

            modal.style.display = 'block';
            body.style.overflow = 'hidden';
            modalBackdrop.style.display = 'block';
        });
    });

    /**
     * Fermer le modal en cliquant à l'extérieur.
     * Vérifie si l'utilisateur clique en dehors du contenu du modal.
     */
    window.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            modalBackdrop.style.display = 'none';
            body.style.overflow = '';
        }
    });

    /**
     * Soumettre le formulaire avec une requête AJAX.
     * Met à jour les données côté serveur et recharge la page en cas de succès.
     */
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form);
        
        fetch('/bookread/update', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    if (modalBackdrop) {
                        modalBackdrop.style.display = 'none';
                    }
                    form.reset();
                    window.location.reload();
                } else {
                    console.error("Erreur lors de la création :", data.message);
                }
            })
            .catch(error => {
                console.error("Erreur AJAX : ", error);
                alert("Une erreur s'est produite.");
            });
    });
});
