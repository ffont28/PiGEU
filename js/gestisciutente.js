
function calcolaCampi(){
  //  alert("in esecuzione calcolaCampi");
    var nome = document.getElementById('hnome').value;
    var cognome = document.getElementById('hcognome').value;
    var cf = document.getElementById('hcodicefiscale').value;
    var indirizzo = document.getElementById('hindirizzo').value;
    var citta = document.getElementById('hcitta').value;
    var email = document.getElementById('hemailpersonale').value;

    document.getElementById('nome').placeholder = nome;
    document.cookie = "nome = " + nome;
    
    document.getElementById('cognome').placeholder = cognome;
    document.cookie = "cognome = " + cognome;
    
    document.getElementById('codicefiscale').placeholder = cf;
    document.cookie = "codicefiscale = " + "cf";
    
    document.getElementById('indirizzo').placeholder = indirizzo;
        document.cookie = "indirizzo = " + indirizzo;
        
    document.getElementById('citta').placeholder = citta;
            document.cookie = "citta = " + citta;
    
    document.getElementById('emailpersonale').placeholder = email;
            document.cookie = "emailpersonale = " + email;


}