<?php

add_action( 'init', function() {
	// Register block styles for both frontend + backend.
	wp_register_style('specific_category_posts-block-style-css', plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), is_admin() ? array( 'wp-editor' ) : null, null);

	// Register block editor script for backend.
	wp_register_script('specific_category_posts-block-js', plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), null, true);

	// Register block editor styles for backend.
	wp_register_style('specific_category_posts-block-editor-css', plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), array( 'wp-edit-blocks' ), null);

	// WP Localized globals. Use dynamic PHP stuff in JavaScript via `cgbGlobal` object.
	wp_localize_script(
		'specific_category_posts-block-js',
		'cgbGlobal', // Array containing dynamic data for a JS Global.
		[
			'pluginDirPath' => plugin_dir_path( __DIR__ ),
			'pluginDirUrl'  => plugin_dir_url( __DIR__ ),
			// Add more data here that you want to access from `cgbGlobal` object.
			'siteUrl'       => get_site_url()
		]
	);

	// Register Gutenberg block on server-side.
	register_block_type('wasek/specific-category-posts', array(
		'style'         => 'specific_category_posts-block-style-css',
		'editor_script' => 'specific_category_posts-block-js',
		'editor_style'  => 'specific_category_posts-block-editor-css',

		'render_callback' => 'render_specific_category_posts'
	));
});


function render_specific_category_posts($attributes){
	$posts = get_posts([
		'category' => $attributes['selectedCategory'],
		'posts_per_page' => $attributes['postsPerPage']
	]);

	ob_start();
	foreach($posts as $post){
		echo '<article class="category-posts-article">
			<a href="'. get_post_permalink($post->ID) .'" class="permalink">
				<h2 class="title">'. $post->post_title .'</h2>
				<span class="img">'. get_the_post_thumbnail($post->ID) .'</span>
			</a>

			<p class="categories"> Categories: '. get_the_category_list(', ', '', $post->ID) .'</p>

			<p class="excerpt">'. get_the_excerpt($post->ID) .'<br />
				<a href="'. get_post_permalink($post->ID) .'" class="readMore">Read More</a>
			</p>
			<hr /><br />
		</article>';
	}
	return ob_get_clean();
}