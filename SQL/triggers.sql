CREATE OR REPLACE FUNCTION check_inserimento_esame() RETURNS TRIGGER as $$
BEGIN
    PERFORM * FROM calendario_esami c
        INNER JOIN insegnamento i ON c.insegnamento = i.codice
        WHERE c.data = NEW.data AND i.anno = (SELECT anno FROM insegnamento WHERE codice = NEW.insegnamento);
    IF FOUND THEN
        RAISE NOTICE 'Impossibile inserire esame di due insegnamenti dello stesso anno nello stesso giorno';
        PERFORM pg_notify('notifica', 'Impossibile inserire esame di due insegnamenti dello stesso anno nello stesso giorno');
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
