<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package universal-example
 */

if ( ! is_active_sidebar( 'recent-posts-sidebar' ) ) {
	return;
}
?>

<aside id="secondary" class="sidebar-recent">
	<?php dynamic_sidebar( 'recent-posts-sidebar' ); ?>
</aside><!-- #secondary -->
