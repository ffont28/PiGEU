--
-- PostgreSQL database dump
--

-- Dumped from database version 14.9 (Ubuntu 14.9-0ubuntu0.22.04.1)
-- Dumped by pg_dump version 14.9 (Ubuntu 14.9-0ubuntu0.22.04.1)

-- Started on 2023-08-24 03:25:04 CEST

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 238 (class 1255 OID 16590)
-- Name: candidato(); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.candidato() RETURNS SETOF character varying
    LANGUAGE plpgsql
    AS $$

BEGIN
        WITH selezione AS (
             SELECT utente FROM docente
             EXCEPT
             SELECT docente FROM docente_responsabile
             GROUP BY 1
             HAVING count(*) >2
             )
             SELECT u.nome, u.cognome FROM utente u
             INNER JOIN selezione s ON u.email = s.utente;

        RETURN;
END;
$$;


ALTER FUNCTION public.candidato() OWNER TO fontanaf;

--
-- TOC entry 268 (class 1255 OID 16931)
-- Name: carriera_completa(character varying); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.carriera_completa(target character varying) RETURNS TABLE(studente character varying, nomstu character varying, cogstu character varying, cdl character varying, matr integer, codins character varying, nomins character varying, nomdoc character varying, cogdoc character varying, voto smallint, data date)
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.carriera_completa(target character varying) OWNER TO fontanaf;

--
-- TOC entry 263 (class 1255 OID 16908)
-- Name: carriera_completa_esami_sostenuti(character varying); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.carriera_completa_esami_sostenuti(target character varying) RETURNS TABLE(studente character varying, nomstu character varying, cogstu character varying, cdl character varying, matr integer, codins character varying, nomins character varying, nomdoc character varying, cogdoc character varying, voto smallint, data date)
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.carriera_completa_esami_sostenuti(target character varying) OWNER TO fontanaf;

--
-- TOC entry 267 (class 1255 OID 16936)
-- Name: carriera_completa_sto(character varying); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.carriera_completa_sto(target character varying) RETURNS TABLE(studente character varying, nomstu character varying, cogstu character varying, cdl character varying, matr integer, codins character varying, nomins character varying, nomdoc character varying, cogdoc character varying, voto smallint, data date)
    LANGUAGE plpgsql
    AS $$
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
        WHERE c.studente = TARGET AND c.studente = TARGET;
        END;
$$;


ALTER FUNCTION public.carriera_completa_sto(target character varying) OWNER TO fontanaf;

--
-- TOC entry 261 (class 1255 OID 16909)
-- Name: carriera_completa_tutti(character varying); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.carriera_completa_tutti(target character varying) RETURNS TABLE(studente character varying, nomstu character varying, cogstu character varying, cdl character varying, matr integer, codins character varying, nomins character varying, nomdoc character varying, cogdoc character varying, voto character varying, data character varying)
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.carriera_completa_tutti(target character varying) OWNER TO fontanaf;

--
-- TOC entry 271 (class 1255 OID 16930)
-- Name: carriera_valida(character varying); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.carriera_valida(target character varying) RETURNS TABLE(studente character varying, nomstu character varying, cogstu character varying, cdl character varying, matr integer, codins character varying, nomins character varying, nomdoc character varying, cogdoc character varying, voto smallint, data date)
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.carriera_valida(target character varying) OWNER TO fontanaf;

--
-- TOC entry 269 (class 1255 OID 16937)
-- Name: carriera_valida_sto(character varying); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.carriera_valida_sto(target character varying) RETURNS TABLE(studente character varying, nomstu character varying, cogstu character varying, cdl character varying, matr integer, codins character varying, nomins character varying, nomdoc character varying, cogdoc character varying, voto smallint, data date)
    LANGUAGE plpgsql
    AS $$
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
                        FROM carriera_storico c2 WHERE c2.insegnamento = c.insegnamento);
END;
$$;


ALTER FUNCTION public.carriera_valida_sto(target character varying) OWNER TO fontanaf;

--
-- TOC entry 258 (class 1255 OID 16935)
-- Name: cdl_di_appartenenza(character varying); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.cdl_di_appartenenza(i character varying) RETURNS TABLE(codice character varying, nome character varying, anno integer, cfu character, codiceins character varying)
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.cdl_di_appartenenza(i character varying) OWNER TO fontanaf;

--
-- TOC entry 265 (class 1255 OID 16591)
-- Name: check_appartenenza_cdl(); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.check_appartenenza_cdl() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.check_appartenenza_cdl() OWNER TO fontanaf;

--
-- TOC entry 260 (class 1255 OID 16938)
-- Name: check_docente_responsabile_max_tre(); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.check_docente_responsabile_max_tre() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    nir INT;
BEGIN

    SELECT COUNT(*)
    INTO nir
    FROM docente_responsabile dr
    WHERE dr.docente = NEW.docente;

    IF nir > 2 THEN
        RAISE NOTICE 'ATTENZIONE: il docente e'' gia'' responsabile di tre insegnamenti';
        PERFORM pg_notify('notifica', 'ATTENZIONE: il docente e'' gia'' responsabile di tre insegnamenti');
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END;
$$;


ALTER FUNCTION public.check_docente_responsabile_max_tre() OWNER TO fontanaf;

--
-- TOC entry 259 (class 1255 OID 16933)
-- Name: check_inserimento_esame(); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.check_inserimento_esame() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- trovo tutti i corsi di laurea di cui fa parte l'insegnamento che voglio inserire
    -- con il corrispettivo anno

    IF EXISTS (
        WITH cdl_insegnamento AS(
            SELECT i.corso_di_laurea cdl, i.anno FROM insegnamento_parte_di_cdl i
            WHERE i.insegnamento = NEW.insegnamento
        ), cdl_coinvolti AS (
            SELECT * FROM insegnamento_parte_di_cdl ip
                      INNER JOIN cdl_insegnamento c ON c.cdl = ip.corso_di_laurea
                                AND c.anno = ip.anno
                      INNER JOIN calendario_esami cal ON ip.insegnamento = cal.insegnamento
                      WHERE cal.data = NEW.data

        )
        SELECT 1 FROM cdl_insegnamento ci
                          INNER JOIN cdl_coinvolti cc ON ci.cdl = cc.corso_di_laurea
    ) THEN
        RAISE NOTICE 'ATTENZIONE: e'' gia'' presente un altro esame dello stesso anno per la data selezionata';
        PERFORM pg_notify('notifica', 'ATTENZIONE: è già presente un altro esame dello stesso anno per la data selezionata');
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;

END;
$$;


ALTER FUNCTION public.check_inserimento_esame() OWNER TO fontanaf;

--
-- TOC entry 262 (class 1255 OID 16940)
-- Name: check_max_resp_per_ins(); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.check_max_resp_per_ins() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

BEGIN

    PERFORM * FROM docente_responsabile dr
    WHERE dr.insegnamento = NEW.insegnamento;
    IF FOUND THEN
        RAISE NOTICE 'ATTENZIONE: un insegnamento non puo'' avere più di un responsabile';
        PERFORM pg_notify('notifica', 'ATTENZIONE: un insegnamento non puo'' avere più di un responsabile');
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END;
$$;


ALTER FUNCTION public.check_max_resp_per_ins() OWNER TO fontanaf;

--
-- TOC entry 266 (class 1255 OID 16593)
-- Name: check_propedeuticita(); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.check_propedeuticita() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- cerco se c'è in propedeuticità e nel caso che in carriera 'insegnamento1' sia superato
    PERFORM * FROM propedeuticita p
        --INNER JOIN carriera c ON c.insegnamento = p.insegnamento1 AND c.studente = NEW.studente
        INNER JOIN calendario_esami ce ON ce.insegnamento = p.insegnamento2
        INNER JOIN studente s ON s.corso_di_laurea = p.corso_di_laurea
        WHERE ce.id = NEW.esame AND s.utente = NEW.studente;
        IF NOT FOUND THEN
            RETURN NEW;
        ELSE        PERFORM * FROM carriera c
                    INNER JOIN calendario_esami ce ON ce.insegnamento = c.insegnamento
                    INNER JOIN propedeuticita p ON p.insegnamento1 = ce.insegnamento
                    INNER JOIN studente s ON s.corso_di_laurea = p.corso_di_laurea
                    WHERE c.studente = NEW.studente
                    AND c.valutazione >= 18;
                    IF FOUND THEN
                        RETURN NEW;
                    ELSE
                        RAISE NOTICE 'ATTENZIONE: non è possibile iscriversi ad un esame senza aver superato la sua propedeuticità';
                        PERFORM pg_notify('notifica', 'ATTENZIONE: non è possibile iscriversi ad un esame senza aver superato la sua propedeuticità');
                        RETURN NULL;
                    END IF;
        END IF;
END;
$$;


ALTER FUNCTION public.check_propedeuticita() OWNER TO fontanaf;

--
-- TOC entry 270 (class 1255 OID 16946)
-- Name: check_propedeuticita_ciclo(); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.check_propedeuticita_ciclo() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    counter INTEGER := 0;
    size INTEGER;
    instemp VARCHAR;
    riga record;
