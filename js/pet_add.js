
"use strict";
document.addEventListener('DOMContentLoaded', () => {
    const breedsByType = JSON.parse(document.getElementById('breedsByTypeData').textContent);

    const speciesSelect = document.getElementById('speciesSelect');
    const breedSelect = document.getElementById('breedSelect');

    speciesSelect.addEventListener('change', () => {
        const selectedType = speciesSelect.value;
        breedSelect.innerHTML = '<option value="">-- Izaberi rasu --</option>';
        if (selectedType && breedsByType[selectedType]) {
            breedsByType[selectedType].forEach(breed => {
                const option = document.createElement('option');
                option.value = breed;
                option.textContent = breed;
                breedSelect.appendChild(option);
            });
        }
    });
});

