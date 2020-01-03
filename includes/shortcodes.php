<?php 
/**
 * Plugin Name: Scoutbook  Gear
 * Plugin URI: http://troop351.org/plugins/scoutbook-gear
 * Description:
 * Version: 0.1
 * Author: Phil Newman
 * Author URI: http://getyourphil.net
 * License: GPL3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.en.html
 **/
// Add Shortcode
function ptn_scoutbook_scanner_app_link() {
    $scoutbook_gear_plugin_dir =  plugins_url('scoutbook-gear');
    $scoutbook_gear_plugin_dir  = str_replace('http://','https://', $scoutbook_gear_plugin_dir );
    $ptn_scoutbook_scanner_app_link = '<a href="'.$scoutbook_gear_plugin_dir.'/scan_app/scan.html'.'">Scanner App</a>';
        return $ptn_scoutbook_scanner_app_link;
}
add_shortcode( 'gear_scanner_app', 'ptn_scoutbook_scanner_app_link' );