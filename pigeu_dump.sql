--
-- PostgreSQL database dump
--

-- Dumped from database version 14.8 (Ubuntu 14.8-0ubuntu0.22.04.1)
-- Dumped by pg_dump version 14.8 (Ubuntu 14.8-0ubuntu0.22.04.1)

-- Started on 2023-08-06 22:51:37 CEST

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
-- TOC entry 227 (class 1255 OID 16387)
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
-- TOC entry 232 (class 1255 OID 16614)
-- Name: check_inserimento_esame(); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.check_inserimento_esame() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    PERFORM * FROM calendario_esami c
        INNER JOIN insegnamento i ON c.insegnamento = i.codice
        WHERE c.data = NEW.data AND i.anno = (SELECT anno FROM insegnamento WHERE codice = NEW.insegnamento);
    IF FOUND THEN
        RAISE NOTICE 'ATTENZIONE: è già presente un altro esame dello stesso anno per la data selezionata';
        PERFORM pg_notify('notifica', 'Impossibile inserire esame di due insegnamenti dello stesso anno nello stesso giorno');
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END;
$$;


ALTER FUNCTION public.check_inserimento_esame() OWNER TO fontanaf;

--
-- TOC entry 244 (class 1255 OID 16672)
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
-- TOC entry 243 (class 1255 OID 16670)
-- Name: inserisci_in_carriera(); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.inserisci_in_carriera() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- creo la carriera (con tutti i voti del CdL pari a 0) lo studente appena inserito
    INSERT INTO carriera (studente, insegnamento, valutazione, data)
    SELECT NEW.utente, i.insegnamento, 0, NULL
    FROM insegnamento_parte_di_cdl i
    WHERE i.corso_di_laurea = NEW.corso_di_laurea;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.inserisci_in_carriera() OWNER TO fontanaf;

--
-- TOC entry 242 (class 1255 OID 16669)
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
-- TOC entry 228 (class 1255 OID 16388)
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
-- TOC entry 241 (class 1255 OID 16528)
-- Name: sposta_dati_studente(character varying); Type: FUNCTION; Schema: public; Owner: fontanaf
--

CREATE FUNCTION public.sposta_dati_studente(e character varying) RETURNS text
    LANGUAGE plpgsql
    AS $_$
DECLARE
  status TEXT := 'Dati spostati con successo.';
BEGIN
  -- INSERT per spostare i dati dalla tabella di origine alla tabella di destinazione
  INSERT INTO utente_storico (email, nome, cognome, indirizzo, citta, codicefiscale, emailpersonale)
  SELECT email, nome, cognome, indirizzo, citta, codicefiscale, emailpersonale
  FROM utente where email = $1;

  -- INSERT studente --> studente_storico
  INSERT INTO studente_storico (utente, matricola, corso_di_laurea)
  SELECT utente, matricola, corso_di_laurea
  FROM studente WHERE utente = $1;

  -- cancello i dati dalla tabella utente dopo averli spostati in utente_storico
  DELETE FROM studente WHERE utente = $1;

  -- cancello i dati dalla tabella utente dopo averli spostati in utente_storico
  DELETE FROM utente where email = $1;

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

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 223 (class 1259 OID 16529)
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
-- TOC entry 224 (class 1259 OID 16616)
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
-- TOC entry 3491 (class 0 OID 0)
-- Dependencies: 224
-- Name: calendario_esami_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: fontanaf
--

ALTER SEQUENCE public.calendario_esami_id_seq OWNED BY public.calendario_esami.id;


--
-- TOC entry 226 (class 1259 OID 16654)
-- Name: carriera; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.carriera (
    studente character varying(50) NOT NULL,
    insegnamento character varying(10) NOT NULL,
    valutazione smallint,
    data date
);


ALTER TABLE public.carriera OWNER TO fontanaf;

--
-- TOC entry 209 (class 1259 OID 16389)
-- Name: corso_di_laurea; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.corso_di_laurea (
    codice character varying(5) NOT NULL,
    nome character varying(50),
    tipo character varying(50)
);


ALTER TABLE public.corso_di_laurea OWNER TO fontanaf;

