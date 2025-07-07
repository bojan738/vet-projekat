"use strict";


function validateCode(form, expectedCode) {
    const userCode = form.code_input.value.trim();
    if (userCode !== expectedCode) {
        alert("Uneli ste pogrešan kod potvrde!");
        return false;
    }
    return true;
}


