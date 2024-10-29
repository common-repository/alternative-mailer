<?php
/*
Plugin Name: Alternative Mailer
Plugin URI: http://wordpress.org/extend/plugins/alternative-mailer/
Description: Alternative Swift Mailer version 3.3.2 for replacing PHPMailer.
Author: Jacob Santos
Version: 0.2
Author URI: http://www.santosj.name
 */

/*
Copyright (c) 2008, Jacob Santos
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
	Redistributions of source code must retain the above copyright
		notice, this list of conditions and the following disclaimer.
	Redistributions in binary form must reproduce the above copyright
		notice, this list of conditions and the following disclaimer in the
		documentation and/or other materials provided with the distribution.
	Neither the name of the Jacob Santos nor the
		names of its contributors may be used to endorse or promote products
		derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY Jacob Santos ``AS IS'' AND ANY
EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL Jacob Santos BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

define('ALTMAILERPATH', dirname(__FILE__));
define('ALTMAILER_VERSION', 0.1);

include_once ALTMAILERPATH . '/hooks/setup.php';

if( !function_exists('wp_mail') )
{
	require_once ALTMAILERPATH . '/swiftmailer/Swift.php';
	require_once ALTMAILERPATH . '/swiftmailer/Swift/Connection/Multi.php';
	require_once ALTMAILERPATH . '/swiftmailer/Swift/Connection/SMTP.php';
	require_once ALTMAILERPATH . '/swiftmailer/Swift/Connection/Sendmail.php';
	require_once ALTMAILERPATH . '/swiftmailer/Swift/Connection/NativeMail.php';
	
	
	if( version_compare(PHP_VERSION, '5.0', '>=') )
	{
		require_once ALTMAILERPATH . '/hooks/wp_mail_php5.php';
	}
	else
	{
		require_once ALTMAILERPATH . '/hooks/wp_mail_php4.php';
	}
	
}

register_activation_hook( __FILE__, array('AltMailer_Setup_Hooks', 'activation') );
register_deactivation_hook( __FILE__, array('AltMailer_Setup_Hooks', 'deactivation') );

new AltMailer_Setup_Hooks();