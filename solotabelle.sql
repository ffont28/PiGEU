--
-- PostgreSQL database dump
--

-- Dumped from database version 14.8 (Ubuntu 14.8-0ubuntu0.22.04.1)
-- Dumped by pg_dump version 14.8 (Ubuntu 14.8-0ubuntu0.22.04.1)

-- Started on 2023-08-12 22:48:55 CEST

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
-- TOC entry 231 (class 1255 OID 16590)
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
-- TOC entry 255 (class 1255 OID 16908)
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
-- TOC entry 254 (class 1255 OID 16909)
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
        WHERE ic.studente = TARGET;
END;
$$;


ALTER FUNCTION public.carriera_completa_tutti(target character varying) OWNER TO fontanaf;

--
-- TOC entry 253 (class 1255 OID 16591)
-- Name: check_appartenenza_cdl(); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.check_appartenenza_cdl() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    PERFORM * FROM calendario_esami c
            INNER JOIN insegnamenti_per_carriera ipc ON ipc.insegnamento = c.insegnamento
            WHERE ipc.studente = NEW.studente;
    IF FOUND THEN
        -- l'esame che si vuole inserire in calendario_esami è presente nel cdL
        RETURN NEW;
    ELSE
        -- l'esame che si vuole inserire in calendario_esami NON è presente nel cdL
        RAISE NOTICE 'ATTENZIONE: l''esame a cui ti vuoi iscrivere non fa parte del CdL a cui è iscritto lo studente';
        PERFORM pg_notify('notifica', 'ATTENZIONE: l''esame a cui ti vuoi iscrivere non fa parte del CdL a cui è iscritto lo studente');
        RETURN NULL;
    END IF;
END;
$$;


ALTER FUNCTION public.check_appartenenza_cdl() OWNER TO fontanaf;

--
-- TOC entry 250 (class 1255 OID 16807)
-- Name: check_inserimento_esame(); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.check_inserimento_esame() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    esame_trovato BOOLEAN;
BEGIN
    -- Verifica la presenza di esami duplicati
    SELECT EXISTS (
        SELECT 1
        FROM calendario_esami c1
        INNER JOIN insegnamento_parte_di_cdl ip ON c1.insegnamento = ip.insegnamento
        WHERE c1.data = NEW.data AND ip.corso_di_laurea = (SELECT corso_di_laurea FROM insegnamento_parte_di_cdl WHERE insegnamento = NEW.insegnamento) AND ip.anno = (SELECT anno FROM insegnamento_parte_di_cdl WHERE insegnamento = NEW.insegnamento)
    ) INTO esame_trovato;

    IF esame_trovato THEN
        RAISE NOTICE 'ATTENZIONE: è già presente un altro esame dello stesso anno per la data selezionata';
        PERFORM pg_notify('notifica', 'ATTENZIONE: è già presente un altro esame dello stesso anno per la data selezionata');
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END;
$$;


ALTER FUNCTION public.check_inserimento_esame() OWNER TO fontanaf;

--
-- TOC entry 245 (class 1255 OID 16593)
-- Name: check_propedeuticita(); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.check_propedeuticita() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- cerco se c'è in propedeuticità e nel caso che in carriera 'insegnamento1' sia superato
    PERFORM * FROM propedeuticita p
        INNER JOIN carriera c ON c.insegnamento = p.insegnamento1 AND c.studente = NEW.studente
        INNER JOIN calendario_esami ce ON ce.insegnamento = p.insegnamento2 and ce.id = NEW.esame
    WHERE c.valutazione < 18;
    IF FOUND THEN
        RAISE NOTICE 'ATTENZIONE: non è possibile iscriversi ad un esame senza aver superato la sua propedeuticità';
        PERFORM pg_notify('notifica', 'ATTENZIONE: non è possibile iscriversi ad un esame senza aver superato la sua propedeuticità');
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END;
$$;


ALTER FUNCTION public.check_propedeuticita() OWNER TO fontanaf;

--
-- TOC entry 252 (class 1255 OID 16845)
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
-- TOC entry 251 (class 1255 OID 16809)
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
-- TOC entry 249 (class 1255 OID 16594)
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
-- TOC entry 246 (class 1255 OID 16595)
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
-- TOC entry 232 (class 1255 OID 16842)
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
-- TOC entry 247 (class 1255 OID 16596)
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
-- TOC entry 248 (class 1255 OID 16597)
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
-- TOC entry 233 (class 1255 OID 16802)
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
-- TOC entry 209 (class 1259 OID 16598)
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
-- TOC entry 210 (class 1259 OID 16601)
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
-- TOC entry 3515 (class 0 OID 0)
-- Dependencies: 210
-- Name: calendario_esami_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: fontanaf
--

