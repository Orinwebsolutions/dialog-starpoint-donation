<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/Orinwebsolutions
 * @since      1.0.0
 *
 * @package    Dialog_Starpoint_Donation
 * @subpackage Dialog_Starpoint_Donation/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h1> <?php echo  'Welcome to settings page.'; ?> </h1>
<form method="POST" action="options.php">
<?php
settings_fields( 'starpoint-donation-setting' );
do_settings_sections( 'starpoint-donation-setting' );
submit_button();
?>
</form>