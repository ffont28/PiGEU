------------------------------------------------ PAGINA DI CREAZIONE VISTE --------------------------------------------
-- CREO UNA VISTA PER PRODURRE LE INFORMAZIONI DI UN CORSO DI LAUREA - specifica 2.2.6
----- scelgo di fare una vista non materializzata perchè così da non essere persistente in memoria
CREATE or replace VIEW informazioni_CdL AS
SELECT c.codice, c.nome, c.tipo, i.codice codicec, i.nome nomec, i.anno , i.descrizione, i.cfu, u.nome nomedoc, u.cognome cognomedoc
FROM corso_di_laurea c INNER JOIN insegnamento_parte_di_cdl ip ON c.codice = ip.corso_di_laurea
                       INNER JOIN insegnamento i ON ip.insegnamento = i.codice
                       INNER JOIN docente_responsabile d ON i.codice = d.insegnamento
                       INNER JOIN utente u ON u.email = d.docente;