--
-- TOC entry 210 (class 1259 OID 16392)
-- Name: credenziali; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.credenziali (
    username character varying(50) NOT NULL,
    password character varying(50)
);


ALTER TABLE public.credenziali OWNER TO fontanaf;

--
-- TOC entry 211 (class 1259 OID 16395)
-- Name: docente; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.docente (
    utente character varying(50) NOT NULL,
    tipo character varying(50)
);


ALTER TABLE public.docente OWNER TO fontanaf;

--
-- TOC entry 212 (class 1259 OID 16398)
-- Name: docente_responsabile; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.docente_responsabile (
    docente character varying(50) NOT NULL,
    insegnamento character varying(10) NOT NULL
);


ALTER TABLE public.docente_responsabile OWNER TO fontanaf;

--
-- TOC entry 213 (class 1259 OID 16401)
-- Name: insegna; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.insegna (
    docente character varying(50) NOT NULL,
    insegnamento character varying(10) NOT NULL
);


ALTER TABLE public.insegna OWNER TO fontanaf;

--
-- TOC entry 214 (class 1259 OID 16404)
-- Name: insegnamento; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.insegnamento (
    codice character varying(10) NOT NULL,
    nome character varying(50),
    anno integer,
    descrizione text,
    cfu character(2)
);


ALTER TABLE public.insegnamento OWNER TO fontanaf;

--
-- TOC entry 215 (class 1259 OID 16409)
-- Name: insegnamento_parte_di_cdl; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.insegnamento_parte_di_cdl (
    insegnamento character varying(10) NOT NULL,
    corso_di_laurea character varying(5) NOT NULL
);


ALTER TABLE public.insegnamento_parte_di_cdl OWNER TO fontanaf;

--
-- TOC entry 225 (class 1259 OID 16639)
-- Name: iscrizione; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.iscrizione (
    studente character varying(50) NOT NULL,
    esame integer NOT NULL
);


ALTER TABLE public.iscrizione OWNER TO fontanaf;

--
-- TOC entry 216 (class 1259 OID 16412)
-- Name: propedeuticita; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.propedeuticita (
    insegnamento1 character varying(10) NOT NULL,
    insegnamento2 character varying(10) NOT NULL
);


ALTER TABLE public.propedeuticita OWNER TO fontanaf;

--
-- TOC entry 217 (class 1259 OID 16415)
-- Name: segreteria; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.segreteria (
    utente character varying(50) NOT NULL,
    livello character varying(9) NOT NULL
);


ALTER TABLE public.segreteria OWNER TO fontanaf;

--
-- TOC entry 218 (class 1259 OID 16418)
-- Name: studente; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.studente (
    utente character varying(50) NOT NULL,
    matricola integer NOT NULL,
    corso_di_laurea character varying(5)
);


ALTER TABLE public.studente OWNER TO fontanaf;

--
-- TOC entry 219 (class 1259 OID 16421)
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
-- TOC entry 3492 (class 0 OID 0)
-- Dependencies: 219
-- Name: studente_matricola_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: fontanaf
--

ALTER SEQUENCE public.studente_matricola_seq OWNED BY public.studente.matricola;


--
-- TOC entry 222 (class 1259 OID 16525)
-- Name: studente_storico; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.studente_storico (
    utente character varying(50),
    matricola integer,
    corso_di_laurea character varying(5)
);


ALTER TABLE public.studente_storico OWNER TO fontanaf;

--
-- TOC entry 220 (class 1259 OID 16425)
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
-- TOC entry 221 (class 1259 OID 16430)
-- Name: utente_storico; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.utente_storico (
    email character varying(50),
    nome character varying(50),
    cognome character varying(50),
    indirizzo character varying(255),
    citta character varying(255),
    codicefiscale character varying(16),
    emailpersonale character varying(60)
);


ALTER TABLE public.utente_storico OWNER TO fontanaf;

--
-- TOC entry 3278 (class 2604 OID 16617)
-- Name: calendario_esami id; Type: DEFAULT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.calendario_esami ALTER COLUMN id SET DEFAULT nextval('public.calendario_esami_id_seq'::regclass);


--
-- TOC entry 3277 (class 2604 OID 16435)
-- Name: studente matricola; Type: DEFAULT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente ALTER COLUMN matricola SET DEFAULT nextval('public.studente_matricola_seq'::regclass);


