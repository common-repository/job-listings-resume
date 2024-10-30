<?php
/**
 * Plugin Name: Job Listings Resume
 * Plugin URI:        http://nootheme.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           0.1.0
 * Author:            NooTheme
 * Author URI:        http://nootheme.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       job-listings-resume
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Job_Listings_Resume' ) ):
	class Job_Listings_Resume {
		/**
		 * Job_Listings_Resume constructor.
		 */
		public function __construct() {

			define( 'JLT_RESUME_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			define( 'JLT_RESUME_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			define( 'JLT_RESUME_PLUGIN_TEMPLATE_DIR', JLT_RESUME_PLUGIN_DIR . 'templates/' );

			add_action( 'init', array( $this, 'load_plugin_textdomain' ), 0 );
			// Includes
			$this->init();

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		public function init() {
			require JLT_RESUME_PLUGIN_DIR . 'functions/loader.php';
			require JLT_RESUME_PLUGIN_DIR . 'includes/loader.php';
			require JLT_RESUME_PLUGIN_DIR . 'classes/loader.php';
		}

		public function load_plugin_textdomain() {

			$locale = apply_filters( 'plugin_locale', get_locale(), 'job-listings-resume' );

			load_textdomain( 'job-listings-resume', WP_LANG_DIR . "/job-listings-resume/job-listings-resume-$locale.mo" );
			load_plugin_textdomain( 'job-listings-resume', false, plugin_basename( dirname( __FILE__ ) . "/languages" ) );
		}

		public function enqueue_scripts() {

			$jlt_resume = array(
				'cfm_remove_resume_detail' => __( 'Are you sure you want to remove this item?', 'job-listings-resume' ),
			);
			wp_register_script( 'jlt-resume', plugin_dir_url( __FILE__ ) . 'assets/frontend/js/resume.js', array( 'jquery' ), '1.0.0', true );
			wp_localize_script( 'jlt-resume', 'JLT_Resume', $jlt_resume );
			wp_enqueue_script( 'jlt-resume' );
		}

		public function enqueue_style() {
			wp_enqueue_style( 'jlt-resume', plugin_dir_url( __FILE__ ) . 'assets/frontend/css/resume.css', array(), '1.0.0', 'all' );
		}

		public function admin_enqueue_scripts( $hook ) {
			if ( $hook == 'post-new.php' || $hook == 'post.php' || $hook == 'edit.php' ) {
				$post_type = isset( $_GET[ 'post_type' ] ) ? $_GET[ 'post_type' ] : get_post_type();

				if ( 'resume' === $post_type ) {
					wp_enqueue_style( 'jlt-resume-admin', plugin_dir_url( __FILE__ ) . 'assets/admin/css/admin-resume.css', array(), '1.0.0', 'all' );
				}
			}
		}
	}
endif;

function run_job_listings_resume() {
	new Job_Listings_Resume();
}

add_action( 'job_listings_loaded', 'run_job_listings_resume' );