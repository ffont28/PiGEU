-- FUNZIONI IMPLEMENTATE NEL DB


-- RICHIESTA 2.2.1 = RIMOZIONE DI STUDENTE PER LAUREA O RINUNCIA AGLI STUDI
--  L'UTENTE VIENE SPOSTATO IN UN'APPOSITA TABELLA DI "STORICO STUDENTE"
    -- TABELLE INTERESSATE:
        -- utente   --> utente_storico
        -- studente --> studente_storico
        -- carriera --> carriera_storica

CREATE OR REPLACE FUNCTION sposta_dati_studente(E varchar) RETURNS TEXT AS $$
DECLARE
  status TEXT := 'Dati spostati con successo.';
BEGIN
  -- INSERT utente --> utente_storico
  INSERT INTO utente_storico (email, nome, cognome, indirizzo, citta, codicefiscale, emailpersonale)
  SELECT email, nome, cognome, indirizzo, citta, codicefiscale, emailpersonale
  FROM utente where email = $1;

  -- INSERT studente --> studente_storico
  INSERT INTO studente_storico (utente, matricola, corso_di_laurea)
  SELECT utente, matricola, corso_di_laurea
  FROM studente WHERE utente = $1;

  -- INSERT carriera --> carriera_storico
  INSERT INTO carriera_storico (studente, insegnamento, valutazione, data)
  SELECT studente, insegnamento, valutazione, data
  FROM carriera WHERE studente = $1;

  -- cancello i dati dalla tabella utente dopo averli spostati in utente_storico
  DELETE FROM utente where email = $1;
  -- le seguenti operazioni sono fatte già in CASCADE
        -- cancello i dati dalla tabella studente dopo averli spostati in stutente_storico
        -- DELETE FROM studente WHERE utente = $1;
        -- cancello i dati dalla tabella carriera dopo averli spostati in carriera_storico
        -- DELETE FROM carriera WHERE email = $1;

  RETURN status;
-- Gestione delle eccezioni
EXCEPTION
  WHEN OTHERS THEN
    -- In caso di errore restituisco il messaggio di errore
    status := 'Errore: ' || SQLERRM;
    RETURN status;
END;
$$ LANGUAGE plpgsql;

------------------------------------------------------------------------------------------
-- FUNZIONE PER VERBALIZZARE UN VOTO A UNO STUDENTE -- ha senso questa funzione perchè controllo il range
-- ma poi ho implementato il trigger ON UPDATE che è meglio
CREATE OR REPLACE FUNCTION verbalizza(I varchar, S varchar, V INT, T TIMESTAMP) RETURNS TEXT AS $$
DECLARE
    status TEXT := 'Errore: non si sono rispettati i parametri di input del voto';
BEGIN
    IF S < 0 OR S > 31 THEN
        RETURN status;
    ELSE
        UPDATE carriera SET valutazione = V, data = T
        WHERE studente = S AND insegnamento = I;

        status := 'Verbalizzazione avvenuta con successo.';
        RETURN status;
    END IF;
-- Gestione delle eccezioni
EXCEPTION
    WHEN OTHERS THEN
        -- In caso di errore restituisco il messaggio di errore
        status := 'Errore: ' || SQLERRM;
    RETURN status;
END;
$$ LANGUAGE plpgsql;

-----------------------------------------------------------------------
SELECT i1.insegnamento, c.data FROM insegnamento_parte_di_cdl i1
    INNER JOIN insegnamento_parte_di_cdl i2 ON i1.corso_di_laurea = i2.corso_di_laurea
                        AND i1.insegnamento <> i2.insegnamento AND i1.anno = i2.anno
    INNER JOIN calendario_esami c ON c.insegnamento = i1.insegnamento;


INSERT INTO calendario_esami (insegnamento, data, ora) VALUES ('TEST', '2023-02-12','13:45')

----- PER OGNI INSEGNAMENTO, A QUALI CDL APPARTIENE
WITH cdldiogniinsegnam AS (SELECT DISTINCT c1.insegnamento, ip.corso_di_laurea
                           FROM calendario_esami c1
                                    INNER JOIN insegnamento_parte_di_cdl ip ON c1.insegnamento = ip.insegnamento
)
SELECT distinct ip.corso_di_laurea FROM insegnamento_parte_di_cdl ip
                          INNER JOIN cdldiogniinsegnam c ON c.insegnamento = ip.insegnamento
        WHERE ip.insegnamento = '51.23.1';

---------------------------------- RIPROVIAMOCI
SELECT ip.insegnamento FROM insegnamento_parte_di_cdl ip
    INNER JOIN;