--
-- TOC entry 3482 (class 0 OID 16529)
-- Dependencies: 223
-- Data for Name: calendario_esami; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.calendario_esami (insegnamento, data, ora, id) FROM stdin;
59.23.1	2023-08-05	19:01:00	1
59.23.1	2023-08-19	19:00:00	2
97.23.1	2023-08-17	02:19:00	13
55.23.1	2023-08-21	11:11:00	16
55.23.1	2023-08-15	12:12:00	20
77.23.1	2023-08-08	23:23:00	25
55.23.1	2023-08-29	23:56:00	27
\.


--
-- TOC entry 3485 (class 0 OID 16654)
-- Dependencies: 226
-- Data for Name: carriera; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.carriera (studente, insegnamento, valutazione, data) FROM stdin;
studente.studente@studenti.unimi.it	59.23.1	0	1999-02-11
studente.studente@studenti.unimi.it	55.23.1	0	1999-02-11
studente.studente@studenti.unimi.it	97.23.1	0	1999-02-11
studente.studente@studenti.unimi.it	51.23.1	0	1999-02-11
studente.studente@studenti.unimi.it	77.23.1	0	1999-02-11
gabriele.dino@studenti.unimi.it	97.23.1	11	1999-02-11
gabriele.dino@studenti.unimi.it	51.23.1	11	1999-02-11
gabriele.dino@studenti.unimi.it	77.23.1	45	1999-02-11
gabriele.dino@studenti.unimi.it	55.23.1	23	2023-08-29
gabriele.dino@studenti.unimi.it	59.23.1	23	2023-08-05
\.


--
-- TOC entry 3468 (class 0 OID 16389)
-- Dependencies: 209
-- Data for Name: corso_di_laurea; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.corso_di_laurea (codice, nome, tipo) FROM stdin;
F1X	informatica	triennale
F1XM	sicurezza dei sistemi informatici	magistrale
MED	Medicina	magistrale a ciclo unico
G1	gabriele	triennale
\.


--
-- TOC entry 3469 (class 0 OID 16392)
-- Dependencies: 210
-- Data for Name: credenziali; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.credenziali (username, password) FROM stdin;
Francesco.Fontana@unimi.it	frafontana28@gmail.com
font	47a282dfe68a42d302e22c4920ed9b5e
Gianpaolo.Rossi@unimi.it	gianpa@rox.it
Stefano.Montanelli@unimi.it	ste@mont.it
MassimoEmilio.Tarallo@unimi.it	max@tar.it
Federico.Fontana	boh@gmail.com
s.s	df487637cef4dc2ce4ce89a036087569
docente@unimi.it	8277e0910d750195b448797616e091ad
Elena.Pagani@unimi.it	991d3a01fa08f5a242bc3da0d76aea29
cecilia.cavaterra@unimi.it	0a2ab694dd7f539402e0f6ab02b21280
nunzioalberto.borghese@unimi.it	b749d6ce0972feda1c5010ad872c48c1
dario.malchiodi@unimi.it	0ef127d4a7807983288467031bd4b7d6
stefano.montanelli@unimi.it	42d7d2dedd600c6f31668179963dd167
francesco.fontana@studenti.unimi.it	3f22faa13ee8ee53da95f974903c9428
gabriele.dino@studenti.unimi.it	8101407685d8fb5d9918c11a7151193d
studente.studente@studenti.unimi.it	653aaa4b849c5cd7a771abd04ac0c76c
\.


--
-- TOC entry 3470 (class 0 OID 16395)
-- Dependencies: 211
-- Data for Name: docente; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.docente (utente, tipo) FROM stdin;
cecilia.cavaterra@unimi.it	associato
nunzioalberto.borghese@unimi.it	ordinario
dario.malchiodi@unimi.it	associato
stefano.montanelli@unimi.it	associato
\.


--
-- TOC entry 3471 (class 0 OID 16398)
-- Dependencies: 212
-- Data for Name: docente_responsabile; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.docente_responsabile (docente, insegnamento) FROM stdin;
nunzioalberto.borghese@unimi.it	55.23.1
dario.malchiodi@unimi.it	97.23.1
stefano.montanelli@unimi.it	51.23.1
cecilia.cavaterra@unimi.it	59.23.1
nunzioalberto.borghese@unimi.it	77.23.1
\.


