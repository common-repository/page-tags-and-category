<?php
/*
Plugin Name: Page Tags And Category
Plugin URI: https://www.digitaladquest.com/wordpress-plugins/
Description: This plugin adds category and tags functionality for WordPress pages. WordPress by default do not have this functionality. Also from settings one can choose to enable only tags or category for pages.
Version: 1.0
Author: Digital Ad Quest
Author URI: https://www.digitaladquest.com/
License: GPLv2 or later
Copyright 2017 Digital Ad Quest

This program is free software:
you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation,
either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program.
If not, see http://www.gnu.org/licenses/gpl-2.0.html
*/

// Adding Menu
add_action('admin_menu', 'daq_ptac_add_menu');
function daq_ptac_add_menu() {
    $page = add_menu_page('Tags &amp; Category', 'Tags &amp; Category', 'administrator', 'tags-categories', 'daq_ptac_menu_function');
}


// Enque CSS
function daq_ptac_custom_wp_admin_style($hook) {
        // Load only on ?page=mypluginname
        if($hook != 'toplevel_page_tags-categories') {
                return;
        }
        wp_enqueue_style( 'custom_wp_admin_css', plugins_url('css/style.css', __FILE__) );
}
add_action( 'admin_enqueue_scripts', 'daq_ptac_custom_wp_admin_style' );


