<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wp-book-author.com
 * @since      1.0.0
 *
 * @package    Wp_Book
 * @subpackage Wp_Book/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_Book
 * @subpackage Wp_Book/includes
 * @author     Shubham Rawat <shubham.rawat@hbwsl.com>
 */
class Wp_Book
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Book_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('WP_BOOK_VERSION')) {
			$this->version = WP_BOOK_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp-book';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Book_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Book_i18n. Defines internationalization functionality.
	 * - Wp_Book_Admin. Defines all hooks for the admin area.
	 * - Wp_Book_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-book-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-book-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-wp-book-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-wp-book-public.php';

		$this->loader = new Wp_Book_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Book_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Wp_Book_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Wp_Book_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('init', $this, 'book_register_post_type');
		$this->loader->add_action('init', $this, 'book_register_taxonomies');
		$this->loader->add_action('add_meta_boxes', $this, 'book_add_meta_box');
		$this->loader->add_action('save_post', $this, 'book_save_meta_box');
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Wp_Book_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wp_Book_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
	public function book_register_post_type()
	{
		register_post_type('book', array(
			'labels' => array(
				'name' => __('Books', 'wp-book'),
				'singular_name' => __('Book', 'wp-book'),
			),
			'public' => true,
			'has_archive' => true,
			'supports' => ['title', 'editor', 'thumbnail'],
		));
	}
	public function book_register_taxonomies()
	{
		register_taxonomy('book_category', 'book', array(
			'labels' => array(
				'name' => __('Book Categories', 'wp-book'),
				'singular_name' => __('Book Category', 'wp-book'),
			),
			'hierarchical' => true,
			'public' => true,
		));

		register_taxonomy('book_tag', 'book', array(
			'labels' => array(
				'name' => __('Book tags', 'wp-book'),
				'singular_name' => __('Book Tag', 'wp-book'),
			),
			'hierarchical' => false,
			'public' => true,
		));
	}

	public function book_add_meta_box()
	{
		add_meta_box(
			'book_meta_box',
			__('Book Details', 'wp-book'),
			[$this, 'book_meta_box_callback'],
			'book',
			'normal',
			'high'
		);
	}

	public function book_meta_box_callback($post)
	{
		wp_nonce_field('book_save_meta_box', 'book_meta_box_nonce');
		$author_name = get_post_meta($post->ID, '_book_author_name', true);
		$price = get_post_meta($post->ID, '_book_price', true);
		$publisher = get_post_meta($post->ID, '_book_publisher', true);
		$year = get_post_meta($post->ID, '_book_year', true);
		$edition = get_post_meta($post->ID, '_book_edition', true);
		$url = get_post_meta($post->ID, '_book_url', true);
		?>
		<p>
			<label for="book_author_name"><?php _e('Author Name:', 'wp-book'); ?></label><br>
			<input type="text" id="book_author_name" name="book_author_name" value="<?php echo esc_attr($author_name); ?>"
				class="widefat">
		</p>
		<p>
			<label for="book_price"><?php _e('Price:', 'wp-book'); ?></label><br>
			<input type="number" id="book_price" name="book_price" value="<?php echo esc_attr($price); ?>" class="widefat"
				step="0.01">
		</p>
		<p>
			<label for="book_publisher"><?php _e('Publisher:', 'wp-book'); ?></label><br>
			<input type="text" id="book_publisher" name="book_publisher" value="<?php echo esc_attr($publisher); ?>"
				class="widefat">
		</p>
		<p>
			<label for="book_year"><?php _e('Year:', 'wp-book'); ?></label><br>
			<input type="number" id="book_year" name="book_year" value="<?php echo esc_attr($year); ?>" class="widefat"
				min="1000" max="<?php echo date('Y'); ?>">
		</p>
		<p>
			<label for="book_edition"><?php _e('Edition:', 'wp-book'); ?></label><br>
			<input type="text" id="book_edition" name="book_edition" value="<?php echo esc_attr($edition); ?>" class="widefat">
		</p>
		<p>
			<label for="book_url"><?php _e('URL:', 'wp-book'); ?></label><br>
			<input type="url" id="book_url" name="book_url" value="<?php echo esc_attr($url); ?>" class="widefat">
		</p>
		<?php
	}

	public function book_save_meta_box($post_id)
	{
		if (!isset($_POST['book_meta_box_nonce']) || !wp_verify_nonce($_POST['book_meta_box_nonce'], 'book_save_meta_box')) {
			return $post_id;
		}

		// Prevent auto-saving from interfering
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		// Check the user's permission to edit the post
		if (!current_user_can('edit_post', $post_id)) {
			return $post_id;
		}

		// Sanitize and save the fields
		$fields = [
			'_book_author_name' => isset($_POST['book_author_name']) ? sanitize_text_field($_POST['book_author_name']) : '',
			'_book_price' => isset($_POST['book_price']) ? floatval($_POST['book_price']) : '',
			'_book_publisher' => isset($_POST['book_publisher']) ? sanitize_text_field($_POST['book_publisher']) : '',
			'_book_year' => isset($_POST['book_year']) ? intval($_POST['book_year']) : '',
			'_book_edition' => isset($_POST['book_edition']) ? sanitize_text_field($_POST['book_edition']) : '',
			'_book_url' => isset($_POST['book_url']) ? esc_url_raw($_POST['book_url']) : '',
		];

		foreach ($fields as $meta_key => $meta_value) {
			if (!empty($meta_value)) {
				update_post_meta($post_id, $meta_key, $meta_value);
			} else {
				delete_post_meta($post_id, $meta_key);
			}
		}
	}
}
function book_create_meta_table()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'book_meta'; // Fixed table name (use underscores).
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        book_id BIGINT(20) UNSIGNED NOT NULL,
        meta_key VARCHAR(255) NOT NULL,
        meta_value LONGTEXT NOT NULL
    ) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php'; // Correct the include statement.
	dbDelta($sql);
}
register_activation_hook(__FILE__, 'book_create_meta_table');