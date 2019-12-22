document.getElementById('add-image').addEventListener('click', () => { 
    // Récupération des futurs champs a créer.
    const widgetsCounter = document.getElementById('widgets-counter');
    const index = parseInt(widgetsCounter.value);

    // Récupérer le prototype des entrées.
    const adImage = document.getElementById('ad_images');
    const tmpl = adImage.dataset.prototype.replace(/__name__/g, index);

    adImage.insertAdjacentHTML('beforeend', tmpl);
    widgetsCounter.value = index + 1;
    // gestion du bouton supprimer
    handleDeleteButtons();
});

function handleDeleteButtons()
{
    [...document.querySelectorAll('button[data-action="delete"]')].forEach(button => {
        button.addEventListener('click', el => {
            const target = el.target.dataset.target;
            document.querySelector(target).remove();
        });
    });
}

function updateCounter() 
{
    const count = parseInt(document.querySelectorAll('#ad_images div.form-group').length);
    document.getElementById('widgets-counter').value = count;
}

updateCounter();
handleDeleteButtons();