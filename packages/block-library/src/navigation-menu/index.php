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
	$colors = [
		'bg_css_classes' => '',
		'text_css_classes' => ''
	];

	// Pick up the background CSS classes.
	if ( array_key_exists('backgroundColor', $attributes ) ) {
		$colors[ 'bg_css_classes' ] .= ' has-background-color';
	}
	if ( array_key_exists('backgroundColorCSSClass', $attributes ) ) {
		$colors[ 'bg_css_classes' ] .= " {$attributes['backgroundColorCSSClass']};";
	}
	$colors[ 'bg_css_classes' ] = esc_attr( trim( $colors[ 'bg_css_classes' ] ) );

	// Pick up the color CSS classes.
	if ( array_key_exists('textColor', $attributes ) ) {
		$colors[ 'text_css_classes' ] .= ' has-text-color;';
	}
	if ( array_key_exists('textColorCSSClass', $attributes ) ) {
		$colors[ 'text_css_classes' ] .= " {$attributes['textColorCSSClass']};";
	}
	$colors[ 'text_css_classes' ] = esc_attr( trim( $colors[ 'text_css_classes' ] ) );

	// Pick up inline Styles.
	$colors[ 'bg_inline_styles'] = 'background-color: ' . esc_attr( $attributes['backgroundColorValue'] ) . ';';
	$colors[ 'text_inline_styles'] = 'color: ' . esc_attr( $attributes['textColorValue'] ) . ';';

	return
		'<nav class="wp-block-navigation-menu">' .
			build_navigation_menu_html( $block, $colors ) .
		'</nav>';
}

/**
 * Walks the inner block structure and returns an HTML list for it.
 *
 * @param array   $block          The block.
 * @param array   $colors         Contains inline syles and CSS classes to apply to menu item.
 *
 * @return string Returns  an HTML list from innerBlocks.
 */
function build_navigation_menu_html( $block, $colors ) {
	$html = '';
	foreach ( (array) $block['innerBlocks'] as $key => $menu_item ) {
		$html .= '<li style="' . $colors['bg_inline_styles'] . ' ' . $colors['text_inline_styles' ] . '">' .
			'<div class="wp-block-navigation-menu-item ' . $colors['bg_css_classes' ] . '">' .
			'<a class="wp-block-navigation-menu-link ' . $colors['text_css_classes' ] . '"';

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
 */
function register_block_core_navigation_menu() {
	$block_content = file_get_contents ( dirname( __FILE__ ) . '/../../../packages/block-library/src/navigation-menu/block.json' );
	if ( ! $block_content ) {
		throw new Error(
			'block.json file not found'
		);
	}
	$block_definition = json_decode( $block_content, true );
	if( is_null( $block_definition ) ) {
		throw new Error(
			'Unable to parse block.json file'
		);
	}

	// Pick up block name and remove it from the block-definition object.
	$block_name = $block_definition['name'];
	unset( $block_definition['name'] );

	// Add render callback into block-definition object.
	$block_definition['render_callback'] = 'render_block_navigation_menu';

	register_block_type(
		$block_name,
		$block_definition
	);
}

add_action( 'init', 'register_block_core_navigation_menu' );
