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
 * Sanitize prompt instructions: reject blank, show warning, return default.
 *
 * @param mixed $input Raw option value.
 * @return string Sanitized value or default if blank.
 */
function kognetiks_ai_summaries_sanitize_prompt_instructions_summary( $input ) {
	$sanitized = sanitize_textarea_field( (string) $input );
	if ( trim( $sanitized ) === '' ) {
		add_settings_error(
			'kognetiks_ai_summaries_messages',
			'prompt_instructions_summary_blank',
			__( 'Summary Instructions cannot be blank. Default instructions have been restored.', 'kognetiks-ai-summaries' ),
			'warning'
		);
		return kognetiks_ai_summaries_get_prompt_instruction_default( 'summary' );
	}
	return $sanitized;
}

/**
 * Sanitize prompt instructions: reject blank, show warning, return default.
 *
 * @param mixed $input Raw option value.
 * @return string Sanitized value or default if blank.
 */
function kognetiks_ai_summaries_sanitize_prompt_instructions_categories( $input ) {
	$sanitized = sanitize_textarea_field( (string) $input );
	if ( trim( $sanitized ) === '' ) {
		add_settings_error(
			'kognetiks_ai_summaries_messages',
			'prompt_instructions_categories_blank',
			__( 'Categories Instructions cannot be blank. Default instructions have been restored.', 'kognetiks-ai-summaries' ),
			'warning'
		);
		return kognetiks_ai_summaries_get_prompt_instruction_default( 'categories' );
	}
	return $sanitized;
}

/**
 * Sanitize prompt instructions: reject blank, show warning, return default.
 *
 * @param mixed $input Raw option value.
 * @return string Sanitized value or default if blank.
 */
function kognetiks_ai_summaries_sanitize_prompt_instructions_tags( $input ) {
	$sanitized = sanitize_textarea_field( (string) $input );
	if ( trim( $sanitized ) === '' ) {
		add_settings_error(
			'kognetiks_ai_summaries_messages',
			'prompt_instructions_tags_blank',
			__( 'Tags Instructions cannot be blank. Default instructions have been restored.', 'kognetiks-ai-summaries' ),
			'warning'
		);
		return kognetiks_ai_summaries_get_prompt_instruction_default( 'tags' );
	}
	return $sanitized;
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
	$nonce = wp_create_nonce( 'kognetiks_ai_summaries_support_nonce' );
	$url   = add_query_arg(
		array(
			'page'     => 'kognetiks-ai-summaries',
			'tab'      => 'support',
			'dir'      => 'summaries',
			'file'     => 'summaries.md',
			'_wpnonce' => $nonce,
		),
		admin_url( 'admin.php' )
	);
	?>
	<p>Configure which content types receive AI summaries, and optionally generate categories and tags for those items.</p>
	<p style="background-color: #e0f7fa; padding: 10px;"><b>For an explanation of the Summaries settings and additional documentation please click <a href="<?php echo esc_url( $url ); ?>">here</a>.</b></p>
	<?php
}

// Section B: LLM Prompt Instructions.
function kognetiks_ai_summaries_llm_prompt_instructions_section_callback( $args ) {
	?>
	<p>Customize the instructions sent to the LLM for generating summaries, categories, and tags. The word/category/tag count from your settings will be automatically appended to these instructions.</p>
	<?php
}

// Summary prompt instructions field.
function kognetiks_ai_summaries_prompt_instructions_summary_callback( $args ) {
	$default = kognetiks_ai_summaries_get_prompt_instruction_default( 'summary' );
	$value   = get_option( 'kognetiks_ai_summaries_prompt_instructions_summary', $default );
	?>
	<textarea id="kognetiks_ai_summaries_prompt_instructions_summary" name="kognetiks_ai_summaries_prompt_instructions_summary" rows="4" cols="80" class="large-text"><?php echo esc_textarea( $value ); ?></textarea>
	<p class="description"><?php esc_html_e( 'Instructions for summary generation. The word count will be appended automatically.', 'kognetiks-ai-summaries' ); ?></p>
	<?php
}