--
-- TOC entry 3472 (class 0 OID 16401)
-- Dependencies: 213
-- Data for Name: insegna; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.insegna (docente, insegnamento) FROM stdin;
\.


--
-- TOC entry 3473 (class 0 OID 16404)
-- Dependencies: 214
-- Data for Name: insegnamento; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.insegnamento (codice, nome, anno, descrizione, cfu) FROM stdin;
59.23.1	Matematica del Continuo	1	L'obiettivo dell'insegnamento è duplice. Anzitutto, fornire agli studenti un linguaggio matematico di base, che li metta grado di formulare correttamente un problema e di comprendere un problema formulato da altri. Inoltre, fornire gli strumenti matematici indispensabili per la soluzione di alcuni problemi specifici, che spaziano dal comportamento delle successioni a quello delle serie e delle funzioni di una variabile.	12
55.23.1	Architettura degli elaboratori i	1	L'insegnamento introduce le conoscenze dei principi che sottendono al funzionamento di un elaboratore digitale; partendo dal livello delle porte logiche si arriva, attraverso alcuni livelli di astrazione intermedi, alla progettazione di ALU firmware e di un'architettura MIPS in grado di eseguire il nucleo delle istruzioni in linguaggio macchina.	6 
97.23.1	Statistica e analisi dei dati	2	L'insegnamento ha lo scopo di introdurre i concetti fondamentali della statistica descrittiva, del calcolo delle probabilità e della statistica inferenziale parametrica.	6 
51.23.1	Basi di dati	2	L'insegnamento fornisce i concetti fondamentali relativi alle basi di dati e ai sistemi per la loro gestione, con particolare riguardo ai sistemi di basi di dati relazionali. Il corso prevede i) una parte di teoria dedicata a modelli, linguaggi, metodologie di progettazione e agli aspetti di sicurezza e transazioni, e ii) una parte di laboratorio dedicata all'uso di strumenti di progettazione e gestione di basi di dati relazionali e alle principali tecnologie di basi di dati e Web.	12
77.23.1	Architettura degli elaboratori ii	1	L'insegnamento fornisce la conoscenza del funzionamento delle architetture digitali approfondendo in particolare la pipe-line, i multi-core e le gerarchie di memoria in modo da potere capire a fondo le problematiche legate ai sistemi operativi e all'ottimizzazione del software. Vengono forniti gli strumenti per valutare le prestazioni dei calcolatori e per ottimizzare le applicazioni.	6 
\.


--
-- TOC entry 3474 (class 0 OID 16409)
-- Dependencies: 215
-- Data for Name: insegnamento_parte_di_cdl; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.insegnamento_parte_di_cdl (insegnamento, corso_di_laurea) FROM stdin;
59.23.1	F1X
55.23.1	F1X
97.23.1	F1X
51.23.1	F1X
77.23.1	F1X
\.


--
-- TOC entry 3484 (class 0 OID 16639)
-- Dependencies: 225
-- Data for Name: iscrizione; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.iscrizione (studente, esame) FROM stdin;
gabriele.dino@studenti.unimi.it	1
francesco.fontana@studenti.unimi.it	2
\.


--
-- TOC entry 3475 (class 0 OID 16412)
-- Dependencies: 216
-- Data for Name: propedeuticita; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.propedeuticita (insegnamento1, insegnamento2) FROM stdin;
59.23.1	97.23.1
\.


--
-- TOC entry 3476 (class 0 OID 16415)
-- Dependencies: 217
-- Data for Name: segreteria; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.segreteria (utente, livello) FROM stdin;
font	studenti
\.


--
-- TOC entry 3477 (class 0 OID 16418)
-- Dependencies: 218
-- Data for Name: studente; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.studente (utente, matricola, corso_di_laurea) FROM stdin;
Francesco.Fontana@unimi.it	18	\N
s.s	77	F1X
francesco.fontana@studenti.unimi.it	81	F1X
gabriele.dino@studenti.unimi.it	82	F1X
studente.studente@studenti.unimi.it	83	F1X
\.


