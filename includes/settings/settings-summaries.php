<?php
/**
 * Kognetiks AI Summaries - Settings - Summaries tab
 *
 * This file contains the code for the Summaries settings page.
 * Configure which content types receive AI summaries and taxonomy generation.
 *
 * @package kognetiks-ai-summaries
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

/**
 * Returns default enabled post types (post => 1, page => 1, others => 0).
 *
 * @return array<string, int> Associative array of post_type => 1|0.
 */
function kognetiks_ai_summaries_default_enabled_post_types() {
	$post_types = get_post_types( array( 'public' => true ), 'names' );
	$defaults  = array();
	foreach ( $post_types as $pt ) {
		$defaults[ $pt ] = ( 'post' === $pt || 'page' === $pt ) ? 1 : 0;
	}
	return $defaults;
}

/**
 * Sanitize enabled post types: only allow keys from get_post_types(), values 0 or 1.
 *
 * @param mixed $input Raw option value.
 * @return array<string, int> Sanitized array.
 */
function kognetiks_ai_summaries_sanitize_enabled_post_types( $input ) {
	$allowed = get_post_types( array( 'public' => true ), 'names' );

	// If not an array (or empty), still return a complete map so the UI stays stable.
	if ( ! is_array( $input ) ) {
		$input = array();
	}

	$out = array();
	foreach ( $allowed as $pt ) {
		// Checkbox semantics: present => enabled, missing => disabled.
		$out[ $pt ] = isset( $input[ $pt ] ) ? 1 : 0;
	}

	return $out;
}

// Section A: Summaries intro.
function kognetiks_ai_summaries_summaries_intro_section_callback( $args ) {
	?>
	<p>Configure which content types receive AI summaries, and optionally generate categories and tags for those items.</p>
	<?php
}

// Section B: Taxonomy generation.
function kognetiks_ai_summaries_taxonomy_generation_section_callback( $args ) {
	?>
	<p>When generating metadata for eligible post types, you can enable or disable automatic category and tag generation.</p>
	<?php
}

// Generate Categories field.
function kognetiks_ai_summaries_generate_categories_callback( $args ) {
	$value = (int) get_option( 'kognetiks_ai_summaries_generate_categories', 1 );
	?>
	<select id="kognetiks_ai_summaries_generate_categories" name="kognetiks_ai_summaries_generate_categories">
		<option value="1" <?php selected( $value, 1 ); ?>><?php esc_html_e( 'Yes', 'kognetiks-ai-summaries' ); ?></option>
		<option value="0" <?php selected( $value, 0 ); ?>><?php esc_html_e( 'No', 'kognetiks-ai-summaries' ); ?></option>
	</select>
	<?php
}

// Generate Tags field.
function kognetiks_ai_summaries_generate_tags_callback( $args ) {
	$value = (int) get_option( 'kognetiks_ai_summaries_generate_tags', 1 );
	?>
	<select id="kognetiks_ai_summaries_generate_tags" name="kognetiks_ai_summaries_generate_tags">
		<option value="1" <?php selected( $value, 1 ); ?>><?php esc_html_e( 'Yes', 'kognetiks-ai-summaries' ); ?></option>
		<option value="0" <?php selected( $value, 0 ); ?>><?php esc_html_e( 'No', 'kognetiks-ai-summaries' ); ?></option>
	</select>
	<p class="description"><?php esc_html_e( 'These settings apply when generating metadata for eligible post types.', 'kognetiks-ai-summaries' ); ?></p>
	<?php
}

// Section C: Post types.
function kognetiks_ai_summaries_post_types_section_callback( $args ) {
	?>
	<p>Choose which post types should receive AI summaries.</p>
	<?php
}

