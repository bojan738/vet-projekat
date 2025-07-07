"use strict";
document.querySelector('.login-form').addEventListener('submit', function(e) {
    const emailInput = this.username.value.trim();
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailPattern.test(emailInput)) {
        alert('Molimo unesite validnu email adresu kao korisničko ime.');
        e.preventDefault();
    }
});