BEGIN
    RAISE LOG 'INIZIA FUNZIONE';

    -- tabella temporanea con le propedeuticità visitate
    CREATE TEMP TABLE propedeuticita_visitate
    (ins1 varchar, ins2 varchar, cdl varchar) ON COMMIT DROP;

    -- simulo inserimento della nuova propedeuticita
    INSERT INTO propedeuticita_visitate
    VALUES (NEW.insegnamento1, NEW.insegnamento2, NEW.corso_di_laurea);

    -- inserisco le propedeuticita presenti
    INSERT INTO propedeuticita_visitate
    SELECT insegnamento1, insegnamento2, corso_di_laurea
    FROM propedeuticita WHERE corso_di_laurea = NEW.corso_di_laurea;
                        --------------------------------------------
    SELECT count(*) INTO size
    FROM propedeuticita_visitate;
            LOOP


                IF counter = size THEN
                    EXIT;
                END IF;

                FOR riga IN
                    SELECT ins1, ins2, cdl
                    FROM propedeuticita_visitate
                    LOOP
                        RAISE LOG 'STATUS: % % %', riga.ins1, riga.ins2, riga.cdl;
                    END LOOP;

                FOR riga IN
                SELECT pv1.ins1, pv2.ins2, pv1.cdl
                FROM propedeuticita_visitate pv1 INNER JOIN  propedeuticita_visitate pv2
                    ON pv1.ins1 <> pv2.ins1 AND pv1.ins2 <> pv2.ins2
                WHERE pv2.ins1 = pv1.ins2
                LOOP
                    IF riga.ins1 = riga.ins2 THEN
                        RAISE EXCEPTION 'Propedeuticita'' circolare non consentita';
                    END IF;
                    RAISE LOG 'INSERISCO % % %',riga.ins1, riga.ins2, NEW.corso_di_laurea;
                    INSERT INTO propedeuticita_visitate
                    VALUES (riga.ins1, riga.ins2, NEW.corso_di_laurea);
--                     size := size + 1;
                END LOOP;

                FOR riga IN
                    SELECT ins1, ins2, cdl
                    FROM propedeuticita_visitate
                    LOOP
                        RAISE LOG 'AFTER : % % %', riga.ins1, riga.ins2, riga.cdl;
                    END LOOP;
                counter := counter + 1;
            END LOOP;

    -- VERIFICA FINALE
    PERFORM *
    FROM propedeuticita_visitate
    WHERE   cdl = NEW.corso_di_laurea
      AND ins1 = NEW.insegnamento2
      AND ins2 = NEW.insegnamento1;
    IF FOUND THEN
        RAISE EXCEPTION 'Propedeuticita'' circolare non consentita';
    ELSE
    RETURN NEW;
    END IF;
END;
$$;


ALTER FUNCTION public.check_propedeuticita_ciclo() OWNER TO fontanaf;

--
-- TOC entry 257 (class 1255 OID 16845)
-- Name: check_registrazione_carriera(); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.check_registrazione_carriera() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.check_registrazione_carriera() OWNER TO fontanaf;

--
-- TOC entry 256 (class 1255 OID 16809)
-- Name: check_voto_valido(); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.check_voto_valido() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF NEW.valutazione < 0 OR NEW.valutazione > 31 THEN
        RAISE NOTICE 'ATTENZIONE: la valutazione deve essere un intero tra 0 e 31 estremi inclusi';
        PERFORM pg_notify('notifica', 'ATTENZIONE: la valutazione deve essere un intero tra 0 e 31 estremi inclusi');
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END;
$$;


ALTER FUNCTION public.check_voto_valido() OWNER TO fontanaf;

--
-- TOC entry 255 (class 1255 OID 16594)
-- Name: inserisci_in_carriera(); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.inserisci_in_carriera() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- creo la carriera (con tutti i voti del CdL pari a 0) lo studente appena inserito
    INSERT INTO carriera (studente, insegnamento, valutazione, data)
    SELECT NEW.utente, i.insegnamento, NULL, NULL
    FROM insegnamento_parte_di_cdl i
    WHERE i.corso_di_laurea = NEW.corso_di_laurea;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.inserisci_in_carriera() OWNER TO fontanaf;

--
-- TOC entry 252 (class 1255 OID 16595)
-- Name: inserisci_in_carriera(character varying); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.inserisci_in_carriera(new_studente character varying) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- cerco se c'è in propedeuticità e nel caso che in carriera 'insegnamento1' sia superato
    INSERT INTO carriera (studente, insegnamento, valutazione, data)
    SELECT s.utente, i.insegnamento, NULL, NULL
    FROM studente s INNER JOIN insegnamento_parte_di_cdl i ON i.corso_di_laurea = s.corso_di_laurea
    WHERE s.utente = new_studente;
END;
$$;


ALTER FUNCTION public.inserisci_in_carriera(new_studente character varying) OWNER TO fontanaf;

--
-- TOC entry 239 (class 1255 OID 16842)
-- Name: inserisci_in_insegnamenti_per_carriera(); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.inserisci_in_insegnamenti_per_carriera() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- creo la carriera (con tutti i voti del CdL pari a 0) lo studente appena inserito
    INSERT INTO insegnamenti_per_carriera (studente, insegnamento, timestamp)
    SELECT NEW.utente, i.insegnamento, CURRENT_TIMESTAMP
    FROM insegnamento_parte_di_cdl i
    WHERE i.corso_di_laurea = NEW.corso_di_laurea;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.inserisci_in_insegnamenti_per_carriera() OWNER TO fontanaf;

