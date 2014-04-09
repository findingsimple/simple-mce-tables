<?php 
/*
Plugin Name: Simple MCE Tables
Plugin URI: http://plugins.findingsimple.com
Description: Adds tiny mce table buttons to the WordPress Editor
Version: 2.0
Author: Finding Simple
Author URI: http://findingsimple.com
License: GPL2
*/
/*
Copyright 2008 - 2014  Finding Simple  (email : plugins@findingsimple.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! class_exists( 'Simple_MCE_Tables' ) ) {

	/**
	 * So that themes and other plugins can customise the text domain, the Simple_MCE_Tables
	 * should not be initialized until after the plugins_loaded and after_setup_theme hooks.
	 * However, it also needs to run early on the init hook.
	 *
	 * @author Jason Conroy <jason@findingsimple.com>
	 * @package Simple MCE Tables
	 * @since 1.0
	 */
	function initialize_mce_tables(){
		Simple_MCE_Tables::init();
	}
	add_action( 'init', 'initialize_mce_tables', -1 ); 

	/**
	 * Plugin Main Class.
	 *
	 * @package Simple MCE Tables
	 */
	class Simple_MCE_Tables {

		/**
		 * Initialize the class
		 */
		public static function init() {

			add_action('admin_init', array( __CLASS__, 'table_addbuttons') );

		}

		/**
		 * Hook into WP filters to add the table MCE buttons 
		 */
		public static function table_addbuttons() {

			global $tinymce_version;

			if ( version_compare( $tinymce_version, '400', '<' ) ) {

				add_filter('mce_external_plugins', array( __CLASS__, 'add_table_plugin_3') );
				
				//Put buttons on the third line of the editor
				add_filter('mce_buttons_3', array( __CLASS__, 'register_table_button_3' ) );

			} else {

				add_filter('mce_external_plugins', array( __CLASS__, 'add_table_plugin_4') );
				
				//Put buttons on the second line of the editor
				add_filter('mce_buttons_2', array( __CLASS__, 'register_table_button_4' ) );

			}
						   
		}

		/**
		 * Register table controls
		 */	 
		public static function register_table_button_3($buttons) {
		
			array_push($buttons, "tablecontrols");
			return $buttons;
		   
		}

		/**
		 * Add TinyMCE table plugin
		 */	 
		public static function add_table_plugin_3($plugin_array) {
			
			$plugin_array['table'] = self::get_url( '/table-3/editor_plugin.js', __FILE__ );
			return $plugin_array;
		
		}


		/**
		 * Register table controls
		 */	 
		public static function register_table_button_4($buttons) {
		
			// in case someone is manipulating other buttons, drop table controls at the end of the row
			if ( ! $pos = array_search( 'undo', $buttons ) ) {
				array_push( $buttons, 'table' );
				return $buttons;
			}

			return array_merge( array_slice( $buttons, 0, $pos ), array( 'table' ), array_slice( $buttons, $pos ) );

		}

		/**
		 * Add TinyMCE table plugin
		 */	 
		public static function add_table_plugin_4($plugin_array) {
			
			$plugin_array['table'] = self::get_url( '/table-4/plugin.min.js', __FILE__ );
			return $plugin_array;
		
		}
		
		/**
		 * Helper function to get the URL of a given file. 
		 * 
		 * As this plugin may be used as both a stand-alone plugin and as a submodule of 
		 * a theme, the standard WP API functions, like plugins_url() can not be used. 
		 */
		public static function get_url( $file ) {

			// Get the path of this file after the WP content directory
			$post_content_path = substr( dirname( str_replace('\\','/',__FILE__) ), strpos( __FILE__, basename( WP_CONTENT_DIR ) ) + strlen( basename( WP_CONTENT_DIR ) ) );

			// Return a content URL for this path & the specified file
			return content_url( $post_content_path . $file );
		}	
		
	}
 
}