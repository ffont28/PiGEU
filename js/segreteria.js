function computeEmailDomain(){
var tipo = document.getElementById('tipo').value;
var dominio = document.getElementById('dominio');

console.log("init");
console.log(tipo);

if (tipo == "Studente"){
console.log("è studente");
dominio.innerText = "@studenti.unimi.it";
}

if (tipo == "Docente"){
console.log("è docente");
dominio.innerText = "@unimi.it";
}

if (tipo == "Segreteria"){
console.log("è segreteria");
dominio.innerText = "@unimi.it";
}

}

function computeEmailUser(){
    var nome = document.getElementById('nome').value;
    var cognome = document.getElementById('cognome').value;

    var dominio = document.getElementById('username');



    provvisorio = cognome + "." + nome;
    console.log(cognome);
    console.log(nome);
    console.log(provvisorio);
    document.getElementById('username').placeholder = provvisorio;

}