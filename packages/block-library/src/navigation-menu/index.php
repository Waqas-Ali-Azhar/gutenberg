<?php
/**
 * Server-side rendering of the `core/navigation-menu` block.
 *
 * @package gutenberg
 */

/**
 * Renders the `core/navigation-menu` block on server.
 *
 * @param array $attributes The block attributes.
 * @param array $content The saved content.
 * @param array $block The parsed block.
 *
 * @return string Returns the post content with the legacy widget added.
 */
function render_block_navigation_menu( $attributes, $content, $block ) {
	$colors = array(
		'bg_css_classes'   => '',
		'text_css_classes' => '',
	);

	// Pick up the background CSS classes.
	if ( array_key_exists( 'backgroundColor', $attributes ) ) {
		$colors['bg_css_classes'] .= ' has-background-color';
	}
	if ( array_key_exists( 'backgroundColorCSSClass', $attributes ) ) {
		$colors['bg_css_classes'] .= " {$attributes['backgroundColorCSSClass']}";
	}
	$colors['bg_css_classes'] = esc_attr( trim( $colors['bg_css_classes'] ) );

	// Pick up the color CSS classes.
	if ( array_key_exists( 'textColor', $attributes ) ) {
		$colors['text_css_classes'] .= ' has-text-color;';
	}
	if ( array_key_exists( 'textColorCSSClass', $attributes ) ) {
		$colors['text_css_classes'] .= " {$attributes['textColorCSSClass']}";
	}
	$colors['text_css_classes'] = esc_attr( trim( $colors['text_css_classes'] ) );

	// Pick up inline Styles.
	$inline_styles = array();
	if ( array_key_exists( 'backgroundColorValue', $attributes ) ) {
		array_push( $inline_styles, 'background-color: ' . esc_attr( $attributes['backgroundColorValue'] ) . ';' );
	}

	if ( array_key_exists( 'textColorValue', $attributes ) ) {
		array_push( $inline_styles, 'color: ' . esc_attr( $attributes['textColorValue'] ) . ';' );
	}
	$colors['inline_styles'] = esc_attr( trim( implode( ' ', $inline_styles ) ) );

	return '<nav class="wp-block-navigation-menu">' .
		build_navigation_menu_html( $block, $colors ) .
	'</nav>';
}

/**
 * Walks the inner block structure and returns an HTML list for it.
 *
 * @param array $block  The block.
 * @param array $colors Contains inline styles and CSS classes to apply to menu item.
 *
 * @return string Returns  an HTML list from innerBlocks.
 */
function build_navigation_menu_html( $block, $colors ) {
	$html = '';
	foreach ( (array) $block['innerBlocks'] as $key => $menu_item ) {
		$inline_styles = ! empty( $colors['inline_styles'] )
			? " style='{$colors['inline_styles']}'"
			: '';

		$html .= '<li' . $inline_styles . '>' .
			'<div class="wp-block-navigation-menu-item ' . $colors['bg_css_classes'] . '">' .
			'<a class="wp-block-navigation-menu-item__link ' . $colors['text_css_classes'] . '"';

		if ( isset( $menu_item['attrs']['destination'] ) ) {
			$html .= ' href="' . $menu_item['attrs']['destination'] . '"';
		}
		if ( isset( $menu_item['attrs']['title'] ) ) {
			$html .= ' title="' . $menu_item['attrs']['title'] . '"';
		}
		$html .= '>';
		if ( isset( $menu_item['attrs']['label'] ) ) {
			$html .= $menu_item['attrs']['label'];
		}
		$html .= '</a>';

		if ( count( (array) $menu_item['innerBlocks'] ) > 0 ) {
			$html .= build_navigation_menu_html( $menu_item, $colors );
		}

		$html .= '</div></li>';
	}
	return '<ul>' . $html . '</ul>';
}

/**
 * Register the navigation menu block.
 *
 * @uses render_block_navigation_menu()
 * @throws WP_Error An WP_Error exception parsing the block definition.
 */
function register_block_core_navigation_menu() {

	register_block_type(
		'core/navigation-menu',
		array(
			'category'        => 'layout',
			'attributes'      => array(
				'className'               => array(
					'type' => 'string',
				),

				'automaticallyAdd'        => array(
					'type'    => 'boolean',
					'default' => false,
				),

				'backgroundColor'         => array(
					'type' => 'string',
				),

				'textColor'               => array(
					'type' => 'string',
				),

				'backgroundColorValue'    => array(
					'type' => 'string',
				),

				'textColorValue'          => array(
					'type' => 'string',
				),

				'customBackgroundColor'   => array(
					'type' => 'string',
				),

				'customTextColor'         => array(
					'type' => 'string',
				),

				'backgroundColorCSSClass' => array(
					'type' => 'string',
				),

				'textColorCSSClass'       => array(
					'type' => 'string',
				),
			),

			'render_callback' => 'render_block_navigation_menu',
		)
	);
}

add_action( 'init', 'register_block_core_navigation_menu' );