--
-- TOC entry 3481 (class 0 OID 16525)
-- Dependencies: 222
-- Data for Name: studente_storico; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.studente_storico (utente, matricola, corso_di_laurea) FROM stdin;
e	55	F1X
o	56	F1X
u	57	F1X
\.


--
-- TOC entry 3479 (class 0 OID 16425)
-- Dependencies: 220
-- Data for Name: utente; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.utente (email, nome, cognome, indirizzo, citta, codicefiscale, emailpersonale) FROM stdin;
Elena.Pagani@unimi.it	Elena	Pagani				e.pag@uni.it
Stefano.Montanelli@unimi.it	Stefano	Montanelli				ste@mont.it
MassimoEmilio.Tarallo@unimi.it	MassimoEmilio	Tarallo				max@tar.it
Francesco.Fontana@unimi.it	Francesco	Fontana	via Regina 1614	Pianello del Lario	FNTFNC92E28E151Z	frafontana28@gmail.com
font	Francesco Stephan Maria	Fontana	via regina 1614		FNTFNC92E28E151Z	
Federico.Fontana	Federico	Fontana			FNTFED	boh@gmail.com
Gianpaolo.Rossi@unimi.it	Gianpaolo	Rossiiiiii				
docente@unimi.it	docente	docente			docente	docente@d.it
cecilia.cavaterra@unimi.it	Cecilia	Cavaterra				c@c.it
nunzioalberto.borghese@unimi.it	Nunzio Alberto	Borghese				n@b.it
dario.malchiodi@unimi.it	Dario	Malchiodi				d@m.it
stefano.montanelli@unimi.it	Stefano	Montanelli				s@m.it
s.s	Francesco	Studente				s@s.s
francesco.fontana@studenti.unimi.it	Francesco	Fontana				f@f.it
gabriele.dino@studenti.unimi.it	Gabriele	Dino				g@d.it
studente.studente@studenti.unimi.it	studente	studente				s@s.it
\.


--
-- TOC entry 3480 (class 0 OID 16430)
-- Dependencies: 221
-- Data for Name: utente_storico; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.utente_storico (email, nome, cognome, indirizzo, citta, codicefiscale, emailpersonale) FROM stdin;
Mia.Martini	Mia	Martini				52273fa9e1b8e53fa0c695b6586e1883
i	Informatico	Informatico	via degli Informatici	Informaticilandia	IN	i@s.it
a	a	a	a	a	a	a@a.it
e	e	e	e	e	e	e@e.i
o	o	o	o	o	ozio	o@o.o
u	u	uu	u	uu	u	u@u.it
\.


--
-- TOC entry 3493 (class 0 OID 0)
-- Dependencies: 224
-- Name: calendario_esami_id_seq; Type: SEQUENCE SET; Schema: public; Owner: fontanaf
--

SELECT pg_catalog.setval('public.calendario_esami_id_seq', 28, true);


--
-- TOC entry 3494 (class 0 OID 0)
-- Dependencies: 219
-- Name: studente_matricola_seq; Type: SEQUENCE SET; Schema: public; Owner: fontanaf
--

SELECT pg_catalog.setval('public.studente_matricola_seq', 83, true);


--
-- TOC entry 3303 (class 2606 OID 16623)
-- Name: calendario_esami calendario_esami_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.calendario_esami
    ADD CONSTRAINT calendario_esami_pkey PRIMARY KEY (id);


--
-- TOC entry 3307 (class 2606 OID 16658)
-- Name: carriera carriera_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.carriera
    ADD CONSTRAINT carriera_pkey PRIMARY KEY (studente, insegnamento);


--
-- TOC entry 3280 (class 2606 OID 16437)
-- Name: corso_di_laurea corso_di_laurea_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.corso_di_laurea
    ADD CONSTRAINT corso_di_laurea_pkey PRIMARY KEY (codice);


--
-- TOC entry 3282 (class 2606 OID 16439)
-- Name: credenziali credenziali_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.credenziali
    ADD CONSTRAINT credenziali_pkey PRIMARY KEY (username);


--
-- TOC entry 3285 (class 2606 OID 16441)
-- Name: docente docente_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente
    ADD CONSTRAINT docente_pkey PRIMARY KEY (utente);