// Post types list field.
function kognetiks_ai_summaries_enabled_post_types_callback( $args ) {
	$post_types = get_post_types( array( 'public' => true ), 'objects' );
	$saved     = get_option( 'kognetiks_ai_summaries_enabled_post_types', array() );
	$defaults  = kognetiks_ai_summaries_default_enabled_post_types();
	$enabled   = is_array( $saved ) ? array_merge( $defaults, $saved ) : $defaults;

	foreach ( $post_types as $post_type ) {
		$name  = $post_type->name;
		$label = $post_type->label;
		$val   = isset( $enabled[ $name ] ) ? (int) $enabled[ $name ] : ( isset( $defaults[ $name ] ) ? (int) $defaults[ $name ] : 0 );
		?>
		<p>
			<label for="kognetiks_ai_summaries_enabled_post_types_<?php echo esc_attr( $name ); ?>">
				<input type="checkbox" id="kognetiks_ai_summaries_enabled_post_types_<?php echo esc_attr( $name ); ?>"
					name="kognetiks_ai_summaries_enabled_post_types[<?php echo esc_attr( $name ); ?>]"
					value="1" <?php checked( $val, 1 ); ?> />
				<?php echo esc_html( $label ); ?> (<code><?php echo esc_html( $name ); ?></code>)
			</label>
		</p>
		<?php
	}
}

/**
 * Register Summaries settings.
 */
function kognetiks_ai_summaries_summaries_settings_init() {
	// Section A: Summaries intro.
	add_settings_section(
		'kognetiks_ai_summaries_summaries_intro_section',
		'Summaries',
		'kognetiks_ai_summaries_summaries_intro_section_callback',
		'kognetiks_ai_summaries_summaries_settings'
	);

	// Section B: Taxonomy generation.
	add_settings_section(
		'kognetiks_ai_summaries_taxonomy_generation_section',
		'Taxonomy Generation',
		'kognetiks_ai_summaries_taxonomy_generation_section_callback',
		'kognetiks_ai_summaries_summaries_taxonomy_settings'
	);

	add_settings_field(
		'kognetiks_ai_summaries_generate_categories',
		__( 'Generate Categories', 'kognetiks-ai-summaries' ),
		'kognetiks_ai_summaries_generate_categories_callback',
		'kognetiks_ai_summaries_summaries_taxonomy_settings',
		'kognetiks_ai_summaries_taxonomy_generation_section'
	);

	add_settings_field(
		'kognetiks_ai_summaries_generate_tags',
		__( 'Generate Tags', 'kognetiks-ai-summaries' ),
		'kognetiks_ai_summaries_generate_tags_callback',
		'kognetiks_ai_summaries_summaries_taxonomy_settings',
		'kognetiks_ai_summaries_taxonomy_generation_section'
	);

	// Section C: Post types.
	add_settings_section(
		'kognetiks_ai_summaries_post_types_section',
		'Post Types',
		'kognetiks_ai_summaries_post_types_section_callback',
		'kognetiks_ai_summaries_summaries_post_types_settings'
	);

	add_settings_field(
		'kognetiks_ai_summaries_enabled_post_types',
		__( 'Enabled post types', 'kognetiks-ai-summaries' ),
		'kognetiks_ai_summaries_enabled_post_types_callback',
		'kognetiks_ai_summaries_summaries_post_types_settings',
		'kognetiks_ai_summaries_post_types_section'
	);

	// Register settings with sanitization.
	register_setting(
		'kognetiks_ai_summaries_summaries_settings',
		'kognetiks_ai_summaries_generate_categories',
		array(
			'type'              => 'integer',
			'sanitize_callback' => function ( $v ) {
				return 1 === (int) $v ? 1 : 0;
			},
		)
	);

	register_setting(
		'kognetiks_ai_summaries_summaries_settings',
		'kognetiks_ai_summaries_generate_tags',
		array(
			'type'              => 'integer',
			'sanitize_callback' => function ( $v ) {
				return 1 === (int) $v ? 1 : 0;
			},
		)
	);

	register_setting(
		'kognetiks_ai_summaries_summaries_settings',
		'kognetiks_ai_summaries_enabled_post_types',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'kognetiks_ai_summaries_sanitize_enabled_post_types',
		)
	);
}
add_action( 'admin_init', 'kognetiks_ai_summaries_summaries_settings_init' );
