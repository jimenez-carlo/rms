<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

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
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

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
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

switch(ENVIRONMENT):

  case 'development':

    # RMS_DB
    defined('RMS_DB_HOST')      ? null : define('RMS_DB_HOST', '172.17.0.4');
    defined('RMS_DB_USER')      ? null : define('RMS_DB_USER', 'root');
    defined('RMS_DB_PASSWORD')  ? null : define('RMS_DB_PASSWORD', 'rootdev');
    defined('RMS_DB_NAME')      ? null : define('RMS_DB_NAME', 'rms_db');

    # PORTAL GLOBAL DB
    defined('PORTAL_GLOBAL_DB_HOST')      ? null : define('PORTAL_GLOBAL_DB_HOST', '172.17.0.4');
    defined('PORTAL_GLOBAL_DB_USER')      ? null : define('PORTAL_GLOBAL_DB_USER', 'root');
    defined('PORTAL_GLOBAL_DB_PASSWORD')  ? null : define('PORTAL_GLOBAL_DB_PASSWORD', 'rootdev');
    defined('PORTAL_GLOBAL_DB_NAME')      ? null : define('PORTAL_GLOBAL_DB_NAME', 'portal_global_db');

    # MNC, MTI, HPTI DEV_CES2
    defined('DEV_CES_DB_HOST')      ? null : define('DEV_CES_DB_HOST', '172.17.0.3');
    defined('DEV_CES_DB_USER')      ? null : define('DEV_CES_DB_USER', 'root');
    defined('DEV_CES_DB_PASSWORD')  ? null : define('DEV_CES_DB_PASSWORD', 'rootdev');
    defined('DEV_CES_DB_NAME')      ? null : define('DEV_CES_DB_NAME', 'dev_ces2');

    # MDI_DEV_CES2
    defined('MDI_DEV_CES_DB_HOST')      ? null : define('MDI_DEV_CES_DB_HOST', '172.17.0.3');
    defined('MDI_DEV_CES_DB_USER')      ? null : define('MDI_DEV_CES_DB_USER', 'root');
    defined('MDI_DEV_CES_DB_PASSWORD')  ? null : define('MDI_DEV_CES_DB_PASSWORD', 'rootdev');
    defined('MDI_DEV_CES_DB_NAME')      ? null : define('MDI_DEV_CES_DB_NAME', 'mdi_dev_ces2');

    # DEV_RMS
    defined('DEV_RMS_DB_HOST')      ? null : define('DEV_RMS_DB_HOST', '172.17.0.3');
    defined('DEV_RMS_DB_USER')      ? null : define('DEV_RMS_DB_USER', 'root');
    defined('DEV_RMS_DB_PASSWORD')  ? null : define('DEV_RMS_DB_PASSWORD', 'rootdev');
    defined('DEV_RMS_DB_NAME')      ? null : define('DEV_RMS_DB_NAME', 'dev_rms');

    # MDI_DEV_RMS
    defined('MDI_DEV_RMS_DB_HOST')      ? null : define('MDI_DEV_RMS_DB_HOST', '172.17.0.3');
    defined('MDI_DEV_RMS_DB_USER')      ? null : define('MDI_DEV_RMS_DB_USER', 'root');
    defined('MDI_DEV_RMS_DB_PASSWORD')  ? null : define('MDI_DEV_RMS_DB_PASSWORD', 'rootdev');
    defined('MDI_DEV_RMS_DB_NAME')      ? null : define('MDI_DEV_RMS_DB_NAME', 'mdi_dev_rms');

  break;

  case 'testing':
    defined('RMS_DB_HOST')      ? null : define('RMS_DB_HOST', 'localhost');
    defined('RMS_DB_USER')      ? null : define('RMS_DB_USER', 'root');
    defined('RMS_DB_PASSWORD')  ? null : define('RMS_DB_PASSWORD', 'root');
    defined('RMS_DB_NAME')      ? null : define('RMS_DB_NAME', 'rms_db');

    defined('PORTAL_GLOBAL_DB_HOST')      ? null : define('PORTAL_GLOBAL_DB_HOST', 'localhost');
    defined('PORTAL_GLOBAL_DB_USER')      ? null : define('PORTAL_GLOBAL_DB_USER', 'root');
    defined('PORTAL_GLOBAL_DB_PASSWORD')  ? null : define('PORTAL_GLOBAL_DB_PASSWORD', 'root');
    defined('PORTAL_GLOBAL_DB_NAME')      ? null : define('PORTAL_GLOBAL_DB_NAME', 'portal_global_db_55');
    break;

  case 'production':

    # RMS_DB
    defined('RMS_DB_HOST')      ? null : define('RMS_DB_HOST', '192.168.100.55');
    defined('RMS_DB_USER')      ? null : define('RMS_DB_USER', 'admin');
    defined('RMS_DB_PASSWORD')  ? null : define('RMS_DB_PASSWORD', 'cmcadmin');
    defined('RMS_DB_NAME')      ? null : define('RMS_DB_NAME', 'rms_db');

    # PORTAL GLOBAL DB
    defined('PORTAL_GLOBAL_DB_HOST')      ? null : define('PORTAL_GLOBAL_DB_HOST', '192.168.100.55');
    defined('PORTAL_GLOBAL_DB_USER')      ? null : define('PORTAL_GLOBAL_DB_USER', 'admin');
    defined('PORTAL_GLOBAL_DB_PASSWORD')  ? null : define('PORTAL_GLOBAL_DB_PASSWORD', 'cmcadmin');
    defined('PORTAL_GLOBAL_DB_NAME')      ? null : define('PORTAL_GLOBAL_DB_NAME', 'portal_global_db');

    # MNC, MTI, HPTI DEV_CES2
    defined('DEV_CES_DB_HOST')      ? null : define('DEV_CES_DB_HOST', '192.168.100.20');
    defined('DEV_CES_DB_USER')      ? null : define('DEV_CES_DB_USER', 'admin');
    defined('DEV_CES_DB_PASSWORD')  ? null : define('DEV_CES_DB_PASSWORD', 'admin');
    defined('DEV_CES_DB_NAME')      ? null : define('DEV_CES_DB_NAME', 'dev_ces2');

    # MDI_DEV_CES2
    defined('MDI_DEV_CES_DB_HOST')      ? null : define('MDI_DEV_CES_DB_HOST', '192.168.100.40');
    defined('MDI_DEV_CES_DB_USER')      ? null : define('MDI_DEV_CES_DB_USER', 'root');
    defined('MDI_DEV_CES_DB_PASSWORD')  ? null : define('MDI_DEV_CES_DB_PASSWORD', 'RestricteD');
    defined('MDI_DEV_CES_DB_NAME')      ? null : define('MDI_DEV_CES_DB_NAME', 'dev_ces2');

    # DEV_RMS
    defined('DEV_RMS_DB_HOST')      ? null : define('DEV_RMS_DB_HOST', '192.168.100.20');
    defined('DEV_RMS_DB_USER')      ? null : define('DEV_RMS_DB_USER', 'admin');
    defined('DEV_RMS_DB_PASSWORD')  ? null : define('DEV_RMS_DB_PASSWORD', 'admin');
    defined('DEV_RMS_DB_NAME')      ? null : define('DEV_RMS_DB_NAME', 'dev_rms');

    # MDI_DEV_RMS
    defined('MDI_DEV_RMS_DB_HOST')      ? null : define('MDI_DEV_RMS_DB_HOST', '192.168.100.40');
    defined('MDI_DEV_RMS_DB_USER')      ? null : define('MDI_DEV_RMS_DB_USER', 'root');
    defined('MDI_DEV_RMS_DB_PASSWORD')  ? null : define('MDI_DEV_RMS_DB_PASSWORD', 'RestricteD');
    defined('MDI_DEV_RMS_DB_NAME')      ? null : define('MDI_DEV_RMS_DB_NAME', 'dev_rms');

  break;

endswitch;
