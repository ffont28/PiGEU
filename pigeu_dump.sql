--
-- PostgreSQL database dump
--

-- Dumped from database version 14.8 (Ubuntu 14.8-0ubuntu0.22.04.1)
-- Dumped by pg_dump version 14.8 (Ubuntu 14.8-0ubuntu0.22.04.1)

-- Started on 2023-07-31 23:48:03 CEST

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
-- TOC entry 222 (class 1255 OID 16536)
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
-- TOC entry 221 (class 1255 OID 16523)
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

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 211 (class 1259 OID 16415)
-- Name: corso_di_laurea; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.corso_di_laurea (
    codice character varying(5) NOT NULL,
    nome character varying(50),
    tipo character varying(50)
);


ALTER TABLE public.corso_di_laurea OWNER TO fontanaf;

--
-- TOC entry 210 (class 1259 OID 16402)
-- Name: credenziali; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.credenziali (
    username character varying(50) NOT NULL,
    password character varying(50)
);


ALTER TABLE public.credenziali OWNER TO fontanaf;

--
-- TOC entry 215 (class 1259 OID 16469)
-- Name: docente; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.docente (
    utente character varying(50) NOT NULL,
    tipo character varying(50)
);


ALTER TABLE public.docente OWNER TO fontanaf;

--
-- TOC entry 217 (class 1259 OID 16494)
-- Name: docente_responsabile; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.docente_responsabile (
    docente character varying(50) NOT NULL,
    insegnamento character varying(5) NOT NULL
);


ALTER TABLE public.docente_responsabile OWNER TO fontanaf;

--
-- TOC entry 216 (class 1259 OID 16479)
-- Name: insegna; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.insegna (
    docente character varying(50) NOT NULL,
    insegnamento character varying(5) NOT NULL
);


ALTER TABLE public.insegna OWNER TO fontanaf;

--
-- TOC entry 212 (class 1259 OID 16421)
-- Name: insegnamento; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.insegnamento (
    codice character varying(5) NOT NULL,
    nome character varying(50),
    anno integer,
    descrizione text,
    cfu character(2)
);


ALTER TABLE public.insegnamento OWNER TO fontanaf;

--
-- TOC entry 220 (class 1259 OID 16537)
-- Name: insegnamento_parte_di_cdl; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.insegnamento_parte_di_cdl (
    insegnamento character varying(5) NOT NULL,
    corso_di_laurea character varying(5) NOT NULL
);


ALTER TABLE public.insegnamento_parte_di_cdl OWNER TO fontanaf;

--
-- TOC entry 213 (class 1259 OID 16443)
-- Name: propedeuticita; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.propedeuticita (
    corso1 character varying(5) NOT NULL,
    corso2 character varying(5) NOT NULL
);


ALTER TABLE public.propedeuticita OWNER TO fontanaf;

--
-- TOC entry 214 (class 1259 OID 16458)
-- Name: segreteria; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.segreteria (
    utente character varying(50) NOT NULL,
    livello character varying(9) NOT NULL
);


ALTER TABLE public.segreteria OWNER TO fontanaf;

--
-- TOC entry 218 (class 1259 OID 16509)
-- Name: studente; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.studente (
    utente character varying(50) NOT NULL,
    matricola integer NOT NULL
);


ALTER TABLE public.studente OWNER TO fontanaf;

--
-- TOC entry 219 (class 1259 OID 16524)
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
-- TOC entry 3443 (class 0 OID 0)
-- Dependencies: 219
-- Name: studente_matricola_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: fontanaf
--

ALTER SEQUENCE public.studente_matricola_seq OWNED BY public.studente.matricola;


--
-- TOC entry 209 (class 1259 OID 16396)
-- Name: utente; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.utente (
    email character varying(50) NOT NULL,
    nome character varying(50),
    cognome character varying(50),
    address character varying(255),
    city character varying(255),
    codicefiscale character varying(16),
    emailpersonale character varying(60)
);


ALTER TABLE public.utente OWNER TO fontanaf;

--
-- TOC entry 3251 (class 2604 OID 16525)
-- Name: studente matricola; Type: DEFAULT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente ALTER COLUMN matricola SET DEFAULT nextval('public.studente_matricola_seq'::regclass);


--
-- TOC entry 3428 (class 0 OID 16415)
-- Dependencies: 211
-- Data for Name: corso_di_laurea; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.corso_di_laurea (codice, nome, tipo) FROM stdin;
F1X	informatica	triennale
F1XM	sicurezza dei sistemi informatici	magistrale
MED	Medicina	magistrale a ciclo unico
\.


--
-- TOC entry 3427 (class 0 OID 16402)
-- Dependencies: 210
-- Data for Name: credenziali; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.credenziali (username, password) FROM stdin;
Francesco.Fontana@unimi.it	frafontana28@gmail.com
font	47a282dfe68a42d302e22c4920ed9b5e
Titta.Rossi@unimi.it	asd@k.i
mario.rosssi@unimi.it	sd@l.l
Gianpaolo.Rossi@unimi.it	gianpa@rox.it
Elena.Pagani@unimi.it	e.pag@uni.it
Stefano.Montanelli@unimi.it	ste@mont.it
MassimoEmilio.Tarallo@unimi.it	max@tar.it
\.