--
-- TOC entry 253 (class 1255 OID 16596)
-- Name: insert_user(character varying, character varying, character varying, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.insert_user(character varying, character varying, character varying, character varying, character varying, character varying) RETURNS character
    LANGUAGE plpgsql
    AS $_$
DECLARE
BEGIN
INSERT INTO utente VALUES ($1,$2,$3,$4,$5,$6);
RAISE INFO 'Inserimento andato a buon fine';
RETURN '0';
EXCEPTION
WHEN unique_violation THEN RAISE INFO 'Errore. Esiste già un record con i valori richiesti';
RETURN '1';
WHEN foreign_key_violation THEN
RAISE INFO 'Errore nella chiave esterna';
RETURN '2';
END;
$_$;


ALTER FUNCTION public.insert_user(character varying, character varying, character varying, character varying, character varying, character varying) OWNER TO fontanaf;

--
-- TOC entry 264 (class 1255 OID 16942)
-- Name: responsabile_non_in_insegna(); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.responsabile_non_in_insegna() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    PERFORM * FROM docente_responsabile dr
    WHERE dr.docente = NEW.docente AND dr.insegnamento = NEW.insegnamento;
    IF FOUND THEN
        RAISE NOTICE 'ATTENZIONE: un insegnamento non puo'' avere più di un responsabile';
        PERFORM pg_notify('notifica', 'ATTENZIONE: un insegnamento non puo'' avere più di un responsabile');
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END;
$$;


ALTER FUNCTION public.responsabile_non_in_insegna() OWNER TO fontanaf;

--
-- TOC entry 254 (class 1255 OID 16597)
-- Name: sposta_dati_studente(character varying); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.sposta_dati_studente(e character varying) RETURNS text
    LANGUAGE plpgsql
    AS $_$
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
$_$;


ALTER FUNCTION public.sposta_dati_studente(e character varying) OWNER TO fontanaf;

--
-- TOC entry 240 (class 1255 OID 16802)
-- Name: verbalizza(character varying, character varying, integer, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.verbalizza(i character varying, s character varying, v integer, t timestamp without time zone) RETURNS text
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.verbalizza(i character varying, s character varying, v integer, t timestamp without time zone) OWNER TO fontanaf;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 215 (class 1259 OID 16598)
-- Name: calendario_esami; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.calendario_esami (
    insegnamento character varying(10) NOT NULL,
    data date NOT NULL,
    ora time without time zone NOT NULL,
    id integer NOT NULL
);


ALTER TABLE public.calendario_esami OWNER TO fontanaf;

--
-- TOC entry 216 (class 1259 OID 16601)
-- Name: calendario_esami_id_seq; Type: SEQUENCE; Schema: public; Owner: fontanaf
--

CREATE SEQUENCE public.calendario_esami_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.calendario_esami_id_seq OWNER TO fontanaf;

--
-- TOC entry 3562 (class 0 OID 0)
-- Dependencies: 216
-- Name: calendario_esami_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: fontanaf
--

ALTER SEQUENCE public.calendario_esami_id_seq OWNED BY public.calendario_esami.id;


--
-- TOC entry 217 (class 1259 OID 16602)
-- Name: carriera; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.carriera (
    studente character varying(50) NOT NULL,
    insegnamento character varying(10) NOT NULL,
    valutazione smallint,
    data date NOT NULL
);


ALTER TABLE public.carriera OWNER TO fontanaf;

--
-- TOC entry 218 (class 1259 OID 16605)
-- Name: carriera_storico; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.carriera_storico (
    studente character varying(50) NOT NULL,
    insegnamento character varying(10) NOT NULL,
    valutazione smallint,
    data date NOT NULL
);


ALTER TABLE public.carriera_storico OWNER TO fontanaf;

--
-- TOC entry 219 (class 1259 OID 16608)
-- Name: corso_di_laurea; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.corso_di_laurea (
    codice character varying(5) NOT NULL,
    nome character varying(50),
    tipo character varying(50)
);


ALTER TABLE public.corso_di_laurea OWNER TO fontanaf;

--
-- TOC entry 220 (class 1259 OID 16611)
-- Name: credenziali; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.credenziali (
    username character varying(50) NOT NULL,
    password character varying(50)
);


ALTER TABLE public.credenziali OWNER TO fontanaf;

--
-- TOC entry 221 (class 1259 OID 16614)
-- Name: docente; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.docente (
    utente character varying(50) NOT NULL,
    tipo character varying(50)
);


ALTER TABLE public.docente OWNER TO fontanaf;

--
-- TOC entry 222 (class 1259 OID 16617)
-- Name: docente_responsabile; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.docente_responsabile (
    docente character varying(50) NOT NULL,
    insegnamento character varying(10) NOT NULL
);


ALTER TABLE public.docente_responsabile OWNER TO fontanaf;

--
-- TOC entry 235 (class 1259 OID 16792)
-- Name: foto_profilo; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.foto_profilo (
    utente character varying(50) NOT NULL,
    path character varying(200),
    "timestamp" timestamp(6) without time zone NOT NULL
);


ALTER TABLE public.foto_profilo OWNER TO fontanaf;

--
-- TOC entry 223 (class 1259 OID 16620)
-- Name: insegnamento; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.insegnamento (
    codice character varying(10) NOT NULL,
    nome character varying(50),
    descrizione text,
    cfu character(2)
);


ALTER TABLE public.insegnamento OWNER TO fontanaf;

--
-- TOC entry 224 (class 1259 OID 16625)
-- Name: insegnamento_parte_di_cdl; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.insegnamento_parte_di_cdl (
    insegnamento character varying(10) NOT NULL,
    corso_di_laurea character varying(5) NOT NULL,
    anno integer NOT NULL
);


ALTER TABLE public.insegnamento_parte_di_cdl OWNER TO fontanaf;

--
-- TOC entry 225 (class 1259 OID 16628)
-- Name: utente; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.utente (
    email character varying(50) NOT NULL,
    nome character varying(50),
    cognome character varying(50),
    indirizzo character varying(255),
    citta character varying(255),
    codicefiscale character varying(16),
    emailpersonale character varying(60)
);


ALTER TABLE public.utente OWNER TO fontanaf;

--
-- TOC entry 226 (class 1259 OID 16633)
-- Name: informazioni_cdl; Type: VIEW; Schema: public; Owner: fontanaf
--

CREATE VIEW public.informazioni_cdl AS
 SELECT c.codice,
    c.nome,
    c.tipo,
    i.codice AS codicec,
    i.nome AS nomec,
    ip.anno,
    i.descrizione,
    i.cfu,
    u.nome AS nomedoc,
    u.cognome AS cognomedoc
   FROM ((((public.corso_di_laurea c
     JOIN public.insegnamento_parte_di_cdl ip ON (((c.codice)::text = (ip.corso_di_laurea)::text)))
     JOIN public.insegnamento i ON (((ip.insegnamento)::text = (i.codice)::text)))
     JOIN public.docente_responsabile d ON (((i.codice)::text = (d.insegnamento)::text)))
     JOIN public.utente u ON (((u.email)::text = (d.docente)::text)));


ALTER TABLE public.informazioni_cdl OWNER TO fontanaf;

--
-- TOC entry 227 (class 1259 OID 16638)
-- Name: insegna; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.insegna (
    docente character varying(50) NOT NULL,
    insegnamento character varying(10) NOT NULL
);


ALTER TABLE public.insegna OWNER TO fontanaf;

--
-- TOC entry 236 (class 1259 OID 16831)
-- Name: insegnamenti_per_carriera; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.insegnamenti_per_carriera (
    studente character varying(50) NOT NULL,
    insegnamento character varying(10) NOT NULL,
    "timestamp" timestamp(6) without time zone
);


ALTER TABLE public.insegnamenti_per_carriera OWNER TO fontanaf;

--
-- TOC entry 3563 (class 0 OID 0)
-- Dependencies: 236
-- Name: TABLE insegnamenti_per_carriera; Type: COMMENT; Schema: public; Owner: fontanaf
--

COMMENT ON TABLE public.insegnamenti_per_carriera IS 'piano di studi di ciascuno studente che si formula all''atto dell''iscrizione.
Se un insegnamento cambia nel tempo, fa fede il piano di studi all''atto dell''iscrizione.
Se uno studente passa in storico, questo piano di studi perde efficacia perchè se si reiscrive forse sarà cambiato il piano di studi per quella laurea';


--
-- TOC entry 228 (class 1259 OID 16641)
-- Name: iscrizione; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.iscrizione (
    studente character varying(50) NOT NULL,
    esame integer NOT NULL
);


ALTER TABLE public.iscrizione OWNER TO fontanaf;

--
-- TOC entry 229 (class 1259 OID 16644)
-- Name: propedeuticita; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.propedeuticita (
    insegnamento1 character varying(10) NOT NULL,
    insegnamento2 character varying(10) NOT NULL,
    corso_di_laurea character varying(5) NOT NULL
);


ALTER TABLE public.propedeuticita OWNER TO fontanaf;

--
-- TOC entry 237 (class 1259 OID 17577)
-- Name: recupero_credenziali; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.recupero_credenziali (
    utente character varying(50) NOT NULL,
    randomvalue character varying(50) NOT NULL
);


ALTER TABLE public.recupero_credenziali OWNER TO fontanaf;

--
-- TOC entry 230 (class 1259 OID 16647)
-- Name: segreteria; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.segreteria (
    utente character varying(50) NOT NULL,
    livello character varying(9) NOT NULL
);


ALTER TABLE public.segreteria OWNER TO fontanaf;

--
-- TOC entry 231 (class 1259 OID 16650)
-- Name: studente; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.studente (
    utente character varying(50) NOT NULL,
    matricola integer NOT NULL,
    corso_di_laurea character varying(5)
);


ALTER TABLE public.studente OWNER TO fontanaf;

--
-- TOC entry 232 (class 1259 OID 16653)
-- Name: studente_matricola_seq; Type: SEQUENCE; Schema: public; Owner: fontanaf
--

CREATE SEQUENCE public.studente_matricola_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.studente_matricola_seq OWNER TO fontanaf;

--
-- TOC entry 3564 (class 0 OID 0)
-- Dependencies: 232
-- Name: studente_matricola_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: fontanaf
--

ALTER SEQUENCE public.studente_matricola_seq OWNED BY public.studente.matricola;


--
-- TOC entry 233 (class 1259 OID 16654)
-- Name: studente_storico; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.studente_storico (
    utente character varying(50) NOT NULL,
    matricola integer,
    corso_di_laurea character varying(5)
);


ALTER TABLE public.studente_storico OWNER TO fontanaf;

--
-- TOC entry 234 (class 1259 OID 16657)
-- Name: utente_storico; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.utente_storico (
    email character varying(50) NOT NULL,
    nome character varying(50),
    cognome character varying(50),
    indirizzo character varying(255),
    citta character varying(255),
    codicefiscale character varying(16),
    emailpersonale character varying(60)
);


ALTER TABLE public.utente_storico OWNER TO fontanaf;

--
-- TOC entry 3319 (class 2604 OID 16662)
-- Name: calendario_esami id; Type: DEFAULT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.calendario_esami ALTER COLUMN id SET DEFAULT nextval('public.calendario_esami_id_seq'::regclass);


--
-- TOC entry 3320 (class 2604 OID 16663)
-- Name: studente matricola; Type: DEFAULT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente ALTER COLUMN matricola SET DEFAULT nextval('public.studente_matricola_seq'::regclass);


--
-- TOC entry 3535 (class 0 OID 16598)
-- Dependencies: 215
-- Data for Name: calendario_esami; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.calendario_esami (insegnamento, data, ora, id) FROM stdin;
55.23.1	2222-02-23	22:22:00	51
IP	2023-08-15	15:50:00	52
IP	2023-08-25	17:50:00	53
97.23.1	2023-08-23	15:50:00	54
116.23.1	2023-12-23	15:50:00	112
55.23.1	2023-08-23	15:50:00	114
55.23.1	2023-09-15	18:10:00	115
59.23.1	2023-08-24	15:50:00	116
55.23.1	2223-12-23	12:12:00	118
\.


--
-- TOC entry 3537 (class 0 OID 16602)
-- Dependencies: 217
-- Data for Name: carriera; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.carriera (studente, insegnamento, valutazione, data) FROM stdin;
demo_studente@studenti.unimi.it	116.23.1	25	2023-12-23
demo_studente@studenti.unimi.it	55.23.1	18	2023-08-23
demo_studente@studenti.unimi.it	55.23.1	23	2023-09-15
demo_studente@studenti.unimi.it	59.23.1	19	2023-08-24
demo_studente@studenti.unimi.it	55.23.1	23	2222-02-23
\.


--
-- TOC entry 3538 (class 0 OID 16605)
-- Dependencies: 218
-- Data for Name: carriera_storico; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.carriera_storico (studente, insegnamento, valutazione, data) FROM stdin;
3.3@studenti.unimi.it	55.23.1	25	2020-10-10
3.3@studenti.unimi.it	55.23.2	23	2020-10-10
\.


--
-- TOC entry 3539 (class 0 OID 16608)
-- Dependencies: 219
-- Data for Name: corso_di_laurea; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.corso_di_laurea (codice, nome, tipo) FROM stdin;
F1X	informatica	triennale
F1XM	sicurezza dei sistemi informatici	magistrale
MED	Medicina	magistrale a ciclo unico
PROVA	Corso Prova	magistrale a ciclo unico
P2	Prova2	magistrale
G1	gabriele2020U2	triennale
DIM	Dimostrazione	triennale
LM-41	Medicina e chirurgia	magistrale a ciclo unico
\.


--
-- TOC entry 3540 (class 0 OID 16611)
-- Dependencies: 220
-- Data for Name: credenziali; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.credenziali (username, password) FROM stdin;
MassimoEmilio.Tarallo@unimi.it	max@tar.it
stefano.montanelli@unimi.it	42d7d2dedd600c6f31668179963dd167
francesco.fontana@studenti.unimi.it	3f22faa13ee8ee53da95f974903c9428
vanessa	282bbbfb69da08d03ff4bcf34a94bc53
paolo.boldi@unimi.it	0d1a3dbbead7b4e0283bc01d2237970b
beatricesanta.palano@unimi.it	70a8c693d2677e1626fa709070f2e64e
stefano.aguzzoli@unimi.it	6f314bd06c21cce3b14993e99f0e305d
paularne.oestvaer@unimi.it	4e35fcd5b53c484ca0cb1c0df4f027b7
giovanni.pighizzini@unimi.it	d63a2506ff26d966a29a9fc7747394a3
massimo.santini@unimi.it	d63a2506ff26d966a29a9fc7747394a3
vincenzo.piuri@unimi.it	f3145c67809a802e71f4c22a95839316
martina.bottanelli@studenti.unimi.it	5f473815df4e9b13c63005d6911ceb10
andrea.fontana@studenti.unimi.it	65ed63eab29c4f10c7bc62e2d4df9a62
paola.brocca@unimi.it	9b11d50a8aef54b5615ad295ac823498
federico.ambrogi@unimi.it	c81ce0de4d3a64bd56a3964dd93f1c11
francesca.bianchi@unimi.it	f8965e3325b36e47560eac617a55890d
marco.foiani@unimi.it	3626630a17fd50ddccfe8da5ccfd115e
francescaromana.bodega@unimi.it	f8965e3325b36e47560eac617a55890d
giacomo.grasselli@unimi.it	f4132d2b9d04c06c313688a957089642
myriam.alcalay@unimi.it	1f0a117bbef89386e5a6259f954a5f96
massimo.aureli@unimi.it	1f0a117bbef89386e5a6259f954a5f96
sergio.abrignani@unimi.it	6f314bd06c21cce3b14993e99f0e305d
paola.bendinelli@unimi.it	0d1a3dbbead7b4e0283bc01d2237970b
demo_studente@studenti.unimi.it	fe01ce2a7fbac8fafaed7c982a04e229
demo_docente@unimi.it	fe01ce2a7fbac8fafaed7c982a04e229
dario.malchiodi@unimi.it	8a49317e060e23bb86f9225ca581e0a9
cecilia.cavaterra@unimi.it	d432eb18017c004fd305969713a17aa8
gabriele.dino@studenti.unimi.it	8bc674f8b3278ec1de6112accd643b4f
lorenzo.filipponi@studenti.unimi.it	3334703c735bd09f54c377b4dfaac1c3
font	47a282dfe68a42d302e22c4920ed9b5e
maria.lora@studenti.unimi.it	333a8f3468fcee90ace874f70396c860
nunzioalberto.borghese@unimi.it	c811f37fa3b8d69c4c6ba388371a98aa
francesca.arnaboldi@unimi.it	a746aa05798d6f060dbb2241369ee640
diegorodolfo.colombo@unimi.it	bf925a01c8287be4ff50ca8b158dbf8d
\.


--
-- TOC entry 3541 (class 0 OID 16614)
-- Dependencies: 221
-- Data for Name: docente; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.docente (utente, tipo) FROM stdin;
cecilia.cavaterra@unimi.it	associato
nunzioalberto.borghese@unimi.it	ordinario
dario.malchiodi@unimi.it	associato
stefano.montanelli@unimi.it	associato
paolo.boldi@unimi.it	ordinario
beatricesanta.palano@unimi.it	associato
stefano.aguzzoli@unimi.it	associato
paularne.oestvaer@unimi.it	ordinario
giovanni.pighizzini@unimi.it	ordinario
massimo.santini@unimi.it	ordinario
vincenzo.piuri@unimi.it	ordinario
demo_docente@unimi.it	associato
francesca.arnaboldi@unimi.it	ricercatore
diegorodolfo.colombo@unimi.it	associato
paola.brocca@unimi.it	associato
federico.ambrogi@unimi.it	associato
francesca.bianchi@unimi.it	ricercatore
marco.foiani@unimi.it	ordinario
francescaromana.bodega@unimi.it	ricercatore
giacomo.grasselli@unimi.it	ordinario
myriam.alcalay@unimi.it	ordinario
massimo.aureli@unimi.it	associato
sergio.abrignani@unimi.it	ordinario
paola.bendinelli@unimi.it	ricercatore
\.


--
-- TOC entry 3542 (class 0 OID 16617)
-- Dependencies: 222
-- Data for Name: docente_responsabile; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.docente_responsabile (docente, insegnamento) FROM stdin;
nunzioalberto.borghese@unimi.it	55.23.1
dario.malchiodi@unimi.it	97.23.1
stefano.montanelli@unimi.it	51.23.1
cecilia.cavaterra@unimi.it	59.23.1
nunzioalberto.borghese@unimi.it	77.23.1
beatricesanta.palano@unimi.it	116.23.1
paolo.boldi@unimi.it	56.23.1
stefano.aguzzoli@unimi.it	115.23.1
paularne.oestvaer@unimi.it	88.23.1
giovanni.pighizzini@unimi.it	52.23.1
massimo.santini@unimi.it	125.23.1
vincenzo.piuri@unimi.it	98.23.1
francesca.arnaboldi@unimi.it	D53-143
diegorodolfo.colombo@unimi.it	D51-53
paola.brocca@unimi.it	D51-54
federico.ambrogi@unimi.it	 D51-56
francesca.bianchi@unimi.it	D51-55
marco.foiani@unimi.it	D51-70
francescaromana.bodega@unimi.it	D51-97
stefano.aguzzoli@unimi.it	D53-19
myriam.alcalay@unimi.it	D51-51
massimo.aureli@unimi.it	D51-52
sergio.abrignani@unimi.it	D51-151
paola.bendinelli@unimi.it	D51-150
\.


--
-- TOC entry 3554 (class 0 OID 16792)
-- Dependencies: 235
-- Data for Name: foto_profilo; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.foto_profilo (utente, path, "timestamp") FROM stdin;
font	../photos/font64d51e41dc62b.jpeg	2023-08-10 19:28:33.913705
nunzioalberto.borghese@unimi.it	photos/nunzioalberto.borghese@unimi.it64d778253bd3e.jpg	2023-08-12 14:16:37.25824
nunzioalberto.borghese@unimi.it	photos/nunzioalberto.borghese@unimi.it64d7782f0e362.png	2023-08-12 14:16:47.069217
nunzioalberto.borghese@unimi.it	photos/nunzioalberto.borghese@unimi.it64d7786d1e347.png	2023-08-12 14:17:49.155824
nunzioalberto.borghese@unimi.it	photos/nunzioalberto.borghese@unimi.it64d77882dc045.png	2023-08-12 14:18:10.914144
nunzioalberto.borghese@unimi.it	/var/www/pigeu/photos/nunzioalberto.borghese@unimi.it64d778f9a75e0.png	2023-08-12 14:20:09.698941
nunzioalberto.borghese@unimi.it	/var/www/pigeu/photos/nunzioalberto.borghese@unimi.it64d7791926d79.png	2023-08-12 14:20:41.173365
nunzioalberto.borghese@unimi.it	../photos/nunzioalberto.borghese@unimi.it64d77943e0051.png	2023-08-12 14:21:23.933917
nunzioalberto.borghese@unimi.it	../photos/nunzioalberto.borghese@unimi.it64d77ae86c77c.png	2023-08-12 14:28:24.457083
gabriele.dino@studenti.unimi.it	../photos/gabriele.dino@studenti.unimi.it64d77bbc12d97.jpeg	2023-08-12 14:31:56.088333
lorenzo.filipponi@studenti.unimi.it	../photos/lorenzo.filipponi@studenti.unimi.it64dbc142cd1ee.jpeg	2023-08-15 20:17:38.853316
\.


--
-- TOC entry 3546 (class 0 OID 16638)
-- Dependencies: 227
-- Data for Name: insegna; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.insegna (docente, insegnamento) FROM stdin;
stefano.aguzzoli@unimi.it	52.23.1
stefano.montanelli@unimi.it	IP
demo_docente@unimi.it	IP
demo_docente@unimi.it	52.23.1
demo_docente@unimi.it	98.23.1
\.


--
-- TOC entry 3555 (class 0 OID 16831)
-- Dependencies: 236
-- Data for Name: insegnamenti_per_carriera; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.insegnamenti_per_carriera (studente, insegnamento, "timestamp") FROM stdin;
demo_studente@studenti.unimi.it	51.23.1	2023-08-14 14:09:55.085238
demo_studente@studenti.unimi.it	55.23.1	2023-08-14 14:09:55.085238
demo_studente@studenti.unimi.it	59.23.1	2023-08-14 14:09:55.085238
demo_studente@studenti.unimi.it	77.23.1	2023-08-14 14:09:55.085238
demo_studente@studenti.unimi.it	97.23.1	2023-08-14 14:09:55.085238
demo_studente@studenti.unimi.it	116.23.1	2023-08-14 14:09:55.085238
demo_studente@studenti.unimi.it	56.23.1	2023-08-14 14:09:55.085238
demo_studente@studenti.unimi.it	115.23.1	2023-08-14 14:09:55.085238
demo_studente@studenti.unimi.it	88.23.1	2023-08-14 14:09:55.085238
demo_studente@studenti.unimi.it	52.23.1	2023-08-14 14:09:55.085238
demo_studente@studenti.unimi.it	125.23.1	2023-08-14 14:09:55.085238
demo_studente@studenti.unimi.it	98.23.1	2023-08-14 14:09:55.085238
demo_studente@studenti.unimi.it	TEST	2023-08-14 14:09:55.085238
martina.bottanelli@studenti.unimi.it	51.23.1	2023-08-11 10:54:44.871076
martina.bottanelli@studenti.unimi.it	77.23.1	2023-08-11 10:54:44.871076
martina.bottanelli@studenti.unimi.it	MAT1	2023-08-11 10:54:44.871076
gabriele.dino@studenti.unimi.it	51.23.1	2023-08-11 12:26:21.989487
gabriele.dino@studenti.unimi.it	55.23.1	2023-08-11 12:26:21.989487
gabriele.dino@studenti.unimi.it	59.23.1	2023-08-11 12:26:21.989487
gabriele.dino@studenti.unimi.it	77.23.1	2023-08-11 12:26:21.989487
gabriele.dino@studenti.unimi.it	97.23.1	2023-08-11 12:26:21.989487
gabriele.dino@studenti.unimi.it	116.23.1	2023-08-11 12:26:21.989487
gabriele.dino@studenti.unimi.it	56.23.1	2023-08-11 12:26:21.989487
gabriele.dino@studenti.unimi.it	115.23.1	2023-08-11 12:26:21.989487
gabriele.dino@studenti.unimi.it	88.23.1	2023-08-11 12:26:21.989487
gabriele.dino@studenti.unimi.it	52.23.1	2023-08-11 12:26:21.989487
gabriele.dino@studenti.unimi.it	125.23.1	2023-08-11 12:26:21.989487
gabriele.dino@studenti.unimi.it	98.23.1	2023-08-11 12:26:21.989487
gabriele.dino@studenti.unimi.it	TEST	2023-08-11 12:26:21.989487
lorenzo.filipponi@studenti.unimi.it	51.23.1	2023-08-15 20:16:21.249916
lorenzo.filipponi@studenti.unimi.it	55.23.1	2023-08-15 20:16:21.249916
lorenzo.filipponi@studenti.unimi.it	59.23.1	2023-08-15 20:16:21.249916
lorenzo.filipponi@studenti.unimi.it	77.23.1	2023-08-15 20:16:21.249916
lorenzo.filipponi@studenti.unimi.it	97.23.1	2023-08-15 20:16:21.249916
lorenzo.filipponi@studenti.unimi.it	116.23.1	2023-08-15 20:16:21.249916
lorenzo.filipponi@studenti.unimi.it	56.23.1	2023-08-15 20:16:21.249916
lorenzo.filipponi@studenti.unimi.it	115.23.1	2023-08-15 20:16:21.249916
lorenzo.filipponi@studenti.unimi.it	88.23.1	2023-08-15 20:16:21.249916
lorenzo.filipponi@studenti.unimi.it	52.23.1	2023-08-15 20:16:21.249916
lorenzo.filipponi@studenti.unimi.it	125.23.1	2023-08-15 20:16:21.249916
lorenzo.filipponi@studenti.unimi.it	98.23.1	2023-08-15 20:16:21.249916
lorenzo.filipponi@studenti.unimi.it	TEST	2023-08-15 20:16:21.249916
andrea.fontana@studenti.unimi.it	51.23.1	2023-08-12 19:46:22.18924
andrea.fontana@studenti.unimi.it	77.23.1	2023-08-12 19:46:22.18924
andrea.fontana@studenti.unimi.it	MAT1	2023-08-12 19:46:22.18924
maria.lora@studenti.unimi.it	51.23.1	2023-08-21 12:57:11.73253
maria.lora@studenti.unimi.it	55.23.1	2023-08-21 12:57:11.73253
maria.lora@studenti.unimi.it	59.23.1	2023-08-21 12:57:11.73253
maria.lora@studenti.unimi.it	77.23.1	2023-08-21 12:57:11.73253
maria.lora@studenti.unimi.it	97.23.1	2023-08-21 12:57:11.73253
maria.lora@studenti.unimi.it	116.23.1	2023-08-21 12:57:11.73253
maria.lora@studenti.unimi.it	56.23.1	2023-08-21 12:57:11.73253
maria.lora@studenti.unimi.it	115.23.1	2023-08-21 12:57:11.73253
maria.lora@studenti.unimi.it	88.23.1	2023-08-21 12:57:11.73253
maria.lora@studenti.unimi.it	52.23.1	2023-08-21 12:57:11.73253
maria.lora@studenti.unimi.it	125.23.1	2023-08-21 12:57:11.73253
maria.lora@studenti.unimi.it	98.23.1	2023-08-21 12:57:11.73253
maria.lora@studenti.unimi.it	TEST	2023-08-21 12:57:11.73253
maria.lora@studenti.unimi.it	IP	2023-08-21 12:57:11.73253
\.


--
-- TOC entry 3543 (class 0 OID 16620)
-- Dependencies: 223
-- Data for Name: insegnamento; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.insegnamento (codice, nome, descrizione, cfu) FROM stdin;
59.23.1	Matematica del Continuo	L'obiettivo dell'insegnamento è duplice. Anzitutto, fornire agli studenti un linguaggio matematico di base, che li metta grado di formulare correttamente un problema e di comprendere un problema formulato da altri. Inoltre, fornire gli strumenti matematici indispensabili per la soluzione di alcuni problemi specifici, che spaziano dal comportamento delle successioni a quello delle serie e delle funzioni di una variabile.	12
55.23.1	Architettura degli elaboratori i	L'insegnamento introduce le conoscenze dei principi che sottendono al funzionamento di un elaboratore digitale; partendo dal livello delle porte logiche si arriva, attraverso alcuni livelli di astrazione intermedi, alla progettazione di ALU firmware e di un'architettura MIPS in grado di eseguire il nucleo delle istruzioni in linguaggio macchina.	6 
97.23.1	Statistica e analisi dei dati	L'insegnamento ha lo scopo di introdurre i concetti fondamentali della statistica descrittiva, del calcolo delle probabilità e della statistica inferenziale parametrica.	6 
51.23.1	Basi di dati	L'insegnamento fornisce i concetti fondamentali relativi alle basi di dati e ai sistemi per la loro gestione, con particolare riguardo ai sistemi di basi di dati relazionali. Il corso prevede i) una parte di teoria dedicata a modelli, linguaggi, metodologie di progettazione e agli aspetti di sicurezza e transazioni, e ii) una parte di laboratorio dedicata all'uso di strumenti di progettazione e gestione di basi di dati relazionali e alle principali tecnologie di basi di dati e Web.	12
77.23.1	Architettura degli elaboratori ii	L'insegnamento fornisce la conoscenza del funzionamento delle architetture digitali approfondendo in particolare la pipe-line, i multi-core e le gerarchie di memoria in modo da potere capire a fondo le problematiche legate ai sistemi operativi e all'ottimizzazione del software. Vengono forniti gli strumenti per valutare le prestazioni dei calcolatori e per ottimizzare le applicazioni.	6 
116.23.1	Linguaggi Formali e Automi	L'insegnamento si prefigge il compito di presentare i concetti della teoria dei linguaggi formali e degli automi centrali in svariati ambiti del contesto informatico attuale, abituando lo studente all'uso di metodi formali.	6 
56.23.1	Programmazione	Obiettivo dell'insegnamento e' introdurre gli studenti alla programmazione imperativa strutturata e al problem solving in piccolo	12
115.23.1	Logica matematica	L'insegnamento ha lo scopo di introdurre i principi fondamentali del ragionamento razionale, tramite l'approccio formale fornito dalla logica matemaica, sia a livello proposizionale che a livello predicativo.	6 
88.23.1	Matematica del Discreto	Gli obiettivi principali dell'insegnamento sono di introdurre il linguaggio dell'algebra e le nozioni di spazio vettoriale e applicazioni lineari e di analizzare il problema della risolubilità dei sistemi di equazioni lineari (anche da un punto di vista algoritmico)	6 
52.23.1	Algoritmi e strutture dati	L'insegnamento ha lo scopo di introdurre i concetti fondamentali riguardanti il progetto e l'analisi di algoritmi e delle strutture dati che essi utilizzano, illustrando le principali tecniche di progettazione e alcune strutture dati fondamentali, insieme all'analisi della complessità computazionale.	12
125.23.1	Programmazione ii	L'insegnamento, che si colloca nel percorso ideale iniziato dall'insegnamento di "Programmazione" e che proseguirà nell'insegnamento di "Ingegneria del software", ha l'obiettivo di presentare alcune astrazioni e concetti utili al progetto, sviluppo e manutenzione di programmi di grandi dimensioni. L'attenzione è focalizzata sul paradigma orientato agli oggetti, con particolare enfasi riguardo al processo di specificazione, modellazione dei tipi di dato e progetto.	6 
98.23.1	Sistemi operativi	L'insegnamento si propone di fornire le conoscenze sui fondamenti teorici, gli algoritmi e le tecnologie riguardanti l'architettura complessiva e la gestione del processore, della memoria centrale, dei dispositivi di ingresso/uscita, del file system, dell'interfaccia utente e degli ambienti distribuiti nei sistemi operativi per le principali tipologie di architetture di elaborazione.	12
TEST	Insegnamento 	SICUREZZA2 INFO3	6 
MAT1	MAT1		6 
IP	InsProva	Questo è un insegnamento per mostrare la funzionalità della base di dati	12
D53-143	Anatomia umana	Alla fine del corso lo studente dovrà: · Aver acquisito conoscenze sufficienti per descrivere l'organizzazione del corpo umano dal livello macroscopico a quello microscopico e per comprendere i meccanismi attraverso i quali tale organizzazione si realizza nel corso dell'organogenesi. · Saper integrare le conoscenze della sistematica anatomica e topografica in relazione alla pratica medica (anatomia funzionale, clinica e radiologica). · Dimostrare di aver acquisito le conoscenze propedeutiche necessarie per seguire con profitto i successivi corsi.	12
D51-53	Chimica e propedeutica biochimica	Fornire agli studenti gli strumenti per conoscere la struttura e il comportamento delle molecole biologicamente attive. I principali tipi di legame chimico, le diverse tipologie di reazione chimica, con gli aspetti cinetici e termodinamici sono gli argomenti da trattare nella parte di Chimica Generale. Nella parte di Chimica Organica si devono trattare le reazioni caratteristiche delle diverse classi di composti, con particolare attenzione agli aspetti stereochimici. Nella parte di Propedeutica Biochimica si devono studiare le molecole biologicamente attive (carboidrati, lipidi, amminoacidi e proteine, acidi nucleici).	6 
D51-54	Fisica medica	Trasmettere il procedimento metodologico della fisica, quale base dell'apprendimento scientifico. Far conoscere i principi fondamentali della fisica e le loro implicazioni in campo biomedico, con particolare riferimento ad alcuni argomenti di rilevanza per la propedeuticità rispetto ai corsi successivi. Impostare e risolvere semplici problemi di fisica sugli argomenti più direttamente connessi al campo biomedico e saper dare valutazioni quantitative e stime dei fenomeni analizzati.	6 
 D51-56	Introduzione alla medicina (D51)	L'insegnamento si propone di fornire agli studenti: a) la consapevolezza che la comprensione dei problemi della medicina contemporanea richiede una riflessione sulla storia della medicina intesa come sintesi tra scienze naturali e scienze umane; b) la consapevolezza che conoscere l'evoluzione storica di alcune importanti malattie serve a comprendere come i concetti di salute e malattia vengano variamente interpretati e valutati a seconda del contesto in cui vengono definiti; c) una introduzione al concetto e principi della salute globale; d) una introduzione al metodo quantitativo in ambito biomedico (biometria); e) la comprensione della dimensione etica dell'agire professionale anche nel campo della ricerca.	6 
