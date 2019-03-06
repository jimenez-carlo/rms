<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
define('EXIT_SUCCESS', 0); // no errors
define('EXIT_ERROR', 1); // generic error
define('EXIT_CONFIG', 3); // configuration error
define('EXIT_UNKNOWN_FILE', 4); // file not found
define('EXIT_UNKNOWN_CLASS', 5); // unknown class
define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
define('EXIT_USER_INPUT', 7); // invalid user input
define('EXIT_DATABASE', 8); // database error
define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

switch(ENVIRONMENT):

  case 'development':

    # RMS_DB
    defined('RMS_DB_HOST')      ? null : define('RMS_DB_HOST', '172.17.0.2');
    defined('RMS_DB_USER')      ? null : define('RMS_DB_USER', 'root');
    defined('RMS_DB_PASSWORD')  ? null : define('RMS_DB_PASSWORD', 'root');
    defined('RMS_DB_NAME')      ? null : define('RMS_DB_NAME', 'rms_db');

    # PORTAL GLOBAL PORTAL FROM 192.168.100.20
    defined('PORTAL_GLOBAL_DB_HOST')      ? null : define('PORTAL_GLOBAL_DB_HOST', '192.168.100.20');
    defined('PORTAL_GLOBAL_DB_USER')      ? null : define('PORTAL_GLOBAL_DB_USER', 'admin');
    defined('PORTAL_GLOBAL_DB_PASSWORD')  ? null : define('PORTAL_GLOBAL_DB_PASSWORD', 'admin');
    defined('PORTAL_GLOBAL_DB_NAME')      ? null : define('PORTAL_GLOBAL_DB_NAME', 'portal_global_db');

    #Production IAAS database for debuggin & testing only
    //defined('RMS_DB_HOST')      ? null : define('RMS_DB_HOST', '172.16.30.156');
    //defined('RMS_DB_USER')      ? null : define('RMS_DB_USER', 'dev');
    //defined('RMS_DB_PASSWORD')  ? null : define('RMS_DB_PASSWORD', 'rootdev');
    //defined('RMS_DB_NAME')      ? null : define('RMS_DB_NAME', 'cps_portal_global_db');

    //defined('PORTAL_GLOBAL_DB_HOST')      ? null : define('PORTAL_GLOBAL_DB_HOST', '172.16.30.156');
    //defined('PORTAL_GLOBAL_DB_USER')      ? null : define('PORTAL_GLOBAL_DB_USER', 'dev');
    //defined('PORTAL_GLOBAL_DB_PASSWORD')  ? null : define('PORTAL_GLOBAL_DB_PASSWORD', 'rootdev');
    //defined('PORTAL_GLOBAL_DB_NAME')      ? null : define('PORTAL_GLOBAL_DB_NAME', 'cps_portal_global_db');
  break;

  case 'testing':
    defined('RMS_DB_HOST')      ? null : define('RMS_DB_HOST', 'localhost');
    defined('RMS_DB_USER')      ? null : define('RMS_DB_USER', 'root');
    defined('RMS_DB_PASSWORD')  ? null : define('RMS_DB_PASSWORD', 'root');
    defined('RMS_DB_NAME')      ? null : define('RMS_DB_NAME', 'rms_db');

    # PORTAL GLOBAL PORTAL FROM 192.168.100.20
    defined('PORTAL_GLOBAL_DB_HOST')      ? null : define('PORTAL_GLOBAL_DB_HOST', 'localhost');
    defined('PORTAL_GLOBAL_DB_USER')      ? null : define('PORTAL_GLOBAL_DB_USER', 'root');
    defined('PORTAL_GLOBAL_DB_PASSWORD')  ? null : define('PORTAL_GLOBAL_DB_PASSWORD', 'root');
    defined('PORTAL_GLOBAL_DB_NAME')      ? null : define('PORTAL_GLOBAL_DB_NAME', 'portal_global_db');

    break;
  case 'production':

    # RMS_DB
    defined('RMS_DB_HOST')      ? null : define('RMS_DB_HOST', '192.168.100.55');
    defined('RMS_DB_USER')      ? null : define('RMS_DB_USER', 'admin');
    defined('RMS_DB_PASSWORD')  ? null : define('RMS_DB_PASSWORD', 'cmcadmin');
    defined('RMS_DB_NAME')      ? null : define('RMS_DB_NAME', 'rms_db');

    # PORTAL GLOBAL
    //defined('PORTAL_GLOBAL_DB_HOST')      ? null : define('PORTAL_GLOBAL_DB_HOST', '');
    //defined('PORTAL_GLOBAL_DB_USER')      ? null : define('PORTAL_GLOBAL_DB_USER', 'appserver');
    //defined('PORTAL_GLOBAL_DB_PASSWORD')  ? null : define('PORTAL_GLOBAL_DB_PASSWORD', 'opulently tadpole mulberry ether overfeed drizzly');
    //defined('PORTAL_GLOBAL_DB_HOST')      ? null : define('PORTAL_GLOBAL_DB_HOST', '192.168.100.20');
    //defined('PORTAL_GLOBAL_DB_USER')      ? null : define('PORTAL_GLOBAL_DB_USER', 'admin');
    //defined('PORTAL_GLOBAL_DB_PASSWORD')  ? null : define('PORTAL_GLOBAL_DB_PASSWORD', 'admin');
    //defined('PORTAL_GLOBAL_DB_NAME')      ? null : define('PORTAL_GLOBAL_DB_NAME', 'portal_global_db');

    # PORTAL GLOBAL PORTAL FROM 192.168.100.20
    defined('PORTAL_GLOBAL_DB_HOST')      ? null : define('PORTAL_GLOBAL_DB_HOST', '192.168.100.20');
    defined('PORTAL_GLOBAL_DB_USER')      ? null : define('PORTAL_GLOBAL_DB_USER', 'admin');
    defined('PORTAL_GLOBAL_DB_PASSWORD')  ? null : define('PORTAL_GLOBAL_DB_PASSWORD', 'admin');
    defined('PORTAL_GLOBAL_DB_NAME')      ? null : define('PORTAL_GLOBAL_DB_NAME', 'portal_global_db');
  break;

endswitch;
