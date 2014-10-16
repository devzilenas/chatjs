<?
class Config { 

# ------------------------------------------------------
# ---------- APPLICATION RELATED -----------------------
# ------------------------------------------------------
	/** Default timezone */
	const TZ                = 'Europe/Vilnius';

# ------------------------------------------------------
# ---------- INSTALLATION RELATED ----------------------
# ------------------------------------------------------

# ---------- WEB SITE HOST -----------------------------
	const BASE                  = 'http://localhost/chatjs';

# ---------- DATABASE CONFIGURATION --------------------
	public static $DB_NAME      = 'chat';
	public static $DB_HOST      = 'localhost';
	public static $DB_USER      = 'root';
	public static $DB_PASSWORD  = '';

# ------------------------------------------------------
# ---------- FOR DEVELOPERS  ---------------------------
# ------------------------------------------------------
	public static $SESSION_SHOW = FALSE;
# ---------- TEST DATABASE -----------------------------
	public static $DB_TEST      = 'chat_test';
}

