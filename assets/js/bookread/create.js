/**
 * Script exécuté une fois que le DOM est complètement chargé.
 * Gère l'envoi d'un formulaire pour ajouter un livre via une requête AJAX.
 */
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("book-form");
    const modal = document.getElementById("book_modal");
    const modalBackdrop = document.getElementById("modal-backdrop");

    /**
     * Événement déclenché lors de la soumission du formulaire.
     * Empêche la soumission par défaut, envoie les données via AJAX, et met à jour l'interface.
     */
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append("user_id", userId);

        fetch("/bookread/create", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    // Ferme le modal
                    modal.classList.remove("show");
                    modal.style.display = "none";
                    if (modalBackdrop) {
                        modalBackdrop.style.display = "none";
                    }

                    form.reset();

                    window.location.reload();
                } else {
                    console.error("Erreur lors de la création :", data.message);
                }
            })
            .catch((error) => {
                console.error("Erreur AJAX : ", error);
                alert("Une erreur s'est produite.");
            });
    });
});