// Add settings link on plugin page
function daq_ptac_plugin_settings_link($links) { 
  $settings_link = '<a href="admin.php?page=tags-categories">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'daq_ptac_plugin_settings_link' );


// Default Options
register_activation_hook( __FILE__, 'daq_ptac_activate' );

function daq_ptac_activate() {
	add_option('daq_tags_enable','1');
	add_option('daq_category_enable','1');	
}


// Display Default Tag Options
function daq_ptac_verify_tags_enable() {
	$enable = get_option('daq_tags_enable');
	
	if ($enable == 1) {
		echo "checked=\"checked\"";
	}
}

function daq_ptac_verify_tags_disable() {
	$enable = get_option('daq_tags_enable');
	
	if ($enable == 0) {
		echo "checked=\"checked\"";
	}
}


// Display Default Category Options
function daq_ptac_verify_category_enable() {
	$enable = get_option('daq_category_enable');
	
	if ($enable == 1) {
		echo "checked=\"checked\"";
	}
}

function daq_ptac_verify_category_disable() {
	$enable = get_option('daq_category_enable');
	
	if ($enable == 0) {
		echo "checked=\"checked\"";
	}
}


// Save Settings
add_action('admin_init', 'daq_ptac_reg_function' );
function daq_ptac_reg_function() {
	register_setting( 'daq-settings-group', 'daq_tags_enable' );
	register_setting( 'daq-settings-group', 'daq_category_enable' );
}


// Enable or Disable Tags Function
$tags_enable = get_option('daq_tags_enable');

if ($tags_enable == 1) :
	// add tag and category support to pages
	function daq_ptac_tags_support_all() {
	  register_taxonomy_for_object_type('post_tag', 'page');  
	}
	
	// ensure all tags and category are included in queries
	function daq_ptac_tags_support_query($wp_query) {
	  if ($wp_query->get('tag')) $wp_query->set('post_type', 'any');
	}
	
	// tag and category hooks
	add_action('init', 'daq_ptac_tags_support_all');
	add_action('pre_get_posts', 'daq_ptac_tags_support_query');	
endif;


// Enable or Disable Category Function
$category_enable = get_option('daq_category_enable');

if ($category_enable == 1) :
	// add tag and category support to pages
	function daq_ptac_category_support_all() {
	  register_taxonomy_for_object_type('category', 'page'); 
	}
	
	// ensure all tags and category are included in queries
	function daq_ptac_category_support_query($wp_query) {
	  if ($wp_query->get('category_name')) $wp_query->set('post_type', 'any');
	}
	
	// tag and category hooks
	add_action('init', 'daq_ptac_category_support_all');
	add_action('pre_get_posts', 'daq_ptac_category_support_query');	
endif;





// FEED TO WP DASHBOARD
add_action( 'wp_dashboard_setup', 'daq_ptac_plugin_setup_function' );
function daq_ptac_plugin_setup_function() {
    add_meta_box( 'daq_ptac_plugin_dashboard_custom_feed', 'Plugin Support', 'daq_ptac_plugin_widget_function', 'dashboard', 'side', 'high' );
}
function daq_ptac_plugin_widget_function() {
    
	echo '<div class="daq-ptac-rss-widget" style="max-height:300px; overflow-y:scroll"><a href="https://www.digitaladquest.com/"><img src="' . plugins_url( 'images/feed-logo.png', __FILE__ ) . '" ></a><br>Thank you for using our plugin <strong>Page Tags & Category</strong>! We hope the plugin works as stated and you liked this plugin, for any support or feedback, Please <a href="https://www.digitaladquest.com/" target="_blank">visit our website.</a><h3><br><strong>Also You May Check Our Latest News &amp; Blog Updates Below:</strong></h3>';
		wp_widget_rss_output(array(
		// CHANGE THE URL BELOW TO THAT OF YOUR FEED
		'url' => 'http://feeds.feedburner.com/DigitalAdQuest',
		// CHANGE 'OrganicWeb News' BELOW TO THE NAME OF YOUR WIDGET
		'title' => 'Digital Ad Quest Updates',
		// CHANGE '2' TO THE NUMBER OF FEED ITEMS YOU WANT SHOWING
		'items' => 3,
		// CHANGE TO '0' IF YOU ONLY WANT THE TITLE TO SHOW
		'show_summary' => 1,
		// CHANGE TO '1' TO SHOW THE AUTHOR NAME
		'show_author' => 0,
		// CHANGE TO '1' TO SHOW THE PUBLISH DATE
		'show_date' => 1
		));
	echo "</div>";
}



// Setting Form For Admin
function daq_ptac_menu_function() {
?>

<div class="wrap">
<h1>Add Tags & Category To Pages</h1>
<div class="daq-ptac-dashboard">
<h1>Settings For Tags & Category To Pages</h1>
	<!-- Display Saved Message-->
 	<?php if( isset($_GET['settings-updated']) ) { ?>
	<div id="message" class="updated settings-error notice is-dismissible">
	<p><strong><?php _e('Settings saved.') ?></strong></p>
	</div>
	<?php } ?>
	
<form method="post" action="options.php">
    <?php settings_fields( 'daq-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row" class="daq-ptac-th-margin">Tags For Pages</th>
        <td>
        	<label> 
        		<input type="radio" value="1" <?php daq_ptac_verify_tags_enable(); ?> name="daq_tags_enable">
        		Enable
        	</label>
        	<br><br>
        	<label>
        		<input type="radio" value="0" <?php daq_ptac_verify_tags_disable(); ?> name="daq_tags_enable">
        		Disable
        	</label>
        </td>
        </tr>
 
        <tr valign="top">
        <th scope="row" class="daq-ptac-th-margin">Category For Pages</th>
        <td>
        	<label> 
        		<input type="radio" value="1" <?php daq_ptac_verify_category_enable(); ?> name="daq_category_enable">
        		Enable
        	</label>
        	<br><br>
        	<label>
        		<input type="radio" value="0" <?php daq_ptac_verify_category_disable(); ?> name="daq_category_enable">
        		Disable
        	</label>
        </td>
        </tr>
    </table>
 
    <p class="daq-ptac-submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
	
 
</form>
</div>
<div class="daq-ptac-sidebar">
<?php
echo '<a href="https://www.digitaladquest.com/"><img src="' . plugins_url( 'images/logo.png', __FILE__ ) . '" ></a> ';
?>
<p class="daq-ptac-text-justify"><strong>Thank you for using our plugin!</strong> We hope the plugin works as stated and you liked this plugin, for any support or feedback, Please <a href="https://www.digitaladquest.com/" target="_blank">visit our website.</a></p>
<a href="https://www.digitaladquest.com/wordpress-plugins/" class="button daq-ptac-width-100">View Our Other Plugins</a><br /><br /><a href="https://www.digitaladquest.com/wordpress-theme/" class="button daq-ptac-width-100">Download WordPress Themes</a><br /><br /><a href="https://www.digitaladquest.com/" class="button-primary daq-ptac-width-100">Visit Our Website</a>
</div>
</div>
<?php } ?>