WITH cdldiogniinsegnam AS (SELECT DISTINCT c1.insegnamento ins, ip.corso_di_laurea cdl
                           FROM calendario_esami c1
                                    INNER JOIN insegnamento_parte_di_cdl ip ON c1.insegnamento = ip.insegnamento
                           ),
    cdldiquestoinsegnam AS (SELECT DISTINCT ip.corso_di_laurea cdl FROM insegnamento_parte_di_cdl ip
                            INNER JOIN  cdldiogniinsegnam c ON ip.corso_di_laurea = c.cdl)
    SELECT * FROM cdldiquestoinsegnam;

--------------------------------------------
WITH selezione1 AS (SELECT DISTINCT c1.insegnamento, ip.corso_di_laurea, ip.anno FROM calendario_esami c1
        INNER JOIN insegnamento_parte_di_cdl ip ON c1.insegnamento = ip.insegnamento
    ), selezione2 AS (SELECT ipcd.insegnamento, ipcd.corso_di_laurea, ipcd.anno FROM insegnamento_parte_di_cdl ipcd
                     WHERE ipcd.insegnamento = 'MAT1')
    SELECT * FROM selezione1 s1 INNER JOIN selezione2 s2 ON s1.corso_di_laurea = s2.corso_di_laurea
        INNER JOIN calendario_esami c ON s1.anno = s2.anno

----------------------------------------------

SELECT DISTINCT c1.insegnamento, ip.corso_di_laurea
FROM calendario_esami c1
         INNER JOIN insegnamento_parte_di_cdl ip ON c1.insegnamento = ip.insegnamento
--------------------------------------------
------------- VERSIONE CHE DOVREBBE FUNZIONARE!
-- NO 2 ESAMI LO STESSO GIORNO SE SONO DELLO STESSO ANNO
-- ed è caricata nel DB
WITH esamipresenti AS (SELECT DISTINCT c1.insegnamento, ip.corso_di_laurea, ip.anno ,c1.data  FROM calendario_esami c1
                       INNER JOIN insegnamento_parte_di_cdl ip ON c1.insegnamento = ip.insegnamento),
    cdltarget AS (SELECT ip.corso_di_laurea, ip.anno FROM insegnamento_parte_di_cdl ip
                    WHERE ip.insegnamento = :target)
    SELECT * FROM esamipresenti e INNER JOIN cdltarget c ON e.corso_di_laurea = c.corso_di_laurea
    WHERE e.anno = :anno AND e.data = :data;

--------------------------------------------------------------------------
-----------------------
----------------------- FUNZIONE CREATA DA ME, NON DA UTILIZZARE!-------------
-------------------------------------------------------------------------------
-- FUNZIONE PER PRODURRE UNA CARRIERA COMPLETA DATO UNO STUDENTE [ANCHE ESAMI MAI SOSTENUTI, ANCHE DUPLICATI]
--- va bene sia per lo studente
CREATE OR REPLACE FUNCTION carriera_completa_tutti(TARGET varchar) RETURNS TABLE (
       studente varchar,
       nomstu varchar,
       cogstu varchar,
       cdl varchar,
       matr integer,
       codins varchar,
       nomins varchar,
       nomdoc varchar,
       cogdoc varchar,
       voto varchar,
       data varchar
   ) AS $$
BEGIN
    RETURN QUERY
        SELECT ic.studente,
               u2.nome nomstu,
               u2.cognome cogstu,
               cdl.nome cdl,
               s.matricola matr,
               ic.insegnamento,
               i.nome,
               u.nome nomedoc,
               u.cognome cogndoc,
               CASE
                   WHEN c.valutazione IS NULL THEN 'non sostenuto'
                   ELSE c.valutazione::VARCHAR
                   END,
               CASE
                   WHEN c.data IS NULL THEN 'non sostenuto'
                   ELSE c.data::VARCHAR
                   END
        FROM insegnamenti_per_carriera ic
                 LEFT JOIN carriera c ON ic.insegnamento = c.insegnamento
                 INNER JOIN docente_responsabile d ON d.insegnamento = ic.insegnamento
                 INNER JOIN utente u ON d.docente = u.email
                 INNER JOIN utente u2 ON ic.studente = u2.email
                 INNER JOIN studente s ON ic.studente = s.utente
                 INNER JOIN corso_di_laurea cdl ON s.corso_di_laurea = cdl.codice
                 INNER JOIN insegnamento i ON ic.insegnamento = i.codice
        WHERE ic.studente = TARGET and c.studente = TARGET;
END;
$$ LANGUAGE plpgsql;

