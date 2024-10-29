<?php

function altmailer_create_multi_connections()
{
	static $multi;

	if( !is_null( $multi ) )
		return $multi;

	$altMailerOptions = get_option('altmailer_options');

	// Seems to be an obscure bug that prevents activation in some cases.
	// Manually install.
	if( false === $altMailerOption ) {
		AltMailer_Setup_Hooks::activation();
		$altMailerOptions = get_option('altmailer_options');
	}

	$multi = new Swift_Connection_Multi();

	// Setup sendmail.
	if( true === $altMailerOptions['use_sendmail'] )
	{
		$sendmail_default = new Swift_Connection_Sendmail($altMailerOptions['sendmail_path']);
		$sendmail_default->setTimeout( $altMailerOptions['timeout'] );
		$multi->addConnection( $sendmail_default );

		if( true === $altMailerOptions['sendmail_autodetect'] )
		{
			$multi->addConnection( new Swift_Connection_Sendmail(Swift_Connection_Sendmail::AUTO_DETECT) );
		}
	}

	if( true === $altMailerOptions['use_smtp'] )
	{
		if( 0 == $altMailerOptions['smtp_encryption'] )
		{
			$smtp = new Swift_Connection_SMTP($altMailerOptions['smtp'], $altMailerOptions['smtp_port']);
		}
		else
		{
			$smtp = new Swift_Connection_SMTP($altMailerOptions['smtp'], $altMailerOptions['smtp_port'], $altMailerOptions['smtp_encryption']);
		}

		$smtp->setTimeout($altMailerOptions['timeout'] );

		if( !empty($altMailerOptions['smtp_username']) )
		{
			$smtp->setUsername( $options['smtp_username'] );
			$smtp->setpassword( $options['smtp_password'] );
		}

		$multi->addConnection( $smtp );
	}

	if( true === $altMailerOptions['use_mail'] )
	{
		$multi->addConnection( new Swift_Connection_NativeMail() );
	}
	
	return $multi;
}

function altmailer_split_headers($headers)
{
	if( empty($headers) )
	{
		return array();
	}

	if( is_array($headers) )
	{
		return $headers;
	}

	$arrNewHeaders = array();
	$headers = str_replace(array("\r\n", "\n\n"), "\n", $headers);
	$arrSplitHeaders = explode("\n", $headers);

	if( empty($arrSplitHeaders) )
	{
		return array();
	}

	foreach( (array) $arrSplitHeaders as $header)
	{
		if( strpos($header, ':') !== false)
		{
			continue;
		}

		list($name, $value) = explode(':', $header, 2);
		$arrNewHeaders[ strtolower(trim($name)) ] = trim($value);
	}

	return $arrNewHeaders;
}

function altmailer_parse_name_email_address($address)
{
	if( strpos($address, '<') !== false )
	{
		list($strFromName, $strFromEmail) = explode('<', $address);

		$strFromName = trim( str_replace(array('"', "'"), '', $strFromName) );
		$strFromEmail = trim( str_replace('>', '', $strFromEmail) );
	}
	else
	{
		$strFromName = '';
		$strFromEmail = trim( $address );
	}
	
	return array( $strFromEmail, $strFromName );
}

function altmailer_create_recipients_list($to, &$headers)
{
	// Create the send list from the to, cc, and bcc headers
	$objRecipients = new Swift_RecipientList();

	if( !empty($to) )
	{
		if( strpos($to, ',') !== false )
		{
			$arrToList = explode(',', $to);

			foreach( (array) $arrToList as $strToAddress )
			{
				list($tmpToEmail, $tmpToName) = altmailer_parse_name_email_address( $strToAddress );

				if( empty($tmpToName) )
				{
					$objRecipients->addTo($tmpToEmail);
				}
				else
				{
					$objRecipients->addTo(new Swift_Address($tmpToName, $tmpToEmail));
				}
			}
		}
		else
		{
			list($tmpToEmail, $tmpToName) = altmailer_parse_name_email_address( $to );

			if( empty($tmpToName) )
			{
				$objRecipients->addTo($tmpToEmail);
			}
			else
			{
				$objRecipients->addTo(new Swift_Address($tmpToName, $tmpToEmail));
			}
		}
	}

	if( true === array_key_exists( 'to', $headers ) )
	{
		if( strpos($headers['to'], ',') !== false )
		{
			$arrToList = explode(',', $headers['to']);

			foreach( (array) $arrToList as $strToAddress )
			{
				list($tmpToEmail, $tmpToName) = altmailer_parse_name_email_address( $strToAddress );

				if( empty($tmpToName) )
				{
					$objRecipients->addTo($tmpToEmail);
				}
				else
				{
					$objRecipients->addTo(new Swift_Address($tmpToName, $tmpToEmail));
				}
			}
		}
		else
		{
			list($tmpToEmail, $tmpToName) = altmailer_parse_name_email_address( $headers['to'] );

			if( empty($tmpToName) )
			{
				$objRecipients->addTo($tmpToEmail);
			}
			else
			{
				$objRecipients->addTo(new Swift_Address($tmpToName, $tmpToEmail));
			}
		}

		unset($headers['to']);
	}

	if( true === array_key_exists( 'cc', $headers ) )
	{
		if( strpos($headers['cc'], ',') !== false )
		{
			$arrToList = explode(',', $headers['cc']);

			foreach( (array) $arrToList as $strToAddress )
			{
				list($tmpToEmail, $tmpToName) = altmailer_parse_name_email_address( $strToAddress );

				if( empty($tmpToName) )
				{
					$objRecipients->addCc($tmpToEmail);
				}
				else
				{
					$objRecipients->addCc(new Swift_Address($tmpToName, $tmpToEmail));
				}
			}
		}
		else
		{
			list($tmpToEmail, $tmpToName) = altmailer_parse_name_email_address( $headers['cc'] );

			if( empty($tmpToName) )
			{
				$objRecipients->addCc($tmpToEmail);
			}
			else
			{
				$objRecipients->addCc(new Swift_Address($tmpToName, $tmpToEmail));
			}
		}

		unset($headers['cc']);
	}

	if( true === array_key_exists( 'bcc', $headers ) )
	{
		if( strpos($headers['bcc'], ',') !== false )
		{
			$arrToList = explode(',', $headers['bcc']);

			foreach( (array) $arrToList as $strToAddress )
			{
				list($tmpToEmail, $tmpToName) = altmailer_parse_name_email_address( $strToAddress );

				if( empty($tmpToName) )
				{
					$objRecipients->addBcc($tmpToEmail);
				}
				else
				{
					$objRecipients->addBcc(new Swift_Address($tmpToName, $tmpToEmail));
				}
			}
		}
		else
		{
			list($tmpToEmail, $tmpToName) = altmailer_parse_name_email_address( $headers['bcc'] );

			if( empty($tmpToName) )
			{
				$objRecipients->addBcc($tmpToEmail);
			}
			else
			{
				$objRecipients->addBcc(new Swift_Address($tmpToName, $tmpToEmail));
			}
		}

		unset($headers['bcc']);
	}
	
	return $objRecipients;
}

