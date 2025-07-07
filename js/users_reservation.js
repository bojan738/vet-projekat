"use strict";
function fetchSlots() {
    const vetId = document.getElementById('vet_id').value;
    const date = document.getElementById('date').value;

    if (!vetId || !date) return;

    fetch(`users_reservation.php?action=slots&vet_id=${vetId}&date=${date}`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('slotsContainer');
            container.innerHTML = '';

            if (!data || data.length === 0) {
                container.innerHTML = "<p>Nema slobodnih termina.</p>";
                return;
            }

            const table = document.createElement('table');
            table.className = 'styled-table';

            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');
            ['Izaberite', 'Početak', 'Kraj'].forEach(text => {
                const th = document.createElement('th');
                th.innerText = text;
                th.style.border = '1px solid #ccc';
                th.style.padding = '8px';
                headerRow.appendChild(th);
            });
            thead.appendChild(headerRow);
            table.appendChild(thead);

            const tbody = document.createElement('tbody');

            data.forEach(slot => {
                const tr = document.createElement('tr');

                const tdRadio = document.createElement('td');
                tdRadio.style.border = '1px solid #ccc';
                tdRadio.style.padding = '8px';
                const radio = document.createElement('input');
                radio.type = 'radio';
                radio.name = 'slot_id';
                radio.value = slot.schedule_id;
                radio.required = true;
                tdRadio.appendChild(radio);
                tr.appendChild(tdRadio);

                const tdStart = document.createElement('td');
                tdStart.innerText = slot.start_time;
                tdStart.style.border = '1px solid #ccc';
                tdStart.style.padding = '8px';
                tr.appendChild(tdStart);

                const tdEnd = document.createElement('td');
                tdEnd.innerText = slot.end_time;
                tdEnd.style.border = '1px solid #ccc';
                tdEnd.style.padding = '8px';
                tr.appendChild(tdEnd);

                tbody.appendChild(tr);
            });

            table.appendChild(tbody);
            container.appendChild(table);
        })
        .catch(error => {
            console.error("Greška pri dohvaćanju termina:", error);
            document.getElementById('slotsContainer').innerHTML = "<p style='color:red;'>Greška pri dohvaćanju termina.</p>";
        });
}

document.getElementById('reservationForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const vetId = document.getElementById('vet_id').value;
    const date = document.getElementById('date').value;
    const petId = document.getElementById('pet_id').value;
    const serviceId = document.getElementById('service_id').value;
    const slotRadio = document.querySelector('input[name="slot_id"]:checked');
    const box = document.getElementById('msgBox');

    if (!vetId || !date || !petId || !slotRadio || !serviceId) {
        box.style.color = 'red';
        box.innerText = '⚠️ Morate popuniti sva polja i izabrati termin.';
        return;
    }

    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('vet_id', vetId);
    formData.append('date', date);
    formData.append('pet_id', petId);
    formData.append('slot_id', slotRadio.value);
    formData.append('service_id', serviceId);

    fetch('users_reservation.php', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            box.style.color = data.success ? 'green' : 'red';
            box.innerText = data.message;
            if (data.success) {
                document.getElementById('slotsContainer').innerHTML = '';
                document.getElementById('reservationForm').reset();
            }
        })
        .catch(error => {
            console.error("Greška pri zakazivanju:", error);
            box.style.color = 'red';
            box.innerText = 'Greška pri zakazivanju termina.';
        });
});
