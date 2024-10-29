<?php

// Quite possible an attempt to access the options page directly.
// That can't be allowed, since the library files are required.
// By returning, we are basically dieing without destroying the
// rest of WordPress execution, if any.
if( !defined('ABSPATH') )
{
	return;
}

/**
 * Strip out magic quotes from sendmail path
 *
 * There is a problem with magic quotes on Windows that doesn't
 * like backslashes used. So this replaces the slashes for both
 * Linux and Windows and replaces it with the directory separator
 * used in either Linux or Windows.
 *
 * @param string $text
 * @return string Corrected path without double backslashes.
 */
function altmailer_strip_extra_slashes($text)
{
	return str_replace(array('\\\\', '\\/'), DIRECTORY_SEPARATOR, $text);
}

?>

<div class="wrap">

<?php

$altmailer_plugin_settings = get_option('altmailer_options');

if( isset( $_POST['settings-update'] ) )
{
	check_admin_referer('altmailer-update_settings');

	if( isset($_POST['altmailer_use_mail']) )
		$altmailer_plugin_settings['use_mail'] = true;
	else
		$altmailer_plugin_settings['use_mail'] = false;

	if( isset($_POST['altmailer_use_sendmail']) )
		$altmailer_plugin_settings['use_sendmail'] = true;
	else
		$altmailer_plugin_settings['use_sendmail'] = false;

	if( isset($_POST['altmailer_use_smtp']) )
		$altmailer_plugin_settings['use_smtp'] = true;
	else
		$altmailer_plugin_settings['use_smtp'] = false;

	if( isset($_POST['altmailer_uninstall_plugin']) )
		$altmailer_plugin_settings['uninstall'] = true;
	else
		$altmailer_plugin_settings['uninstall'] = false;

	$altmailer_plugin_settings['timeout'] = (int) $_POST['altmailer_timeout'];

	if( isset($_POST['altmailer_sendmail_autodetect']) )
		$altmailer_plugin_settings['sendmail_autodetect'] = true;
	else
		$altmailer_plugin_settings['sendmail_autodetect'] = false;

	if( is_email($_POST['altmailer_sendmail_from']) )
		$altmailer_plugin_settings['sendmail_from'] = $_POST['altmailer_sendmail_from'];

	$altmailer_plugin_settings['sendmail_path'] = altmailer_strip_extra_slashes($_POST['altmailer_sendmail_path']);
	$altmailer_plugin_settings['smtp'] = $_POST['altmailer_smtp_host'];
	$altmailer_plugin_settings['smtp_port'] = (int) $_POST['altmailer_smtp_port'];

	if( is_numeric($_POST['altmailer_smtp_encryption']) && $_POST['altmailer_smtp_encryption'] < 3 && $_POST['altmailer_smtp_encryption'] >= 0)
		$altmailer_plugin_settings['smtp_encryption'] = (int) $_POST['altmailer_smtp_encryption'];

	$altmailer_plugin_settings['smtp_username'] = $_POST['altmailer_smtp_username'];
	$altmailer_plugin_settings['smtp_password'] = $_POST['altmailer_smtp_password'];

	update_option('altmailer_options', $altmailer_plugin_settings);
}

?>

<form action="" method="post">

<?php 
wp_nonce_field('altmailer-update_settings'); 
?>

<h2>Alternative Mailer Configuration</h2>

<p>It is recommended that you never disable mail, in case the below two do not function. If SMTP and Sendmail both fail, then you should have the PHP mail() function to fall back on.</p>

<table class="widefat">

<tr>
	<th width="200px"></th>
	<td>
		<input type="checkbox" name="altmailer_use_mail"<?php if($altmailer_plugin_settings['use_mail'] == true) : ?> checked="checked"<?php endif; ?> /> Enable PHP Mail?
	</td>
</tr>

<tr class="alternate">
	<th></th>
	<td>
		<input type="checkbox" name="altmailer_use_sendmail"<?php if($altmailer_plugin_settings['use_sendmail'] == true) : ?> checked="checked"<?php endif; ?> /> Enable Sendmail?
	</td>
</tr>