ALTER SEQUENCE public.calendario_esami_id_seq OWNED BY public.calendario_esami.id;


--
-- TOC entry 211 (class 1259 OID 16602)
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
-- TOC entry 212 (class 1259 OID 16605)
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
-- TOC entry 213 (class 1259 OID 16608)
-- Name: corso_di_laurea; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.corso_di_laurea (
    codice character varying(5) NOT NULL,
    nome character varying(50),
    tipo character varying(50)
);


ALTER TABLE public.corso_di_laurea OWNER TO fontanaf;

--
-- TOC entry 214 (class 1259 OID 16611)
-- Name: credenziali; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.credenziali (
    username character varying(50) NOT NULL,
    password character varying(50)
);


ALTER TABLE public.credenziali OWNER TO fontanaf;

--
-- TOC entry 215 (class 1259 OID 16614)
-- Name: docente; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.docente (
    utente character varying(50) NOT NULL,
    tipo character varying(50)
);


ALTER TABLE public.docente OWNER TO fontanaf;

--
-- TOC entry 216 (class 1259 OID 16617)
-- Name: docente_responsabile; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.docente_responsabile (
    docente character varying(50) NOT NULL,
    insegnamento character varying(10) NOT NULL
);


ALTER TABLE public.docente_responsabile OWNER TO fontanaf;

--
-- TOC entry 229 (class 1259 OID 16792)
-- Name: foto_profilo; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.foto_profilo (
    utente character varying(50) NOT NULL,
    path character varying(200),
    "timestamp" timestamp(6) without time zone NOT NULL
);


ALTER TABLE public.foto_profilo OWNER TO fontanaf;

--
-- TOC entry 217 (class 1259 OID 16620)
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
-- TOC entry 218 (class 1259 OID 16625)
-- Name: insegnamento_parte_di_cdl; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.insegnamento_parte_di_cdl (
    insegnamento character varying(10) NOT NULL,
    corso_di_laurea character varying(5) NOT NULL,
    anno integer NOT NULL
);


ALTER TABLE public.insegnamento_parte_di_cdl OWNER TO fontanaf;

--
-- TOC entry 219 (class 1259 OID 16628)
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
-- TOC entry 220 (class 1259 OID 16633)
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
-- TOC entry 221 (class 1259 OID 16638)
-- Name: insegna; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.insegna (
    docente character varying(50) NOT NULL,
    insegnamento character varying(10) NOT NULL
);


ALTER TABLE public.insegna OWNER TO fontanaf;

--
-- TOC entry 230 (class 1259 OID 16831)
-- Name: insegnamenti_per_carriera; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.insegnamenti_per_carriera (
    studente character varying(50) NOT NULL,
    insegnamento character varying(10) NOT NULL,
    "timestamp" timestamp(6) without time zone
);


ALTER TABLE public.insegnamenti_per_carriera OWNER TO fontanaf;

--
-- TOC entry 3516 (class 0 OID 0)
-- Dependencies: 230
-- Name: TABLE insegnamenti_per_carriera; Type: COMMENT; Schema: public; Owner: fontanaf
--

COMMENT ON TABLE public.insegnamenti_per_carriera IS 'piano di studi di ciascuno studente che si formula all''atto dell''iscrizione.
Se un insegnamento cambia nel tempo, fa fede il piano di studi all''atto dell''iscrizione.
Se uno studente passa in storico, questo piano di studi perde efficacia perchè se si reiscrive forse sarà cambiato il piano di studi per quella laurea';


--
-- TOC entry 222 (class 1259 OID 16641)
-- Name: iscrizione; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.iscrizione (
    studente character varying(50) NOT NULL,
    esame integer NOT NULL
);


ALTER TABLE public.iscrizione OWNER TO fontanaf;

--
-- TOC entry 223 (class 1259 OID 16644)
-- Name: propedeuticita; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.propedeuticita (
    insegnamento1 character varying(10) NOT NULL,
    insegnamento2 character varying(10) NOT NULL,
    corso_di_laurea character varying(5) NOT NULL
);


ALTER TABLE public.propedeuticita OWNER TO fontanaf;

--
-- TOC entry 224 (class 1259 OID 16647)
-- Name: segreteria; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.segreteria (
    utente character varying(50) NOT NULL,
    livello character varying(9) NOT NULL
);


ALTER TABLE public.segreteria OWNER TO fontanaf;

--
-- TOC entry 225 (class 1259 OID 16650)
-- Name: studente; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.studente (
    utente character varying(50) NOT NULL,
    matricola integer NOT NULL,
    corso_di_laurea character varying(5)
);


