------------------------------------------------------------------------------
------ VERIFICA CHE IN UNA DATA d NON POSSANO ESSERE INSERITI DUE ESAMI DELLO STESSO ANNO
----- sotto c'è la versione più aggiornata, questo trigger è stato DROPPED
CREATE OR REPLACE FUNCTION check_inserimento_esame() RETURNS TRIGGER as $$
BEGIN
--     PERFORM * FROM calendario_esami c
--        -- INNER JOIN insegnamento i ON c.insegnamento = i.codice
--         INNER JOIN insegnamento_parte_di_cdl ip1 ON c.insegnamento= ip1.insegnamento
--         INNER JOIN insegnamento_parte_di_cdl ip2 ON ip1.anno = ip2.anno AND
--         WHERE c.data = NEW.data AND ip.anno = (SELECT anno FROM insegnamento_parte_di_cdl ip
--                                                WHERE codice = NEW.insegnamento);

        WITH esamipresenti AS (SELECT DISTINCT c1.insegnamento, ip.corso_di_laurea, ip.anno ,c1.data
                               FROM calendario_esami c1
                               INNER JOIN insegnamento_parte_di_cdl ip ON c1.insegnamento = ip.insegnamento),
             cdltarget AS (SELECT ip.corso_di_laurea, ip.anno
                           FROM insegnamento_parte_di_cdl ip
                           WHERE ip.insegnamento = NEW.insegnamento)
        PERFORM *
        FROM esamipresenti e INNER JOIN cdltarget c
            ON e.corso_di_laurea = c.corso_di_laurea AND e.anno = c.anno
            WHERE e.data = NEW.data;
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
------------------------------------------------------------------------------
------ VERIFICA CHE L'INSEGNAMENTO A CUI LO STUDENTE SI VUOLE ISCRIVERE FACCIA PARTE DEL SUO CdL
CREATE OR REPLACE FUNCTION check_appartenenza_cdl() RETURNS TRIGGER as $$
BEGIN
    PERFORM * FROM calendario_esami c
            INNER JOIN insegnamenti_per_carriera ipc ON ipc.insegnamento = c.insegnamento
            WHERE ipc.studente = NEW.studente AND NEW.esame = c.id;
    IF FOUND THEN
        -- l'esame che si vuole inserire in calendario_esami è presente in ipc
        RETURN NEW;
    ELSE
        -- l'esame che si vuole inserire in calendario_esami NON è presente in ipc
        RAISE NOTICE 'ATTENZIONE: non è consentita l''iscrizione ad un esame che non appartiene al corso di laurea di uno studente';
        PERFORM pg_notify('notifica', 'ATTENZIONE: non è consentita l''iscrizione ad un esame che non appartiene al corso di laurea di uno studente');
        RETURN NULL;
    END IF;
END;
$$ language 'plpgsql';

CREATE OR REPLACE TRIGGER no_iscriz_se_non_in_CdL
    BEFORE INSERT OR UPDATE ON iscrizione
    FOR EACH ROW
EXECUTE FUNCTION check_appartenenza_cdl();
-------------------------------------------------------------------------------
------ VERIFICA CHE VENGA RISPETTATA LA PROPEDEUTICITA TRA I CORSI PER ISCRIVERSI A UN ESAME
CREATE OR REPLACE FUNCTION check_propedeuticita() RETURNS TRIGGER as $$
BEGIN
    -- cerco se c'è in propedeuticità e nel caso che in carriera 'insegnamento1' sia superato
    PERFORM * FROM propedeuticita p
        --INNER JOIN carriera c ON c.insegnamento = p.insegnamento1 AND c.studente = NEW.studente
        INNER JOIN calendario_esami ce ON ce.insegnamento = p.insegnamento2
        WHERE ce.id = NEW.esame;
        IF NOT FOUND THEN
            RETURN NEW;
        ELSE        PERFORM * FROM carriera c
                    INNER JOIN calendario_esami ce ON ce.insegnamento = c.insegnamento
                    INNER JOIN propedeuticita p ON p.insegnamento1 = ce.insegnamento
                    WHERE c.valutazione >= 18;
                    IF FOUND THEN
                        RETURN NEW;
                    ELSE
                        RAISE NOTICE 'ATTENZIONE: non è possibile iscriversi ad un esame senza aver superato la sua propedeuticità';
                        PERFORM pg_notify('notifica', 'ATTENZIONE: non è possibile iscriversi ad un esame senza aver superato la sua propedeuticità');
                        RETURN NULL;
                    END IF;
        END IF;
