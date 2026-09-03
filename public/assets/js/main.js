// Platzhalter: später fürs Burger-Menü / UI
console.log("trinken: main.js geladen");

document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.getElementById('kfzMenuToggle');
    const navigation = document.getElementById('kfzMainNavigation');

    if (!menuToggle || !navigation) {
        return;
    }

    menuToggle.addEventListener('click', function () {
        const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';

        menuToggle.setAttribute('aria-expanded', String(!isOpen));
        navigation.classList.toggle('is-open', !isOpen);
    });
});