<tr>
	<th></th>
	<td>
		<input type="checkbox" name="altmailer_use_smtp"<?php if($altmailer_plugin_settings['use_smtp'] == true) : ?> checked="checked"<?php endif; ?> /> Enable SMTP?
	</td>
</tr>

<tr class="alternate">
	<th></th>
	<td>
		<input type="checkbox" name="altmailer_uninstall_plugin"<?php if($altmailer_plugin_settings['uninstall'] == true) : ?> checked="checked"<?php endif; ?> /> <strong>Uninstall?</strong>
	</td>
</tr>

<tr>
	<th align="right">Timeout:</th>
	<td>
		<input type="text" name="altmailer_timeout" value="<?php echo (int) $altmailer_plugin_settings['timeout']; ?>" size="2" />
	</td>
</tr>

</table>

<h2>Sendmail Settings</h2>

<table class="widefat">

<tr>
	<th width="200px" align="right">Use Autodetect?</th>
	<td><input type="checkbox" name="altmailer_sendmail_autodetect"<?php if($altmailer_plugin_settings['sendmail_autodetect'] == true) : ?> checked="checked"<?php endif; ?> /><br />Autodetect will try to autodetect the path of the sendmail application on your system.</td>
</tr>

<tr class="alternate">
	<th align="right">Sendmail From Address:</th>
	<td><input type="text" name="altmailer_sendmail_from" value="<?php
	if( empty($altmailer_plugin_settings['sendmail_from']) ) : $altmailer_plugin_settings['sendmail_from'] = get_option('admin_email'); endif;
	echo attribute_escape($altmailer_plugin_settings['sendmail_from']); ?>" /><br />What Email to use by default if the from address is not set (<strong>required for sendmail to work</strong>).</td>
</tr>

<tr>
	<th align="right">Sendmail Path:</th>
	<td><input type="text" name="altmailer_sendmail_path" value="<?php echo altmailer_strip_extra_slashes(attribute_escape($altmailer_plugin_settings['sendmail_path'])); ?>" /><br />Can be the path to any mailing agent which accepts "-t" or equivalent parameter.</td>
</tr>

</table>

<h2>SMTP Settings</h2>

<table class="widefat">

<tr>
	<th width="200px" align="right">SMTP Host:</th>
	<td><input type="text" name="altmailer_smtp_host" value="<?php echo attribute_escape($altmailer_plugin_settings['smtp']); ?>" /><br />Host to connect to for SMTP.</td>
</tr>

<tr class="alternate">
	<th align="right">SMTP Port:</th>
	<td><input type="text" name="altmailer_smtp_port" value="<?php echo (int) $altmailer_plugin_settings['smtp_port']; ?>" size="5" /><br />The port to use when connecting.</td>
</tr>

<tr>
	<th align="right">SMTP Encryption:</th>
	<td>
		<select name="altmailer_smtp_encryption" size="1">
			<option value="0"<?php if($altmailer_plugin_settings['smtp_encryption'] == 0) :?> selected="selected"<?php endif; ?>>None</option>
			<option value="1"<?php if($altmailer_plugin_settings['smtp_encryption'] == 1) :?> selected="selected"<?php endif; ?>>SSL</option>
			<option value="2"<?php if($altmailer_plugin_settings['smtp_encryption'] == 2) :?> selected="selected"<?php endif; ?>>TLS</option>
		</select><br />
		Currently is not implemented, but will set the encryption for SMTP using either SSL or TLS.
	</td>
</tr>

<tr class="alternate">
	<th align="right">SMTP Username:</th>
	<td><input type="text" name="altmailer_smtp_username" value="<?php echo attribute_escape($altmailer_plugin_settings['smtp_username']); ?>" /><br />Use this username when connecting to the SMTP server.</td>
</tr>

<tr>
	<th align="right">SMTP Password:</th>
	<td><input type="text" name="altmailer_smtp_password" value="<?php echo attribute_escape($altmailer_plugin_settings['smtp_password']); ?>" /><br />Use this password when connecting to the SMTP server.</td>
</tr>

</table>

<div align="center"><input type="submit" value="Update" name="settings-update" /></div>

</form>

</div>