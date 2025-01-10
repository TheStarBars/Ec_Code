document.addEventListener('DOMContentLoaded', function() {
    // Capture tous les liens de type data-modal-toggle
    document.querySelectorAll('[data-modal-toggle="#book_reading_modal"]').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            console.log(this); // Vérifie l'élément cliqué
            console.log(document.querySelector('#book_reading_modal')); // Vérifie si le modal est bien sélectionné
            const bookId = this.getAttribute('data-book');
            const description = this.getAttribute('data-description');
            const rating = this.getAttribute('data-rating');
            const checked = this.getAttribute('data-checked');
            const bookReadId = this.getAttribute('data-id');

            console.log('bookId:', bookId);
            console.log('description:', description);
            console.log('rating:', rating);
            console.log('checked:', checked);

            // Si les données sont présentes, remplir le modal
            document.querySelector('#book').value = bookId || '';
            document.querySelector('#description').value = description || '';  // Remplissage du champ description
            document.querySelector('#check').checked = checked === 'checked';

            const ratingValue = parseFloat(rating).toFixed(1); // Assure-toi que la note est bien formatée
            const ratingSelect = document.querySelector('#rating');
            let found = false;
            Array.from(ratingSelect.options).forEach(option => {
                if (option.value === ratingValue) {
                    option.selected = true;
                    found = true;
                }
            });
            if (!found) {
                console.log("La note " + ratingValue + " n'a pas été trouvée parmi les options.");
            }

            const modal = document.getElementById("book_reading_modal");
            modal.style.display = 'block';
        });
    });

    // Capture la soumission du formulaire de mise à jour
    document.querySelector('#book-reading-form').addEventListener('submit', function(e) {
        e.preventDefault();  // Empêche la soumission classique du formulaire

        // Assure que la description est incluse dans FormData
        const descriptionField = document.querySelector('#description');
        if (!descriptionField.value) {
            console.log('Erreur: La description est vide.');
        }

        // Envoie les données via AJAX vers la route /bookread/update
        const formData = new FormData(this);  // Collecte toutes les données du formulaire
        fetch('/bookread/update', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Mise à jour réussie');
                    // Ferme le modal après la mise à jour
                    document.getElementById("book_reading_modal").style.display = 'none';
                } else {
                    alert('Erreur lors de la mise à jour');
                }
            })
            .catch(error => {
                console.error('Erreur AJAX:', error);
                alert('Une erreur est survenue.');
            });
    });
});
