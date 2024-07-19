<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '3.1.0' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

// require( get_stylesheet_directory() . '/memberpress/memberpress.php');

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}

		if ( apply_filters( 'hello_elementor_register_menus', true ) ) {
			register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'hello-elementor' ) ] );
		}

		if ( apply_filters( 'hello_elementor_post_type_support', true ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}

		if ( apply_filters( 'hello_elementor_add_theme_support', true ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);

			/*
			 * Editor Style.
			 */
			add_editor_style( 'classic-editor.css' );

			/*
			 * Gutenberg wide images.
			 */
			add_theme_support( 'align-wide' );

			/*
			 * WooCommerce.
			 */
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', true ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option( $theme_version_option_name );

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_display_header_footer' ) ) {
	/**
	 * Check whether to display header footer.
	 *
	 * @return bool
	 */
	function hello_elementor_display_header_footer() {
		$hello_elementor_header_footer = true;

		return apply_filters( 'hello_elementor_header_footer', $hello_elementor_header_footer );
	}
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		$min_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'hello_elementor_enqueue_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( hello_elementor_display_header_footer() ) {
			wp_enqueue_style(
				'hello-elementor-header-footer',
				get_template_directory_uri() . '/header-footer' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		if ( apply_filters( 'hello_elementor_register_elementor_locations', true ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( ! function_exists( 'hello_elementor_add_description_meta_tag' ) ) {
	/**
	 * Add description meta tag with excerpt text.
	 *
	 * @return void
	 */
	function hello_elementor_add_description_meta_tag() {
		if ( ! apply_filters( 'hello_elementor_description_meta_tag', true ) ) {
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		$post = get_queried_object();
		if ( empty( $post->post_excerpt ) ) {
			return;
		}

		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $post->post_excerpt ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );

// Admin notice
if ( is_admin() ) {
	require get_template_directory() . '/includes/admin-functions.php';
}

// Settings page
require get_template_directory() . '/includes/settings-functions.php';

// Header & footer styling option, inside Elementor
require get_template_directory() . '/includes/elementor-functions.php';

if ( ! function_exists( 'hello_elementor_customizer' ) ) {
	// Customizer controls
	function hello_elementor_customizer() {
		if ( ! is_customize_preview() ) {
			return;
		}

		if ( ! hello_elementor_display_header_footer() ) {
			return;
		}

		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_elementor_customizer' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check whether to display the page title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		wp_body_open();
	}
}








function enqueue_custom_scripts() {
    wp_enqueue_script('custom-js', get_template_directory_uri() . '/js/custom-ajax-script.js', array('jquery'), null, true);
    wp_localize_script('custom-js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');


function custom_filter_form() {
    $taxonomies = array('dance-style', 'language-spoken', 'nationalities', 'performance-skill', 'musical-instrument', 'athletic-skill', 'accent');
    ?>
    <form id="filter-form">
        <input type="hidden" name="post_type" value="talent" />
        <?php foreach ($taxonomies as $taxonomy): ?>
            <div class="filter">
                <label for="<?php echo $taxonomy; ?>"><?php echo ucwords(str_replace('_', ' ', $taxonomy)); ?></label>
                <?php
                $terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false));
                if ($terms) : ?>
                    <select name="<?php echo $taxonomy; ?>">
                        <option value="">Select <?php echo ucwords(str_replace('_', ' ', $taxonomy)); ?></option>
                        <?php foreach ($terms as $term) : ?>
                            <option value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit">Search</button>
    </form>

	<div class="properties-list">

	</div>
    <?php
}

add_shortcode('custom_filter_form', 'custom_filter_form');













function filter_form_submission() {
	parse_str($_POST['form_data'], $form_data);

	// Extract individual taxonomy terms from the parsed array
    $dance_style = isset($form_data['dance-style']) ? sanitize_text_field($form_data['dance-style']) : '';
    $language_spoken = isset($form_data['language-spoken']) ? sanitize_text_field($form_data['language-spoken']) : '';
    $nationalities = isset($form_data['nationalities']) ? sanitize_text_field($form_data['nationalities']) : '';
    $performance_skill = isset($form_data['performance-skill']) ? sanitize_text_field($form_data['performance-skill']) : '';
    $musical_instrument = isset($form_data['musical-instrument']) ? sanitize_text_field($form_data['musical-instrument']) : '';
    $athletic_skill = isset($form_data['athletic-skill']) ? sanitize_text_field($form_data['athletic-skill']) : '';
    $accent = isset($form_data['accent']) ? sanitize_text_field($form_data['accent']) : '';

    $tax_query = array('relation' => 'AND'); // Initialize with AND relation for all taxonomies
	
	// Add taxonomy queries if they are set
    if ($dance_style) {
        $tax_query[] = array(
            'taxonomy' => 'dance-style',
            'field' => 'slug',
            'terms' => $dance_style,
        );
    }
    if ($language_spoken) {
        $tax_query[] = array(
            'taxonomy' => 'language-spoken',
            'field' => 'slug',
            'terms' => $language_spoken,
        );
    }
    if ($nationalities) {
        $tax_query[] = array(
            'taxonomy' => 'nationalities',
            'field' => 'slug',
            'terms' => $nationalities,
        );
    }
    if ($performance_skill) {
        $tax_query[] = array(
            'taxonomy' => 'performance-skill',
            'field' => 'slug',
            'terms' => $performance_skill,
        );
    }
    if ($musical_instrument) {
        $tax_query[] = array(
            'taxonomy' => 'musical-instrument',
            'field' => 'slug',
            'terms' => $musical_instrument,
        );
    }
    if ($athletic_skill) {
        $tax_query[] = array(
            'taxonomy' => 'athletic-skill',
            'field' => 'slug',
            'terms' => $athletic_skill,
        );
    }
    if ($accent) {
        $tax_query[] = array(
            'taxonomy' => 'accent',
            'field' => 'slug',
            'terms' => $accent,
        );
    }

    $args = array(
        'post_type' => 'talent', 	// Change to your CPT slug
        'posts_per_page' => -1,
        'tax_query' => $tax_query,
    );

    $properties = new WP_Query($args);

    ob_start();

    if ($properties->have_posts()) {
        while ($properties->have_posts()) {
            $properties->the_post();             // Change “properties” to your CPT slug
            echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a><br>';
            if (has_post_thumbnail()) {
                echo '<a href="' . get_permalink() . '">';
                the_post_thumbnail('thumbnail'); // Adjust the image size and styles as needed
                echo '</a>';
            }
            echo '<br>';
        }
    } else {
        echo 'No Talent found';
    }

    wp_reset_postdata();

    $response = ob_get_clean();
    echo $response;

    wp_die();

}

// Register the AJAX action
add_action('wp_ajax_filter_form_submission', 'filter_form_submission');
add_action('wp_ajax_nopriv_filter_form_submission', 'filter_form_submission');











// function handle_filter_form_submission() {
//     // Initialize args array
//     $args = array( 
//         'posts_per_page' => -1,
//         'post_type'      => 'talent',
//         'post_status'    => 'publish'
//     );

//     // Initialize tax_query
//     $tax_query = array('relation' => 'AND'); // Changed to 'AND' for stricter matching

//     // Check if form_data is set and sanitize it
//     if (isset($_POST['form_data']) && is_array($_POST['form_data'])) {
//         foreach ($_POST['form_data'] as $taxonomy => $terms) {
//             // Sanitize taxonomy and terms
//             $taxonomy = sanitize_key($taxonomy);
//             $terms = array_map('sanitize_text_field', (array) $terms);
            
//             // Add taxonomy query
//             if (!empty($terms) && taxonomy_exists($taxonomy)) {
//                 $tax_query[] = array(
//                     'taxonomy' => $taxonomy,
//                     'field'    => 'slug',
//                     'terms'    => $terms,
//                 );
//             }
//         }
        
//         // Add tax_query to args
//         if (count($tax_query) > 1) { // Check if tax_query has been populated
//             $args['tax_query'] = $tax_query;
//         } else {
//             // Remove tax_query if not used
//             unset($args['tax_query']);
//         }
//     }

//     // Debug: Output the args for verification
//     echo '<pre>';
//     print_r($args);
//     echo '</pre>';

//     // Get posts based on arguments
//     $services_data = get_posts($args);

//     // Debug: Output the results
//     echo '<pre>';
//     print_r($services_data);
//     echo '</pre>';

//     // Optional: You might want to use wp_send_json_success for AJAX responses
//     // wp_send_json_success($services_data);
// }

// // Register the AJAX action
// add_action('wp_ajax_filter_form_submission', 'handle_filter_form_submission');
// add_action('wp_ajax_nopriv_filter_form_submission', 'handle_filter_form_submission');




