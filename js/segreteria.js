if (window.XMLHttpRequest) {
    console.log("uaooo");
     // Supports Ajax.

    } else {
    console.log("buuuuuhhh");

     //No.
}


//tipoDocente.style.display = "none";

function computeEmailDomain(){
//var tipoDocente = document.getElementById('tipodocente');
var tipo = document.getElementById('tipo').value;
var dominio = document.getElementById('dominio');
let tipodocente = document.getElementById("tipodocente");
let tiposegreteria = document.getElementById("tiposegreteria");
let cdl = document.getElementById("cdl");

//let hidden = tipodocente.getAttribute("hidden");

    document.cookie = "dominio = " + "@studenti.unimi.it";


    if (tipo == "studente"){
        console.log("è studente");
        dominio.innerText = "@studenti.unimi.it";
        document.cookie = "dominio = " + "@studenti.unimi.it";

        tipodocente.setAttribute("hidden", "hidden");
        tiposegreteria.setAttribute("hidden", "hidden");
        cdl.removeAttribute("hidden");

    }

    if (tipo == "docente"){
        console.log("è docente");
        dominio.innerText = "@unimi.it";
        document.cookie = "dominio = " + "@unimi.it";

        tipodocente.removeAttribute("hidden");
        tiposegreteria.setAttribute("hidden", "hidden");
        cdl.setAttribute("hidden", "hidden");

    }

    if (tipo == "segreteria"){
        console.log("è segreteria");
        dominio.innerText = "@unimi.it";
        document.cookie = "dominio = " + "@unimi.it";

        tiposegreteria.removeAttribute("hidden");
        tipodocente.setAttribute("hidden", "hidden");
        cdl.setAttribute("hidden", "hidden");

    }

}

function computeEmailUser(){
    var nome = document.getElementById('nome').value;
    var cognome = document.getElementById('cognome').value;

    var dominio = document.getElementById('username');



    provvisorio = nome+ "." + cognome;

    //dominio.value = provvisorio;

    console.log(cognome);
    console.log(nome);
    console.log(provvisorio);
    document.getElementById('username').placeholder = provvisorio;
    document.getElementById('username').innerText = provvisorio;
    document.cookie = "username = " + provvisorio;

}