--
-- TOC entry 3432 (class 0 OID 16469)
-- Dependencies: 215
-- Data for Name: docente; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.docente (utente, tipo) FROM stdin;
Gianpaolo.Rossi@unimi.it	a contratto
Elena.Pagani@unimi.it	a contratto
Stefano.Montanelli@unimi.it	associato
MassimoEmilio.Tarallo@unimi.it	associato
\.


--
-- TOC entry 3434 (class 0 OID 16494)
-- Dependencies: 217
-- Data for Name: docente_responsabile; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.docente_responsabile (docente, insegnamento) FROM stdin;
MassimoEmilio.Tarallo@unimi.it	MAT
Elena.Pagani@unimi.it	STAT
Stefano.Montanelli@unimi.it	ARC1
\.


--
-- TOC entry 3433 (class 0 OID 16479)
-- Dependencies: 216
-- Data for Name: insegna; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.insegna (docente, insegnamento) FROM stdin;
\.


--
-- TOC entry 3429 (class 0 OID 16421)
-- Dependencies: 212
-- Data for Name: insegnamento; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.insegnamento (codice, nome, anno, descrizione, cfu) FROM stdin;
MAT	matematica del continuo	1	tanta matematica, aiuto!	12
STAT	statistica	2	tanta varianza	6 
ARC1	architettura degli elaboratori	1	ALU	6 
\.


--
-- TOC entry 3437 (class 0 OID 16537)
-- Dependencies: 220
-- Data for Name: insegnamento_parte_di_cdl; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.insegnamento_parte_di_cdl (insegnamento, corso_di_laurea) FROM stdin;
MAT	F1X
STAT	F1X
ARC1	F1X
\.


--
-- TOC entry 3430 (class 0 OID 16443)
-- Dependencies: 213
-- Data for Name: propedeuticita; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.propedeuticita (corso1, corso2) FROM stdin;
MAT	STAT
\.


--
-- TOC entry 3431 (class 0 OID 16458)
-- Dependencies: 214
-- Data for Name: segreteria; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.segreteria (utente, livello) FROM stdin;
font	studenti
\.


--
-- TOC entry 3435 (class 0 OID 16509)
-- Dependencies: 218
-- Data for Name: studente; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.studente (utente, matricola) FROM stdin;
Francesco.Fontana@unimi.it	18
Titta.Rossi@unimi.it	19
mario.rosssi@unimi.it	36
\.


--
-- TOC entry 3426 (class 0 OID 16396)
-- Dependencies: 209
-- Data for Name: utente; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.utente (email, nome, cognome, address, city, codicefiscale, emailpersonale) FROM stdin;
Gianpaolo.Rossi@unimi.it	Gianpaolo	Rossi			BOH	gianpa@rox.it
Elena.Pagani@unimi.it	Elena	Pagani				e.pag@uni.it
Stefano.Montanelli@unimi.it	Stefano	Montanelli				ste@mont.it
MassimoEmilio.Tarallo@unimi.it	MassimoEmilio	Tarallo				max@tar.it
font	font	\N	\N	\N	f	frafont
Francesco.Fontana@unimi.it	Francesco	Fontana	via Regina 1614	Pianello del Lario	FNTFNC92E28E151Z	frafontana28@gmail.com
Titta.Rossi@unimi.it	Titta	Rossi	sda	asda	asdasas	asd@k.i
mario.rosssi@unimi.it	mario	rosssi				sd@l.l
\.


--
-- TOC entry 3444 (class 0 OID 0)
-- Dependencies: 219
-- Name: studente_matricola_seq; Type: SEQUENCE SET; Schema: public; Owner: fontanaf
--

SELECT pg_catalog.setval('public.studente_matricola_seq', 36, true);


--
-- TOC entry 3258 (class 2606 OID 16420)
-- Name: corso_di_laurea corso_di_laurea_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.corso_di_laurea
    ADD CONSTRAINT corso_di_laurea_pkey PRIMARY KEY (codice);


--
-- TOC entry 3255 (class 2606 OID 16406)
-- Name: credenziali credenziali_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.credenziali
    ADD CONSTRAINT credenziali_pkey PRIMARY KEY (username);


--
-- TOC entry 3266 (class 2606 OID 16473)
-- Name: docente docente_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente
    ADD CONSTRAINT docente_pkey PRIMARY KEY (utente);


--
-- TOC entry 3270 (class 2606 OID 16498)
-- Name: docente_responsabile docente_responsabile_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente_responsabile
    ADD CONSTRAINT docente_responsabile_pkey PRIMARY KEY (docente, insegnamento);


--
-- TOC entry 3274 (class 2606 OID 16541)
-- Name: insegnamento_parte_di_cdl insegnamento_parte_di_cdl_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamento_parte_di_cdl
    ADD CONSTRAINT insegnamento_parte_di_cdl_pkey PRIMARY KEY (insegnamento, corso_di_laurea);