D51-55	Istologia ed embriologia	Il corso tratta la morfologia e l'ultrastruttura delle cellule, e la struttura dei tessuti umani, come anche i principali metodi e gli strumenti utilizzati per l'indagine morfologica. Gli aspetti morfologici e strutturali di cellule e tessuti sono descritti e discussi in relazione al loro ruolo funzionale. Le conoscenze acquisite sono fondamentali per lo studio dell'anatomia umana, e per comprendere la fisiologia e la patologia dei diversi organi e sistemi. Alla fine del corso lo studente dovrà: · Aver acquisito conoscenze sufficienti per descrivere ed identificare i diversi tessuti e le diverse strutture cellulari · Dimostrare di aver acquisito le conoscenze propedeutiche necessarie per seguire con profitto i successivi corsi.	6 
D51-70	Biologia e genetica 1 anno	Negli ultimi anni è apparso sempre più evidente l'importanza per la medicina moderna dello sviluppo di terapie avanzate (terapia genica, terapia cellulare, genome editing ecc), della medicina personalizzata o di approcci terapeutici razionalmente dedotti in base ai difetti molecolari che caratterizzano la cellula o l'organo affetti. Queste terapie si avvalgono dei continui ed esponenziali progressi in biologia molecolare e cellulare. L'obiettivo di questo modulo è quindi quello di fornire al futuro medico le adeguate conoscenze e gli strumenti utili a comprendere i meccanismi molecolari e cellulari alla base di diverse patologie umane e/o dei trattamenti terapeutici disponibili. In particolare, le lezioni di Biologia Cellulare contribuiranno alla formazione di un medico che: conosca i principi e le vie di segnalazione cellulare ed intercelluare che regolano la proliferazione, il differenziamento, la trasformazione e la morte della cellula eucariotica; conosca i principali modelli sperimentali che vengono utilizzati in ricerca preclinica per studiare i meccanismi cellulari di base e per modellare specifiche classi di patologie dell'uomo. Le lezioni di Biologia Molecolare, invece, permetteranno al futuro medico di conoscere: · i principali meccanismi molecolari coinvolti nel corretto flusso dell'informazione genica e il suo mantenimento; · le conseguenze associate a difetti nei processi molecolari sopra citati e i possibili approcci molecolari volti alla loro normalizzazione; · le principali tecniche di biologia molecolare utili alla ricerca biomedica, alla diagnosi molecolare o allo sviluppo di terapie avanzate.	6 
D51-97	Fisiologia umana	l'insegnamento della Fisiologia Umana è mirato a far comprendere: - il funzionamento degli organi e degli apparati del corpo umano - i meccanismi di controllo omeostatico, vale a dire le modalità secondo cui organi ed apparati cooperano nel mantenere gli equilibri energetici e l'organizzazione chimico-fisica dell'organismo (fisiologia della vita vegetativa) - le modalità attraverso le quali si svolgono i rapporti tra l'organismo e l'ambiente (fisiologia della vita di relazione)	12
D53-19	Urgenze ed emergenze medico chirurgiche	Alla fine del corso lo studente dovrà essere in grado di: ‒ Valutare i parametri vitali del paziente ‒ Riconoscere e gestire una situazione clinica di emergenza, sia in ambito intraospedaliero, sia sul territorio ‒ Eseguire le manovre di rianimazione cardiopolmonare di base nell'adulto e nel bambino ‒ Eseguire una valutazione primaria e secondaria in un paziente traumatizzato ‒ Eseguire manovre di primo soccorso	6 
D51-51	Biologia e genetica 2 anno	Obiettivo del corso è quello di descrivere i meccanismi genetici alla base della trasmissione dei caratteri mendeliani, per identificare le modalità di trasmissione di caratteri patologici nell'uomo e di calcolarne il rischio riproduttivo, attraverso lo studio degli alberi genealogici. Il corso è inoltre finalizzato a fornire al futuro medico gli strumenti per la comprensione dei meccanismi molecolari responsabili delle principali patologie genetiche su base genica, cromosomica e genomica, e la diagnosi mediante test di nuova generazione. Tali competenze permetteranno di indirizzare il paziente ad approfondimenti diagnostici e clinici più mirati per valutare il rischio di trasmissione di un carattere patologico, identificare la suscettibilità a una specifica malattia, per minimizzare i fattori di rischio ambientali attraverso stili di vita adeguati.	6 
D51-52	Chimica biologica	Alla fine del corso lo studente dovrà: ● Descrivere le trasformazioni chimiche che avvengono nell'organismo umano a livello cellulare, tissutale ed integrato. ● Conoscere e spiegare il meccanismo biochimico dei fenomeni biologici normali e le basi chimiche e molecolari dell'omeostasi dell'organismo umano.	12
D51-151	Immunologia e immunopatologia	- Conoscere l'organizzazione e il funzionamento del sistema immune, delle sue cellule e dei suoi mediatori; - conoscere le tappe essenziali dell'ontogenesi del sistema immune e le sue modificazioni nel corso della vita (vita intrauterina, neonato, adulto, anziano); - conoscere i meccanismi di difesa dell'immunità innata e adattativa verso batteri, virus, funghi, protozoi e elminti; - comprendere il ruolo del sistema immune in gravidanza, nelle trasfusioni, nel rigetto dei trapianti e nel controllo della crescita tumorale; - spiegare i fondamenti eziopatogenetici dei principali quadri immunopatologici: reazioni di ipersensibilità, malattie autoimmunitarie e autoinfiammatorie, immunodeficienze; - comprendere i principi di funzionamento dei vaccini e dei principali approcci immunoterapeutici; - spiegare i principi di test diagnostici basati su tecniche immunologiche.	6 
D51-150	Patologia e fisiopatologia generale 3 anno	Nel corso di Patologia e Fisiopatologia Generale vengono delineati i principi fondamentali della moderna patologia cellulare e molecolare, nonché dei processi patologici multicellulari degenerativi, infiammatori e neoplastici e la fisiopatologia cellulare e i meccanismi della patologia d'organo e delle funzioni integrate.	9 
\.