ALTER TABLE public.studente OWNER TO fontanaf;

--
-- TOC entry 226 (class 1259 OID 16653)
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
-- TOC entry 3517 (class 0 OID 0)
-- Dependencies: 226
-- Name: studente_matricola_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: fontanaf
--

ALTER SEQUENCE public.studente_matricola_seq OWNED BY public.studente.matricola;


--
-- TOC entry 227 (class 1259 OID 16654)
-- Name: studente_storico; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.studente_storico (
    utente character varying(50) NOT NULL,
    matricola integer,
    corso_di_laurea character varying(5)
);


ALTER TABLE public.studente_storico OWNER TO fontanaf;

--
-- TOC entry 228 (class 1259 OID 16657)
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
-- TOC entry 3300 (class 2604 OID 16662)
-- Name: calendario_esami id; Type: DEFAULT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.calendario_esami ALTER COLUMN id SET DEFAULT nextval('public.calendario_esami_id_seq'::regclass);


--
-- TOC entry 3301 (class 2604 OID 16663)
-- Name: studente matricola; Type: DEFAULT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente ALTER COLUMN matricola SET DEFAULT nextval('public.studente_matricola_seq'::regclass);


--
-- TOC entry 3303 (class 2606 OID 16665)
-- Name: calendario_esami calendario_esami_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.calendario_esami
    ADD CONSTRAINT calendario_esami_pkey PRIMARY KEY (id);


--
-- TOC entry 3305 (class 2606 OID 16830)
-- Name: carriera carriera_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.carriera
    ADD CONSTRAINT carriera_pkey PRIMARY KEY (studente, data, insegnamento);


--
-- TOC entry 3307 (class 2606 OID 16875)
-- Name: carriera_storico carriera_storico_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.carriera_storico
    ADD CONSTRAINT carriera_storico_pkey PRIMARY KEY (studente, insegnamento, data);


--
-- TOC entry 3309 (class 2606 OID 16669)
-- Name: corso_di_laurea corso_di_laurea_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.corso_di_laurea
    ADD CONSTRAINT corso_di_laurea_pkey PRIMARY KEY (codice);


--
-- TOC entry 3311 (class 2606 OID 16671)
-- Name: credenziali credenziali_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.credenziali
    ADD CONSTRAINT credenziali_pkey PRIMARY KEY (username);


--
-- TOC entry 3314 (class 2606 OID 16673)
-- Name: docente docente_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente
    ADD CONSTRAINT docente_pkey PRIMARY KEY (utente);


--
-- TOC entry 3316 (class 2606 OID 16675)
-- Name: docente_responsabile docente_responsabile_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente_responsabile
    ADD CONSTRAINT docente_responsabile_pkey PRIMARY KEY (docente, insegnamento);


--
-- TOC entry 3338 (class 2606 OID 16796)
-- Name: foto_profilo foto_profilo_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.foto_profilo
    ADD CONSTRAINT foto_profilo_pkey PRIMARY KEY (utente, "timestamp");


--
-- TOC entry 3340 (class 2606 OID 16835)
-- Name: insegnamenti_per_carriera insegnamenti_per_carriera_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamenti_per_carriera
    ADD CONSTRAINT insegnamenti_per_carriera_pkey PRIMARY KEY (studente, insegnamento);


--
-- TOC entry 3320 (class 2606 OID 16677)
-- Name: insegnamento_parte_di_cdl insegnamento_parte_di_cdl_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamento_parte_di_cdl
    ADD CONSTRAINT insegnamento_parte_di_cdl_pkey PRIMARY KEY (insegnamento, corso_di_laurea, anno);


--
-- TOC entry 3318 (class 2606 OID 16679)
-- Name: insegnamento insegnamento_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamento
    ADD CONSTRAINT insegnamento_pkey PRIMARY KEY (codice);


--
-- TOC entry 3326 (class 2606 OID 16681)
-- Name: iscrizione iscrizione_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.iscrizione
    ADD CONSTRAINT iscrizione_pkey PRIMARY KEY (studente, esame);


--
-- TOC entry 3324 (class 2606 OID 16683)
-- Name: insegna isnegna_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegna
    ADD CONSTRAINT isnegna_pkey PRIMARY KEY (docente, insegnamento);


--
-- TOC entry 3328 (class 2606 OID 16685)
-- Name: propedeuticita propedeuticita_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.propedeuticita
    ADD CONSTRAINT propedeuticita_pkey PRIMARY KEY (insegnamento1, insegnamento2, corso_di_laurea);