--------------------------------------------------------------------------
-----------------------
----------------------- FUNZIONE DA DROPPARE ------------------
--------------------------------------------------------------
-- FUNZIONE PER PRODURRE UNA CARRIERA COMPLETA DATO UNO STUDENTE [SOLO ESAMI SOSTENUTI, ANCHE DUPICATI]
--- va bene sia per lo studente
CREATE OR REPLACE FUNCTION carriera_completa_esami_sostenuti(TARGET varchar) RETURNS TABLE (
                studente varchar,
                nomstu varchar,
                cogstu varchar,
                cdl varchar,
                matr integer,
                codins varchar,
                nomins varchar,
                nomdoc varchar,
                cogdoc varchar,
                voto smallint,
                data date
            ) AS $$
BEGIN
    RETURN QUERY
        SELECT ic.studente,
               u2.nome nomstu,
               u2.cognome cogstu,
               cdl.nome cdl,
               s.matricola matr,
               ic.insegnamento,
               i.nome,
               u.nome nomedoc,
               u.cognome cogndoc,
               c.valutazione,
               c.data
        FROM insegnamenti_per_carriera ic
                 INNER JOIN carriera c ON ic.insegnamento = c.insegnamento
                 INNER JOIN docente_responsabile d ON d.insegnamento = ic.insegnamento
                 INNER JOIN utente u ON d.docente = u.email
                 INNER JOIN utente u2 ON ic.studente = u2.email
                 INNER JOIN studente s ON ic.studente = s.utente
                 INNER JOIN corso_di_laurea cdl ON s.corso_di_laurea = cdl.codice
                 INNER JOIN insegnamento i ON ic.insegnamento = i.codice
        WHERE ic.studente = TARGET;
END;
$$ LANGUAGE plpgsql;

--------------------------------------------------------------------------
-- VERSIONE CORRETTA ------------
-- FUNZIONE PER PRODURRE UNA CARRIERA COMPLETA DATO UNO STUDENTE [SOLO ESAMI SOSTENUTI, ANCHE DUPICATI]
--- va bene sia per lo studente
CREATE OR REPLACE FUNCTION carriera_completa(TARGET varchar) RETURNS TABLE (
            studente varchar,
            nomstu varchar,
            cogstu varchar,
            cdl varchar,
            matr integer,
            codins varchar,
            nomins varchar,
            nomdoc varchar,
            cogdoc varchar,
            voto smallint,
            data date
            ) AS $$
BEGIN
    RETURN QUERY
        SELECT DISTINCT ic.studente,
               u2.nome nomstu,
               u2.cognome cogstu,
               cdl.nome cdl,
               s.matricola matr,
               ic.insegnamento,
               i.nome,
               u.nome nomedoc,
               u.cognome cogndoc,
               c.valutazione,
               c.data
        FROM insegnamenti_per_carriera ic
                 INNER JOIN carriera c ON ic.insegnamento = c.insegnamento
                 INNER JOIN docente_responsabile d ON d.insegnamento = ic.insegnamento
                 INNER JOIN utente u ON d.docente = u.email
                 INNER JOIN utente u2 ON ic.studente = u2.email
                 INNER JOIN studente s ON ic.studente = s.utente
                 INNER JOIN corso_di_laurea cdl ON s.corso_di_laurea = cdl.codice
                 INNER JOIN insegnamento i ON ic.insegnamento = i.codice
        WHERE ic.studente = TARGET AND c.studente = TARGET
        GROUP BY ic.studente, u2.nome, u2.cognome, cdl.nome, s.matricola, ic.insegnamento, i.nome, u.nome, u.cognome, c.valutazione, c.data;
END;
$$ LANGUAGE plpgsql;
----------------------------------
CREATE OR REPLACE FUNCTION carriera_completa_sto(TARGET varchar) RETURNS TABLE (
               studente varchar,
               nomstu varchar,
               cogstu varchar,
               cdl varchar,
               matr integer,
               codins varchar,
               nomins varchar,
               nomdoc varchar,
               cogdoc varchar,
               voto smallint,
               data date
           ) AS $$
BEGIN
    RETURN QUERY
        SELECT DISTINCT c.studente,
                        u2.nome nomstu,
                        u2.cognome cogstu,
                        cdl.nome cdl,
                        s.matricola matr,
                        c.insegnamento,
                        i.nome,
                        u.nome nomedoc,
                        u.cognome cogndoc,
                        c.valutazione,
                        c.data
        FROM carriera_storico c
                 INNER JOIN docente_responsabile d ON d.insegnamento = c.insegnamento
                 INNER JOIN utente u ON d.docente = u.email
                 INNER JOIN utente_storico u2 ON c.studente = u2.email
                 INNER JOIN studente_storico s ON c.studente = s.utente
                 INNER JOIN corso_di_laurea cdl ON s.corso_di_laurea = cdl.codice
                 INNER JOIN insegnamento i ON c.insegnamento = i.codice
        WHERE c.studente = TARGET;
        END;