--
-- TOC entry 3544 (class 0 OID 16625)
-- Dependencies: 224
-- Data for Name: insegnamento_parte_di_cdl; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.insegnamento_parte_di_cdl (insegnamento, corso_di_laurea, anno) FROM stdin;
77.23.1	F1XM	1
51.23.1	P2	1
51.23.1	PROVA	1
51.23.1	F1X	2
55.23.1	F1X	1
51.23.1	MED	1
59.23.1	F1X	1
59.23.1	P2	1
77.23.1	F1X	1
97.23.1	F1X	2
51.23.1	G1	2
77.23.1	MED	5
59.23.1	G1	1
77.23.1	PROVA	1
55.23.1	G1	1
77.23.1	G1	1
116.23.1	F1X	1
56.23.1	F1X	1
115.23.1	F1X	1
88.23.1	F1X	1
52.23.1	F1X	2
125.23.1	F1X	2
98.23.1	F1X	2
TEST	F1X	3
TEST	F1XM	2
52.23.1	F1XM	1
52.23.1	G1	1
56.23.1	G1	1
116.23.1	G1	1
52.23.1	MED	1
TEST	G1	1
IP	DIM	1
IP	F1X	1
52.23.1	DIM	1
55.23.1	DIM	1
77.23.1	DIM	1
51.23.1	DIM	1
116.23.1	DIM	1
115.23.1	DIM	1
59.23.1	DIM	1
88.23.1	DIM	1
56.23.1	DIM	1
125.23.1	DIM	1
D53-143	LM-41	1
D51-53	LM-41	1
D51-54	LM-41	1
 D51-56	LM-41	1
