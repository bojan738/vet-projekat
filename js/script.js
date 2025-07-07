"use strict";
document.addEventListener("DOMContentLoaded", function () {
    const carouselInner = document.querySelector("#serviceCarousel .carousel-inner");
    if (!carouselInner) return;

    const items = carouselInner.querySelectorAll(".carousel-item");
    let currentIndex = 0;

    const showSlide = (index) => {
        items.forEach((item, i) => {
            item.classList.remove("active");
            if (i === index) {
                item.classList.add("active");
            }
        });
    };

    const nextSlide = () => {
        currentIndex = (currentIndex + 1) % items.length;
        showSlide(currentIndex);
    };


    setInterval(nextSlide, 5000);
});

function roundTimeToStep(input) {
    const step = 1800;
    let [h, m] = input.value.split(':').map(Number);
    if (isNaN(h) || isNaN(m)) return;

    let totalSeconds = h * 3600 + m * 60;
    let rounded = Math.round(totalSeconds / step) * step;

    if (rounded < 8 * 3600) rounded = 8 * 3600;
    if (rounded > 22 * 3600) rounded = 22 * 3600;

    let rh = Math.floor(rounded / 3600);
    let rm = Math.floor((rounded % 3600) / 60);
    input.value = `${rh.toString().padStart(2, '0')}:${rm.toString().padStart(2, '0')}`;
}


const vremeStartInput = document.getElementById('vreme_start');
if (vremeStartInput) {
    vremeStartInput.addEventListener('change', function () {
        roundTimeToStep(this);
    });
}
const vremeEndInput = document.getElementById('vreme_end');
if (vremeEndInput) {
    vremeEndInput.addEventListener('change', function () {
        roundTimeToStep(this);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('addScheduleForm');
    const formMessage = document.getElementById('formMessage');

    if (form && formMessage) {
        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            formMessage.textContent = '';
            formMessage.style.color = 'red';

            const data = {
                day_of_week: form.day_of_week.value,
                start_time: form.start_time.value,
                end_time: form.end_time.value
            };

            if (!data.day_of_week || !data.start_time || !data.end_time) {
                formMessage.textContent = 'Sva polja su obavezna.';
                return;
            }

            if (data.end_time <= data.start_time) {
                formMessage.textContent = 'Kraj termina mora biti posle početka.';
                return;
            }

            try {
                const response = await fetch('vet_add_schedule_ajax.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data),
                });

                const result = await response.json();

                if (result.success) {
                    formMessage.style.color = 'green';
                    formMessage.textContent = 'Termin je uspešno dodat!';
                    form.reset();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    formMessage.textContent = result.message || 'Došlo je do greške.';
                }
            } catch {
                formMessage.textContent = 'Greška prilikom slanja zahteva.';
            }
        });
    }
});


function setupPriceUpdater() {
    const treatmentSelect = document.getElementById('treatment');
    const priceInput = document.getElementById('price');

    if (!treatmentSelect || !priceInput) return;

    const updatePrice = () => {
        const selectedOption = treatmentSelect.options[treatmentSelect.selectedIndex];
        const price = selectedOption ? selectedOption.getAttribute('data-price') : '';
        priceInput.value = price ? parseFloat(price).toFixed(2) : '';
    };

    treatmentSelect.addEventListener('change', updatePrice);


    updatePrice();
}

document.addEventListener('DOMContentLoaded', setupPriceUpdater);

document.getElementById('noteForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const noShow = document.getElementById('no_show').checked;
    const treatment = document.getElementById('treatment_id').value;

    if (!noShow && treatment === '') {
        alert("Tretman je obavezan ako se korisnik pojavio.");
        return;
    }

    try {
        const response = await fetch('vet_save_note.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            window.location.reload();
        } else {
            alert(result.message || 'Greška pri unosu.');
        }

    } catch (err) {
        console.error(err);
        alert("Došlo je do greške.");
    }
});
