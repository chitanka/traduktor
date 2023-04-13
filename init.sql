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

--
-- Name: ac (Access Control); Type: DOMAIN; Schema: public
-- Values:
-- a - all
-- g - group
-- m - moderator
-- o - owner
--

CREATE DOMAIN ac AS character(1)
	CONSTRAINT ac_check CHECK (((VALUE)::text = ANY (ARRAY['a'::text, 'g'::text, 'm'::text, 'o'::text])));




--
-- Name: downloaded_book(integer, inet, inet); Type: FUNCTION; Schema: public
--

CREATE FUNCTION downloaded_book(_book_id integer, _ip inet, _via inet) RETURNS integer
    LANGUAGE plpgsql
    AS $$
			BEGIN
				IF _via IS NULL THEN
					IF EXISTS (SELECT 1 FROM download_log WHERE book_id = _book_id AND ip = _ip AND _via IS NULL LIMIT 1) THEN RETURN 0; END IF;
				ELSE
					IF EXISTS (SELECT 1 FROM download_log WHERE book_id = _book_id AND ip = _ip AND via = _via LIMIT 1) THEN RETURN 0; END IF;
				END IF;

				INSERT INTO download_log (book_id, ip, via) VALUES (_book_id, _ip, _via);
				UPDATE books SET n_dl = n_dl + 1, n_dl_today = n_dl_today + 1 WHERE id = _book_id;
				RETURN 1;
			END;
			$$;




--
-- Name: downloaded_book(integer, integer, inet, inet); Type: FUNCTION; Schema: public
--

CREATE FUNCTION downloaded_book(_book_id integer, _chap_id integer, _ip inet, _via inet) RETURNS integer
    LANGUAGE plpgsql
    AS $$
			BEGIN
				IF _via IS NULL THEN
					IF EXISTS (SELECT 1 FROM download_log WHERE chap_id = _chap_id AND ip = _ip AND _via IS NULL LIMIT 1) THEN RETURN 0; END IF;
				ELSE
					IF EXISTS (SELECT 1 FROM download_log WHERE chap_id = _chap_id AND ip = _ip AND via = _via LIMIT 1) THEN RETURN 0; END IF;
				END IF;

				INSERT INTO download_log (chap_id, ip, via) VALUES (_chap_id, _ip, _via);
				UPDATE books SET n_dl = n_dl + 1, n_dl_today = n_dl_today + 1 WHERE id = _book_id;
				UPDATE chapters SET n_dl = n_dl + 1, n_dl_today = n_dl_today + 1 WHERE id = _chap_id;
				RETURN 1;
			END;
			$$;




--
-- Name: group_join(integer, integer); Type: FUNCTION; Schema: public
--

CREATE FUNCTION group_join(_user_id integer, _book_id integer) RETURNS void
    LANGUAGE plpgsql
    AS $_$
			BEGIN
					IF NOT EXISTS(SELECT * FROM groups WHERE user_id = $1 AND book_id = $2) THEN
						INSERT INTO groups (user_id, book_id, status) VALUES($1, $2, 1);
					ELSE
						UPDATE groups SET status = 1 WHERE user_id = $1 AND book_id = $2;
					END IF;

					RETURN;
			END;
			$_$;




--
-- Name: moder_book_cat_put(integer); Type: FUNCTION; Schema: public
--

CREATE FUNCTION moder_book_cat_put(_book_id integer) RETURNS void
    LANGUAGE plpgsql
    AS $_$
			BEGIN
				IF NOT EXISTS(SELECT * FROM moder_book_cat WHERE book_id = $1) THEN
					INSERT INTO moder_book_cat (book_id) VALUES($1);
				ELSE
					UPDATE moder_book_cat SET cdate = now() WHERE book_id = $1;
				END IF;

				RETURN;
			END;
			$_$;




--
-- Name: rate_tr(integer, integer, integer); Type: FUNCTION; Schema: public
--

CREATE FUNCTION rate_tr(_user_id integer, _tr_id integer, _mark integer) RETURNS void
    LANGUAGE plpgsql
    AS $_$
			BEGIN
					IF NOT EXISTS(SELECT * FROM marks WHERE user_id = $1 AND tr_id = $2) THEN
						INSERT INTO marks (user_id, tr_id, mark) VALUES($1, $2, $3);
					ELSE
						UPDATE marks SET mark = $3 WHERE user_id = $1 AND tr_id = $2;
					END IF;

					RETURN;
			END;
			$_$;




--
-- Name: ready(integer, integer); Type: FUNCTION; Schema: public
--

CREATE FUNCTION ready(n_verses integer, d_vars integer) RETURNS double precision
    LANGUAGE plpgsql
    AS $$
			BEGIN
				IF n_verses = 0 THEN RETURN 0; ELSE RETURN d_vars::float / n_verses::float; END IF;
			END;
			$$;




--
-- Name: seen_orig(integer, integer, integer); Type: FUNCTION; Schema: public
--