D51-55	LM-41	1
D51-70	LM-41	1
D51-97	LM-41	2
D53-19	LM-41	2
D51-51	LM-41	2
D51-52	LM-41	2
D51-151	LM-41	2
D51-150	LM-41	3
\.


--
-- TOC entry 3547 (class 0 OID 16641)
-- Dependencies: 228
-- Data for Name: iscrizione; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.iscrizione (studente, esame) FROM stdin;
demo_studente@studenti.unimi.it	112
maria.lora@studenti.unimi.it	52
demo_studente@studenti.unimi.it	114
demo_studente@studenti.unimi.it	115
demo_studente@studenti.unimi.it	51
demo_studente@studenti.unimi.it	116
\.


--
-- TOC entry 3548 (class 0 OID 16644)
-- Dependencies: 229
-- Data for Name: propedeuticita; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.propedeuticita (insegnamento1, insegnamento2, corso_di_laurea) FROM stdin;
59.23.1	97.23.1	F1X
D53-143	D51-55	LM-41
\.


--
-- TOC entry 3556 (class 0 OID 17577)
-- Dependencies: 237
-- Data for Name: recupero_credenziali; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.recupero_credenziali (utente, randomvalue) FROM stdin;
font	64e2228fabd1d
\.


--
-- TOC entry 3549 (class 0 OID 16647)
-- Dependencies: 230
-- Data for Name: segreteria; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.segreteria (utente, livello) FROM stdin;
font	studenti
\.


