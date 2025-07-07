"use strict";




document.addEventListener('DOMContentLoaded', () => {
    const noShowCheckbox = document.getElementById('no_show');
    const noteField = document.getElementById('note');
    const treatmentField = document.getElementById('treatment_id');
    const submitBtn = document.getElementById('submitBtn');

    noShowCheckbox.addEventListener('change', () => {
        const disabled = noShowCheckbox.checked;
        noteField.disabled = disabled;
        treatmentField.disabled = disabled;
        submitBtn.disabled = disabled;
        noteField.required = !disabled;
        treatmentField.required = !disabled;
    });
});
