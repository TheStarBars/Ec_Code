document.addEventListener('DOMContentLoaded', function () {
    const modal = document.querySelector('#book_reading_modal');
    const form = document.querySelector('#book-reading-form');
    const modalBackdrop = document.querySelector('.modal-backdrop'); // Sélectionner l'overlay de la page
    const body = document.querySelector('body'); // Le body pour gérer l'effet de blocage

    // Ouvrir le modal avec les données
    document.querySelectorAll('[data-modal-toggle="#book_reading_modal"]').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
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

            // Afficher le modal et ajouter l'overlay
            modal.style.display = 'block'; // Afficher le modal
            body.style.overflow = 'hidden'; // Désactiver le défilement de la page derrière le modal
            modalBackdrop.style.display = 'block'; // Afficher l'overlay
        });
    });

    // Fermer le modal si on clique en dehors
    window.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Soumettre le formulaire
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch('/bookread/update', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Ferme le modal
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    if (modalBackdrop) {
                        modalBackdrop.style.display = 'none';
                    }

                    // Réinitialise le formulaire
                    form.reset();

                    // Recharge la page pour refléter les nouvelles données
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

