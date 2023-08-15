function testpassword(modulo){
    // VERIFICO CHE IL CAMPO PASSWORD SIA VALORIZZATO
    if (modulo.password1.value == ""){
        alert("Errore: inserire una password!")
        modulo.password1.focus()
        return false
    }
    // VERIFICA DELL'UGUAGLIANZA DELLE DUE PASSWORD
    if (modulo.password1.value != modulo.password2.value) {
        alert("Le due password devono coincidere!")
        modulo.password1.focus()
        modulo.password1.select()
        return false
    }
    return true
}

function indietro() {
    window.history.back();
}
/*
// Data Picker Initialization
$('.datepicker').datepicker({
    inline: true
});
 */

const alertMessage = document.getElementsByName('alert-message');
console.log("ALERT MESSAGES: " + alertMessage.length);
function showAlertMessage() {

    alertMessage.style.display = 'block'; // Mostra il messaggio di avviso

    setTimeout(function() {
        alertMessage.style.display = 'none'; // Nascondi il messaggio di avviso dopo 5 secondi
    }, 5000);
}
function submitForm() {
    // Trova il form utilizzando l'ID del form
    var form = document.getElementById('inserimentoInsegnamentoEData');

    // Esegui il submit del form
    form.submit();
}

function isCookieSet(name) {
    return document.cookie.indexOf(name + "=") !== -1;
}

function getCookie(name) {
    const value = "; " + document.cookie;
    const parts = value.split("; " + name + "=");

    if (parts.length === 2) {
        return parts.pop().split(";").shift();
    }
}

function getNumericCookie(name) {
    const value = getCookie(name); // Assume che tu abbia una funzione getCookie() come descritto in precedenza
    if (value) {
        return parseInt(value); // Converti la stringa in un numero intero
    }
    return null; // Se il cookie non è presente
}

function setCookie(name, value, days) {
    const expirationDate = new Date();
    expirationDate.setTime(expirationDate.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = "expires=" + expirationDate.toUTCString();
    document.cookie = name + "=" + value.toString() + ";" + expires + ";path=/";
}

