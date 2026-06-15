<?php
/**
 * Plugin Name: Photo Trail
 * Plugin URI: https://github.com/DELCUSEAllan/photo-trail
 * Description: Creates an interactive photo trail effect that displays random images following the user's cursor.
 * Version: 1.0.0
 * Author: Allan Delcuse
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: photo-trail
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PHOTO_TRAIL_VERSION', '1.0.0' );
define( 'PHOTO_TRAIL_OPTION_NAME', 'photo_trail_settings' );

function photo_trail_default_settings() {
	return array(
		'page_slug'      => 'pele-mele',
		'image_folder'   => 'photo-trail/images',
		'spawn_delay'    => 180,
		'animation_time' => 3500,
		'image_size'     => 240,
	);
}

function photo_trail_get_settings() {
	$saved_settings = get_option( PHOTO_TRAIL_OPTION_NAME, array() );

	return wp_parse_args( $saved_settings, photo_trail_default_settings() );
}

function photo_trail_activate() {
	if ( false === get_option( PHOTO_TRAIL_OPTION_NAME ) ) {
		add_option( PHOTO_TRAIL_OPTION_NAME, photo_trail_default_settings() );
	}
}

register_activation_hook( __FILE__, 'photo_trail_activate' );

function photo_trail_sanitize_image_folder( $folder ) {
	$folder = sanitize_text_field( $folder );
	$folder = wp_normalize_path( $folder );
	$folder = trim( $folder, "/ \t\n\r\0\x0B" );

	if (
		empty( $folder ) ||
		false !== strpos( $folder, '..' ) ||
		false !== strpos( $folder, ':' )
	) {
		return photo_trail_default_settings()['image_folder'];
	}

	return $folder;
}

function photo_trail_sanitize_settings( $input ) {
	$defaults = photo_trail_default_settings();

	return array(
		'page_slug'      => isset( $input['page_slug'] ) ? sanitize_title( $input['page_slug'] ) : $defaults['page_slug'],
		'image_folder'   => isset( $input['image_folder'] ) ? photo_trail_sanitize_image_folder( $input['image_folder'] ) : $defaults['image_folder'],
		'spawn_delay'    => isset( $input['spawn_delay'] ) ? max( 80, min( 1000, absint( $input['spawn_delay'] ) ) ) : $defaults['spawn_delay'],
		'animation_time' => isset( $input['animation_time'] ) ? max( 500, min( 10000, absint( $input['animation_time'] ) ) ) : $defaults['animation_time'],
		'image_size'     => isset( $input['image_size'] ) ? max( 80, min( 600, absint( $input['image_size'] ) ) ) : $defaults['image_size'],
	);
}

function photo_trail_register_settings() {
	register_setting(
		'photo_trail_settings_group',
		PHOTO_TRAIL_OPTION_NAME,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'photo_trail_sanitize_settings',
			'default'           => photo_trail_default_settings(),
		)
	);
}

add_action( 'admin_init', 'photo_trail_register_settings' );

function photo_trail_add_settings_page() {
	add_options_page(
		'Photo Trail',
		'Photo Trail',
		'manage_options',
		'photo-trail',
		'photo_trail_render_settings_page'
	);
}

add_action( 'admin_menu', 'photo_trail_add_settings_page' );

function photo_trail_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = photo_trail_get_settings();
	?>

	<div class="wrap photo-trail-admin">
		<h1>Photo Trail</h1>

		<p>
			Configure the page and image folder used by the Photo Trail effect.
		</p>

		<form method="post" action="options.php">
			<?php settings_fields( 'photo_trail_settings_group' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="photo-trail-page-slug">Target page slug</label>
					</th>
					<td>
						<input
							type="text"
							id="photo-trail-page-slug"
							name="<?php echo esc_attr( PHOTO_TRAIL_OPTION_NAME ); ?>[page_slug]"
							value="<?php echo esc_attr( $settings['page_slug'] ); ?>"
							class="regular-text"
						>
						<p class="description">Example: <code>pele-mele</code></p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="photo-trail-image-folder">Image folder</label>
					</th>
					<td>
						<input
							type="text"
							id="photo-trail-image-folder"
							name="<?php echo esc_attr( PHOTO_TRAIL_OPTION_NAME ); ?>[image_folder]"
							value="<?php echo esc_attr( $settings['image_folder'] ); ?>"
							class="regular-text"
						>
						<p class="description">
							Folder inside <code>wp-content</code>. Example:
							<code>photo-trail/images</code>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="photo-trail-spawn-delay">Spawn delay</label>
					</th>
					<td>
						<input
							type="number"
							id="photo-trail-spawn-delay"
							name="<?php echo esc_attr( PHOTO_TRAIL_OPTION_NAME ); ?>[spawn_delay]"
							value="<?php echo esc_attr( $settings['spawn_delay'] ); ?>"
							min="80"
							max="1000"
						>
						<p class="description">Delay between two images in milliseconds.</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="photo-trail-animation-time">Animation duration</label>
					</th>
					<td>
						<input
							type="number"
							id="photo-trail-animation-time"
							name="<?php echo esc_attr( PHOTO_TRAIL_OPTION_NAME ); ?>[animation_time]"
							value="<?php echo esc_attr( $settings['animation_time'] ); ?>"
							min="500"
							max="10000"
						>
						<p class="description">Animation duration in milliseconds.</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="photo-trail-image-size">Maximum image size</label>
					</th>
					<td>
						<input
							type="number"
							id="photo-trail-image-size"
							name="<?php echo esc_attr( PHOTO_TRAIL_OPTION_NAME ); ?>[image_size]"
							value="<?php echo esc_attr( $settings['image_size'] ); ?>"
							min="80"
							max="600"
						>
						<p class="description">Maximum image width in pixels.</p>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>

	<?php
}

function photo_trail_enqueue_admin_assets( $hook ) {
	if ( 'settings_page_photo-trail' !== $hook ) {
		return;
	}

	wp_enqueue_style(
		'photo-trail-admin-style',
		plugin_dir_url( __FILE__ ) . 'assets/css/admin.css',
		array(),
		PHOTO_TRAIL_VERSION
	);
}

add_action( 'admin_enqueue_scripts', 'photo_trail_enqueue_admin_assets' );

function photo_trail_get_images() {
	$settings        = photo_trail_get_settings();
	$relative_folder = photo_trail_sanitize_image_folder( $settings['image_folder'] );
	$image_dir       = wp_normalize_path( WP_CONTENT_DIR . '/' . $relative_folder . '/' );
	$image_url       = content_url( $relative_folder . '/' );

	if ( ! is_dir( $image_dir ) || ! is_readable( $image_dir ) ) {
		return array();
	}

	$real_image_dir = realpath( $image_dir );

	if ( false === $real_image_dir ) {
		return array();
	}

	$allowed_extensions = array( 'jpg', 'jpeg', 'png', 'webp', 'gif' );
	$images             = array();

	try {
		$directory = new DirectoryIterator( $real_image_dir );
	} catch ( Exception $exception ) {
		return array();
	}

	foreach ( $directory as $file ) {
		if ( $file->isDot() || ! $file->isFile() ) {
			continue;
		}

		$extension = strtolower( $file->getExtension() );

		if ( ! in_array( $extension, $allowed_extensions, true ) ) {
			continue;
		}

		$real_file_path = realpath( $file->getPathname() );

		if (
			false === $real_file_path ||
			0 !== strpos( $real_file_path, $real_image_dir )
		) {
			continue;
		}

		$images[] = esc_url_raw( $image_url . rawurlencode( $file->getFilename() ) );
	}

	return $images;
}

function photo_trail_enqueue_front_assets() {
	$settings = photo_trail_get_settings();

	if (
		is_admin() ||
		empty( $settings['page_slug'] ) ||
		! is_page( $settings['page_slug'] )
	) {
		return;
	}

	$images = photo_trail_get_images();

	if ( empty( $images ) ) {
		return;
	}

	wp_enqueue_style(
		'photo-trail-style',
		plugin_dir_url( __FILE__ ) . 'assets/css/photo-trail.css',
		array(),
		PHOTO_TRAIL_VERSION
	);

	wp_enqueue_script(
		'photo-trail-script',
		plugin_dir_url( __FILE__ ) . 'assets/js/photo-trail.js',
		array(),
		PHOTO_TRAIL_VERSION,
		true
	);

	wp_add_inline_script(
		'photo-trail-script',
		'window.photoTrailConfig = ' . wp_json_encode(
			array(
				'images'        => $images,
				'spawnDelay'    => absint( $settings['spawn_delay'] ),
				'animationTime' => absint( $settings['animation_time'] ),
				'imageSize'     => absint( $settings['image_size'] ),
			)
		) . ';',
		'before'
	);
}

add_action( 'wp_enqueue_scripts', 'photo_trail_enqueue_front_assets' );
