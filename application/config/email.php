<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['useragent']    = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36";
$config['protocol']     = 'mail';

$config['smtp_host']    = getenv('SMTP_HOST');
$config['smtp_port']    = getenv('SMTP_PORT');
// $config['smtp_user']    = getenv('SMTP_USER');
// $config['smtp_pass']    = getenv('SMTP_PASS');

$config['wordwrap']     = false;
$config['mailtype']     = 'html';
$config['charset']      = 'utf-8';

$config['validate']     = true;

$config['priority']     = 3;

$config['crlf']         = '\r\n';
$config['newline']      = '\r\n';

/* End of file config.php */
/* Location: ./application/config/email.php */
