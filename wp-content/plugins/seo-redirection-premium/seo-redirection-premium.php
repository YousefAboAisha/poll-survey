<?php
/*
  Plugin Name: SEO Redirection Premium
  Plugin URI: https://www.wp-buy.com/product/seo-redirection-premium-wordpress-plugin/
  Description: Manage all your 301 redirects and monitor 404 errors and more ..
  Version: 5.1
  Author: wp-buy
  Author URI: https://www.wp-buy.com
  Text Domain: wsr
 */
//define('ALLOW_UNFILTERED_UPLOADS', true);
define('SR_PLUGIN_NAME', 'SEO Redirection Premium');
define('SR_PLUGINS_URL', plugins_url() . '/seo-redirection-premium/');

require_once "SRP_PLUGIN.php";
require_once "custom/installer.php";
require_once "custom/lib/cf.SR_redirect_cache.class.php";
require_once "custom/lib/cf.SR_database.class.php";
require_once "custom/lib/cf.SR_option_manager.class.php";
require_once "custom/lib/cf.SR_redirect_manager.class.php";
require_once "custom/lib/cf.SR_plugin_menus.class.php";
require_once "custom/lib/cf.SR_test_regex.class.php";
require_once "custom/lib/cf.SR_custom_app.class.php";

function SRP_buddy_press_check_locking()
{

  // ensure is_plugin_active() exists (not on frontend)
  if( !function_exists('is_plugin_active') ) {

    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

  }
  if (is_plugin_active('lock-my-bp/bp-lock.php')) {
      $bplock_general_settings = get_option('bplock_general_settings');
      if ($bplock_general_settings && isset($bplock_general_settings) && is_array($bplock_general_settings)) {

          $get_c_id = get_the_ID();
          if (isset($bplock_general_settings['locked_pages'])) {
              if (in_array($get_c_id, $bplock_general_settings['locked_pages']) && !is_user_logged_in()) {
                  return true;
              }
          }
      }
  }
  return false;
}

SRP_PLUGIN::init('wp-seo-redirection-group', __FILE__);

SR_plugin_menus::init();
SR_plugin_menus::hook_menus();

SR_custome_app::hook_scripts();

seo_redirection_installer::set_version("4.8");
seo_redirection_installer::hook_installer();

SR_redirect_manager::hook_redirection();

function SR_multiple_plugin_activate() {
    global $wpdb;
	
// ensure is_plugin_active() exists (not on frontend)
    if( !function_exists('is_plugin_active_for_network') ) {

            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    }

		
    if (is_multisite()) {
        if (is_plugin_active_for_network(__FILE__)) {
            $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blogids as $blog_id) {
                switch_to_blog($blog_id);
            }
        }
    }
}
register_activation_hook(__FILE__, 'SR_multiple_plugin_activate');

//---------------------------------------------------------------------------------------------
//Add plugin settings link to Plugins page
//---------------------------------------------------------------------------------------------
function SR_plugin_plugin_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=seo-redirection-premium.php">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
    return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'SR_plugin_plugin_add_settings_link' );

require 'plugin-update-checker/plugin-update-checker.php';
$MyUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://www.wp-buy.com/wp-update-server-php7/?action=get_metadata&slug=seo-redirection-premium', //Metadata URL.
	__FILE__, //Full path to the main plugin file.
	'seo-redirection-premium' //Plugin slug. Usually it's the same as the name of the directory.
);

add_filter('pre_get_table_charset', function($charset, $table) {
    global $table_prefix;
    $table_name = $table_prefix . 'WP_SEO_Redirection';
    if($table == $table_name){
        return 'utf8mb4';
    }

}, 10, 2);

// Add Table Indexes By : Ibrahim Shatat
function SRP_UpdateIndexes() {
    
    global $wpdb;
    
    $Update_Tables_Indexes = get_option('Update_Tables_Indexes');
    if (!is_array($Update_Tables_Indexes)) {
        $Update_Tables_Indexes = array(
            "q1" => false,
            "q2" => false,
            "q3" => false,
            "q4" => false
        );
    }
    
    $table_name = $wpdb->prefix . 'WP_SEO_Redirection';

    if (!$Update_Tables_Indexes["q1"]) {
        $sql = "ALTER TABLE $table_name DROP INDEX `redirect_from`;";
        $r = $wpdb->query($sql);
        $Update_Tables_Indexes["q1"] = ($r === false) ? false : true;
    }
    
    // Add a new index
    if (!$Update_Tables_Indexes["q2"]) {
        $sql = "ALTER TABLE $table_name ADD INDEX redirect_from (`redirect_from`(200), `cat`, `blog`);";
        $r = $wpdb->query($sql);
        $Update_Tables_Indexes["q2"] = ($r === false) ? false : true;
    }
    
    $table_name = $wpdb->prefix . 'WP_SEO_404_links';
    
    if (!$Update_Tables_Indexes["q3"]) {
        $sql = "ALTER TABLE $table_name DROP INDEX `link`;";
        $r = $wpdb->query($sql);
        $Update_Tables_Indexes["q3"] = ($r === false) ? false : true;
    }
    
    // Add a new index
    if (!$Update_Tables_Indexes["q4"]) {
        $sql = "ALTER TABLE $table_name ADD INDEX link (`link`(200), `blog`);";
        $r = $wpdb->query($sql);
        $Update_Tables_Indexes["q4"] = ($r === false) ? false : true;
    }
    
    update_option('Update_Tables_Indexes', $Update_Tables_Indexes);
}
if (in_array(false,get_option('Update_Tables_Indexes'))) {
    add_action('init', 'SRP_UpdateIndexes');
}