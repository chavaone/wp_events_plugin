<?php
if(!class_exists('Eventos_Post_Type'))
{
    /**
     * A PostTypeTemplate class that provides 3 additional meta fields
     */
    class Eventos_Post_Type
    {
        const POST_TYPE = "eventos";

        public function __construct()
        {
            // register actions
            add_action('init', array(&$this, 'init'));
            add_action('admin_init', array(&$this, 'admin_init'));
        }


        public function init()
        {
            // Initialize Post Type
            $this->create_post_type();
            add_action ("save_post", array(&$this, 'save_post'));
            add_filter ("manage_edit-albums_columns", array(&$this, "edit_columns"));
            add_action ("manage_posts_custom_column", array(&$this, "custom_columns"));
            add_action ("admin_head", array(&$this, 'add_menu_icons_styles' ));
        }


        public function create_post_type()
        {
            $labels = array(
                'name' => _x('Eventos', 'post type general name'),
                'singular_name' => _x('Evento', 'post type singular name'),
                'add_new' => _x('Engadir novo', 'events'),
                'add_new_item' => __('Engadir novo evento'),
                'edit_item' => __('Editar Evento'),
                'new_item' => __('Novo Evento'),
                'view_item' => __('Ver Evento'),
                'search_items' => __('Buscar Evento'),
                'not_found' =>  __('Non se atoparon eventos'),
                'not_found_in_trash' => __('Non se atoparon eventos no lixo'),
                'parent_item_colon' => '',
            );

            $args = array(
                'label' => __('Eventos'),
                'labels' => $labels,
                'public' => true,
                'can_export' => true,
                'show_ui' => true,
                '_builtin' => false,
                'capability_type' => 'post',
                'menu_icon' => "",
                'menu_position' => 5,
                'hierarchical' => false,
                'rewrite' => array( "slug" => "eventos" ),
                'supports'=> array(
                  'title',
                  'thumbnail',
                  'editor',
                  'revisions',
                  'comments'
                  ) ,
                'show_in_nav_menus' => true,
                'has_archive' => true
            );

            register_post_type(self::POST_TYPE, $args);
        }


        public function save_post($post_id)
        {
            global $post;

            /* Get the post type object. */
            $post_type = get_post_type_object( $post->post_type );

            /* Check if the current user has permission to edit the post. */
            if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
              return $post_id;

            $new_meta_value = strtotime ( $_POST["eventos_date"] . $_POST["eventos_time"] );

            /* Get the meta key. */
            $meta_key = 'event_date';

            /* Get the meta value of the custom field key. */
            $meta_value = get_post_meta($post_id, $meta_key, true );

            /* If a new meta value was added and there was no previous value, add it. */
            if ( $new_meta_value && '' == $meta_value )
              add_post_meta( $post_id, $meta_key, $new_meta_value, true );

            /* If the new meta value does not match the old value, update it. */
            elseif ( $new_meta_value && $new_meta_value != $meta_value )
              update_post_meta( $post_id, $meta_key, $new_meta_value );
        }


        public function edit_columns($columns)
        {
              $columns = array(
                  "cb" => "<input type=\"checkbox\" />",
                  "title" => "Evento",
                  "col_ev_date" => "Data",
                  "col_ev_thumb" => "Imaxe",
                  );

              return $columns;
        }


        public function custom_columns($column)
        {
            global $post;

            $custom = get_post_custom();

            switch ($column)
            {
            case "col_ev_date":
            if (isset($custom["event_date"][0]))
            {
              $startd = $custom["event_date"][0];
              $startdate = date("F j, Y", $startd);
              echo $startdate;
            }
            break;
            case "col_ev_thumb":
            $thumb_id = get_post_thumbnail_id();
            $thumb_url = wp_get_attachment_image_src($thumb_id,'thumbnail-size', true);
            echo '<img src="' . $thumb_url[0] . '" height="175px"/>';
            break;
            }
        }


        public function add_menu_icons_styles()
        {?>
            <style>
                #adminmenu .menu-icon-eventos div.wp-menu-image:before
                {
                  content: "\f145";
                }
            </style>
            <?php
        }


        public function admin_init()
        {
            add_action('admin_print_styles', array(&$this, 'metabox_admin_styles'));
            add_action('admin_print_scripts', array(&$this, 'metabox_admin_scripts'));
            add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
        }


        public function metabox_admin_styles ()
        {
            global $post_type;

            if ( $post_type == 'eventos')
            {
              wp_enqueue_style('ui-datepicker', plugins_url ("css/pikaday.css", dirname(__FILE__)));
            }
        }


        public function metabox_admin_scripts ()
        {
            global $post_type;

            if( 'eventos' == $post_type )
            {
              wp_enqueue_script('pickaday', plugins_url ("js/pikaday.js", dirname(__FILE__)));
              wp_enqueue_script('date_metabox', plugins_url ("js/date_metabox.js", dirname(__FILE__)), array('pickaday'), false, true);
            }
        }


        public function add_meta_boxes()
        {

            // Add this metabox to every selected post
            add_meta_box(
              'events_meta',      // Unique ID
              'Data do evento',    // Title
              array(&$this, 'add_inner_meta_boxes'),   // Callback function
              'eventos',         // Admin page (or post type)
              'normal',         // Context
              'default'         // Priority
            );
        } // END public function add_meta_boxes()


        public function add_inner_meta_boxes($post)
        {
            // Render the job order metabox
            include(plugin_dir_path(dirname(__FILE__)) . sprintf("templates/%s_metabox.php", self::POST_TYPE));
        } // END public function add_inner_meta_boxes($post)

    } // END class Post_Type_Template
} // END if(!class_exists('Post_Type_Template'))
