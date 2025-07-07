"use strict";
document.querySelector('.login-form').addEventListener('submit', function(event) {
    const email = this.email.value.trim();
    const firstName = this.first_name.value.trim();
    const lastName = this.last_name.value.trim();
    const phone = this.phone.value.trim();
    const address = this.address.value.trim();
    const password = this.password.value;
    const confirmPassword = this.confirm_password.value;

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phonePattern = /^[0-9]*$/;

    if (!firstName || !lastName) {
        alert("Ime i prezime su obavezni.");
        event.preventDefault();
        return;
    }

    if (!emailPattern.test(email)) {
        alert("Unesite validnu email adresu.");
        event.preventDefault();
        return;
    }

    if (!address) {
        alert("Adresa je obavezna.");
        event.preventDefault();
        return;
    }

    if (phone && !phonePattern.test(phone)) {
        alert("Broj telefona može sadržati samo cifre.");
        event.preventDefault();
        return;
    }

    if (password !== confirmPassword) {
        alert("Lozinke se ne poklapaju.");
        event.preventDefault();
        return;
    }
});