END;
$$ language 'plpgsql';

CREATE OR REPLACE TRIGGER no_esami_senza_propedeuticita
    BEFORE INSERT OR UPDATE ON iscrizione
    FOR EACH ROW
EXECUTE FUNCTION check_propedeuticita();

-------------------------------------------------------------------------------
------ INSERISCI RIGHE NELLA TABELLA CARRIERA APPENA INSERITO UN NUOVO STUDENTE
---------------------------------------------------------- trigger rimosso
CREATE OR REPLACE FUNCTION inserisci_in_insegnamenti_per_carriera() RETURNS TRIGGER as $$
BEGIN
    -- creo la carriera (con tutti i voti del CdL pari a 0) lo studente appena inserito
    INSERT INTO insegnamenti_per_carriera (studente, insegnamento, timestamp)
    SELECT NEW.utente, i.insegnamento, CURRENT_TIMESTAMP
    FROM insegnamento_parte_di_cdl i
    WHERE i.corso_di_laurea = NEW.corso_di_laurea;
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE OR REPLACE TRIGGER creazione_insegnamenti_per_carriera
    AFTER INSERT ON studente
    FOR EACH ROW
EXECUTE FUNCTION inserisci_in_insegnamenti_per_carriera();

-------------------------------------------------------------------------------
--- ANDRÀ AGGGIUNTO TRIGGER SUL CONTROLLO CHE UN VOTO INSERITO (MEMORIZZATO COME INTEGER) SIA COMPRESO TRA 0 E 30
--- ho realizzato invece la funzione, meglio così

-------------------------------------------------------------------------------

-- ANDRÀ AGGIUNTO IL TRIGGER CHE CONTROLLA CHE UNO STUDENTE PUÒ ISCRIVERSI SOLAMENTE A UN ESAME PREVISTO DAL SUO CDL


--------------------------------------------- versione più aggiornata
CREATE OR REPLACE FUNCTION check_inserimento_esame() RETURNS TRIGGER AS $$
DECLARE
    esame_trovato BOOLEAN;
BEGIN
    -- Verifico se ci sono esami duplicati
    SELECT EXISTS (
        SELECT 1
        FROM calendario_esami c1
                 INNER JOIN insegnamento_parte_di_cdl ip ON c1.insegnamento = ip.insegnamento
        WHERE c1.data = NEW.data AND ip.corso_di_laurea = (SELECT corso_di_laurea FROM insegnamento_parte_di_cdl WHERE insegnamento = NEW.insegnamento) AND ip.anno = (SELECT anno FROM insegnamento_parte_di_cdl WHERE insegnamento = NEW.insegnamento)
    ) INTO esame_trovato;

    IF esame_trovato THEN
        RAISE NOTICE 'ATTENZIONE: e'' gia'' presente un altro esame dello stesso anno per la data selezionata';
        PERFORM pg_notify('notifica', 'ATTENZIONE: è già presente un altro esame dello stesso anno per la data selezionata');
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE TRIGGER no_esami_stesso_anno_stesso_giorno
    BEFORE INSERT OR UPDATE ON calendario_esami
    FOR EACH ROW
EXECUTE FUNCTION check_inserimento_esame();

-------------
CREATE OR REPLACE FUNCTION check_inserimento_esame() RETURNS TRIGGER AS $$
BEGIN
    -- trovo tutti i corsi di laurea di cui fa parte l'insegnamento che voglio inserire
    -- con il corrispettivo anno
    WITH cdl_insegnamento AS(
        SELECT i.corso_di_laurea cdl, i.anno FROM insegnamento_parte_di_cdl i
        WHERE i.insegnamento = NEW.insegnamento
    )
    PERFORM * FROM calendario_esami c
        INNER JOIN insegnamento_parte_di_cdl ip ON c.insegnamento = ip.insegnamento
        INNER JOIN cdl_insegnamento t ON t.cdl = ip.corso_di_laurea
    WHERE ip.insegnamento = NEW.insegnamento AND ip.anno = t.anno;
    IF FOUND THEN
        RAISE NOTICE 'ATTENZIONE: e'' gia'' presente un altro esame dello stesso anno per la data selezionata';
        PERFORM pg_notify('notifica', 'ATTENZIONE: è già presente un altro esame dello stesso anno per la data selezionata');
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;