CREATE FUNCTION seen_orig(_user_id integer, _orig_id integer, _n_comments integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
			BEGIN
					IF EXISTS(SELECT * FROM seen WHERE user_id = _user_id AND orig_id = _orig_id) THEN UPDATE seen SET seen=now(), n_comments = _n_comments WHERE user_id = _user_id AND orig_id = _orig_id;
					ELSE INSERT INTO seen (user_id, orig_id, seen, n_comments, track) VALUES(_user_id, _orig_id, now(), _n_comments, false);
					END IF;
					RETURN;
			END;
			$$;




--
-- Name: seen_post(integer, integer, integer); Type: FUNCTION; Schema: public
--

CREATE FUNCTION seen_post(_user_id integer, _post_id integer, _n_comments integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
			BEGIN
					IF EXISTS(SELECT * FROM seen WHERE user_id = _user_id AND post_id = _post_id) THEN UPDATE seen SET seen=now(), n_comments = _n_comments WHERE user_id = _user_id AND post_id = _post_id;
					ELSE INSERT INTO seen (user_id, post_id, seen, n_comments, track) VALUES(_user_id, _post_id, now(), _n_comments, false);
					END IF;
					RETURN;
			END;
			$$;




--
-- Name: track_orig(integer, integer, integer); Type: FUNCTION; Schema: public
--

CREATE FUNCTION track_orig(_user_id integer, _orig_id integer, _inc integer) RETURNS void
    LANGUAGE plpgsql
    AS $_$
			BEGIN
					IF EXISTS(SELECT * FROM seen WHERE user_id = $1 AND orig_id = $2) THEN UPDATE seen SET track = true, n_comments = n_comments + _inc WHERE user_id = _user_id AND orig_id = _orig_id;
					ELSE INSERT INTO seen (user_id, orig_id, seen, n_comments, track) VALUES(_user_id, _orig_id, NULL, _inc, true);
					END IF;
					RETURN;
			END;
			$_$;




--
-- Name: track_post(integer, integer, integer); Type: FUNCTION; Schema: public
--

CREATE FUNCTION track_post(_user_id integer, _post_id integer, _inc integer) RETURNS void
    LANGUAGE plpgsql
    AS $_$
			BEGIN
					IF EXISTS(SELECT * FROM seen WHERE user_id = $1 AND post_id = $2) THEN UPDATE seen SET track = true, n_comments = n_comments + _inc WHERE user_id = _user_id AND post_id = _post_id;
					ELSE INSERT INTO seen (user_id, post_id, seen, n_comments, track) VALUES(_user_id, _post_id, NULL, _inc, true);
					END IF;
					RETURN;
			END;
			$_$;




SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: ban; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE ban (
    user_id integer NOT NULL,
    until date DEFAULT '2031-08-08'::date NOT NULL
);




--
-- Name: blog_posts; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE blog_posts (
    id integer NOT NULL,
    user_id integer NOT NULL,
    book_id integer,
    cdate timestamp with time zone DEFAULT now() NOT NULL,
    n_comments integer DEFAULT 0 NOT NULL,
    lastcomment timestamp with time zone DEFAULT now(),
    topics smallint NOT NULL,
    title character varying(256),
    body text NOT NULL
);




--
-- Name: blog_posts_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE blog_posts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;




--
-- Name: blog_posts_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE blog_posts_id_seq OWNED BY blog_posts.id;


--
-- Name: book_ban_reasons; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE book_ban_reasons (
    book_id integer NOT NULL,
    cdate timestamp without time zone DEFAULT now() NOT NULL,
    title character varying(255),
    url character varying(255),
    email character varying(255),
    message text
);




--
-- Name: book_cat_export; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE book_cat_export (
    book_id integer,
    cat_id integer
);




--
-- Name: book_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE book_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;




--
-- Name: bookmarks; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE bookmarks (
    id integer NOT NULL,
    user_id integer NOT NULL,
    book_id integer NOT NULL,
    orig_id integer,
    ord smallint,
    note character varying(255),
    cdate timestamp with time zone DEFAULT now() NOT NULL,
    watch boolean DEFAULT false NOT NULL
);




--
-- Name: bookmarks_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE bookmarks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;




--
-- Name: bookmarks_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE bookmarks_id_seq OWNED BY bookmarks.id;


--
-- Name: books; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE books (
    id integer NOT NULL,
    cdate timestamp with time zone DEFAULT now() NOT NULL,
    owner_id integer NOT NULL,
    typ character(1) DEFAULT 'A'::bpchar NOT NULL,
    opts bit(8) DEFAULT (0)::bit(8) NOT NULL,
    cat_id integer,
    topics bit(32) DEFAULT (0)::bit(32) NOT NULL,
    s_lang smallint NOT NULL,
    t_lang smallint NOT NULL,
    s_title character varying(255) NOT NULL,
    t_title character varying(255) NOT NULL,
    descr text,
    img smallint[] NOT NULL,
    n_chapters integer DEFAULT 0 NOT NULL,
    n_verses integer DEFAULT 0 NOT NULL,
    n_vars integer DEFAULT 0 NOT NULL,
    d_vars integer DEFAULT 0 NOT NULL,
    n_invites smallint DEFAULT 30 NOT NULL,
    n_dl integer DEFAULT 0 NOT NULL,
    n_dl_today integer DEFAULT 0 NOT NULL,
    last_tr timestamp with time zone,
    facecontrol smallint DEFAULT 0 NOT NULL,
    ac_read ac DEFAULT 'a'::bpchar NOT NULL,
    ac_trread ac DEFAULT 'a'::bpchar NOT NULL,
    ac_gen ac DEFAULT 'a'::bpchar NOT NULL,
    ac_rate ac DEFAULT 'a'::bpchar NOT NULL,
    ac_comment ac DEFAULT 'a'::bpchar NOT NULL,
    ac_tr ac DEFAULT 'a'::bpchar NOT NULL,
    ac_blog_r ac DEFAULT 'a'::bpchar NOT NULL,
    ac_blog_c ac DEFAULT 'a'::bpchar NOT NULL,
    ac_blog_w ac DEFAULT 'a'::bpchar NOT NULL,
    ac_chap_edit ac DEFAULT 'o'::bpchar NOT NULL,
    ac_book_edit ac DEFAULT 'o'::bpchar NOT NULL,
    ac_membership ac DEFAULT 'm'::bpchar NOT NULL,
    ac_announce ac DEFAULT 'm'::bpchar NOT NULL,
    CONSTRAINT books_ac_announce_check CHECK (((ac_announce)::bpchar = ANY (ARRAY['g'::bpchar, 'm'::bpchar, 'o'::bpchar]))),
    CONSTRAINT books_ac_book_edit_check CHECK (((ac_book_edit)::bpchar = ANY (ARRAY['m'::bpchar, 'o'::bpchar]))),
    CONSTRAINT books_ac_chap_edit_check CHECK (((ac_chap_edit)::bpchar = ANY (ARRAY['m'::bpchar, 'o'::bpchar]))),
    CONSTRAINT books_ac_membership_check CHECK (((ac_membership)::bpchar = ANY (ARRAY['m'::bpchar, 'o'::bpchar])))
);




--
-- Name: books_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE books_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;




--
-- Name: books_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE books_id_seq OWNED BY books.id;


--
-- Name: catalog; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE catalog (
    id integer NOT NULL,
    pid integer,
    mp smallint[] NOT NULL,
    title text,
    available boolean DEFAULT true NOT NULL
);




--
-- Name: catalog_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE catalog_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;




--
-- Name: catalog_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE catalog_id_seq OWNED BY catalog.id;


--
-- Name: chapters; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE chapters (
    id integer NOT NULL,
    book_id integer NOT NULL,
    cdate timestamp with time zone DEFAULT now() NOT NULL,
    last_tr timestamp with time zone,
    n_verses integer DEFAULT 0 NOT NULL,
    n_vars integer DEFAULT 0 NOT NULL,
    d_vars integer DEFAULT 0 NOT NULL,
    n_dl integer DEFAULT 0 NOT NULL,
    n_dl_today integer DEFAULT 0 NOT NULL,
    ord integer NOT NULL,
    status smallint NOT NULL,
    title character varying(300) NOT NULL,
    ac_read ac,
    ac_trread ac,
    ac_gen ac,
    ac_rate ac,
    ac_comment ac,
    ac_tr ac
);




--
-- Name: chapters_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE chapters_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;




--
-- Name: chapters_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE chapters_id_seq OWNED BY chapters.id;


--
-- Name: comments; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE comments (
    id integer NOT NULL,
    post_id integer,
    orig_id integer,
    pid integer,
    mp smallint[] NOT NULL,
    cdate timestamp with time zone DEFAULT now() NOT NULL,
    ip inet,
    user_id integer,
    body text NOT NULL,
    rating smallint DEFAULT 0 NOT NULL,
    n_votes smallint DEFAULT 0 NOT NULL
);




--
-- Name: comments_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE comments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;




--
-- Name: comments_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE comments_id_seq OWNED BY comments.id;


--
-- Name: comments_rating; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE comments_rating (
    cdate timestamp with time zone DEFAULT now() NOT NULL,
    comment_id integer NOT NULL,
    user_id integer NOT NULL,
    mark smallint DEFAULT 0 NOT NULL
);




--
-- Name: dict; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE dict (
    id integer NOT NULL,
    book_id integer NOT NULL,
    cdate timestamp with time zone DEFAULT now() NOT NULL,
    user_id integer NOT NULL,
    term character varying(255) NOT NULL,
    descr character varying(255) NOT NULL
);




--
-- Name: dict_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE dict_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;




--
-- Name: dict_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE dict_id_seq OWNED BY dict.id;


--
-- Name: dima360; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE dima360 (
    id integer,
    login text
);




--
-- Name: download_log; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE download_log (
    chap_id integer NOT NULL,
    ip inet NOT NULL,
    via inet
);




--
-- Name: group_queue; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE group_queue (
    book_id integer NOT NULL,
    user_id integer NOT NULL,
    cdate timestamp with time zone DEFAULT now() NOT NULL,
    message text
);




--
-- Name: groups; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE groups (
    book_id integer NOT NULL,
    user_id integer NOT NULL,
    status smallint DEFAULT 0 NOT NULL,
    since timestamp with time zone DEFAULT now() NOT NULL,
    last_tr timestamp with time zone,
    n_trs integer DEFAULT 0 NOT NULL,
    rating integer DEFAULT 0 NOT NULL
);




--
-- Name: invites; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE invites (
    cdate timestamp with time zone DEFAULT now() NOT NULL,
    from_uid integer NOT NULL,
    to_uid integer NOT NULL,
    book_id integer NOT NULL
);




--
-- Name: karma_rates; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE karma_rates (
    dat timestamp with time zone DEFAULT now() NOT NULL,
    from_uid integer NOT NULL,
    to_uid integer NOT NULL,
    mark smallint NOT NULL,
    note character varying(255) DEFAULT ''::character varying NOT NULL,
    CONSTRAINT karma_rates_mark_check CHECK ((abs(mark) = 1))
);




--
-- Name: languages; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE languages (
    id smallint NOT NULL,
    typ smallint NOT NULL,
    title character varying(100),
    title_r character varying(100)
);




--
-- Name: languages_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE languages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;




--
-- Name: languages_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE languages_id_seq OWNED BY languages.id;


--
-- Name: mail_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE mail_id_seq
    START WITH 533179
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;




--
-- Name: mail; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE mail (
    id integer DEFAULT nextval('mail_id_seq'::regclass) NOT NULL,
    user_id integer NOT NULL,
    buddy_id integer,
    folder smallint NOT NULL,
    cdate timestamp with time zone DEFAULT now() NOT NULL,
    subj character varying(255) NOT NULL,
    body text NOT NULL,
    seen boolean
);




--
-- Name: marks; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE marks (
    user_id integer NOT NULL,
    tr_id integer NOT NULL,
    mark smallint NOT NULL,
    cdate timestamp with time zone DEFAULT now() NOT NULL
);




--
-- Name: moder_book_cat; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE moder_book_cat (
    book_id integer NOT NULL,
    cdate timestamp with time zone DEFAULT now() NOT NULL
);




--
-- Name: moving; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE moving (
    ip inet NOT NULL,
    cdate timestamp with time zone DEFAULT now() NOT NULL,
    x smallint NOT NULL,
    y smallint NOT NULL,
    color smallint[] NOT NULL,
    t character varying(120) NOT NULL
);




--
-- Name: notices; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE notices (
    id integer NOT NULL,
    user_id integer NOT NULL,
    cdate timestamp with time zone DEFAULT now() NOT NULL,
    seen boolean DEFAULT false NOT NULL,
    typ smallint,
    msg text DEFAULT ''::text NOT NULL
);




--
-- Name: notices_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE notices_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;




--
-- Name: notices_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE notices_id_seq OWNED BY notices.id;


--
-- Name: orig; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE orig (
    id integer NOT NULL,
    chap_id integer NOT NULL,
    ord integer,
    t1 time(3) without time zone,
    t2 time(3) without time zone,
    body text DEFAULT ''::text NOT NULL,
    n_comments smallint DEFAULT 0 NOT NULL,
    n_trs smallint DEFAULT 0 NOT NULL
);




--
-- Name: orig_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE orig_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;




--
-- Name: orig_id_seq1; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE orig_id_seq1
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;




--
-- Name: orig_id_seq1; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE orig_id_seq1 OWNED BY orig.id;


--
-- Name: orig_old_id; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE orig_old_id (
    id integer NOT NULL,
    chap_id integer NOT NULL,
    old_id integer NOT NULL
);




--
-- Name: poll_answers; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE poll_answers (
    poll_id smallint NOT NULL,
    q_id smallint NOT NULL,
    user_id integer,
    cdate timestamp without time zone DEFAULT now() NOT NULL,
    ip inet NOT NULL,
    answer text DEFAULT ''::text NOT NULL
);




--
-- Name: poll_tmp; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE poll_tmp (
    poll_id smallint NOT NULL,
    q_id smallint NOT NULL,
    user_id integer,
    cdate timestamp without time zone NOT NULL,
    ip inet NOT NULL,
    answer text NOT NULL
);




--
-- Name: recalc_log; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE recalc_log (
    book_id integer NOT NULL,
    user_id integer NOT NULL,
    dat timestamp with time zone DEFAULT now() NOT NULL
);




--
-- Name: reg_invites; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE reg_invites (
    id integer NOT NULL,
    from_id integer NOT NULL,
    to_id integer,
    to_email character varying(256),
    cdate timestamp without time zone DEFAULT now() NOT NULL,
    message text,
    code character varying(80)
);




--
-- Name: reg_invites_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE reg_invites_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;




--
-- Name: reg_invites_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE reg_invites_id_seq OWNED BY reg_invites.id;


--
-- Name: remind_tokens; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE remind_tokens (
    user_id integer NOT NULL,
    cdate timestamp without time zone DEFAULT now() NOT NULL,
    code character varying(80),
    attempts smallint DEFAULT 10 NOT NULL
);




--
-- Name: search_history; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE search_history (
    cdate timestamp with time zone DEFAULT now() NOT NULL,
    ip inet NOT NULL,
    request character varying(255) NOT NULL
);




--
-- Name: seen; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE seen (
    user_id integer NOT NULL,
    post_id integer,
    orig_id integer,
    seen timestamp with time zone DEFAULT now(),
    n_comments integer DEFAULT 0 NOT NULL,
    track boolean DEFAULT false NOT NULL
);




--
-- Name: tmp_dllog; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE tmp_dllog (
    book_id integer NOT NULL,
    chap_id integer NOT NULL,
    dat integer NOT NULL,
    ip integer NOT NULL
);




--
-- Name: translate; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE translate (
    id integer NOT NULL,
    book_id integer NOT NULL,
    chap_id integer NOT NULL,
    orig_id integer NOT NULL,
    user_id integer,
    cdate timestamp with time zone DEFAULT now() NOT NULL,
    rating smallint DEFAULT 0 NOT NULL,
    n_votes smallint DEFAULT 0 NOT NULL,
    body text NOT NULL
);




--
-- Name: translate_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE translate_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;




--
-- Name: translate_id_seq1; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE translate_id_seq1
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;




--
-- Name: translate_id_seq1; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE translate_id_seq1 OWNED BY translate.id;


--
-- Name: user_tr_stat; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE user_tr_stat (
    user_id integer NOT NULL,
    book_id integer NOT NULL,
    n_trs integer NOT NULL
);




--
-- Name: userinfo; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE userinfo (
    user_id integer NOT NULL,
    prop_id smallint NOT NULL,
    value text NOT NULL
);



--
-- Name: users; Type: TABLE; Schema: public; Tablespace: 
--

CREATE TABLE users (
    id integer NOT NULL,
    cdate timestamp with time zone DEFAULT now() NOT NULL,
    lastseen timestamp with time zone DEFAULT now() NOT NULL,
    can bit(16) DEFAULT B'0000000011110011'::"bit" NOT NULL,
    login character varying(16) NOT NULL,
    pass character varying(32) NOT NULL,
    email character varying(255) NOT NULL,
    sex character(1) DEFAULT 'x'::bpchar NOT NULL,
    lang smallint NOT NULL,
    upic smallint[],
    ini bit(16) DEFAULT B'0000011100011111'::"bit" NOT NULL,
    rate_t integer DEFAULT 0 NOT NULL,
    rate_c integer DEFAULT 0 NOT NULL,
    rate_u smallint DEFAULT 0 NOT NULL,
    n_trs integer DEFAULT 0 NOT NULL,
    n_comments integer DEFAULT 0 NOT NULL,
    n_karma integer DEFAULT 0 NOT NULL,
    invited_by integer,
    n_invites smallint DEFAULT 0 NOT NULL,
    state SMALLINT DEFAULT 0 NOT NULL,
    CONSTRAINT users_login_check CHECK (((login)::text <> ''::text)),
    CONSTRAINT users_pass_check CHECK (((pass)::text <> ''::text)),
    CONSTRAINT users_sex_check CHECK ((sex = ANY (ARRAY['x'::bpchar, 'm'::bpchar, 'f'::bpchar, '-'::bpchar])))
);




--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;




--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY blog_posts ALTER COLUMN id SET DEFAULT nextval('blog_posts_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY bookmarks ALTER COLUMN id SET DEFAULT nextval('bookmarks_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY books ALTER COLUMN id SET DEFAULT nextval('books_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY catalog ALTER COLUMN id SET DEFAULT nextval('catalog_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY chapters ALTER COLUMN id SET DEFAULT nextval('chapters_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY comments ALTER COLUMN id SET DEFAULT nextval('comments_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY dict ALTER COLUMN id SET DEFAULT nextval('dict_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY languages ALTER COLUMN id SET DEFAULT nextval('languages_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY notices ALTER COLUMN id SET DEFAULT nextval('notices_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY orig ALTER COLUMN id SET DEFAULT nextval('orig_id_seq1'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY reg_invites ALTER COLUMN id SET DEFAULT nextval('reg_invites_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY translate ALTER COLUMN id SET DEFAULT nextval('translate_id_seq1'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Data for Name: ban; Type: TABLE DATA; Schema: public
--

COPY ban (user_id, until) FROM stdin;
\.


--
-- Data for Name: blog_posts; Type: TABLE DATA; Schema: public
--

COPY blog_posts (id, user_id, book_id, cdate, n_comments, lastcomment, topics, title, body) FROM stdin;
\.


--
-- Name: blog_posts_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('blog_posts_id_seq', 1, false);


--
-- Data for Name: book_ban_reasons; Type: TABLE DATA; Schema: public
--

COPY book_ban_reasons (book_id, cdate, title, url, email, message) FROM stdin;
\.


--
-- Data for Name: book_cat_export; Type: TABLE DATA; Schema: public
--

COPY book_cat_export (book_id, cat_id) FROM stdin;
\.


--
-- Name: book_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('book_id_seq', 1, false);


--
-- Name: bookmarks_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('bookmarks_id_seq', 1, false);


--
-- Name: books_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('books_id_seq', 1, false);


--
-- Data for Name: catalog; Type: TABLE DATA; Schema: public
--

COPY catalog (id, pid, mp, title, available) FROM stdin WITH DELIMITER ',' NULL AS 'NULL';
1,NULL,{1},Текстове,true
2,NULL,{2},Субтитри,true
\.


--
-- Name: catalog_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('catalog_id_seq', 1, false);


--
-- Name: chapters_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('chapters_id_seq', 1, false);


--
-- Data for Name: comments; Type: TABLE DATA; Schema: public
--

COPY comments (id, post_id, orig_id, pid, mp, cdate, ip, user_id, body, rating, n_votes) FROM stdin;
\.


--
-- Name: comments_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('comments_id_seq', 1, false);


--
-- Data for Name: comments_rating; Type: TABLE DATA; Schema: public
--

COPY comments_rating (cdate, comment_id, user_id, mark) FROM stdin;
\.


--
-- Data for Name: dict; Type: TABLE DATA; Schema: public
--

COPY dict (id, book_id, cdate, user_id, term, descr) FROM stdin;
\.


--
-- Name: dict_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('dict_id_seq', 1, false);


--
-- Data for Name: dima360; Type: TABLE DATA; Schema: public
--

COPY dima360 (id, login) FROM stdin;
\.


--
-- Data for Name: download_log; Type: TABLE DATA; Schema: public
--

COPY download_log (chap_id, ip, via) FROM stdin;
\.


--
-- Data for Name: group_queue; Type: TABLE DATA; Schema: public
--

COPY group_queue (book_id, user_id, cdate, message) FROM stdin;
\.


--
-- Data for Name: invites; Type: TABLE DATA; Schema: public
--

COPY invites (cdate, from_uid, to_uid, book_id) FROM stdin;
\.


--
-- Data for Name: karma_rates; Type: TABLE DATA; Schema: public
--

COPY karma_rates (dat, from_uid, to_uid, mark, note) FROM stdin;
\.


--
-- Data for Name: languages; Type: TABLE DATA; Schema: public
--

COPY languages (id, typ, title) FROM stdin;
1	10	български
2	10	английски
3	10	немски
4	10	испански
5	10	френски
6	10	италиански
7	10	руски
8	30	гръцки
9	30	румънски
10	30	турски
11	30	полски
12	30	чешки
13	30	холандски
14	30	португалски
15	20	украински
16	20	белоруски
17	20	молдoвски
18	30	шведски
19	30	финландски
20	30	норвежки
21	30	датски
22	30	албански
23	20	арменски
24	20	грузински
25	30	шотландски
26	30	ирландски
27	30	исландски
28	40	корейски
29	20	литовски
30	20	естонски
31	30	сърбохърватски
32	30	словашки
33	30	словенски
34	40	арабски
35	40	иврит
36	40	китайски
37	40	японски
38	40	монголски
39	60	африкаанс
40	40	хинди
41	40	санскрит
42	40	виетнамски
43	40	индонезийски
44	40	филипински
45	40	пакистански
46	200	древногръцки
47	200	есперанто
\.


--
-- Name: languages_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('languages_id_seq', 48, true);


--
-- Name: mail_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('mail_id_seq', 1, false);


--
-- Data for Name: marks; Type: TABLE DATA; Schema: public
--

COPY marks (user_id, tr_id, mark, cdate) FROM stdin;
\.


--
-- Data for Name: moder_book_cat; Type: TABLE DATA; Schema: public
--

COPY moder_book_cat (book_id, cdate) FROM stdin;
\.


--
-- Data for Name: moving; Type: TABLE DATA; Schema: public
--

COPY moving (ip, cdate, x, y, color, t) FROM stdin;
\.


--
-- Data for Name: notices; Type: TABLE DATA; Schema: public
--

COPY notices (id, user_id, cdate, seen, typ, msg) FROM stdin;
\.


--
-- Name: notices_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('notices_id_seq', 1, false);


--
-- Name: orig_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('orig_id_seq', 1, false);


--
-- Name: orig_id_seq1; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('orig_id_seq1', 1, false);


--
-- Data for Name: orig_old_id; Type: TABLE DATA; Schema: public
--

COPY orig_old_id (id, chap_id, old_id) FROM stdin;
\.


--
-- Data for Name: poll_answers; Type: TABLE DATA; Schema: public
--

COPY poll_answers (poll_id, q_id, user_id, cdate, ip, answer) FROM stdin;
\.


--
-- Data for Name: poll_tmp; Type: TABLE DATA; Schema: public
--

COPY poll_tmp (poll_id, q_id, user_id, cdate, ip, answer) FROM stdin;
\.


--
-- Data for Name: recalc_log; Type: TABLE DATA; Schema: public
--

COPY recalc_log (book_id, user_id, dat) FROM stdin;
\.




--
-- Name: reg_invites_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('reg_invites_id_seq', 1, false);


--
-- Data for Name: seen; Type: TABLE DATA; Schema: public
--

COPY seen (user_id, post_id, orig_id, seen, n_comments, track) FROM stdin;
\.


--
-- Data for Name: tmp_dllog; Type: TABLE DATA; Schema: public
--

COPY tmp_dllog (book_id, chap_id, dat, ip) FROM stdin;
\.


--
-- Name: translate_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('translate_id_seq', 1, false);


--
-- Name: translate_id_seq1; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('translate_id_seq1', 1, true);


--
-- Data for Name: user_tr_stat; Type: TABLE DATA; Schema: public
--

COPY user_tr_stat (user_id, book_id, n_trs) FROM stdin;
\.


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('users_id_seq', 1, false);


--
-- Name: ban_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY ban
    ADD CONSTRAINT ban_pkey PRIMARY KEY (user_id);


--
-- Name: blog_posts_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY blog_posts
    ADD CONSTRAINT blog_posts_pkey PRIMARY KEY (id);


--
-- Name: book_ban_reasons_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY book_ban_reasons
    ADD CONSTRAINT book_ban_reasons_pkey PRIMARY KEY (book_id);


--
-- Name: bookmarks_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY bookmarks
    ADD CONSTRAINT bookmarks_pkey PRIMARY KEY (id);


--
-- Name: books_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY books
    ADD CONSTRAINT books_pkey PRIMARY KEY (id);


--
-- Name: catalog_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY catalog
    ADD CONSTRAINT catalog_pkey PRIMARY KEY (id);


--
-- Name: chapters_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY chapters
    ADD CONSTRAINT chapters_pkey PRIMARY KEY (id);


--
-- Name: comments_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY comments
    ADD CONSTRAINT comments_pkey PRIMARY KEY (id);


--
-- Name: dict_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY dict
    ADD CONSTRAINT dict_pkey PRIMARY KEY (id);


--
-- Name: languages_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_pkey PRIMARY KEY (id);


--
-- Name: mail_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY mail
    ADD CONSTRAINT mail_pkey PRIMARY KEY (id);


--
-- Name: moder_book_cat_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY moder_book_cat
    ADD CONSTRAINT moder_book_cat_pkey PRIMARY KEY (book_id);


--
-- Name: notices_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY notices
    ADD CONSTRAINT notices_pkey PRIMARY KEY (id);


--
-- Name: orig_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY orig
    ADD CONSTRAINT orig_pkey PRIMARY KEY (id);


--
-- Name: reg_invites_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY reg_invites
    ADD CONSTRAINT reg_invites_pkey PRIMARY KEY (id);


--
-- Name: remind_tokens_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY remind_tokens
    ADD CONSTRAINT remind_tokens_pkey PRIMARY KEY (user_id);


--
-- Name: translate_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY translate
    ADD CONSTRAINT translate_pkey PRIMARY KEY (id);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: blog_posts_book_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX blog_posts_book_id ON blog_posts USING btree (book_id, topics);


--
-- Name: bookmarks_book_id_idx; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX bookmarks_book_id_idx ON bookmarks USING btree (book_id);


--
-- Name: bookmarks_user_id_book_id_orig_id_idx; Type: INDEX; Schema: public; Tablespace: 
--

CREATE UNIQUE INDEX bookmarks_user_id_book_id_orig_id_idx ON bookmarks USING btree (user_id, book_id, orig_id);


--
-- Name: books_cat_id_idx; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX books_cat_id_idx ON books USING btree (cat_id);


--
-- Name: books_owner_id_idx; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX books_owner_id_idx ON books USING btree (owner_id);


--
-- Name: catalog_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX catalog_id ON catalog USING btree (pid);


--
-- Name: catalog_mp; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX catalog_mp ON catalog USING btree (mp);


--
-- Name: chapters_book_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX chapters_book_id ON chapters USING btree (book_id);


--
-- Name: comments_orig_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX comments_orig_id ON comments USING btree (orig_id);


--
-- Name: comments_post_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX comments_post_id ON comments USING btree (post_id);


--
-- Name: comments_rating_comment_id_user_id_idx; Type: INDEX; Schema: public; Tablespace: 
--

CREATE UNIQUE INDEX comments_rating_comment_id_user_id_idx ON comments_rating USING btree (comment_id, user_id);


--
-- Name: comments_user_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX comments_user_id ON comments USING btree (user_id);


--
-- Name: dict_book_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX dict_book_id ON dict USING btree (book_id);


--
-- Name: download_log_chap_id_ip_via_idx; Type: INDEX; Schema: public; Tablespace: 
--

CREATE UNIQUE INDEX download_log_chap_id_ip_via_idx ON download_log USING btree (chap_id, ip, via);


--
-- Name: group_queue_book_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX group_queue_book_id ON group_queue USING btree (book_id);


--
-- Name: group_queue_pk; Type: INDEX; Schema: public; Tablespace: 
--

CREATE UNIQUE INDEX group_queue_pk ON group_queue USING btree (user_id, book_id);


--
-- Name: groups_book_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX groups_book_id ON groups USING btree (book_id);


--
-- Name: groups_pk; Type: INDEX; Schema: public; Tablespace: 
--

CREATE UNIQUE INDEX groups_pk ON groups USING btree (user_id, book_id);


--
-- Name: invites_book_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX invites_book_id ON invites USING btree (book_id);


--
-- Name: invites_to_uid; Type: INDEX; Schema: public; Tablespace: 
--

CREATE UNIQUE INDEX invites_to_uid ON invites USING btree (to_uid, book_id);


--
-- Name: karma_rates_from_uid; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX karma_rates_from_uid ON karma_rates USING btree (from_uid);


--
-- Name: karma_rates_pk; Type: INDEX; Schema: public; Tablespace: 
--

CREATE UNIQUE INDEX karma_rates_pk ON karma_rates USING btree (to_uid, from_uid);


--
-- Name: languages_typ_idx; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX languages_typ_idx ON languages USING btree (typ);


--
-- Name: mail_user_id_folder_idx; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX mail_user_id_folder_idx ON mail USING btree (user_id, folder);


--
-- Name: marks_pk; Type: INDEX; Schema: public; Tablespace: 
--

CREATE UNIQUE INDEX marks_pk ON marks USING btree (tr_id, user_id);


--
-- Name: notices_user_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX notices_user_id ON notices USING btree (user_id, seen);


--
-- Name: orig_chap_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX orig_chap_id ON orig USING btree (chap_id);


--
-- Name: orig_old_id_chap_id_old_id_idx; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX orig_old_id_chap_id_old_id_idx ON orig_old_id USING btree (chap_id, old_id);


--
-- Name: poll_answers_poll_id_user_id_idx; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX poll_answers_poll_id_user_id_idx ON poll_answers USING btree (poll_id, user_id);


--
-- Name: recalc_log_book_id_idx; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX recalc_log_book_id_idx ON recalc_log USING btree (book_id);


--
-- Name: reg_invites_from_id_idx; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX reg_invites_from_id_idx ON reg_invites USING btree (from_id);


--
-- Name: search_history_lower_idx; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX search_history_lower_idx ON search_history USING btree (lower((request)::text));


--
-- Name: seen_orig_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE UNIQUE INDEX seen_orig_id ON seen USING btree (orig_id, user_id);


--
-- Name: seen_post_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE UNIQUE INDEX seen_post_id ON seen USING btree (post_id, user_id);


--
-- Name: seen_user_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX seen_user_id ON seen USING btree (user_id);


--
-- Name: tmp_dllog_chap_id_dat_idx; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX tmp_dllog_chap_id_dat_idx ON tmp_dllog USING btree (chap_id, dat);


--
-- Name: translate_book_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX translate_book_id ON translate USING btree (book_id);


--
-- Name: translate_chap_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX translate_chap_id ON translate USING btree (chap_id);


--
-- Name: translate_orig_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX translate_orig_id ON translate USING btree (orig_id);


--
-- Name: translate_user_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX translate_user_id ON translate USING btree (user_id);


--
-- Name: user_tr_stat_book_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE INDEX user_tr_stat_book_id ON user_tr_stat USING btree (book_id);


--
-- Name: user_tr_stat_user_id; Type: INDEX; Schema: public; Tablespace: 
--

CREATE UNIQUE INDEX user_tr_stat_user_id ON user_tr_stat USING btree (user_id, book_id);


--
-- Name: userinfo_pk; Type: INDEX; Schema: public; Tablespace: 
--

CREATE UNIQUE INDEX userinfo_pk ON userinfo USING btree (user_id, prop_id);


--
-- Name: users_login_idx; Type: INDEX; Schema: public; Tablespace: 
--

CREATE UNIQUE INDEX users_login_idx ON users USING btree (lower((login)::text));


--
-- Name: ban_user_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY ban
    ADD CONSTRAINT ban_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: blog_posts_book_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY blog_posts
    ADD CONSTRAINT blog_posts_book_id_fkey FOREIGN KEY (book_id) REFERENCES books(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: blog_posts_user_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY blog_posts
    ADD CONSTRAINT blog_posts_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: book_ban_reasons_book_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY book_ban_reasons
    ADD CONSTRAINT book_ban_reasons_book_id_fkey FOREIGN KEY (book_id) REFERENCES books(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: bookmarks_book_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY bookmarks
    ADD CONSTRAINT bookmarks_book_id_fkey FOREIGN KEY (book_id) REFERENCES books(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: bookmarks_orig_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY bookmarks
    ADD CONSTRAINT bookmarks_orig_id_fkey FOREIGN KEY (orig_id) REFERENCES orig(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: bookmarks_user_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY bookmarks
    ADD CONSTRAINT bookmarks_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: books_cat_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY books
    ADD CONSTRAINT books_cat_id_fkey FOREIGN KEY (cat_id) REFERENCES catalog(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: books_owner_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY books
    ADD CONSTRAINT books_owner_id_fkey FOREIGN KEY (owner_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: books_s_lang_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY books
    ADD CONSTRAINT books_s_lang_fkey FOREIGN KEY (s_lang) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: books_t_lang_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY books
    ADD CONSTRAINT books_t_lang_fkey FOREIGN KEY (t_lang) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: catalog_pid_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY catalog
    ADD CONSTRAINT catalog_pid_fkey FOREIGN KEY (pid) REFERENCES catalog(id) ON DELETE RESTRICT;


--
-- Name: chapters_book_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY chapters
    ADD CONSTRAINT chapters_book_id_fkey FOREIGN KEY (book_id) REFERENCES books(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: comments_orig_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY comments
    ADD CONSTRAINT comments_orig_id_fkey FOREIGN KEY (orig_id) REFERENCES orig(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: comments_pid_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY comments
    ADD CONSTRAINT comments_pid_fkey FOREIGN KEY (pid) REFERENCES comments(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: comments_post_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY comments
    ADD CONSTRAINT comments_post_id_fkey FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: comments_rating_comment_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY comments_rating
    ADD CONSTRAINT comments_rating_comment_id_fkey FOREIGN KEY (comment_id) REFERENCES comments(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: comments_rating_user_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY comments_rating
    ADD CONSTRAINT comments_rating_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: comments_user_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY comments
    ADD CONSTRAINT comments_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: dict_book_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY dict
    ADD CONSTRAINT dict_book_id_fkey FOREIGN KEY (book_id) REFERENCES books(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: dict_user_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY dict
    ADD CONSTRAINT dict_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: group_queue_book_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY group_queue
    ADD CONSTRAINT group_queue_book_id_fkey FOREIGN KEY (book_id) REFERENCES books(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: group_queue_user_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY group_queue
    ADD CONSTRAINT group_queue_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: groups_book_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY groups
    ADD CONSTRAINT groups_book_id_fkey FOREIGN KEY (book_id) REFERENCES books(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: groups_user_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY groups
    ADD CONSTRAINT groups_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: invites_book_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY invites
    ADD CONSTRAINT invites_book_id_fkey FOREIGN KEY (book_id) REFERENCES books(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: invites_from_uid_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY invites
    ADD CONSTRAINT invites_from_uid_fkey FOREIGN KEY (from_uid) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: invites_to_uid_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY invites
    ADD CONSTRAINT invites_to_uid_fkey FOREIGN KEY (to_uid) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: karma_rates_from_uid_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY karma_rates
    ADD CONSTRAINT karma_rates_from_uid_fkey FOREIGN KEY (from_uid) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: karma_rates_to_uid_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY karma_rates
    ADD CONSTRAINT karma_rates_to_uid_fkey FOREIGN KEY (to_uid) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: marks_tr_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY marks
    ADD CONSTRAINT marks_tr_id_fkey FOREIGN KEY (tr_id) REFERENCES translate(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: marks_user_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY marks
    ADD CONSTRAINT marks_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: moder_book_cat_book_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY moder_book_cat
    ADD CONSTRAINT moder_book_cat_book_id_fkey FOREIGN KEY (book_id) REFERENCES books(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: notices_user_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY notices
    ADD CONSTRAINT notices_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: orig_chap_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY orig
    ADD CONSTRAINT orig_chap_id_fkey FOREIGN KEY (chap_id) REFERENCES chapters(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: poll_answers_user_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY poll_answers
    ADD CONSTRAINT poll_answers_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: reg_invites_from_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY reg_invites
    ADD CONSTRAINT reg_invites_from_id_fkey FOREIGN KEY (from_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: reg_invites_to_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY reg_invites
    ADD CONSTRAINT reg_invites_to_id_fkey FOREIGN KEY (to_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: remind_tokens_user_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY remind_tokens
    ADD CONSTRAINT remind_tokens_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: seen_orig_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY seen
    ADD CONSTRAINT seen_orig_id_fkey FOREIGN KEY (orig_id) REFERENCES orig(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: seen_post_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY seen
    ADD CONSTRAINT seen_post_id_fkey FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: seen_user_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY seen
    ADD CONSTRAINT seen_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: translate_chap_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY translate
    ADD CONSTRAINT translate_chap_id_fkey FOREIGN KEY (chap_id) REFERENCES chapters(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: translate_user_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY translate
    ADD CONSTRAINT translate_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: userinfo_user_id_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY userinfo
    ADD CONSTRAINT userinfo_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users_invited_by_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_invited_by_fkey FOREIGN KEY (invited_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: users_lang_fkey; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_lang_fkey FOREIGN KEY (lang) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--
