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
		$this->loader->add_action('init', $this, 'book_init');
		$this->loader->add_action('add_meta_boxes', $this, 'book_add_meta_box');
		$this->loader->add_action('save_post', $this, 'book_save_meta_box');
		$this->loader->add_action('admin_menu', $this, 'add_settings_page');
		$this->loader->add_action('admin_init', $this, 'register_settings');
		$this->loader->add_action('wp_dashboard_setup', $this,'custom_dashboard_widget');
	}
	public function book_init()
	{
		$this->book_register_post_type();
		$this->book_register_taxonomies();
		$this->create_custom_block();
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

	public function add_settings_page()
	{
		add_options_page(
			__('Book Settings', 'wp-book'),
			__('Book Settings', 'wp-book'),
			'manage_options',
			'wp-book-settings',
			[$this, 'render_settings_page']
		);
	}

	public function render_settings_page()
	{
		?>
		<div class="wrap">
			<h1><?php esc_html_e('Book Settings', 'wp-book'); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields('wp_book_settings_group');
				do_settings_sections('wp-book-settings');
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
	public function register_settings()
	{
		register_setting('wp_book_settings_group', 'wp_book_currency', [
			'type' => 'string',
			'description' => __('Currency for book prices', 'wp-book'),
			'sanitize_callback' => 'sanitize_text_field',
			'default' => 'USD',
		]);

		register_setting('wp_book_settings_group', 'wp_book_books_per_page', [
			'type' => 'integer',
			'description' => __('Number of books displayed per page', 'wp-book'),
			'sanitize_callback' => 'intval',
			'default' => 10,
		]);

		add_settings_section(
			'wp_book_general_settings',
			__('General Settings', 'wp-book'),
			null,
			'wp-book-settings'
		);

		add_settings_field(
			'wp_book_currency',
			__('Currency', 'wp-book'),
			[$this, 'render_currency_field'],
			'wp-book-settings',
			'wp_book_general_settings'
		);

		add_settings_field(
			'wp_book_books_per_page',
			__('Books Per Page', 'wp-book'),
			[$this, 'render_books_per_page_field'],
			'wp-book-settings',
			'wp_book_general_settings'
		);
	}

	public function render_currency_field()
	{
		$value = get_option('wp_book_currency', 'USD');
		?>
		<input type="text" name="wp_book_currency" value="<?php echo esc_attr($value); ?>" class="regular-text">
		<p class="description"><?php esc_html_e('Enter the currency for book prices (e.g., USD, EUR).', 'wp-book'); ?></p>
		<?php
	}

	public function render_books_per_page_field()
	{
		$value = get_option('wp_book_books_per_page', 10);
		?>
		<input type="number" name="wp_book_books_per_page" value="<?php echo esc_attr($value); ?>" min="1" class="small-text">
		<p class="description"><?php esc_html_e('Enter the number of books to display per page.', 'wp-book'); ?></p>
		<?php
	}

	public function book_shortcode($atts)
	{
		$atts = shortcode_atts(
			array(
				'id' => '',
				'author_name' => '',
				'year' => '',
				'category' => '',
				'tag' => '',
				'publisher' => ''
			),
			$atts,
			'book'
		);

		$args = array(
			'post_type' => 'book',
			'post_per_page' => -1
		);

		if (!empty($atts['id'])) {
			$args['p'] = $atts['id'];
		}

		if (!empty($atts['author_name'])) {
			$args['meta_query'][] = [
				'key' => 'author_name',
				'value' => $atts['author_name'],
				'compare' => 'LIKE'
			];
		}

		if (!empty($atts['year'])) {
			$args['meta_query'][] = [
				'key' => 'year',
				'value' => $atts['year'],
				'compare' => '='
			];
		}

		if (!empty($atts['publisher'])) {
			$args['meta_query'][] = [
				'key' => 'publisher',
				'value' => $atts['publisher'],
				'compare' => 'LIKE'
			];
		}

		if (!empty($atts['category'])) {
			$args['tax_query'][] = [
				'taxonomy' => 'category',
				'field' => 'slug',
				'terms' => $atts['category']
			];
		}

		if (!empty($atts['tag'])) {
			$args['tax_query'][] = [
				'taxonomy' => 'post_tag',
				'field' => 'slug',
				'terms' => $atts['tag']
			];
		}

		$query = new WP_Query($args);

		if (!$query->have_posts()) {
			return '<p>No books found.</p>';
		}

		$output = '<div class="book-list">';
		while ($query->have_posts()) {
			$query->the_post();
			$author_name = get_post_meta(get_the_ID(), 'author_name', true);
			$year = get_post_meta(get_the_ID(), 'year', true);
			$publisher = get_post_meta(get_the_ID(), 'publisher', true);

			$output .= '<div class="book">';
			$output .= '<h3>' . get_the_title() . '</h3>';
			$output .= '<p><strong>Author:</strong> ' . esc_html($author_name) . '</p>';
			$output .= '<p><strong>Year:</strong> ' . esc_html($year) . '</p>';
			$output .= '<p><strong>Publisher:</strong> ' . esc_html($publisher) . '</p>';
			$output .= '<p><strong>Category:</strong> ' . esc_html(get_the_category_list(', ')) . '</p>';
			$output .= '<p><strong>Tags:</strong> ' . esc_html(get_the_tag_list('', ', ')) . '</p>';
			$output .= '</div>';
		}
		$output .= '</div>';
		wp_reset_postdata();

		return $output;
	}
	function enqueue_block_editor_assets()
	{
		wp_enqueue_script(
			'custom-block-script',
			plugins_url('block.js', __FILE__),
			array('wp-blocks', 'wp-element')
		);

		wp_enqueue_style(
			'custom-block-style',
			plugins_url('block.css', __FILE__),
			array('wp-edit-blocks')
		);
	}

	public function create_custom_block()
	{
		wp_register_script(
			'custom-wp-block',
			plugin_dir_url(__FILE__) . '/block.js',
			array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-block-editor'),
		);
		register_block_type('wp-book/custom-wp-block', array(
			'editor_script' => 'custom-wp-block',
		));
	}

	// Hook to add custom widget to the dashboard
	public function custom_dashboard_widget()
	{
		wp_add_dashboard_widget(
			'custom_book_categories', // Widget ID
			'Top 5 Book Categories',  // Widget Title
			[$this,'display_top_book_categories'] // Function to display the widget content
		);
	}


	// Function to display the top 5 book categories
	public function display_top_book_categories()
	{
		// Query to get book categories and their post count
		$args = array(
			'taxonomy' => 'book_category', // Use category taxonomy
			'orderby' => 'post_count',    // Order categories by post count
			'order' => 'ASC',     // Descending order (highest count first)
			'number' => 5,          // Limit to top 5 categories
			'hide_empty' => true,       // Exclude categories with no posts
		);

		// Get the categories
		$categories = get_terms($args);

		// Check if there are categories available
		if (!empty($categories) && !is_wp_error($categories)) {
			echo '<ul>';
			foreach ($categories as $category) {
				// Display the category name and post count
				echo '<li>' . esc_html($category->name) . ' (' . $category->count . ' posts)</li>';
			}
			echo '</ul>';
		} else {
			echo 'No categories found.';
		}
	}

}
function book_create_meta_table()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'book_meta';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        book_id BIGINT(20) UNSIGNED NOT NULL,
        meta_key VARCHAR(255) NOT NULL,
        meta_value LONGTEXT NOT NULL
    ) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta($sql);

	// Verify if the table exists after creation
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		error_log("Error: The $table_name table could not be created.");
	}
}

register_activation_hook(__FILE__, 'book_create_meta_table');
add_shortcode('book', 'book_shortcode');