<?php
/**
 * Sets up the activation, deactivation, and init hooks
 *
 * Implements an installer, upgrader, and uninstaller.
 */

/**
 * Sets up the activation, deactivation, and init hooks.
 *
 * Most of the methods are static, so that the constructor
 * just adds class with the method name. This allows for
 * the Constructor to be called like a function and lose
 * the reference when we are done.
 *
 * @final
 * @version 0.1
 */
class AltMailer_Setup_Hooks
{

	/**
	 * PHP4 style Constructor
	 *
	 * Calls PHP5 style constructor, which does all of the work.
	 *
	 * @version 0.1
	 * @access public
	 * @return AltMailer_Setup_Hooks
	 */
	function AltMailer_Setup_Hooks()
	{
		$this->__construct();
	}

	/**
	 * PHP5 style Constructor
	 *
	 * Hooks the object static methods into the respective actions.
	 *
	 * @version 0.1
	 * @access public
	 * @return AltMailer_Setup_Hooks
	 */
	function __construct()
	{
		add_action('admin_head', array(__CLASS__, 'init'));
	}

	/**
	 * Setups whether the plugin needs to be installed/upgraded or not.
	 *
	 * Called on activation of the plugin.
	 *
	 * @static
	 * @version 0.1
	 */
	function activation()
	{
		$options = get_option('altmailer_options');

		if( false === $options )
		{
			echo "Installing";
			new AltMailer_Setup_Installer(); // Install
		}
		else if( $options['version'] != ALTMAILER_VERSION )
		{
			new AltMailer_Setup_Installer(ALTMAILER_VERSION, true); // Upgrade
		}
	}

	/**
	 * Will uninstall, if user requests it.
	 *
	 * Called on deactivation of the plugin.
	 *
	 * @static
	 * @version 0.1
	 */
	function deactivation()
	{
		$options = get_option('altmailer_options');
		
		if( is_array($options) && true === $options['uninstall'] )
		{
			new AltMailer_Setup_Uninstaller(); // Uninstall
		}
	}

	/**
	 * Sets up the administration page hook.
	 *
	 * Called when WordPress is initialized.
	 *
	 * @static
	 * @version 0.1
	 */
	function init()
	{
		if( is_admin() ) 
			add_submenu_page('plugins.php', 'AltMailer', 'AltMailer', 'manage_options', ALTMAILERPATH . '/admin/altmailer.php');
	}
}

/**
 * Creates the options for which the plugin will use during
 * execution and allow for changes to be made in the
 * administration.
 *
 * @final
 * @version 0.1
 */
class AltMailer_Setup_Installer
{

	/**
	 * Holds the AltMailer Version Number
	 * @access private
	 * @var float
	 * @version 0.1
	 */
	var $version;

	/**
	 * Holds the AltMailer Version Number
	 * @access private
	 * @var float
	 * @version 0.1
	 */
	var $installed;

	/**
	 * PHP4 style Constructor
	 *
	 * Calls PHP5 style constructor, which does all of the work.
	 *
	 * @version 0.1
	 * @access public
	 * @param bool|float $version false, if installing, else version number
	 * @return AltMailer_Setup_Installer
	 */
	function AltMailer_Setup_Installer($version=0.1, $installed=false)
	{
		$this->__construct($version, $installed);
	}

	/**
	 * PHP5 style Constructor
	 *
	 * Sets up the version property and calls install/upgrade
	 * execution path.
	 *
	 * @version 0.1
	 * @access public
	 * @param bool|float $version false, if installing, else version number
	 * @return AltMailer_Setup_Installer
	 */
	function __construct($version=0.1, $installed=false)
	{
		$this->version = $version;
		$this->installed = $installed;

		$this->runVersion01();
	}

	/**
	 * Installs version 0.1 of AltMailer
	 *
	 * Checks the currently installed version and will skip the version
	 * setup if the system is already installed. This is the first
	 * version, so this will always be installed, so there is no need
	 * to compare versions.
	 *
	 * @version 0.1
	 * @access private
	 */
	function runVersion01()
	{
		if( true === $this->installed )
			return false;

		$options = array();
		$options['version'] = '0.1';
		$options['timeout'] = 2;

		// Should we remove the altmailer_options when deactivating?
		$options['uninstall'] = false;

		// Should not be disabled to use as a fail safe.
		$options['use_mail'] = true;

		// SMTP options, available under Windows only.
		$options['use_smtp'] = true;
		$options['smtp'] = @ini_get('SMTP');
		$options['smtp_port'] = @ini_get('smtp_port');
		$options['smtp_encryption'] = 0;
		$options['smtp_username'] = '';
		$options['smtp_password'] = '';

		// Sendmail options, to use when sendmail is installed or when using Linux.
		$options['use_sendmail'] = true;
		$options['sendmail_autodetect'] = false;
		$options['sendmail_from'] = @ini_get('sendmail_from');
		$options['sendmail_path'] = @ini_get('sendmail_path');

		add_option('altmailer_options', $options, null, 'yes');
	}

}

/**
 * Creates the uninstaller for which the plugin will use to
 * clean up the variables created to manage the plugin options.
 *
 * @final
 * @version 0.1
 */
class AltMailer_Setup_Uninstaller
{

	/**
	 * PHP4 style Constructor
	 *
	 * Calls PHP5 style constructor, which does all of the work.
	 *
	 * @version 0.1
	 * @access public
	 * @return AltMailer_Setup_Uninstaller
	 */
	function AltMailer_Setup_Uninstaller()
	{
		$this->__construct();
	}

	/**
	 * PHP5 style Constructor
	 *
	 * Removes everything that was setup by AltMailer
	 *
	 * @version 0.1
	 * @access public
	 * @return AltMailer_Setup_Uninstaller
	 */
	function __construct()
	{
		delete_option('altmailer_options');
	}

}