--
-- TOC entry 3287 (class 2606 OID 16579)
-- Name: docente_responsabile docente_responsabile_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente_responsabile
    ADD CONSTRAINT docente_responsabile_pkey PRIMARY KEY (docente, insegnamento);


--
-- TOC entry 3293 (class 2606 OID 16593)
-- Name: insegnamento_parte_di_cdl insegnamento_parte_di_cdl_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamento_parte_di_cdl
    ADD CONSTRAINT insegnamento_parte_di_cdl_pkey PRIMARY KEY (insegnamento, corso_di_laurea);


--
-- TOC entry 3291 (class 2606 OID 16540)
-- Name: insegnamento insegnamento_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamento
    ADD CONSTRAINT insegnamento_pkey PRIMARY KEY (codice);


--
-- TOC entry 3305 (class 2606 OID 16643)
-- Name: iscrizione iscrizione_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.iscrizione
    ADD CONSTRAINT iscrizione_pkey PRIMARY KEY (studente, esame);


--
-- TOC entry 3289 (class 2606 OID 16572)
-- Name: insegna isnegna_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegna
    ADD CONSTRAINT isnegna_pkey PRIMARY KEY (docente, insegnamento);


--
-- TOC entry 3295 (class 2606 OID 16607)
-- Name: propedeuticita propedeuticita_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.propedeuticita
    ADD CONSTRAINT propedeuticita_pkey PRIMARY KEY (insegnamento1, insegnamento2);


--
-- TOC entry 3297 (class 2606 OID 16453)
-- Name: segreteria segreteria_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.segreteria
    ADD CONSTRAINT segreteria_pkey PRIMARY KEY (utente);


--
-- TOC entry 3299 (class 2606 OID 16455)
-- Name: studente studente_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente
    ADD CONSTRAINT studente_pkey PRIMARY KEY (utente);


--
-- TOC entry 3301 (class 2606 OID 16457)
-- Name: utente utente_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.utente
    ADD CONSTRAINT utente_pkey PRIMARY KEY (email);


--
-- TOC entry 3283 (class 1259 OID 16458)
-- Name: fki_username; Type: INDEX; Schema: public; Owner: fontanaf
--

CREATE INDEX fki_username ON public.credenziali USING btree (username);


--
-- TOC entry 3326 (class 2620 OID 16671)
-- Name: studente creazione_carriera; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER creazione_carriera AFTER INSERT ON public.studente FOR EACH ROW EXECUTE FUNCTION public.inserisci_in_carriera();


--
-- TOC entry 3328 (class 2620 OID 16677)
-- Name: iscrizione no_esami_senza_propedeuticita; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER no_esami_senza_propedeuticita BEFORE INSERT OR UPDATE ON public.iscrizione FOR EACH ROW EXECUTE FUNCTION public.check_propedeuticita();


--
-- TOC entry 3327 (class 2620 OID 16615)
-- Name: calendario_esami no_esami_stesso_anno_stesso_giorno; Type: TRIGGER; Schema: public; Owner: fontanaf
--

CREATE TRIGGER no_esami_stesso_anno_stesso_giorno BEFORE INSERT OR UPDATE ON public.calendario_esami FOR EACH ROW EXECUTE FUNCTION public.check_inserimento_esame();


--
-- TOC entry 3316 (class 2606 OID 16601)
-- Name: propedeuticita propedeuticità_corso1; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.propedeuticita
    ADD CONSTRAINT "propedeuticità_corso1" FOREIGN KEY (insegnamento1) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3495 (class 0 OID 0)
-- Dependencies: 3316
-- Name: CONSTRAINT "propedeuticità_corso1" ON propedeuticita; Type: COMMENT; Schema: public; Owner: fontanaf
--

COMMENT ON CONSTRAINT "propedeuticità_corso1" ON public.propedeuticita IS 'il corso1 è propedeutico al corso2';


--
-- TOC entry 3317 (class 2606 OID 16608)
-- Name: propedeuticita propedeuticità_corso2; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.propedeuticita
    ADD CONSTRAINT "propedeuticità_corso2" FOREIGN KEY (insegnamento2) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3496 (class 0 OID 0)