function wp_mail($to, $subject, $message, $headers = '' )
{
	// Compact the input, apply the filters, and extract them back out
	// Keep this so that other plugins that expect this hook, will still
	// work.
	extract( apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers' ) ) );

	$objSwiftConnections = altmailer_create_multi_connections();
	$swift = new Swift( $objSwiftConnections );

	$headers = altmailer_split_headers($headers);

	// Get the content type and charset if available
	if( true === array_key_exists('content-type', $headers) )
	{
		if( strpos( $headers['content-type'], ';' ) !== false )
		{
			list($strContentType, $strCharset) = explode(';', $headers['content-type'], 2);

			$strContentType = trim($strContentType);
			$strCharset = trim( str_replace( array('charset=', '"', "'"), '', $strCharset) );
		}
		else
		{
			$strContentType = $headers[ 'content-type' ];
			$strCharset = get_bloginfo( 'charset' );
		}

		unset($headers['content-type']);
	}
	else
	{
		$strContentType = 'text/plain';
		$strCharset = get_bloginfo( 'charset' );
	}

	$strContentType = apply_filters( 'wp_mail_content_type', $strContentType );
	$strCharset = apply_filters( 'wp_mail_charset', $strCharset );

	if( 'text/html' == $strContentType)
	{
		$objMessage =& new Swift_Message($subject);
		// Replace block level tags with double new lines
		$plaintextMessage = str_replace(array('</div>', '</p>', '</ol>', '</table>'), "\n\n", $message);
		// Replace br elements with single new line
		$plaintextMessage = str_replace(array('<br/>', '<br />'), "\n", $plaintextMessage);
		$plaintextMessage = strip_tags($plaintextMessage);

		$messagePartPlaintext =& new Swift_Message_Part($plaintextMessage);
		$messagePartPlaintext->setCharset( $strCharset );
		$objMessage->attach( $messagePartPlaintext );

		$messagePartHTML =& new Swift_Message_Part($message, 'text/html');
		$messagePartHTML->setCharset( $strCharset );
		$objMessage->attach( $messagePartHTML );
	}
	else if( 'text/plain' == $strContentType || empty($strContentType) )
	{
		$objMessage =& new Swift_Message($subject, $message);
		$objMessage->setCharset( $strCharset );
	}
	else
	{
		// Really shouldn't be doing this, but trust the user to know what they
		// are doing, even though we really shouldn't do this, in case of
		// attachments.
		$objMessage = new Swift_Message($subject, $message, $strContentType);
		$objMessage->setCharset( $strCharset );
	}

	// Get and set the from name and email address
	if( true === array_key_exists('from', $headers) )
	{
		list($strFromEmail, $strFromName) = altmailer_parse_name_email_address( $headers['from'] );

		if( empty($strFromName) )
		{
			$strFromName = 'WordPress';
		}

		unset( $headers['from'] );
	}
	else
	{
		$strFromName = 'WordPress';

		// Get the site domain and get rid of www.
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}

		$strFromEmail = 'wordpress@' . $sitename;
	}

	$strFromEmail = apply_filters('wp_mail_from', $strFromEmail);
	$strFromName = apply_filters('wp_mail_from_name', $strFromName);

	if( !empty($strFromName) )
	{
		$objFromAddress = new Swift_Address($strFromName, $strFromEmail);
	}
	else
	{
		$objFromAddress = $strFromEmail;
	}

	// Get the recipients list
	$objRecipients = altmailer_create_recipients_list($to, $headers);

	// Add the rest of the headers that are left over
	if( count($headers) > 0 )
	{
		foreach( $headers as $name => $value)
		{
			$objMessage->headers->set($name, $value);
		}
	}

	$response = $swift->batchSend($objMessage, $objRecipients, $objFromAddress);

	do_action( 'altmailer_sender_return', $response );

	$swift->disconnect();

	return $response;

}