--
-- TOC entry 3550 (class 0 OID 16650)
-- Dependencies: 231
-- Data for Name: studente; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.studente (utente, matricola, corso_di_laurea) FROM stdin;
francesco.fontana@studenti.unimi.it	81	F1X
vanessa	85	F1X
martina.bottanelli@studenti.unimi.it	96	MED
gabriele.dino@studenti.unimi.it	97	F1X
andrea.fontana@studenti.unimi.it	100	MED
demo_studente@studenti.unimi.it	107	F1X
lorenzo.filipponi@studenti.unimi.it	109	F1X
maria.lora@studenti.unimi.it	110	F1X
\.


--
-- TOC entry 3552 (class 0 OID 16654)
-- Dependencies: 233
-- Data for Name: studente_storico; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.studente_storico (utente, matricola, corso_di_laurea) FROM stdin;
s.s	77	F1X
Francesco.Fontana@unimi.it	18	\N
3.3@studenti.unimi.it	106	F1X
.filipponi@studenti.unimi.it	108	F1X
\.


--
-- TOC entry 3545 (class 0 OID 16628)
-- Dependencies: 225
-- Data for Name: utente; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.utente (email, nome, cognome, indirizzo, citta, codicefiscale, emailpersonale) FROM stdin;
MassimoEmilio.Tarallo@unimi.it	MassimoEmilio	Tarallo				max@tar.it
cecilia.cavaterra@unimi.it	Cecilia	Cavaterra				c@c.it
dario.malchiodi@unimi.it	Dario	Malchiodi				d@m.it
stefano.montanelli@unimi.it	Stefano	Montanelli				s@m.it
francesco.fontana@studenti.unimi.it	Francesco	Fontana				f@f.it
vanessa	Vanessa	Olivo				vanessa@olivo.it
paolo.boldi@unimi.it	Paolo	Boldi				p@b.it
beatricesanta.palano@unimi.it	Beatrice Santa	Palano				b@p.it
paularne.oestvaer@unimi.it	Paul Arne	Oestvaer				p@o.it
giovanni.pighizzini@unimi.it	Giovanni	Pighizzini				g@p.it
vincenzo.piuri@unimi.it	Vincenzo	Piuri				v@p.it
massimo.santini@unimi.it	Massimo	Santini				m@s.it
sergio.abrignani@unimi.it	Sergio	Abrignani	Via Francesco Sforza, 35	Milano		s@a.it
martina.bottanelli@studenti.unimi.it	Martina	Bottanelli				m@b.it
andrea.fontana@studenti.unimi.it	Andrea	Fontana				a@f.it
paola.bendinelli@unimi.it	Paola	Bendinelli	Via Mangiagalli, 31	Milano		p@b.it
nunzioalberto.borghese@unimi.it	Nunzio Alberto	Borghese		asdsadMilano		n@b.it
gabriele.dino@studenti.unimi.it	Gabriele	Dino	via Uguaglianza 18	Vergiate		g@d.it
stefano.aguzzoli@unimi.it	Stefano	Aguzzoli		Milano Assago Forum2		s@a.it
demo_studente@studenti.unimi.it	Demo	Studente	Via dimostrazione 0	Proof	DMMSTD	demo@demo.demo
demo_docente@unimi.it	Demo	Docente	Via dimostrazione 0	Proof	DMMDCT	demo@demo.demo
lorenzo.filipponi@studenti.unimi.it	Lorenzo	Filipponi				l@f.it
font	Francesco Stephan Maria	Fontana	via regina 1614		FNTFNC92E28E151Z	frafontana28@gmail.com
maria.lora@studenti.unimi.it	Maria	Lora		Legnago		mery.lora.26@gmail.com
francesca.arnaboldi@unimi.it	Francesca	Arnaboldi				fra@arna.it
diegorodolfo.colombo@unimi.it	Diego Rodolfo	Colombo				diego@rodolf.it
paola.brocca@unimi.it	Paola	Brocca				paola@b.it
federico.ambrogi@unimi.it	Federico	Ambrogi	Campus Cascina Rosa, Via Vanzetti 5	Milano		f@a.it
francesca.bianchi@unimi.it	Francesca	Bianchi	Via Mangiagalli 31	Milano		f@b.it
marco.foiani@unimi.it	Marco	Foiani	Via Adamello, 16	Milano		m@f.it
francescaromana.bodega@unimi.it	Francescaromana	Bodega	via Mangiagalli, 32	Milano		f@b.it
giacomo.grasselli@unimi.it	Giacomo	Grasselli	Via Francesco Sforza, 35	Milano		g@g.it
myriam.alcalay@unimi.it	Myriam	Alcalay	Via Ripamonti, 435	Milano		m@a.it
massimo.aureli@unimi.it	Massimo	Aureli	Via Fratelli Cervi, 93	Milano		m@a.it
\.


--
-- TOC entry 3553 (class 0 OID 16657)
-- Dependencies: 234
-- Data for Name: utente_storico; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.utente_storico (email, nome, cognome, indirizzo, citta, codicefiscale, emailpersonale) FROM stdin;
s.s	Francesco	Studente				s@s.s
Francesco.Fontana@unimi.it	Francesco	Fontana	via Regina 1614	Pianello del Lario	FNTFNC92E28E151Z	frafontana28@gmail.com
3.3@studenti.unimi.it	3	3				3@3.3
.filipponi@studenti.unimi.it	   	Filipponi				l@f.it
\.


--
-- TOC entry 3565 (class 0 OID 0)
-- Dependencies: 216
-- Name: calendario_esami_id_seq; Type: SEQUENCE SET; Schema: public; Owner: fontanaf
--

SELECT pg_catalog.setval('public.calendario_esami_id_seq', 118, true);


--
-- TOC entry 3566 (class 0 OID 0)
-- Dependencies: 232
-- Name: studente_matricola_seq; Type: SEQUENCE SET; Schema: public; Owner: fontanaf
--

SELECT pg_catalog.setval('public.studente_matricola_seq', 110, true);


--
-- TOC entry 3322 (class 2606 OID 16665)
-- Name: calendario_esami calendario_esami_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.calendario_esami
    ADD CONSTRAINT calendario_esami_pkey PRIMARY KEY (id);


--
-- TOC entry 3324 (class 2606 OID 16830)
-- Name: carriera carriera_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.carriera
    ADD CONSTRAINT carriera_pkey PRIMARY KEY (studente, data, insegnamento);


--
-- TOC entry 3326 (class 2606 OID 16875)
-- Name: carriera_storico carriera_storico_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.carriera_storico
    ADD CONSTRAINT carriera_storico_pkey PRIMARY KEY (studente, insegnamento, data);


--
-- TOC entry 3328 (class 2606 OID 16669)
-- Name: corso_di_laurea corso_di_laurea_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.corso_di_laurea
    ADD CONSTRAINT corso_di_laurea_pkey PRIMARY KEY (codice);


--
-- TOC entry 3330 (class 2606 OID 16671)
-- Name: credenziali credenziali_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.credenziali
    ADD CONSTRAINT credenziali_pkey PRIMARY KEY (username);


--
-- TOC entry 3333 (class 2606 OID 16673)
-- Name: docente docente_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente
    ADD CONSTRAINT docente_pkey PRIMARY KEY (utente);


--
-- TOC entry 3335 (class 2606 OID 16675)
-- Name: docente_responsabile docente_responsabile_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente_responsabile
    ADD CONSTRAINT docente_responsabile_pkey PRIMARY KEY (docente, insegnamento);


--
-- TOC entry 3357 (class 2606 OID 16796)
-- Name: foto_profilo foto_profilo_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.foto_profilo
    ADD CONSTRAINT foto_profilo_pkey PRIMARY KEY (utente, "timestamp");


--
-- TOC entry 3359 (class 2606 OID 16835)
-- Name: insegnamenti_per_carriera insegnamenti_per_carriera_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamenti_per_carriera
    ADD CONSTRAINT insegnamenti_per_carriera_pkey PRIMARY KEY (studente, insegnamento);


--
-- TOC entry 3339 (class 2606 OID 16677)
-- Name: insegnamento_parte_di_cdl insegnamento_parte_di_cdl_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamento_parte_di_cdl
    ADD CONSTRAINT insegnamento_parte_di_cdl_pkey PRIMARY KEY (insegnamento, corso_di_laurea, anno);


--
-- TOC entry 3337 (class 2606 OID 16679)
-- Name: insegnamento insegnamento_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamento
    ADD CONSTRAINT insegnamento_pkey PRIMARY KEY (codice);


--
-- TOC entry 3345 (class 2606 OID 16681)
-- Name: iscrizione iscrizione_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.iscrizione
    ADD CONSTRAINT iscrizione_pkey PRIMARY KEY (studente, esame);


--
-- TOC entry 3343 (class 2606 OID 16683)
-- Name: insegna isnegna_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegna
    ADD CONSTRAINT isnegna_pkey PRIMARY KEY (docente, insegnamento);


--
-- TOC entry 3347 (class 2606 OID 16685)
-- Name: propedeuticita propedeuticita_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.propedeuticita
    ADD CONSTRAINT propedeuticita_pkey PRIMARY KEY (insegnamento1, insegnamento2, corso_di_laurea);


--
-- TOC entry 3361 (class 2606 OID 17581)
-- Name: recupero_credenziali recupero_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.recupero_credenziali
    ADD CONSTRAINT recupero_pkey PRIMARY KEY (utente, randomvalue);


--
-- TOC entry 3349 (class 2606 OID 16687)
-- Name: segreteria segreteria_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.segreteria
    ADD CONSTRAINT segreteria_pkey PRIMARY KEY (utente);


--
-- TOC entry 3351 (class 2606 OID 16689)
-- Name: studente studente_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente
    ADD CONSTRAINT studente_pkey PRIMARY KEY (utente);


--
-- TOC entry 3353 (class 2606 OID 16868)
-- Name: studente_storico studente_storico_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente_storico
    ADD CONSTRAINT studente_storico_pkey PRIMARY KEY (utente);


