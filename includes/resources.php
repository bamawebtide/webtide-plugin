<?php

// Register the taxonomy
add_action( 'init', 'ua_webtide_register_resources_taxonomies' );
function ua_webtide_register_resources_taxonomies() {

	// Resource categories
	register_taxonomy( 'resources_cats', 'resources', array(
		'label'                 => 'Categories',
		'labels'                => array(
			'name'                       => 'Categories',
			'singular_name'              => 'Category',
			'menu_name'                  => 'Categories',
			'all_items'                  => 'All Categories',
			'edit_item'                  => 'Edit Category',
			'view_item'                  => 'View Category',
			'update_item'                => 'Update Category',
			'add_new_item'               => 'Add New Category',
			'new_item_name'              => 'New Category Name',
			'parent_item'                => 'Parent Category',
			'parent_item_colon'          => 'Parent Category:',
			'search_items'               => 'Search Categories',
			'popular_items'              => 'Popular Categories',
			'separate_items_with_commas' => 'Separate categories with commas',
			'add_or_remove_items'        => 'Add or remove categories',
			'choose_from_most_used'      => 'Choose from the most used categories',
			'not_found'                  => 'No categories found.',
		),
		'public'                => true,
		'hierarchical'          => true,
		'show_in_nav_menus'     => false,
		'show_admin_column'     => true,
		'rewrite'               => array( 'slug' => 'resources/categories' ),
		'capabilities'          => array(
			'manage_terms'  => 'manage_resources_cats',
			'edit_terms'    => 'manage_resources_cats',
			'delete_terms'  => 'manage_resources_cats',
			'assign_terms'  => 'edit_resources_cats',
		),
	) );

	// Resource tags
	register_taxonomy( 'resources_tags', 'resources', array(
		'label'                 => 'Tags',
		'labels'                => array(
			'name'                       => 'Tags',
			'singular_name'              => 'Tag',
			'menu_name'                  => 'Tags',
			'all_items'                  => 'All Tags',
			'edit_item'                  => 'Edit Tag',
			'view_item'                  => 'View Tag',
			'update_item'                => 'Update Tag',
			'add_new_item'               => 'Add New Tag',
			'new_item_name'              => 'New Tag Name',
			'parent_item'                => NULL,
			'parent_item_colon'          => NULL,
			'search_items'               => 'Search Tags',
			'popular_items'              => 'Popular Tags',
			'separate_items_with_commas' => 'Separate tags with commas',
			'add_or_remove_items'        => 'Add or remove tags',
			'choose_from_most_used'      => 'Choose from the most used tags',
			'not_found'                  => 'No tags found.',
		),
		'public'                => true,
		'hierarchical'          => false,
		'show_in_nav_menus'     => false,
		'show_admin_column'     => true,
		'rewrite'               => array( 'slug' => 'resources/tags' ),
		'capabilities'          => array(
			'manage_terms'  => 'manage_resources_tags',
			'edit_terms'    => 'manage_resources_tags',
			'delete_terms'  => 'manage_resources_tags',
			'assign_terms'  => 'edit_resources_tags',
		),
	) );

}