$$ LANGUAGE plpgsql;
--------------------------------------------------------------------------
-- VERSIONE CORRETTA ------------
-- FUNZIONE PER PRODURRE UNA CARRIERA COMPLETA DATO UNO STUDENTE [SOLO ESAMI SOSTENUTI, ANCHE DUPICATI]
--- va bene sia per lo studente
CREATE OR REPLACE FUNCTION carriera_valida(TARGET varchar) RETURNS TABLE (
           studente varchar,
           nomstu varchar,
           cogstu varchar,
           cdl varchar,
           matr integer,
           codins varchar,
           nomins varchar,
           nomdoc varchar,
           cogdoc varchar,
           voto smallint,
           data date
       ) AS $$
BEGIN
    RETURN QUERY
        SELECT DISTINCT (c.studente),
               u2.nome nomstu,
               u2.cognome cogstu,
               cdl.nome cdl,
               s.matricola matr,
               c.insegnamento,
               i.nome,
               u.nome nomedoc,
               u.cognome cogndoc,
               c.valutazione,
               c.data
        FROM carriera c
                 INNER JOIN docente_responsabile d ON d.insegnamento = c.insegnamento
                 INNER JOIN utente u ON d.docente = u.email
                 INNER JOIN utente u2 ON c.studente = u2.email
                 INNER JOIN studente s ON c.studente = s.utente
                 INNER JOIN corso_di_laurea cdl ON s.corso_di_laurea = cdl.codice
                 INNER JOIN insegnamento i ON c.insegnamento = i.codice
        WHERE c.studente = TARGET
          AND c.valutazione >= 18
          AND c.data = (SELECT MAX(c2.data)
                            FROM carriera c2 WHERE c2.insegnamento = c.insegnamento
                                             AND c2.studente = s.utente)
    GROUP BY c.studente, u2.nome, u2.cognome, cdl.nome, s.matricola, c.insegnamento, i.nome, u.nome, u.cognome, c.valutazione, c.data;
END;
$$ LANGUAGE plpgsql;

--------------

CREATE OR REPLACE FUNCTION carriera_valida_sto(TARGET varchar) RETURNS TABLE (
                 studente varchar,
                 nomstu varchar,
                 cogstu varchar,
                 cdl varchar,
                 matr integer,
                 codins varchar,
                 nomins varchar,
                 nomdoc varchar,
                 cogdoc varchar,
                 voto smallint,
                 data date
             ) AS $$
BEGIN
    RETURN QUERY
        SELECT DISTINCT c.studente,
                        u2.nome nomstu,
                        u2.cognome cogstu,
                        cdl.nome cdl,
                        s.matricola matr,
                        c.insegnamento,
                        i.nome,
                        u.nome nomedoc,
                        u.cognome cogndoc,
                        c.valutazione,
                        c.data
        FROM  carriera_storico c
                 INNER JOIN docente_responsabile d ON d.insegnamento = c.insegnamento
                 INNER JOIN utente u ON d.docente = u.email
                 INNER JOIN utente_storico u2 ON c.studente = u2.email
                 INNER JOIN studente_storico s ON c.studente = s.utente
                 INNER JOIN corso_di_laurea cdl ON s.corso_di_laurea = cdl.codice
                 INNER JOIN insegnamento i ON c.insegnamento = i.codice
        WHERE c.studente = TARGET
          AND c.valutazione >= 18
          AND c.data = (SELECT MAX(c2.data)
                        FROM carriera_storico c2 WHERE c2.insegnamento = c.insegnamento
                                                   AND c2.studente = s.utente);
END;
$$ LANGUAGE plpgsql;


----------------------------------------------------
----------- creo una funzione che restituisca una tabella con tutti i cdl di cui fa parte un dato insegnamento
CREATE OR REPLACE FUNCTION cdl_di_appartenenza(I varchar) RETURNS TABLE (
             codice varchar,
             nome varchar,
             anno integer,
             cfu char,
             codiceins varchar
         ) AS $$
BEGIN
    RETURN QUERY
        SELECT c.codice,
               c.nome,
               ip.anno,
               ins.cfu,
               ins.codice
        FROM corso_di_laurea c
                 INNER JOIN insegnamento_parte_di_cdl ip ON c.codice = ip.corso_di_laurea
                 INNER JOIN insegnamento ins ON ip.insegnamento = ins.codice
        WHERE ip.insegnamento = I;
END;
$$ LANGUAGE plpgsql;