--
-- TOC entry 3341 (class 2606 OID 16691)
-- Name: utente utente_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.utente
    ADD CONSTRAINT utente_pkey PRIMARY KEY (email);


--
-- TOC entry 3355 (class 2606 OID 16866)
-- Name: utente_storico utente_storico_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.utente_storico
    ADD CONSTRAINT utente_storico_pkey PRIMARY KEY (email);


--
-- TOC entry 3331 (class 1259 OID 16692)
-- Name: fki_username; Type: INDEX; Schema: public; Owner: fontanaf
--

CREATE INDEX fki_username ON public.credenziali USING btree (username);


--
-- TOC entry 3394 (class 2620 OID 16844)
-- Name: studente creazione_insegnamenti_per_carriera; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER creazione_insegnamenti_per_carriera AFTER INSERT ON public.studente FOR EACH ROW EXECUTE FUNCTION public.inserisci_in_insegnamenti_per_carriera();


--
-- TOC entry 3388 (class 2620 OID 16941)
-- Name: docente_responsabile max_un_resp_per_ins; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER max_un_resp_per_ins BEFORE INSERT OR UPDATE ON public.docente_responsabile FOR EACH ROW EXECUTE FUNCTION public.check_max_resp_per_ins();


--
-- TOC entry 3393 (class 2620 OID 16947)
-- Name: propedeuticita no_cicli_propedeuticità; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER "no_cicli_propedeuticità" BEFORE INSERT OR UPDATE ON public.propedeuticita FOR EACH ROW EXECUTE FUNCTION public.check_propedeuticita_ciclo();


--
-- TOC entry 3386 (class 2620 OID 16810)
-- Name: carriera no_esami_senza_propedeuticita; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER no_esami_senza_propedeuticita BEFORE INSERT OR UPDATE ON public.carriera FOR EACH ROW EXECUTE FUNCTION public.check_voto_valido();


--
-- TOC entry 3392 (class 2620 OID 16694)
-- Name: iscrizione no_esami_senza_propedeuticita; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER no_esami_senza_propedeuticita BEFORE INSERT OR UPDATE ON public.iscrizione FOR EACH ROW EXECUTE FUNCTION public.check_propedeuticita();


--
-- TOC entry 3385 (class 2620 OID 16934)
-- Name: calendario_esami no_esami_stesso_anno_stesso_giorno; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER no_esami_stesso_anno_stesso_giorno BEFORE INSERT OR UPDATE ON public.calendario_esami FOR EACH ROW EXECUTE FUNCTION public.check_inserimento_esame();


--
-- TOC entry 3391 (class 2620 OID 16696)
-- Name: iscrizione no_iscriz_se_non_in_cdl; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER no_iscriz_se_non_in_cdl BEFORE INSERT OR UPDATE ON public.iscrizione FOR EACH ROW EXECUTE FUNCTION public.check_appartenenza_cdl();


--
-- TOC entry 3387 (class 2620 OID 16846)
-- Name: carriera registrazione_in_carriera; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER registrazione_in_carriera BEFORE INSERT OR UPDATE ON public.carriera FOR EACH ROW EXECUTE FUNCTION public.check_registrazione_carriera();


--
-- TOC entry 3389 (class 2620 OID 16939)
-- Name: docente_responsabile responsab_non_piu_di_tre; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER responsab_non_piu_di_tre BEFORE INSERT OR UPDATE ON public.docente_responsabile FOR EACH ROW EXECUTE FUNCTION public.check_docente_responsabile_max_tre();


--
-- TOC entry 3390 (class 2620 OID 16943)
-- Name: insegna responsabile_non_in_insegna; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER responsabile_non_in_insegna BEFORE INSERT OR UPDATE ON public.insegna FOR EACH ROW EXECUTE FUNCTION public.responsabile_non_in_insegna();


--
-- TOC entry 3363 (class 2606 OID 16920)
-- Name: carriera carriera_insegnamento_fkey; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.carriera
    ADD CONSTRAINT carriera_insegnamento_fkey FOREIGN KEY (insegnamento, studente) REFERENCES public.insegnamenti_per_carriera(insegnamento, studente) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3375 (class 2606 OID 16697)
-- Name: propedeuticita propedeuticità_corso1; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.propedeuticita
    ADD CONSTRAINT "propedeuticità_corso1" FOREIGN KEY (insegnamento1) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3567 (class 0 OID 0)
-- Dependencies: 3375
-- Name: CONSTRAINT "propedeuticità_corso1" ON propedeuticita; Type: COMMENT; Schema: public; Owner: fontanaf
--

COMMENT ON CONSTRAINT "propedeuticità_corso1" ON public.propedeuticita IS 'il corso1 è propedeutico al corso2';


--
-- TOC entry 3376 (class 2606 OID 16702)
-- Name: propedeuticita propedeuticità_corso2; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.propedeuticita
    ADD CONSTRAINT "propedeuticità_corso2" FOREIGN KEY (insegnamento2) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3568 (class 0 OID 0)
-- Dependencies: 3376
-- Name: CONSTRAINT "propedeuticità_corso2" ON propedeuticita; Type: COMMENT; Schema: public; Owner: fontanaf
--

COMMENT ON CONSTRAINT "propedeuticità_corso2" ON public.propedeuticita IS 'il corso1 è propedeutico al corso2';


--
-- TOC entry 3384 (class 2606 OID 17582)
-- Name: recupero_credenziali recupero_utente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.recupero_credenziali
    ADD CONSTRAINT recupero_utente_fkey FOREIGN KEY (utente) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3373 (class 2606 OID 16707)
-- Name: iscrizione rif_calendario_esami; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.iscrizione
    ADD CONSTRAINT rif_calendario_esami FOREIGN KEY (esame) REFERENCES public.calendario_esami(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3369 (class 2606 OID 16712)
-- Name: insegnamento_parte_di_cdl rif_cdl; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamento_parte_di_cdl
    ADD CONSTRAINT rif_cdl FOREIGN KEY (corso_di_laurea) REFERENCES public.corso_di_laurea(codice) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3379 (class 2606 OID 16717)
-- Name: studente rif_corso_di_laurea; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente
    ADD CONSTRAINT rif_corso_di_laurea FOREIGN KEY (corso_di_laurea) REFERENCES public.corso_di_laurea(codice) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3377 (class 2606 OID 16722)
-- Name: propedeuticita rif_corso_di_laurea; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.propedeuticita
    ADD CONSTRAINT rif_corso_di_laurea FOREIGN KEY (corso_di_laurea) REFERENCES public.corso_di_laurea(codice) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3371 (class 2606 OID 16727)
-- Name: insegna rif_docente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegna
    ADD CONSTRAINT rif_docente FOREIGN KEY (docente) REFERENCES public.docente(utente) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3367 (class 2606 OID 16732)
-- Name: docente_responsabile rif_docente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente_responsabile
    ADD CONSTRAINT rif_docente FOREIGN KEY (docente) REFERENCES public.docente(utente) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3372 (class 2606 OID 16737)
-- Name: insegna rif_insegnamento; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegna
    ADD CONSTRAINT rif_insegnamento FOREIGN KEY (insegnamento) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3362 (class 2606 OID 16742)
-- Name: calendario_esami rif_insegnamento; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.calendario_esami
    ADD CONSTRAINT rif_insegnamento FOREIGN KEY (insegnamento) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3370 (class 2606 OID 16747)
-- Name: insegnamento_parte_di_cdl rif_insegnamento; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamento_parte_di_cdl
    ADD CONSTRAINT rif_insegnamento FOREIGN KEY (insegnamento) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3368 (class 2606 OID 16757)
-- Name: docente_responsabile rif_insegnamento; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente_responsabile
    ADD CONSTRAINT rif_insegnamento FOREIGN KEY (insegnamento) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3374 (class 2606 OID 16762)
-- Name: iscrizione rif_studente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.iscrizione
    ADD CONSTRAINT rif_studente FOREIGN KEY (studente) REFERENCES public.studente(utente) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3383 (class 2606 OID 16925)
-- Name: insegnamenti_per_carriera rif_studente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamenti_per_carriera
    ADD CONSTRAINT rif_studente FOREIGN KEY (studente) REFERENCES public.studente(utente) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3364 (class 2606 OID 16876)
-- Name: carriera_storico rif_studente_storico; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.carriera_storico
    ADD CONSTRAINT rif_studente_storico FOREIGN KEY (studente) REFERENCES public.studente_storico(utente) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3378 (class 2606 OID 16772)
-- Name: segreteria rif_utente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.segreteria
    ADD CONSTRAINT rif_utente FOREIGN KEY (utente) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3569 (class 0 OID 0)
-- Dependencies: 3378
-- Name: CONSTRAINT rif_utente ON segreteria; Type: COMMENT; Schema: public; Owner: fontanaf
--

COMMENT ON CONSTRAINT rif_utente ON public.segreteria IS 'riferimento alla tabella utente';


--
-- TOC entry 3366 (class 2606 OID 16777)
-- Name: docente rif_utente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente
    ADD CONSTRAINT rif_utente FOREIGN KEY (utente) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3380 (class 2606 OID 16782)
-- Name: studente rif_utente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente
    ADD CONSTRAINT rif_utente FOREIGN KEY (utente) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3382 (class 2606 OID 16797)
-- Name: foto_profilo rif_utente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.foto_profilo
    ADD CONSTRAINT rif_utente FOREIGN KEY (utente) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3381 (class 2606 OID 16869)
-- Name: studente_storico rif_utente_storico; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente_storico
    ADD CONSTRAINT rif_utente_storico FOREIGN KEY (utente) REFERENCES public.utente_storico(email) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3365 (class 2606 OID 16787)
-- Name: credenziali username; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.credenziali
    ADD CONSTRAINT username FOREIGN KEY (username) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


-- Completed on 2023-08-24 03:25:04 CEST

--
-- PostgreSQL database dump complete
--

