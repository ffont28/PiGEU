-- FUNZIONI IMPLEMENTATE NEL DB


-- RICHIESTA 2.2.1 = RIMOZIONE DI STUDENTE PER LAUREA O RINUNCIA AGLI STUDI
--  L'UTENTE VIENE SPOSTATO IN UN'APPOSITA TABELLA DI "STORICO STUDENTE"
    -- TABELLE INTERESSATE:
        -- UTENTE   --> STORICO UTENTE
        -- STUDENTE --> STORICO STUDENTE
        -- CARRIERA --> STORICO CARRIERA

CREATE OR REPLACE FUNCTION sposta_dati_studente() RETURNS TEXT AS $$
DECLARE
  status TEXT := 'Dati spostati con successo.';
BEGIN
  -- Eseguire l'istruzione INSERT per spostare i dati dalla tabella di origine alla tabella di destinazione
  INSERT INTO tabella_utenti_destinazione (id, nome, cognome, email)
  SELECT id, nome, cognome, email
  FROM tabella_utenti_originale;


  RETURN status;
EXCEPTION
  WHEN OTHERS THEN
    -- In caso di errore, restituiamo un messaggio di errore
    status := 'Errore: ' || SQLERRM;
    RETURN status;
END;
$$ LANGUAGE plpgsql;