END;
$$ LANGUAGE plpgsql;
-----------------------------------
----RIPROVO ANCORA
CREATE OR REPLACE FUNCTION check_inserimento_esame() RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (
        SELECT 1
        FROM insegnamento_parte_di_cdl ip
--             INNER JOIN insegnamento i ON ip.insegnamento = i.codice
        --WHERE i.codice = NEW.insegnamento
--           WHERE ip.anno = ANY (
--             SELECT anno
--             FROM insegnamento_parte_di_cdl ip2
--                 WHERE ip2.insegnamento = NEW.insegnamento )
        WHERE ip.corso_di_laurea = ANY (
            SELECT corso_di_laurea
            FROM calendario_esami c
                INNER JOIN insegnamento_parte_di_cdl ip2 ON c.insegnamento = ip2.insegnamento
                WHERE ip2.anno = ANY (SELECT anno from cdl_di_appartenenza(NEW.insegnamento))
        )
    ) THEN
        RAISE NOTICE 'Violazione della condizione: esiste già un esame dello stesso anno per la data selezionata.';
        PERFORM pg_notify('notifica', 'Violazione della condizione: esiste già un esame dello stesso anno per la data selezionata');
        RETURN NULL;
    ELSE
        RETURN NEW;
        END IF;

END;
$$ LANGUAGE plpgsql;
--------------------------------------------------------------
-------------- VERIFICA CHE IL VOTO VERBALIZZATO SIA COMPRESO TRA 0 E 31 (= 30L)
CREATE OR REPLACE FUNCTION check_voto_valido() RETURNS TRIGGER as $$
BEGIN
    IF NEW.valutazione < 0 OR NEW.valutazione > 31 THEN
        RAISE NOTICE 'ATTENZIONE: la valutazione deve essere un intero tra 0 e 31 estremi inclusi';
        PERFORM pg_notify('notifica', 'ATTENZIONE: la valutazione deve essere un intero tra 0 e 31 estremi inclusi');
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END;
$$ language 'plpgsql';

CREATE OR REPLACE TRIGGER no_esami_senza_propedeuticita
    BEFORE UPDATE ON carriera
    FOR EACH ROW
EXECUTE FUNCTION check_voto_valido();
--------------------------------------------------------------------
-- VERIFICA CHE NEL MOMENTO DELLA REGISTRAZIONE DEL VOTO IN CARRIERA, QUEL VOTO SIA DI UN
-- INSEGNAMENTO CHE APPARTIENE AL CDL A CUI LO STUDENTE È ISCRITTO
CREATE OR REPLACE FUNCTION check_registrazione_carriera() RETURNS TRIGGER as $$
BEGIN
    PERFORM studente, insegnamento FROM insegnamenti_per_carriera
        WHERE insegnamento = NEW.insegnamento AND studente = NEW.studente;
    IF FOUND THEN
        -- si vuole verbalizzare un voto di insegnamento presente nel calendario insegnamenti a cui appartiene studente
        RETURN NEW;
    ELSE
        -- l'esame che si vuole inserire in calendario_esami NON è presente nel cdL
        RAISE NOTICE 'ATTENZIONE: l''esame a cui ti vuoi iscrivere non fa parte del CdL a cui è iscritto lo studente';
        PERFORM pg_notify('notifica', 'ATTENZIONE: l''esame a cui ti vuoi iscrivere non fa parte del CdL a cui è iscritto lo studente');
        RETURN NULL;
    END IF;
END;
$$ language 'plpgsql';

CREATE OR REPLACE TRIGGER registrazione_in_carriera
    BEFORE INSERT OR UPDATE ON carriera -- metto anche UPDATE PERCHÈ SE L'ins VIENE RIMOSSO, IL VOTO NON PUÒ ESSERE TOCCATO
    FOR EACH ROW
EXECUTE FUNCTION check_registrazione_carriera();