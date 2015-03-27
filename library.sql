--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: authors; Type: TABLE; Schema: public; Owner: danis; Tablespace: 
--

CREATE TABLE authors (
    id integer NOT NULL,
    name character varying
);


ALTER TABLE authors OWNER TO danis;

--
-- Name: authors_id_seq; Type: SEQUENCE; Schema: public; Owner: danis
--

CREATE SEQUENCE authors_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE authors_id_seq OWNER TO danis;

--
-- Name: authors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: danis
--

ALTER SEQUENCE authors_id_seq OWNED BY authors.id;


--
-- Name: books; Type: TABLE; Schema: public; Owner: danis; Tablespace: 
--

CREATE TABLE books (
    id integer NOT NULL,
    title character varying
);


ALTER TABLE books OWNER TO danis;

--
-- Name: books_authors; Type: TABLE; Schema: public; Owner: danis; Tablespace: 
--

CREATE TABLE books_authors (
    id integer NOT NULL,
    book_id integer,
    author_id integer
);


ALTER TABLE books_authors OWNER TO danis;

--
-- Name: books_authors_id_seq; Type: SEQUENCE; Schema: public; Owner: danis
--

CREATE SEQUENCE books_authors_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE books_authors_id_seq OWNER TO danis;

--
-- Name: books_authors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: danis
--

ALTER SEQUENCE books_authors_id_seq OWNED BY books_authors.id;


--
-- Name: books_id_seq; Type: SEQUENCE; Schema: public; Owner: danis
--

CREATE SEQUENCE books_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE books_id_seq OWNER TO danis;

--
-- Name: books_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: danis
--

ALTER SEQUENCE books_id_seq OWNED BY books.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: danis
--

ALTER TABLE ONLY authors ALTER COLUMN id SET DEFAULT nextval('authors_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: danis
--

ALTER TABLE ONLY books ALTER COLUMN id SET DEFAULT nextval('books_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: danis
--

ALTER TABLE ONLY books_authors ALTER COLUMN id SET DEFAULT nextval('books_authors_id_seq'::regclass);


--
-- Data for Name: authors; Type: TABLE DATA; Schema: public; Owner: danis
--

COPY authors (id, name) FROM stdin;
\.


--
-- Name: authors_id_seq; Type: SEQUENCE SET; Schema: public; Owner: danis
--

SELECT pg_catalog.setval('authors_id_seq', 1, false);


--
-- Data for Name: books; Type: TABLE DATA; Schema: public; Owner: danis
--

COPY books (id, title) FROM stdin;
\.


--
-- Data for Name: books_authors; Type: TABLE DATA; Schema: public; Owner: danis
--

COPY books_authors (id, book_id, author_id) FROM stdin;
\.


--
-- Name: books_authors_id_seq; Type: SEQUENCE SET; Schema: public; Owner: danis
--

SELECT pg_catalog.setval('books_authors_id_seq', 1, false);


--
-- Name: books_id_seq; Type: SEQUENCE SET; Schema: public; Owner: danis
--

SELECT pg_catalog.setval('books_id_seq', 1, false);


--
-- Name: authors_pkey; Type: CONSTRAINT; Schema: public; Owner: danis; Tablespace: 
--

ALTER TABLE ONLY authors
    ADD CONSTRAINT authors_pkey PRIMARY KEY (id);


--
-- Name: books_authors_pkey; Type: CONSTRAINT; Schema: public; Owner: danis; Tablespace: 
--

ALTER TABLE ONLY books_authors
    ADD CONSTRAINT books_authors_pkey PRIMARY KEY (id);


--
-- Name: books_pkey; Type: CONSTRAINT; Schema: public; Owner: danis; Tablespace: 
--

ALTER TABLE ONLY books
    ADD CONSTRAINT books_pkey PRIMARY KEY (id);


--
-- Name: public; Type: ACL; Schema: -; Owner: danis
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM danis;
GRANT ALL ON SCHEMA public TO danis;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

