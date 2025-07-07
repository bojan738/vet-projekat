"use strict";
document.addEventListener('DOMContentLoaded', () => {
    const breedsByType = window.breedsByType || {};
    const speciesSelects = document.querySelectorAll('.species-select');

    speciesSelects.forEach(speciesSelect => {
        speciesSelect.addEventListener('change', () => {
            populateBreed(speciesSelect);
        });


        populateBreed(speciesSelect);
    });
});

function populateBreed(speciesSelect) {
    const form = speciesSelect.closest('form');
    const breedSelect = form.querySelector('.breed-select');
    const selectedType = speciesSelect.value;

    breedSelect.innerHTML = '<option value="">-- Izaberi rasu --</option>';
    if (breedsByType[selectedType]) {
        breedsByType[selectedType].forEach(breed => {
            const option = document.createElement('option');
            option.value = breed;
            option.textContent = breed;
            breedSelect.appendChild(option);
        });


        const currentBreed = form.querySelector('.current-breed-value')?.value;
        if (currentBreed) {
            [...breedSelect.options].forEach(opt => {
                if (opt.value === currentBreed) opt.selected = true;
            });
        }
    }
}
