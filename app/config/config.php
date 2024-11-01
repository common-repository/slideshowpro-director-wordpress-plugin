<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//base url

$config['base_url']	= '';

//index page

$config['index_page'] = '';

//uri settings

$config['uri_protocol']	= "QUERY_STRING";

//suffixes

$config['url_suffix'] = "";

//language

$config['language']	= "english";

//charset

$config['charset'] = "UTF-8";

//Class Extension Convention

$config['subclass_prefix'] = 'MY_';

//allowed url chars

$config['permitted_uri_chars'] = '';

$config['enable_query_strings'] = TRUE;


/*
|--------------------------------------------------------------------------
| Error Logging Threshold
|--------------------------------------------------------------------------
|
| If you have enabled error logging, you can set an error threshold to 
| determine what gets logged. Threshold options are:
| You can enable error logging by setting a threshold over zero. The
| threshold determines what gets logged. Threshold options are:
|
|	0 = Disables logging, Error logging TURNED OFF
|	1 = Error Messages (including PHP errors)
|	2 = Debug Messages
|	3 = Informational Messages
|	4 = All Messages
|
| For a live site you'll usually only enable Errors (1) to be logged otherwise
| your log files will fill up very fast.
|
*/
$config['log_threshold'] = 0;

//Error Logging Directory Path

$config['log_path'] = '';

//Date Format for Logs

$config['log_date_format'] = 'Y-m-d H:i:s';

//Cache Directory Path

$config['cache_path'] = '';

/*
|--------------------------------------------------------------------------
| Session Variables
|--------------------------------------------------------------------------
|
| 'session_cookie_name' = the name you want for the cookie
| 'encrypt_sess_cookie' = TRUE/FALSE (boolean).  Whether to encrypt the cookie
| 'session_expiration'  = the number of SECONDS you want the session to last.
|  by default sessions last 7200 seconds (two hours).  Set to zero for no expiration.
| 'time_to_update'		= how many seconds between SP refreshing Session Information
|
*/
$config['sess_cookie_name']		= 'sp_session';
$config['sess_expiration']		= 7200;
$config['sess_encrypt_cookie']	= FALSE;
$config['sess_use_database']	= FALSE;
$config['sess_table_name']		= 'sp_sessions';
$config['sess_match_ip']		= FALSE;
$config['sess_match_useragent']	= TRUE;
$config['sess_time_to_update'] 	= 300;

/*
|--------------------------------------------------------------------------
| Cookie Related Variables
|--------------------------------------------------------------------------
|
| 'cookie_prefix' = Set a prefix if you need to avoid collisions
| 'cookie_domain' = Set to .your-domain.com for site-wide cookies
| 'cookie_path'   =  Typically will be a forward slash
|
*/
$config['cookie_prefix']	= "";
$config['cookie_domain']	= "";
$config['cookie_path']		= "/";

//Global XSS Filtering

$config['global_xss_filtering'] = FALSE;

//gzip

$config['compress_output'] = FALSE;

//Master Time Reference

$config['time_reference'] = 'local';

//Rewrite PHP Short Tags

$config['rewrite_short_tags'] = FALSE;