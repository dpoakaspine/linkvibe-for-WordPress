<?php
//defined( 'ABSPATH' ) or die( 'Nope! );

/*
Plugin Name: linkvibe for WordPress
Description: Adds an awesome link tracker and site analyzing tool to your WordPress site.
Version:     1.3
Author:      Stefan Böttcher
Text Domain: linkvibe
*/

// Create the function to output the contents of our Dashboard Widget
function iframe_dashboard_widget_function() {
    // Display whatever it is you want to show
    echo '<iframe src="http://linkvibe.de/?appid='.str_replace(array('http://','https://','//'),'',get_site_url()).'&domain='.get_site_url().'&bg=ffffff" width="100%" height="1000px" frameBorder="0">Browser not compatible.</iframe>';
}

// Create the function use in the action hook
function add_linkvibe_dashboard_widgets() {
    wp_add_dashboard_widget('iframe_dashboard_widget', '<a href="admin.php?page=linkvibe">'.__('Settings').'</a> &nbsp;&nbsp;Awesome Link Tracking with linkvibe', 'iframe_dashboard_widget_function');
}

// Hook into the 'wp_dashboard_setup' action to register our other functions
add_action('wp_dashboard_setup', 'add_linkvibe_dashboard_widgets' );


add_action('wp_footer', 'add_linkvibe');

function add_linkvibe() {

echo '
<script src="//linkvibe.de/embed.js"></script>
<script type="text/javascript">
   linkvibe.init(["'.str_replace(array('http://','https://','//'),'',get_site_url()).'"]);
   linkvibe.go();
</script>
';

}

add_action('admin_menu', 'register_my_custom_submenu_page');

function register_my_custom_submenu_page() {
	add_menu_page( 'linkvibe '.__('Settings'), 'linkvibe', 'manage_options', 'linkvibe', 'options_page_fn', 'dashicons-share-alt', 3 );

}

function linkvibe_menu_page_callback() {



}


add_action('admin_init', 'sampleoptions_init_fn' );
// Register our settings. Add the settings section, and settings fields
function sampleoptions_init_fn(){
	register_setting('plugin_options', 'plugin_options', 'plugin_options_validate' );
	add_settings_section('main_section', 'Please see the linkvibe widget in action on <a href="index.php">your dashboard</a>', 'section_text_fn', __FILE__);
	add_settings_field('plugin_text_string', 'API-Key', 'setting_string_fn', __FILE__, 'main_section');
	//add_settings_field('plugin_textarea_string', 'Large Textbox!', 'setting_textarea_fn', __FILE__, 'main_section');
	//add_settings_field('plugin_chk2', 'track all links (even untagged)', 'setting_chk2_fn', __FILE__, 'main_section');
	add_settings_field('radio_buttons', 'track all links', 'setting_radio_yesno', __FILE__, 'main_section');
	//add_settings_field('plugin_chk1', 'Restore Defaults Upon Reactivation?', 'setting_chk1_fn', __FILE__, 'main_section');
}

// Display the admin options page
function options_page_fn() {


  echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
    echo '<h2><span class="dashicons dashicons-share-alt" style="font-size:1.4em"></span>&nbsp; linkvibe '.__('Settings').'</h2>';

  echo '</div>';
?>
	<div class="wrap">

		<form action="options.php" method="post">
					<?php
if ( function_exists('wp_nonce_field') )
	wp_nonce_field('plugin-name-action_' . "yep");
?>
		<?php settings_fields('plugin_options'); ?>
		<?php do_settings_sections(__FILE__); ?>
		<p class="submit">
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
		</p>
		</form>
	</div>
  <iframe src="<?php echo 'http://linkvibe.de/?appid='.str_replace(array('http://','https://','//'),'',get_site_url()).'&domain='.get_site_url().'&bg=f1f1f1;' ?>" style="width:100%;min-height:800px;"></iframe>
<?php
}


// Validate user data for some/all of your input fields
function plugin_options_validate($input) {
	// Check our textbox option field contains no HTML tags - if so strip them out
	$input['text_string'] =  wp_filter_nohtml_kses($input['text_string']);
	return $input; // return validated input
}

// TEXTAREA - Name: plugin_options[text_area]
function setting_textarea_fn() {
	$options = get_option('plugin_options');
	echo "<textarea id='plugin_textarea_string' name='plugin_options[text_area]' rows='7' cols='50' type='textarea'>{$options['text_area']}</textarea>";
}
// TEXTBOX - Name: plugin_options[text_string]
function setting_string_fn() {
	$options = get_option('plugin_options');
	echo "<input id='plugin_text_string' auto-complete='off' name='plugin_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
}

// CHECKBOX - Name: plugin_options[chkbox1]
function setting_chk1_fn() {
	$options = get_option('plugin_options');
	if($options['chkbox1']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='plugin_chk1' name='plugin_options[chkbox1]' type='checkbox' />";
}
// CHECKBOX - Name: plugin_options[chkbox2]
function setting_chk2_fn() {
	$options = get_option('plugin_options');
	if($options['chkbox2']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='plugin_chk2' name='plugin_options[chkbox2]' type='checkbox' />";
}
// RADIO-BUTTON - Name: plugin_options[option_set1]
function setting_radio_yesno() {
	$options = get_option('plugin_options');
	$items = array("yes", "no");
	foreach($items as $item) {
		$checked = ($options['option_set1']==$item) ? ' checked="checked" ' : '';
    if(empty($options['option_set1']) && $item=="yes") { $checked = 'checked="checked"'; }
		echo "<label><input ".$checked." value='$item' name='plugin_options[option_set1]' type='radio' /> $item</label><br />";
	}
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function wpet_validate_options($input) {
	// Sanitize textarea input (strip html tags, and escape characters)
	//$input['textarea_one'] = wp_filter_nohtml_kses($input['textarea_one']);
	//$input['textarea_two'] = wp_filter_nohtml_kses($input['textarea_two']);
	//$input['textarea_three'] = wp_filter_nohtml_kses($input['textarea_three']);
	return $input;
}

require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://raw.githubusercontent.com/dpoakaspine/linkvibe-for-WordPress/master/version.json',
    __FILE__
);

?>