--
-- TOC entry 3330 (class 2606 OID 16687)
-- Name: segreteria segreteria_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.segreteria
    ADD CONSTRAINT segreteria_pkey PRIMARY KEY (utente);


--
-- TOC entry 3332 (class 2606 OID 16689)
-- Name: studente studente_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente
    ADD CONSTRAINT studente_pkey PRIMARY KEY (utente);


--
-- TOC entry 3334 (class 2606 OID 16868)
-- Name: studente_storico studente_storico_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente_storico
    ADD CONSTRAINT studente_storico_pkey PRIMARY KEY (utente);


--
-- TOC entry 3322 (class 2606 OID 16691)
-- Name: utente utente_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.utente
    ADD CONSTRAINT utente_pkey PRIMARY KEY (email);


--
-- TOC entry 3336 (class 2606 OID 16866)
-- Name: utente_storico utente_storico_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.utente_storico
    ADD CONSTRAINT utente_storico_pkey PRIMARY KEY (email);


--
-- TOC entry 3312 (class 1259 OID 16692)
-- Name: fki_username; Type: INDEX; Schema: public; Owner: fontanaf
--

CREATE INDEX fki_username ON public.credenziali USING btree (username);


--
-- TOC entry 3369 (class 2620 OID 16844)
-- Name: studente creazione_insegnamenti_per_carriera; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER creazione_insegnamenti_per_carriera AFTER INSERT ON public.studente FOR EACH ROW EXECUTE FUNCTION public.inserisci_in_insegnamenti_per_carriera();


--
-- TOC entry 3366 (class 2620 OID 16810)
-- Name: carriera no_esami_senza_propedeuticita; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER no_esami_senza_propedeuticita BEFORE UPDATE ON public.carriera FOR EACH ROW EXECUTE FUNCTION public.check_voto_valido();


--
-- TOC entry 3367 (class 2620 OID 16694)
-- Name: iscrizione no_esami_senza_propedeuticita; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER no_esami_senza_propedeuticita BEFORE INSERT OR UPDATE ON public.iscrizione FOR EACH ROW EXECUTE FUNCTION public.check_propedeuticita();


--
-- TOC entry 3364 (class 2620 OID 16808)
-- Name: calendario_esami no_esami_stesso_anno_stesso_giorno; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER no_esami_stesso_anno_stesso_giorno BEFORE INSERT OR UPDATE ON public.calendario_esami FOR EACH ROW EXECUTE FUNCTION public.check_inserimento_esame();


--
-- TOC entry 3368 (class 2620 OID 16696)
-- Name: iscrizione no_iscriz_se_non_in_cdl; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER no_iscriz_se_non_in_cdl BEFORE INSERT OR UPDATE ON public.iscrizione FOR EACH ROW EXECUTE FUNCTION public.check_appartenenza_cdl();


--
-- TOC entry 3365 (class 2620 OID 16846)
-- Name: carriera registrazione_in_carriera; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER registrazione_in_carriera BEFORE INSERT OR UPDATE ON public.carriera FOR EACH ROW EXECUTE FUNCTION public.check_registrazione_carriera();


--
-- TOC entry 3355 (class 2606 OID 16697)
-- Name: propedeuticita propedeuticità_corso1; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.propedeuticita
    ADD CONSTRAINT "propedeuticità_corso1" FOREIGN KEY (insegnamento1) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3518 (class 0 OID 0)
-- Dependencies: 3355
-- Name: CONSTRAINT "propedeuticità_corso1" ON propedeuticita; Type: COMMENT; Schema: public; Owner: fontanaf
--

COMMENT ON CONSTRAINT "propedeuticità_corso1" ON public.propedeuticita IS 'il corso1 è propedeutico al corso2';


--
-- TOC entry 3356 (class 2606 OID 16702)
-- Name: propedeuticita propedeuticità_corso2; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.propedeuticita
    ADD CONSTRAINT "propedeuticità_corso2" FOREIGN KEY (insegnamento2) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3519 (class 0 OID 0)
-- Dependencies: 3356
-- Name: CONSTRAINT "propedeuticità_corso2" ON propedeuticita; Type: COMMENT; Schema: public; Owner: fontanaf
--

COMMENT ON CONSTRAINT "propedeuticità_corso2" ON public.propedeuticita IS 'il corso1 è propedeutico al corso2';


