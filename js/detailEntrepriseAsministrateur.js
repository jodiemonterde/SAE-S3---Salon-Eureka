// Déclarez une variable globale pour suivre le nombre d'intervenants
var numeroIntervenant = 2;

function ajouterIntervenant(event) {
    // Empêcher l'envoi automatique du formulaire
    event.preventDefault();

    // Cloner la div du premier intervenant
    var intervenantDiv = document.getElementById('intervenantTemplate').cloneNode(true);
    let trash = intervenantDiv.firstElementChild.firstElementChild.querySelector('.trash');
    trash.removeAttribute('hidden');
    
    // Mettre à jour les IDs et les noms des champs dans le nouveau div
    mettreAJourIDs(intervenantDiv);

    // Mettre à jour le texte du h2 pour refléter le numéro d'intervenant actuel
    var numeroIntervenantElement = intervenantDiv.querySelector('#numeroIntervenant_' + numeroIntervenant);

    if (numeroIntervenantElement) {
        numeroIntervenantElement.innerText = numeroIntervenant;
    }

    // Mettre à jour le nom du champ filieresIntervenant
    var filieresIntervenantInputs = intervenantDiv.querySelectorAll('[name^="filieresIntervenant"]');
    filieresIntervenantInputs.forEach(function (input) {
        input.name = 'filieresIntervenant_' + numeroIntervenant + '[]';
    });

    // Ajouter un gestionnaire d'événements au bouton de suppression
    var boutonSuppression = intervenantDiv.querySelector('.icon-title');
    if (boutonSuppression) {
        boutonSuppression.addEventListener('click', function () {
            // Appeler la fonction de suppression avec l'intervenantDiv
            supprimerIntervenant(intervenantDiv);
        });
    }

    // Ajouter le nouveau div au conteneur
    document.getElementById('intervenantsContainer').appendChild(intervenantDiv);

    // Incrémenter le numéro d'intervenant pour le prochain ajout
    numeroIntervenant++; 
}

function mettreAJourIDs(div) {
    // Parcourir tous les champs du div et mettre à jour les IDs et les noms
    var champs = div.querySelectorAll('[id], [name]');
    champs.forEach(function (champ) {
        champ.id = champ.id + '_' + numeroIntervenant;
        champ.name = champ.name + '_' + numeroIntervenant;

        // Vérifier si le champ est un h2 avec l'id 'numeroIntervenant' et le mettre à jour si nécessaire
        if (champ.tagName === 'H2' && champ.id === 'numeroIntervenant') {
            champ.innerText = numeroIntervenant;
        }
    });
}

function supprimerIntervenant(intervenantDiv) {
    // Récupérer le numéro de l'intervenant à supprimer
    var numeroIntervenantASupprimerElement = intervenantDiv.querySelector('[id^="numeroIntervenant"]');

    // Vérifier si l'élément est trouvé avant de tenter de lire 'innerText'
    if (numeroIntervenantASupprimerElement) {
        var numeroIntervenantASupprimer = parseInt(numeroIntervenantASupprimerElement.innerText);

        // Supprimer l'intervenantDiv du DOM
        intervenantDiv.remove();

        // Mettre à jour les numéros d'intervenants pour les éléments restants
        var intervenantsRestants = document.querySelectorAll('[id^="numeroIntervenant"]');
        intervenantsRestants.forEach(function (intervenant) {
            var numeroIntervenantActuel = parseInt(intervenant.innerText);

            // Si le numéro de l'intervenant est supérieur à celui supprimé, décrémenter le numéro
            if (numeroIntervenantActuel > numeroIntervenantASupprimer) {
                intervenant.innerText = numeroIntervenantActuel - 1;
            }
        });

        // Mettre à jour le name des champs filieresIntervenant pour les éléments restants
        var filieresIntervenantInputs = document.querySelectorAll('[name^="filieresIntervenant"]');
        filieresIntervenantInputs.forEach(function (input) {
            // Ajouter une vérification pour s'assurer que 'input.name' n'est pas nul
            if (input.name) {
                var matches = input.name.match(/\d+/);
                if (matches) {
                    var numeroIntervenantActuel = parseInt(matches[0]);

                    // Si le numéro de l'intervenant est supérieur à celui supprimé, décrémenter le numéro
                    if (numeroIntervenantActuel > numeroIntervenantASupprimer) {
                        var nouveauNumeroIntervenant = numeroIntervenantActuel - 1;
                        input.name = 'filieresIntervenant_' + nouveauNumeroIntervenant + '[]';
                        input.id = 'filieresIntervenant_' + nouveauNumeroIntervenant;
                    }
                }
            }
        });

        // Mettre à jour le name des champs nomIntervenant pour les éléments restants
        var nomIntervenantInputs = document.querySelectorAll('[name^="nomIntervenant"]');
        nomIntervenantInputs.forEach(function (input) {
            // Ajouter une vérification pour s'assurer que 'input.name' n'est pas nul
            if (input.name) {
                var matches = input.name.match(/\d+/);
                if (matches) {
                    var numeroIntervenantActuel = parseInt(matches[0]);

                    // Si le numéro de l'intervenant est supérieur à celui supprimé, décrémenter le numéro
                    if (numeroIntervenantActuel > numeroIntervenantASupprimer) {
                        var nouveauNumeroIntervenant = numeroIntervenantActuel - 1;
                        input.name = 'nomIntervenant_' + nouveauNumeroIntervenant;
                        input.id = 'nomIntervenant_' + nouveauNumeroIntervenant;
                    }
                }
            }
        });

        // Décrémenter le numéro d'intervenant pour le prochain ajout
        numeroIntervenant--;
    } else {
        console.error("Erreur: Impossible de trouver l'élément 'numeroIntervenant' à supprimer.");
    }
}

document.getElementById('myForm').addEventListener('submit', function(e) {
    var intervenantContainers = document.querySelectorAll('.intervenantContainer');
    for (var i = 0; i < intervenantContainers.length; i++) {
        var checkboxes = intervenantContainers[i].querySelectorAll('input[type="checkbox"]');
        var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
        if (!checkedOne) {
            alert("Veuillez sélectionner au moins une filière pour chaque intervenant.");
            e.preventDefault();
            return;
        }
    }
});




