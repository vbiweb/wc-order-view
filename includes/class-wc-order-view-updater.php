<?php

/**
 * Checks the Wordpress Transient for Updates for the GitHub/WordPress Repository
 *
 * @link       https://kgopalkrishna.com
 * @since      1.2.0
 *
 * @package    Wc_Order_View
 * @subpackage Wc_Order_View/includes
 */

/**
 * Fired during plugin update.
 *
 * This class defines all code necessary to run during the plugin's update process.
 *
 * @since      1.2.0
 * @package    Wc_Order_View
 * @subpackage Wc_Order_View/includes
 * @author     K Gopal Krishna <kggopal12@gmail.com>
 */
class Wc_Order_View_Updater {

	/**
	 * The file path passed to the constructor of this class.
	 *
	 * @since    1.2.0
	 * @access   protected
	 * @var      string    $file    The file path passed to the constructor of this class.
	 */
	protected $file;

	/**
	 * Contains the plugin data retrieved from get_plugin_data() method.
	 *
	 * @since    1.2.0
	 * @access   protected
	 * @var      array    $plugin    Contains the plugin data retrieved from get_plugin_data() method.
	 */
	protected $plugin;

	/**
	 * Contains the basename of the plugin retrieved from plugin_basename() method.
	 *
	 * @since    1.2.0
	 * @access   protected
	 * @var      string    $basename    Contains the basename of the plugin retrieved from plugin_basename() method.
	 */
	protected $basename;

	/**
	 * Tracks the plugin's active/inactive status.
	 *
	 * @since    1.2.0
	 * @access   protected
	 * @var      boolean    $active    Tracks the plugin's active/inactive status.
	 */
	protected $active;

	/**
	 * Github Username.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      string    $username    Github Username.
	 */
	private $username;

	/**
	 * Github Repository Name.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      string    $repository    Github Repository Name.
	 */
	private $repository;

	/**
	 * Github Response.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      string    $github_response    Github Response.
	 */
	private $github_response;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.2.0
	 * @param    string    $file     The file path of this plugin.
	 */
	public function __construct( $file ) {

		$this->file = $file;

		add_action( 'admin_init', array( $this, 'set_plugin_properties' ) );

		return $this;

	}

	/**
	 * Retrieving the plugin properties and setting them to the protected properties in this class.
	 *
	 * @since    1.2.0
	 */
	public function set_plugin_properties() {

		$this->plugin   = get_plugin_data( $this->file );
		$this->basename = plugin_basename( $this->file );
		$this->active   = is_plugin_active( $this->basename );

	}

	/**
	 * Sets the GitHub Username property in this class.
	 *
	 * @since    1.2.0
	 * @param    string    $username     The username passed will be set to the corresponding property in this class.
	 */
	public function set_username( $username ) {
	  $this->username = $username;
	}

	/**
	 *  Sets the GitHub Repository Name property in this class.
	 *
	 * @since    1.2.0
	 * @param    string    $repository     The repository name passed will be set to the corresponding property in this class.
	 */
	public function set_repository( $repository ) {
	  $this->repository = $repository;
	}

	/**
	 *  Retrieves the repository information and sets the GitHub Response property in this class.
	 *
	 * @since    1.2.0
	 */
	private function get_repository_info() {
		if ( is_null( $this->github_response ) ) { // Do we have a response?
			$request_uri = sprintf( 'https://api.github.com/repos/%s/%s/releases', $this->username, $this->repository ); // Build URI
			
			$response = json_decode( wp_remote_retrieve_body( wp_remote_get( $request_uri ) ), true ); // Get JSON and parse it
			if( is_array( $response ) ) { // If it is an array
			    $response = current( $response ); // Get the first item
			}
			
			$this->github_response = $response; // Set it to our property  
		}
	}

	/**
	 *  Initializes the updater
	 *
	 * @since    1.2.0
	 */
	public function initialize() {
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'modify_transient' ), 10, 1 );
		add_filter( 'plugins_api', array( $this, 'plugin_popup' ), 10, 3);
		add_filter( 'upgrader_post_install', array( $this, 'after_install' ), 10, 3 );
	}


	/**
	 *  Updates the WordPress Transient based on changes detected in GitHub Repository
	 *
	 * @since    1.2.0
	 * @param    string    $transient     The transient option passed by WordPress
	 */
	public function modify_transient( $transient ) {
		if( property_exists( $transient, 'checked') ) { // Check if transient has a checked property
		    if( $checked = $transient->checked ) { // Did WordPress check for updates?
		      	$this->get_repository_info(); // Get the repo info
		      	$out_of_date = version_compare( $this->github_response['tag_name'], $checked[$this->basename], 'gt' ); // Check if we're out of date
		      	if( $out_of_date ) {
		        	$new_files = $this->github_response['zipball_url']; // Get the ZIP
		        	$slug = current( explode('/', $this->basename ) ); // Create valid slug
		        	$plugin = array( // setup our plugin info
		          		'url' => $this->plugin["PluginURI"],
		          		'slug' => $slug,
		          		'package' => $new_files,
		          		'new_version' => $this->github_response['tag_name']
		        	);
		        	$transient->response[ $this->basename ] = (object) $plugin; // Return it in response
		    	}
		    }
		}
		return $transient; // Return filtered transient
	}

	/**
	 *  Content for the update popup with relevant plugin information and release notes from GitHub
	 *
	 * @since    1.2.0
	 * @param    (false|object|array)    $result     	The result object or array. Default false.
	 * @param    string    				 $action     	The type of information being requested from the Plugin Installation API.
	 * @param    object   				 $args     		Plugin API arguments.
	 */
	public function plugin_popup( $result, $action, $args ) {
		if( ! empty( $args->slug ) ) { // If there is a slug
			if( $args->slug == current( explode( '/' , $this->basename ) ) ) { // And it's our slug
				$this->get_repository_info(); // Get our repo info
				// Set it to an array
				$plugin = array(
					'name'              => $this->plugin["Name"],
					'slug'              => $this->basename,
					'version'           => $this->github_response['tag_name'],
					'author'            => $this->plugin["AuthorName"],
					'author_profile'    => $this->plugin["AuthorURI"],
					'last_updated'      => $this->github_response['published_at'],
					'homepage'          => $this->plugin["PluginURI"],
					'short_description' => $this->plugin["Description"],
					'sections'          => array( 
					    'Description'   => $this->plugin["Description"],
					    'Updates'       => $this->github_response['body'],
					),
					'download_link'     => $this->github_response['zipball_url']
				);
				return (object) $plugin; // Return the data
			}
		}   
		return $result; // Otherwise return default
	}

	/**
	 *  Actions to be performed after the plugin is updated
	 *
	 * @since    1.2.0
	 * @param    bool    	$response     	Installation response.
	 * @param    array    	$hook_extra     Extra arguments passed to hooked filters.
	 * @param    array   	$result     	Installation result data.
	 */
	public function after_install( $response, $hook_extra, $result ) {
		global $wp_filesystem; // Get global FS object

		$install_directory = plugin_dir_path( $this->file ); // Our plugin directory 
		$wp_filesystem->move( $result['destination'], $install_directory ); // Move files to the plugin dir
		$result['destination'] = $install_directory; // Set the destination for the rest of the stack

		if ( $this->active ) { // If it was active
			activate_plugin( $this->basename ); // Reactivate
		}
		return $result;
	}

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.2.0
	 */
	public static function update() {

	}

}
