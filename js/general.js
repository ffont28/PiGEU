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