<?php 
# Database Configuration
define( 'DB_NAME', 'wp_liwcheckout' );
define( 'DB_USER', 'liwcheckout' );
define( 'DB_PASSWORD', '2sF7Z9UR9DgDvuF0Z9Rk' );
define( 'DB_HOST', '127.0.0.1' );
define( 'DB_HOST_SLAVE', '127.0.0.1' );
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_unicode_ci');
$table_prefix = 'wpsm_';

# Security Salts, Keys, Etc
define('AUTH_KEY',         'pfbgr]*6.:,Hlc{ga|3DI,a]gFP,]Y]Lo-#4q*k?BUqQdIzMCyHZ&AxYTB`07nzF');
define('SECURE_AUTH_KEY',  '3k#7_1#1,+QPA#|J<gXfiTk+3:T*?$3S$V[6uHrRRp+d$(P]7RkG*.A@oOA_dBnQ');
define('LOGGED_IN_KEY',    'Lg-A;FX{0LN6H}MhSm!5BL5V##$-qjKr>]?h9-Nv338}~FhLl*3~h]%RSu[k]xY&');
define('NONCE_KEY',        'I_h{!S_<G52&P/HAm+qe5y&KE[-]nz%w,YY@$(I^QQXf^Ee{,@ne%iQb4^-WB&A7');
define('AUTH_SALT',        '9$lx|/.lM)f9j:+WJquwrn0TcP(I1Ec_+h2{}I=#DKWQ-}4._2!z;J3CH:[-:+RP');
define('SECURE_AUTH_SALT', '{`vrW-+W@+?)>%Mwmgno4{e/r6^|itDeFM,[V$W=[u^NOy<7j1k%6;QC94vRT6:l');
define('LOGGED_IN_SALT',   'o{[?IMYrLmX|0SL06,;cpYbsFIA-r4 tQZT*7G>H/&Rjkk<D phbAHZTq]o=r!li');
define('NONCE_SALT',       'YjS/NKLSa|O)+-1r;dgB+4Emhv+Aj};=<Z};Rp.)zX#TR-cU> qbq>NQFMzBbGPB');


//define( 'WP_DEBUG', true );
//define( 'WP_DEBUG_LOG', true );
//define( 'WP_DEBUG_DISPLAY', false );


# Localized Language Stuff

define( 'WP_CACHE', TRUE );

define( 'WP_AUTO_UPDATE_CORE', false );

define( 'PWP_NAME', 'liwcheckout' );

define( 'FS_METHOD', 'direct' );

define( 'FS_CHMOD_DIR', 0775 );

define( 'FS_CHMOD_FILE', 0664 );

define( 'PWP_ROOT_DIR', '/nas/wp' );

define( 'WPE_APIKEY', '1b01b707ba3766302d3b4cfb80364671e4df11d0' );

define( 'WPE_CLUSTER_ID', '101132' );

define( 'WPE_CLUSTER_TYPE', 'pod' );

define( 'WPE_ISP', true );

define( 'WPE_BPOD', false );

define( 'WPE_RO_FILESYSTEM', false );

define( 'WPE_LARGEFS_BUCKET', 'largefs.wpengine' );

define( 'WPE_SFTP_PORT', 2222 );

define( 'WPE_LBMASTER_IP', '' );

define( 'WPE_CDN_DISABLE_ALLOWED', false );

define( 'DISALLOW_FILE_MODS', FALSE );

define( 'DISALLOW_FILE_EDIT', FALSE );

define( 'DISABLE_WP_CRON', false );

define( 'WPE_FORCE_SSL_LOGIN', false );

define( 'FORCE_SSL_LOGIN', false );

/*SSLSTART*/ if ( isset($_SERVER['HTTP_X_WPE_SSL']) && $_SERVER['HTTP_X_WPE_SSL'] ) $_SERVER['HTTPS'] = 'on'; /*SSLEND*/

define( 'WPE_EXTERNAL_URL', false );

define( 'WP_POST_REVISIONS', FALSE );

define( 'WPE_WHITELABEL', 'wpengine' );

define( 'WP_TURN_OFF_ADMIN_BAR', false );

define( 'WPE_BETA_TESTER', false );

umask(0002);

$wpe_cdn_uris=array ( );

$wpe_no_cdn_uris=array ( );

$wpe_content_regexs=array ( );

$wpe_all_domains=array ( 0 => 'liwcheckout.wpengine.com', );

$wpe_varnish_servers=array ( 0 => 'pod-101132', );

$wpe_special_ips=array ( 0 => '104.199.124.111', );

$wpe_ec_servers=array ( );

$wpe_largefs=array ( );

$wpe_netdna_domains=array ( );

$wpe_netdna_domains_secure=array ( );

$wpe_netdna_push_domains=array ( );

$wpe_domain_mappings=array ( );

$memcached_servers=array ( );


# WP Engine ID


# WP Engine Settings






define('WP_DEBUG', false);
define('', true);

define('', true);

define('', true);

# That's It. Pencils down
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-settings.php');



//define('WP_SITEURL','https://liw.staging.wpengine.com' );

//define('WP_HOME','https://liw.staging.wpengine.com' );




















