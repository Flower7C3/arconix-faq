<?php
/**
 * Plugin Name: Arconix FAQ
 * Plugin URI: https://github.com/Flower7C3/arconix-faq
 * Org Plugin URI: http://arconixpc.com/plugins/arconix-faq
 * Description: Plugin to handle the display of FAQs
 *
 * Version: 1.4.3
 *
 * Author: Bartłomiej Jakub Kwiatek
 * Author URI: http://kwiatek.pro
 * Org Author: John Gardner
 * Org Author URI: http://arconixpc.com/
 *
 * License: GNU General Public License v2.0
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */


require_once( plugin_dir_path( __FILE__ ) . 'includes/class-arconix-faq.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/class-arconix-faq-admin.php' );

new Arconix_FAQ_Admin;
