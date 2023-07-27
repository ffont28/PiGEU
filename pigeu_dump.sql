--
-- PostgreSQL database dump
--

-- Dumped from database version 14.8 (Ubuntu 14.8-0ubuntu0.22.04.1)
-- Dumped by pg_dump version 14.8 (Ubuntu 14.8-0ubuntu0.22.04.1)

-- Started on 2023-07-27 22:42:49 CEST

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

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 210 (class 1259 OID 16402)
-- Name: credenziali; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.credenziali (
    password character(32) NOT NULL,
    username character varying(50) NOT NULL
);


ALTER TABLE public.credenziali OWNER TO fontanaf;

--
-- TOC entry 209 (class 1259 OID 16396)
-- Name: utente; Type: TABLE; Schema: public; Owner: fontanaf
--

CREATE TABLE public.utente (
    email character varying(50) NOT NULL,
    nome character varying(50),
    cognome character varying(50),
    tipoutenza character varying(10) NOT NULL,
    address character varying(255),
    city character varying(255)
);


ALTER TABLE public.utente OWNER TO fontanaf;

--
-- TOC entry 3359 (class 0 OID 16402)
-- Dependencies: 210
-- Data for Name: credenziali; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.credenziali (password, username) FROM stdin;
\.


--
-- TOC entry 3358 (class 0 OID 16396)
-- Dependencies: 209
-- Data for Name: utente; Type: TABLE DATA; Schema: public; Owner: fontanaf
--

COPY public.utente (email, nome, cognome, tipoutenza, address, city) FROM stdin;
\.


--
-- TOC entry 3216 (class 2606 OID 16406)
-- Name: credenziali credenziali_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.credenziali
    ADD CONSTRAINT credenziali_pkey PRIMARY KEY (username);


--
-- TOC entry 3212 (class 2606 OID 16401)
-- Name: utente tipoutenza; Type: CHECK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE public.utente
    ADD CONSTRAINT tipoutenza CHECK (((tipoutenza)::text = ANY ((ARRAY['segreteria'::character varying, 'docente'::character varying, 'studente'::character varying])::text[]))) NOT VALID;


--
-- TOC entry 3214 (class 2606 OID 16408)
-- Name: utente utente_pkey; Type: CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.utente
    ADD CONSTRAINT utente_pkey PRIMARY KEY (email);


--
-- TOC entry 3217 (class 1259 OID 16414)
-- Name: fki_username; Type: INDEX; Schema: public; Owner: fontanaf
--

CREATE INDEX fki_username ON public.credenziali USING btree (username);


--
-- TOC entry 3218 (class 2606 OID 16409)
-- Name: credenziali username; Type: FK CONSTRAINT; Schema: public; Owner: fontanaf
--

ALTER TABLE ONLY public.credenziali
    ADD CONSTRAINT username FOREIGN KEY (username) REFERENCES public.utente(email) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


-- Completed on 2023-07-27 22:42:49 CEST

--
-- PostgreSQL database dump complete
--

