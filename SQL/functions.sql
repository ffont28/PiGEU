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