// Categories prompt instructions field.
function kognetiks_ai_summaries_prompt_instructions_categories_callback( $args ) {
	$default = kognetiks_ai_summaries_get_prompt_instruction_default( 'categories' );
	$value   = get_option( 'kognetiks_ai_summaries_prompt_instructions_categories', $default );
	?>
	<textarea id="kognetiks_ai_summaries_prompt_instructions_categories" name="kognetiks_ai_summaries_prompt_instructions_categories" rows="4" cols="80" class="large-text"><?php echo esc_textarea( $value ); ?></textarea>
	<p class="description"><?php esc_html_e( 'Instructions for category generation. The category count will be appended automatically.', 'kognetiks-ai-summaries' ); ?></p>
	<?php
}

// Tags prompt instructions field.
function kognetiks_ai_summaries_prompt_instructions_tags_callback( $args ) {
	$default = kognetiks_ai_summaries_get_prompt_instruction_default( 'tags' );
	$value   = get_option( 'kognetiks_ai_summaries_prompt_instructions_tags', $default );
	?>
	<textarea id="kognetiks_ai_summaries_prompt_instructions_tags" name="kognetiks_ai_summaries_prompt_instructions_tags" rows="4" cols="80" class="large-text"><?php echo esc_textarea( $value ); ?></textarea>
	<p class="description"><?php esc_html_e( 'Instructions for tag generation. The tag count will be appended automatically.', 'kognetiks-ai-summaries' ); ?></p>
	<?php
}

// Section C: Taxonomy generation.
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

	// Section B: LLM Prompt Instructions.
	add_settings_section(
		'kognetiks_ai_summaries_llm_prompt_instructions_section',
		'LLM Prompt Instructions',
		'kognetiks_ai_summaries_llm_prompt_instructions_section_callback',
		'kognetiks_ai_summaries_summaries_prompt_settings'
	);

	add_settings_field(
		'kognetiks_ai_summaries_prompt_instructions_summary',
		__( 'Summary Instructions', 'kognetiks-ai-summaries' ),
		'kognetiks_ai_summaries_prompt_instructions_summary_callback',
		'kognetiks_ai_summaries_summaries_prompt_settings',
		'kognetiks_ai_summaries_llm_prompt_instructions_section'
	);

	add_settings_field(
		'kognetiks_ai_summaries_prompt_instructions_categories',
		__( 'Categories Instructions', 'kognetiks-ai-summaries' ),
		'kognetiks_ai_summaries_prompt_instructions_categories_callback',
		'kognetiks_ai_summaries_summaries_prompt_settings',
		'kognetiks_ai_summaries_llm_prompt_instructions_section'
	);

	add_settings_field(
		'kognetiks_ai_summaries_prompt_instructions_tags',
		__( 'Tags Instructions', 'kognetiks-ai-summaries' ),
		'kognetiks_ai_summaries_prompt_instructions_tags_callback',
		'kognetiks_ai_summaries_summaries_prompt_settings',
		'kognetiks_ai_summaries_llm_prompt_instructions_section'
	);

	// Section C: Taxonomy generation.
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

	// Register LLM prompt instructions settings.
	register_setting(
		'kognetiks_ai_summaries_summaries_settings',
		'kognetiks_ai_summaries_prompt_instructions_summary',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'kognetiks_ai_summaries_sanitize_prompt_instructions_summary',
		)
	);

	register_setting(
		'kognetiks_ai_summaries_summaries_settings',
		'kognetiks_ai_summaries_prompt_instructions_categories',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'kognetiks_ai_summaries_sanitize_prompt_instructions_categories',
		)
	);

	register_setting(
		'kognetiks_ai_summaries_summaries_settings',
		'kognetiks_ai_summaries_prompt_instructions_tags',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'kognetiks_ai_summaries_sanitize_prompt_instructions_tags',
		)
	);

	// Section D: Post types.
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
