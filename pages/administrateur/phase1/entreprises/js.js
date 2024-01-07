// Déclarez une variable globale pour suivre le nombre d'intervenants
var numeroIntervenant = 1;

function ajouterIntervenant(event) {
    // Empêcher l'envoi automatique du formulaire
    event.preventDefault();

    // Cloner la div du premier intervenant
    var intervenantDiv = document.getElementById('intervenantTemplate').cloneNode(true);

    // Mettre à jour les IDs et les noms des champs dans le nouveau div
    mettreAJourIDs(intervenantDiv);

    // Trouver l'élément h2 avec l'id 'numeroIntervenant' dans le div cloné
    var numeroIntervenantElement = intervenantDiv.querySelector('#numeroIntervenant');

    // Mettre à jour le texte du h2 pour refléter le numéro d'intervenant actuel
    if (numeroIntervenantElement) {
        numeroIntervenantElement.innerText = numeroIntervenant;
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