--
-- TOC entry 3260 (class 2606 OID 16427)
-- Name: insegnamento insegnamento_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamento
    ADD CONSTRAINT insegnamento_pkey PRIMARY KEY (codice);


--
-- TOC entry 3268 (class 2606 OID 16483)
-- Name: insegna isnegna_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegna
    ADD CONSTRAINT isnegna_pkey PRIMARY KEY (docente, insegnamento);


--
-- TOC entry 3262 (class 2606 OID 16447)
-- Name: propedeuticita propedeuticita_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.propedeuticita
    ADD CONSTRAINT propedeuticita_pkey PRIMARY KEY (corso1, corso2);


--
-- TOC entry 3264 (class 2606 OID 16463)
-- Name: segreteria segreteria_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.segreteria
    ADD CONSTRAINT segreteria_pkey PRIMARY KEY (utente);


--
-- TOC entry 3272 (class 2606 OID 16513)
-- Name: studente studente_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente
    ADD CONSTRAINT studente_pkey PRIMARY KEY (utente);


--
-- TOC entry 3253 (class 2606 OID 16408)
-- Name: utente utente_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.utente
    ADD CONSTRAINT utente_pkey PRIMARY KEY (email);


--
-- TOC entry 3256 (class 1259 OID 16414)
-- Name: fki_username; Type: INDEX; Schema: public; Owner: fontanaf
--

CREATE INDEX fki_username ON public.credenziali USING btree (username);


--
-- TOC entry 3276 (class 2606 OID 16448)
-- Name: propedeuticita propedeuticità_corso1; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.propedeuticita
    ADD CONSTRAINT "propedeuticità_corso1" FOREIGN KEY (corso1) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3445 (class 0 OID 0)
-- Dependencies: 3276
-- Name: CONSTRAINT "propedeuticità_corso1" ON propedeuticita; Type: COMMENT; Schema: public; Owner: fontanaf
--

COMMENT ON CONSTRAINT "propedeuticità_corso1" ON public.propedeuticita IS 'il corso1 è propedeutico al corso2';


--
-- TOC entry 3277 (class 2606 OID 16453)
-- Name: propedeuticita propedeuticità_corso2; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.propedeuticita
    ADD CONSTRAINT "propedeuticità_corso2" FOREIGN KEY (corso2) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3446 (class 0 OID 0)
-- Dependencies: 3277
-- Name: CONSTRAINT "propedeuticità_corso2" ON propedeuticita; Type: COMMENT; Schema: public; Owner: fontanaf
--

COMMENT ON CONSTRAINT "propedeuticità_corso2" ON public.propedeuticita IS 'il corso1 è propedeutico al corso2';


--
-- TOC entry 3286 (class 2606 OID 16547)
-- Name: insegnamento_parte_di_cdl rif_cdl; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamento_parte_di_cdl
    ADD CONSTRAINT rif_cdl FOREIGN KEY (corso_di_laurea) REFERENCES public.corso_di_laurea(codice) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3280 (class 2606 OID 16484)
-- Name: insegna rif_docente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegna
    ADD CONSTRAINT rif_docente FOREIGN KEY (docente) REFERENCES public.docente(utente) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3282 (class 2606 OID 16499)
-- Name: docente_responsabile rif_docente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente_responsabile
    ADD CONSTRAINT rif_docente FOREIGN KEY (docente) REFERENCES public.docente(utente) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3281 (class 2606 OID 16489)
-- Name: insegna rif_insegnamento; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegna
    ADD CONSTRAINT rif_insegnamento FOREIGN KEY (insegnamento) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3283 (class 2606 OID 16504)
-- Name: docente_responsabile rif_insegnamento; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente_responsabile
    ADD CONSTRAINT rif_insegnamento FOREIGN KEY (insegnamento) REFERENCES public.insegnamento(codice);


--
-- TOC entry 3285 (class 2606 OID 16542)
-- Name: insegnamento_parte_di_cdl rif_insegnamento; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.insegnamento_parte_di_cdl
    ADD CONSTRAINT rif_insegnamento FOREIGN KEY (insegnamento) REFERENCES public.insegnamento(codice) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3278 (class 2606 OID 16464)
-- Name: segreteria rif_utente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.segreteria
    ADD CONSTRAINT rif_utente FOREIGN KEY (utente) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3447 (class 0 OID 0)
-- Dependencies: 3278
-- Name: CONSTRAINT rif_utente ON segreteria; Type: COMMENT; Schema: public; Owner: fontanaf
--

COMMENT ON CONSTRAINT rif_utente ON public.segreteria IS 'riferimento alla tabella utente';


--
-- TOC entry 3279 (class 2606 OID 16474)
-- Name: docente rif_utente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.docente
    ADD CONSTRAINT rif_utente FOREIGN KEY (utente) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3284 (class 2606 OID 16530)
-- Name: studente rif_utente; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.studente
    ADD CONSTRAINT rif_utente FOREIGN KEY (utente) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3275 (class 2606 OID 16409)
-- Name: credenziali username; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.credenziali
    ADD CONSTRAINT username FOREIGN KEY (username) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


-- Completed on 2023-07-31 23:48:03 CEST

--
-- PostgreSQL database dump complete
--

