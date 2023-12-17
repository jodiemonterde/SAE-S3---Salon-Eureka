$(document).ready(function () {
    // Vérifiez la largeur de l'écran lors du chargement initial
    checkScreenSize();

    // Vérifiez la largeur de l'écran lors du redimensionnement de la fenêtre
    $(window).resize(checkScreenSize);

    // Gestionnaire d'événements pour le bouton sur les petits écrans
    $('.accordion button[data-bs-toggle="collapse"]').on('click', function () {
        if (window.innerWidth < 768) {
            // Obtenez l'ID de l'accordéon à partir de l'attribut data-bs-target du bouton
            var accordionId = $(this).attr('data-bs-target');

            // Ajoutez 'modal' à l'ID de l'accordéon pour obtenir l'ID de la modale
            var modalId = accordionId.replace('#companyAccordion', '#modal');

            // Affichez la modale correspondante
            showModal(modalId);
        }
    });

    // Gestionnaire d'événements pour les accordéons
    $('.accordion').on('shown.bs.collapse', function () {
        // Ajoute la classe pour l'accordéon ouvert
        $(this).find('.card-header').addClass('accordion-header-opened');
    });

    $('.accordion').on('hidden.bs.collapse', function () {
        // Retire la classe pour l'accordéon fermé
        $(this).find('.card-header').removeClass('accordion-header-opened');
    });

    function showModal(modalId) {
        // Supprimez la modal existante s'il y en a une
        var existingModal = bootstrap.Modal.getInstance($(modalId)[0]);
        if (existingModal) {
            existingModal.dispose();
        }

        // Utilisez le gestionnaire d'événements Bootstrap pour afficher la modal
        $(modalId).on('shown.bs.modal', function () {
            $(modalId).off('shown.bs.modal'); // Désactivez l'événement après son déclenchement
            $(modalId).modal('show');
        });

        // Affichez la modal
        $(modalId).modal('show');
    }

    function checkScreenSize() {
        // Obtenez la largeur actuelle de l'écran
        var screenWidth = window.innerWidth;

        // Sélectionnez tous les éléments d'accordéon
        var accordions = $(".accordion");

        accordions.each(function () {
            // Fermez l'accordéon actuel
            $(this).find('.collapse').removeClass('show');

            // Si l'écran est petit, désactivez complètement l'accordéon actuel
            if (screenWidth < 768) {
                // Désactivez l'accordéon en supprimant les attributs de données
                $(this).find('[data-bs-toggle="collapse"]').attr('data-bs-toggle', '');
            } else {
                // Si l'écran est large, réactivez l'accordéon en rétablissant les attributs de données
                $(this).find('[data-bs-toggle=""]').attr('data-bs-toggle', 'collapse');
            }
        });
    }
});
