ALTER TABLE books
  ALTER COLUMN ac_tr SET DEFAULT 'g'::bpchar,
  ALTER COLUMN ac_blog_w SET DEFAULT 'g'::bpchar,
  ALTER COLUMN ac_chap_edit SET DEFAULT 'm'::bpchar;

UPDATE books SET
  facecontrol = 1,
  ac_read = 'a'::bpchar,
  ac_trread = 'a'::bpchar,
  ac_gen = 'a'::bpchar,
	ac_rate = 'a'::bpchar,
	ac_comment = 'a'::bpchar,
	ac_tr = 'g'::bpchar,
	ac_blog_r = 'a'::bpchar,
	ac_blog_c = 'a'::bpchar,
	ac_blog_w = 'g'::bpchar,
	ac_announce = 'm'::bpchar,
	ac_chap_edit = 'm'::bpchar,
	ac_book_edit = 'o'::bpchar,
	ac_membership = 'm'::bpchar;