-- Dependencies: 3317
-- Name: CONSTRAINT "propedeuticità_corso2" ON propedeuticita; Type: COMMENT; Schema: public; Owner: fontanaf
--

COMMENT ON CONSTRAINT "propedeuticità_corso2" ON public.propedeuticita IS 'il corso1 è propedeutico al corso2';


--
-- TOC entry 3323 (class 2606 OID 16649)
-- Name: iscrizione rif_calendario_esami; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.iscrizione
    ADD CONSTRAINT rif_calendario_esami FOREIGN KEY (esame) REFERENCES public.calendario_esami(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3314 (class 2606 OID 16469)
-- Name: insegnamento_parte_di_cdl rif_cdl; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamento_parte_di_cdl
    ADD CONSTRAINT rif_cdl FOREIGN KEY (corso_di_laurea) REFERENCES public.corso_di_laurea(codice) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3319 (class 2606 OID 16474)
-- Name: studente rif_corso_di_laurea; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente
    ADD CONSTRAINT rif_corso_di_laurea FOREIGN KEY (corso_di_laurea) REFERENCES public.corso_di_laurea(codice) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3312 (class 2606 OID 16479)
-- Name: insegna rif_docente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegna
    ADD CONSTRAINT rif_docente FOREIGN KEY (docente) REFERENCES public.docente(utente) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3310 (class 2606 OID 16484)
-- Name: docente_responsabile rif_docente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente_responsabile
    ADD CONSTRAINT rif_docente FOREIGN KEY (docente) REFERENCES public.docente(utente) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3313 (class 2606 OID 16573)
-- Name: insegna rif_insegnamento; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegna
    ADD CONSTRAINT rif_insegnamento FOREIGN KEY (insegnamento) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3311 (class 2606 OID 16580)
-- Name: docente_responsabile rif_insegnamento; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente_responsabile
    ADD CONSTRAINT rif_insegnamento FOREIGN KEY (insegnamento) REFERENCES public.insegnamento(codice);


--
-- TOC entry 3321 (class 2606 OID 16587)
-- Name: calendario_esami rif_insegnamento; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.calendario_esami
    ADD CONSTRAINT rif_insegnamento FOREIGN KEY (insegnamento) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3315 (class 2606 OID 16594)
-- Name: insegnamento_parte_di_cdl rif_insegnamento; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamento_parte_di_cdl
    ADD CONSTRAINT rif_insegnamento FOREIGN KEY (insegnamento) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3325 (class 2606 OID 16664)
-- Name: carriera rif_insegnamento; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.carriera
    ADD CONSTRAINT rif_insegnamento FOREIGN KEY (insegnamento) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3322 (class 2606 OID 16644)
-- Name: iscrizione rif_studente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.iscrizione
    ADD CONSTRAINT rif_studente FOREIGN KEY (studente) REFERENCES public.studente(utente) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3324 (class 2606 OID 16659)
-- Name: carriera rif_studente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.carriera
    ADD CONSTRAINT rif_studente FOREIGN KEY (studente) REFERENCES public.studente(utente) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3318 (class 2606 OID 16504)
-- Name: segreteria rif_utente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.segreteria
    ADD CONSTRAINT rif_utente FOREIGN KEY (utente) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3497 (class 0 OID 0)
-- Dependencies: 3318
-- Name: CONSTRAINT rif_utente ON segreteria; Type: COMMENT; Schema: public; Owner: fontanaf
--

COMMENT ON CONSTRAINT rif_utente ON public.segreteria IS 'riferimento alla tabella utente';


--
-- TOC entry 3309 (class 2606 OID 16509)
-- Name: docente rif_utente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente
    ADD CONSTRAINT rif_utente FOREIGN KEY (utente) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3320 (class 2606 OID 16514)
-- Name: studente rif_utente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente
    ADD CONSTRAINT rif_utente FOREIGN KEY (utente) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3308 (class 2606 OID 16519)
-- Name: credenziali username; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.credenziali
    ADD CONSTRAINT username FOREIGN KEY (username) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


-- Completed on 2023-08-06 22:51:37 CEST

--
-- PostgreSQL database dump complete
--

