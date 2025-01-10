document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("book-form");
    const modal = document.getElementById("book_modal");
    const modalBackdrop = document.getElementById("modal-backdrop"); // Sélectionnez l'élément de fond (overlay)

    form.addEventListener("submit", function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append("user_id", userId); // Ajoute l'ID de l'utilisateur dans les données du formulaire

        fetch("/bookread/create", {
            method: "POST",
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    if (modalBackdrop) {
                        modalBackdrop.style.display = 'none';
                    }
                    window.location.reload();
                } else {
                }
            })
            .catch(error => {
                console.error("Erreur AJAX : ", error);
                alert("Une erreur s'est produite.");
            });
    });
});