--
-- TOC entry 3353 (class 2606 OID 16707)
-- Name: iscrizione rif_calendario_esami; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.iscrizione
    ADD CONSTRAINT rif_calendario_esami FOREIGN KEY (esame) REFERENCES public.calendario_esami(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3349 (class 2606 OID 16712)
-- Name: insegnamento_parte_di_cdl rif_cdl; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamento_parte_di_cdl
    ADD CONSTRAINT rif_cdl FOREIGN KEY (corso_di_laurea) REFERENCES public.corso_di_laurea(codice) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3359 (class 2606 OID 16717)
-- Name: studente rif_corso_di_laurea; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente
    ADD CONSTRAINT rif_corso_di_laurea FOREIGN KEY (corso_di_laurea) REFERENCES public.corso_di_laurea(codice) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3357 (class 2606 OID 16722)
-- Name: propedeuticita rif_corso_di_laurea; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.propedeuticita
    ADD CONSTRAINT rif_corso_di_laurea FOREIGN KEY (corso_di_laurea) REFERENCES public.corso_di_laurea(codice) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3351 (class 2606 OID 16727)
-- Name: insegna rif_docente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegna
    ADD CONSTRAINT rif_docente FOREIGN KEY (docente) REFERENCES public.docente(utente) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3347 (class 2606 OID 16732)
-- Name: docente_responsabile rif_docente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente_responsabile
    ADD CONSTRAINT rif_docente FOREIGN KEY (docente) REFERENCES public.docente(utente) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3352 (class 2606 OID 16737)
-- Name: insegna rif_insegnamento; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegna
    ADD CONSTRAINT rif_insegnamento FOREIGN KEY (insegnamento) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3341 (class 2606 OID 16742)
-- Name: calendario_esami rif_insegnamento; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.calendario_esami
    ADD CONSTRAINT rif_insegnamento FOREIGN KEY (insegnamento) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3350 (class 2606 OID 16747)
-- Name: insegnamento_parte_di_cdl rif_insegnamento; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamento_parte_di_cdl
    ADD CONSTRAINT rif_insegnamento FOREIGN KEY (insegnamento) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3342 (class 2606 OID 16752)
-- Name: carriera rif_insegnamento; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.carriera
    ADD CONSTRAINT rif_insegnamento FOREIGN KEY (insegnamento) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3348 (class 2606 OID 16757)
-- Name: docente_responsabile rif_insegnamento; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente_responsabile
    ADD CONSTRAINT rif_insegnamento FOREIGN KEY (insegnamento) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3354 (class 2606 OID 16762)
-- Name: iscrizione rif_studente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.iscrizione
    ADD CONSTRAINT rif_studente FOREIGN KEY (studente) REFERENCES public.studente(utente) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3343 (class 2606 OID 16767)
-- Name: carriera rif_studente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.carriera
    ADD CONSTRAINT rif_studente FOREIGN KEY (studente) REFERENCES public.studente(utente) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3363 (class 2606 OID 16888)
-- Name: insegnamenti_per_carriera rif_studente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamenti_per_carriera
    ADD CONSTRAINT rif_studente FOREIGN KEY (studente) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3344 (class 2606 OID 16876)
-- Name: carriera_storico rif_studente_storico; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.carriera_storico
    ADD CONSTRAINT rif_studente_storico FOREIGN KEY (studente) REFERENCES public.studente_storico(utente) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3358 (class 2606 OID 16772)
-- Name: segreteria rif_utente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.segreteria
    ADD CONSTRAINT rif_utente FOREIGN KEY (utente) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3520 (class 0 OID 0)
-- Dependencies: 3358
-- Name: CONSTRAINT rif_utente ON segreteria; Type: COMMENT; Schema: public; Owner: fontanaf
--

COMMENT ON CONSTRAINT rif_utente ON public.segreteria IS 'riferimento alla tabella utente';


--
-- TOC entry 3346 (class 2606 OID 16777)
-- Name: docente rif_utente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente
    ADD CONSTRAINT rif_utente FOREIGN KEY (utente) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3360 (class 2606 OID 16782)
-- Name: studente rif_utente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente
    ADD CONSTRAINT rif_utente FOREIGN KEY (utente) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3362 (class 2606 OID 16797)
-- Name: foto_profilo rif_utente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.foto_profilo
    ADD CONSTRAINT rif_utente FOREIGN KEY (utente) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3361 (class 2606 OID 16869)
-- Name: studente_storico rif_utente_storico; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente_storico
    ADD CONSTRAINT rif_utente_storico FOREIGN KEY (utente) REFERENCES public.utente_storico(email) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3345 (class 2606 OID 16787)
-- Name: credenziali username; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.credenziali
    ADD CONSTRAINT username FOREIGN KEY (username) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


-- Completed on 2023-08-12 22:48:55 CEST

--
-- PostgreSQL database dump complete
--

