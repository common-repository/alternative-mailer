=== Alternative Mailer - Swift Mailer ===
Contributors: darkdragon, jacobsantos
Donate link: http://sourceforge.net/project/project_donations.php?group_id=170045
Tags: wp_mail, email, smtp, swift mailer, admin
Requires at least: 2.0.4
Tested up to: 2.5.0
Stable tag: 0.2.1

Use an alternative mailer solution, other than PHPMailer in WordPress installations. 

== Description ==

This package includes Swift Mailer for PHP 4 and PHP 5 versions. It enhances the option set over Shiftthis SMTP plugin and allows for Sendmail and PHP native mail() solutions with SMTP. So everything you can do with Shiftthis, you can do with this plugin plus more.

This plugin does not automatically handle attachments like Shiftthis SMTP plugin does, however this plugin works more like the built-in plugin that WordPress has. You should be able to access Gmail accounts just like Shiftthis plugin.

If you send email using the HTML content type, then this plugin will help you out and prevent the email from being picked up as spam by sending HTML content email with both HTML and plaintext headers. This allows email clients which don't support HTML to still read the email, while still benefitting those that do have email clients that accept HTML. You can not disable this feature.

The donation link is to the author of Swift Mailer, so please donate to the coder.

== Installation ==

1. Upload the 'altmailer' folder to the 'wp-content\plugins' directory
1. Activate the plugin titled "Alternative Mailer" through the "Plugins" menu in WordPress
1. Once you activation the plugin, everything that is configured for SMTP and/or sendmail will automatically be pulled in and used. 
1. If you want to change options for your own settings, then you can do so at the 'Altmailer' tab under 'Plugins'.

== Screenshots ==

No screenshots.

== Configuration ==
= Plugin Options =

* **Enable PHP Mail()** - Use PHP mail() as a fall back, can be disabled, but you must have working configurations for either sendmail or SMTP or you won't send any mail.
* **Enable Sendmail** - Enables/disables sendmail method. Disable if you know for sure that your host doesn't support or have another mailing agent installed.
* **Enable SMTP** - Enables/disables SMTP method. Disable if you know for sure that your host doesn't support SMTP method or you can't or don't want to send mail using SMTP.
* **Uninstall** - Uninstalls the plugin options, only check if you are no going to use the plugin because you will lose all of your settings when you deactivate the plugin. Please, do not remove the plugin until you deactivate, because that is only time that the plugin will be uninstalled. if you want to remove then do so after you deactivate the plugin.
* **Timeout** - Set the timeout between failure. Defaults to 2 seconds, but that might be too high. If you are sensing a slow down, then lower this value to either 1 or 0. If you are finding that email is not being sent, then raise this value.

= SMTP Options =

* **SMTP Host** - Set the email SMTP server (smtp.example.com)
* **SMTP Port** - Set the SMTP connection port.
* **SMTP Encryption** - Set the encryption of the connection. Only supports SSL and TLS. (Implementation incomplete.)
* **SMTP Username** - Optional. If needed then set the username for the connection.
* **SMTP Password** - Optional. If needed then set the password for the connection.

= Sendmail Options =

* **Use Autodetect** - Try to find sendmail in common installation paths. Will slow down the sending of mail.
* **Sendmail From Address** - The email address to use as the "From:" address, when none is given. This option is required, when sending email using this method. The option page will first use the setting in PHP INI, if it exists or it will use the administration email when WordPress was installed. The latter is not saved until you go to the options page and save the page (it will appear in the textbox). If you use the adminstrator email, it will not change when you change the email in another location. You will have to change the setting on the Alternative Mailer settings page as well or to any other email address.
* **Sendmail Path** - The path to sendmail or any other mailing agent. If using sendmail, then this option is required.

== Changelog ==

0.2 - Completed the Alternative Mailer settings page.
0.1 - Initial stable release. Admin implementation is incomplete, but the sending of email is functional equivalent to PHPMailer and WordPress built-in wp_mail() function.

== TODO ==

1. Finish implementing the encryption levels.
1. Allow for specifying which send mail method is first, currently if sendmail is enabled it is used first, then SMTP, and finally mail(). Sometimes, SMTP should be first for Gmail and have sendmail as a fall back before going to mail().
1. Implement automatic attachment detection support and apply it as a part.
1. For SMTP, implement setting and add a feature to use POP before SMTP. Some SMTP servers might require that POP authentication is performed first before the SMTP connection will be complete.
1. Allow testing the sending of email, using the settings.

== Frequently Asked Questions ==

There are no questions currently. However, if you need help, then please send an email to wordpress [at] [My last name][First letter of my firstname].name and I'll see what I can do.

It should be mentioned that this plugin has not actually been tested with versions under 2.3.2, so use at your own risk. With later versions, I plan on testing the plugin. I do know that you need at least 2.0.4, because of nonce function usage. The plugin won't work without them.

This is also early beta software, which works for me, however, it might not work you. If it doesn't work, then do contact me and I'll fix any problems in the next release leading up to the stable 1.0 release.