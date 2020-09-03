<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$class = fw_ext_builder_get_item_width(
	'page-builder',
	$atts['width'] . '/frontend_class'
);
?>
<div class="<?php echo esc_attr( $class ); ?>">
    <div class="tile is-child">
		<?php echo do_shortcode( $content ); ?>
    </div>
    <!-- /.tile is-child -->
</div>
