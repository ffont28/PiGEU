CREATE OR REPLACE FUNCTION check_inserimento_esame() RETURNS TRIGGER as $$
BEGIN
    PERFORM * FROM calendario_esami c
        INNER JOIN insegnamento i ON c.insegnamento = i.codice
        WHERE c.data = NEW.data AND i.anno = (SELECT anno FROM insegnamento WHERE codice = NEW.insegnamento);
    IF FOUND THEN
        RAISE NOTICE 'ATTENZIONE: è già presente un altro esame dello stesso anno per la data selezionata';
        PERFORM pg_notify('notifica', 'ATTENZIONE: è già presente un altro esame dello stesso anno per la data selezionata+');
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END;
$$ language 'plpgsql';


CREATE OR REPLACE TRIGGER no_esami_stesso_anno_stesso_giorno
BEFORE INSERT OR UPDATE ON calendario_esami
FOR EACH ROW
EXECUTE FUNCTION check_inserimento_esame();



CREATE OR REPLACE FUNCTION check_propedeuticita() RETURNS TRIGGER as $$
BEGIN
    -- cerco se c'è in propedeuticità e nel caso che in carriera 'insegnamento1' sia superato
    PERFORM * FROM propedeuticita p
        INNER JOIN carriera c ON c.insegnamento = p.insegnamento1 AND c.studente = NEW.studente
        INNER JOIN calendario_esami ce ON ce.insegnamento = p.insegnamento2 and ce.id = NEW.esame
    WHERE c.valutazione < 18;
    IF FOUND THEN
        RAISE EXCEPTION 'ATTENZIONE: non è possibile iscriversi ad un esame senza aver superato la sua propedeuticità';
     --   PERFORM pg_notify('notifica', 'ATTENZIONE: non è possibile iscriversi ad un esame senza aver superato la sua propedeuticità');
     --   RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END;
$$ language 'plpgsql';

CREATE OR REPLACE TRIGGER no_esami_senza_propedeuticita
    BEFORE INSERT OR UPDATE ON iscrizione
    FOR EACH ROW
EXECUTE FUNCTION check_propedeuticita();


CREATE OR REPLACE FUNCTION inserisci_in_carriera() RETURNS TRIGGER as $$
BEGIN
    -- cerco se c'è in propedeuticità e nel caso che in carriera 'insegnamento1' sia superato
    INSERT INTO carriera (studente, insegnamento, valutazione, data)
    SELECT NEW.utente, i.insegnamento, 0, NULL
    FROM insegnamento_parte_di_cdl i
    WHERE i.corso_di_laurea = NEW.corso_di_laurea;
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE OR REPLACE TRIGGER creazione_carriera
    AFTER INSERT OR UPDATE ON studente
    FOR EACH ROW
EXECUTE FUNCTION inserisci_in_carriera();

