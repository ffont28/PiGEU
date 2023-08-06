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

