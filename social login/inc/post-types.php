<?php

namespace Evelyn;

class Post_Types
{
    public static function boot()
    {
        return new self();
    }
    public function __construct()
    {
        add_action('init', [$this, 'register_post_types']);
    }
    public function register_post_types()
    {
        register_post_type('listing_item', array(
            'labels' => [
                'name'               => _x('Listings', 'Listing post type labels', 'my-listing'),
                'singular_name'      => _x('Listing', 'Listing post type labels', 'my-listing'),
                'menu_name'          => _x('Listings', 'Listing post type labels', 'my-listing'),
                'all_items'          => _x('All Listings', 'Listing post type labels', 'my-listing'),
                'add_new'            => _x('Add new', 'Listing post type labels', 'my-listing'),
                'add_new_item'       => _x('Add Listing', 'Listing post type labels', 'my-listing'),
                'edit'               => _x('Edit', 'Listing post type labels', 'my-listing'),
                'edit_item'          => _x('Edit Listing', 'Listing post type labels', 'my-listing'),
                'new_item'           => _x('New Listing', 'Listing post type labels', 'my-listing'),
                'view'               => _x('View Listing', 'Listing post type labels', 'my-listing'),
                'view_item'          => _x('View Listing', 'Listing post type labels', 'my-listing'),
                'search_items'       => _x('Search Listings', 'Listing post type labels', 'my-listing'),
                'not_found'          => _x('No listings found', 'Listing post type labels', 'my-listing'),
                'not_found_in_trash' => _x('No listings found in trash', 'Listing post type labels', 'my-listing'),
                'parent'             => _x('Parent Listing', 'Listing post type labels', 'my-listing'),
            ],

            'description'         => '',
            'public'              => true,
            'show_ui'             => true,
            'capability_type'     => 'page',
            'map_meta_cap'        => true,
            'publicly_queryable'  => true,
            'exclude_from_search' => false,
            'hierarchical'        => false,
            'query_var'           => true,
            'supports'            => ['title', 'custom-fields', 'publicize', 'thumbnail', 'comments'],
            'menu_position'       => 3,
            'has_archive'         => _x('listings', 'Listing post type archive slug', 'my-listing'),
            'show_in_nav_menus'   => false,
            'delete_with_user'    => true,
        ));
    }
}
