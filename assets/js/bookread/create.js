document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("book-form");
    const modal = document.getElementById("book_modal");
    const modalBackdrop = document.getElementById("modal-backdrop");

    form.addEventListener("submit", function(e) {
        e.preventDefault();
        console.log("form submitted");
        const formData = new FormData(form);
        formData.append("user_id", userId); // Ajoute l'ID de l'utilisateur dans les données du formulaire

        fetch("/bookread/create", {
            method: "POST",
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
