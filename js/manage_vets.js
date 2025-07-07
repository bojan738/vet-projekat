"use strict";
const form = document.getElementById('vetForm');
const messageBox = document.getElementById('messageBox');
const submitBtn = document.getElementById('submitBtn');
const cancelBtn = document.getElementById('cancelBtn');
const formError = document.getElementById('formError');

form.addEventListener('submit', e => {
    e.preventDefault();
    messageBox.textContent = '';
    formError.textContent = '';

    const firstName = form.first_name.value.trim();
    const lastName = form.last_name.value.trim();
    const email = form.email.value.trim();
    const phoneNumber = form.phone_number.value.trim();

    // Klijentska validacija
    if (!firstName || !lastName) {
        formError.textContent = 'Ime i prezime su obavezni.';
        return;
    }
    if (!validateEmail(email)) {
        formError.textContent = 'Molimo unesite validnu email adresu.';
        return;
    }
    if (phoneNumber && !/^\d+$/.test(phoneNumber)) {
        formError.textContent = 'Telefon mora sadržati samo brojeve.';
        return;
    }

    const formData = new FormData(form);


    const isUpdate = formData.get('id') !== '';
    formData.append('action', isUpdate ? 'update' : 'add');

    fetch('', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            messageBox.style.color = data.success ? 'green' : 'red';
            messageBox.textContent = data.message;

            if (data.success) {

                setTimeout(() => location.reload(), 1500);
            }
        })
        .catch(() => {
            messageBox.style.color = 'red';
            messageBox.textContent = 'Greška pri obradi zahteva.';
        });
});


document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.closest('tr');
        const id = row.dataset.id;
        document.getElementById('vetId').value = id;
        document.getElementById('firstName').value = row.children[0].textContent;
        document.getElementById('lastName').value = row.children[1].textContent;
        document.getElementById('email').value = row.children[2].textContent;
        document.getElementById('phoneNumber').value = row.children[3].textContent;
        document.getElementById('address').value = row.children[4].textContent;
        document.getElementById('specialization').value = row.children[5].textContent;
        document.getElementById('licenseNumber').value = row.children[6].textContent;

        submitBtn.textContent = 'Sačuvaj izmene';
        cancelBtn.style.display = 'inline-block';
        window.scrollTo(0, 0);
    });
});


document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', () => {
        if (!confirm('Da li ste sigurni da želite da obrišete veterinara?')) return;

        const row = btn.closest('tr');
        const id = row.dataset.id;

        fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({action: 'delete', id: id})
        })
            .then(res => res.json())
            .then(data => {
                messageBox.style.color = data.success ? 'green' : 'red';
                messageBox.textContent = data.message;
                if(data.success){
                    row.remove();
                }
            })
            .catch(() => {
                messageBox.style.color = 'red';
                messageBox.textContent = 'Greška pri brisanju veterinara.';
            });
    });
});

cancelBtn.addEventListener('click', () => {
    form.reset();
    document.getElementById('vetId').value = '';
    submitBtn.textContent = 'Dodaj';
    cancelBtn.style.display = 'none';
    messageBox.textContent = '';
    formError.textContent = '';
});

function validateEmail(email) {
    // Jednostavan regex za email validaciju
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}
