<?php

add_action( 'widgets_init', function(){
     return register_widget( 'SF_ptrp' );
});

class SF_ptrp extends WP_Widget {
    
    function __construct() {
   	    parent::__construct( 'sf_ptrp', 'SF Related by Tag' );
    }
  
    public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['title-post'] = strip_tags( $new_instance['title-post'] );
		$instance['number'] = intval( $new_instance['number'] );
		$instance['number-post'] = intval( $new_instance['number-post'] );
		return $instance;
	}
	
    public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
			$title_post = $instance[ 'title-post' ];
			$number = $instance[ 'number' ];
			$number_post = $instance[ 'number-post' ];
		}
		else {
			$title = __( 'Related Posts', 'sf_ptrp' );
			$title_post = __( 'Related Pages', 'sf_ptrp' );
			$number = 5;
			$number_post = 5;
		}
		?>
    <p>
        <label for="sf_ptrp-title"><?php _e('Title displayed on page:'); ?> <input style="width: 250px;" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></label>
        <label for="sf_ptrp-number"><?php _e('Number of related posts:'); ?> <input style="width: 50px;" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" /></label>
    </p>
    <p>
        <label for="sf_ptrp-title-post"><?php _e('Title displayed on blog post:'); ?> <input style="width: 250px;" id="<?php echo $this->get_field_id( 'title-post' ); ?>" name="<?php echo $this->get_field_name( 'title-post' ); ?>" type="text" value="<?php echo $title_post; ?>" /></label>    
        <label for="sf_ptrp-number-post"><?php _e('Number of related pages:'); ?> <input style="width: 50px;" id="<?php echo $this->get_field_id( 'number-post' ); ?>" name="<?php echo $this->get_field_name( 'number-post' ); ?>" type="text" value="<?php echo $number_post; ?>" /></label>        	    
	</p>
    <?php
    }
  
  
    public function widget( $args, $options ) {
		global $post;
		
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

        $qargs = null;
               
        if (is_page()) {
            $title = $options['title'];
            $number = $options['number'];
            
            $number_page = get_post_meta($post->ID, '_sf_ptrp_number', true);  
            if($number_page) { 
                $number = $number_page; 
            }  
            $tags = get_post_meta($post->ID, '_sf_ptrp_tags', true);    
            if($tags && $number) {
                $qargs = array(
                	'post_type' => 'post',
                	'tag_slug__in' => explode(",",preg_replace("/\s*\,\s*/",",",$tags)),
                	'orderby' => 'date',
                	'posts_per_page' => $number
                );                    
            }
        } elseif(is_single()) {
            
            $title = $options['title-post'];
            $number = $options['number-post'];
            
            $number_page = get_post_meta($post->ID, '_sf_ptrp_number', true);  
            if($number_page) { 
                $number = $number_page; 
            }  
            $tags_query = wp_get_post_tags($post->ID);    
            $tags = array();
            $metaQuery = array('relation' => 'OR');
            
            foreach($tags_query as $tag) {
                if($tag->name) {
                    $tags[] = $tag->name;
                    $metaQuery[] = array(
                        'key' => '_sf_ptrp_tags', 
                    	'value' => $tag->name, 
                    	'compare' => 'LIKE'
                    );          
                }      
            }
            if($tags && $number) {
                $qargs = array(
                	'post_type' => 'page',                	
                	'orderby' => 'date',
                	'posts_per_page' => $number,
                	'meta_query' => $metaQuery             	                	
                );                    
            }
        }
        
        if($qargs) {
            $the_query = new WP_Query( $qargs );        
            
            if($the_query->post_count) {
                
                echo $args['before_widget'];
                echo $args['before_title'];
                
                echo $title;
                
                echo $args['after_title'];
                //echo 'number of posts: ' . $number . "<br>";    
                //print_r($qargs);
                echo "<ul>";
                // The Loop
                while ( $the_query->have_posts() ) :
                    $the_query->the_post();
                    echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
                endwhile;
                echo "</ul>";
                // Restore original Query & Post Data
                wp_reset_query();
                wp_reset_postdata();
        
                echo $args['after_widget'];
            }
        }
    }
  
  /*function register(){
    register_sidebar_widget('Page Tags Related Posts', array('sf_ptrp', 'widget'));
    register_widget_control('Page Tags Related Posts', array('sf_ptrp', 'control'));
  }*/
}
/*
function widget_sf_ptrp_register() {
	if ( function_exists('register_sidebar_widget') ) :
    	function widget_sf_ptrp($args) {
    		extract($args);
    		$options = get_option('widget_akismet');
    		$count = get_option('akismet_spam_count');
    		?>
    			<?php echo $before_widget; ?>
    				<?php echo $before_title . $options['title'] . $after_title; ?>
    				!!!
                    <div id="akismetwrap"><div id="akismetstats"><a id="aka" href="http://akismet.com" title=""><?php printf( _n( '%1$s%2$s%3$s %4$sspam comment%5$s %6$sblocked by%7$s<br />%8$sAkismet%9$s', '%1$s%2$s%3$s %4$sspam comments%5$s %6$sblocked by%7$s<br />%8$sAkismet%9$s', $count ), '<span id="akismet1"><span id="akismetcount">', number_format_i18n( $count ), '</span>', '<span id="akismetsc">', '</span></span>', '<span id="akismet2"><span id="akismetbb">', '</span>', '<span id="akismeta">', '</span></span>' ); ?></a></div></div>
    			<?php echo $after_widget; ?>
    	<?php
    	}


	function widget_sf_ptrp_control() {
	    $options = $newoptions = get_option('widget_sf_ptrp');
		if ( isset( $_POST['sf_ptrp-submit'] ) && $_POST["sf_ptrp-submit"] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST["sf_ptrp-title"]));
			if ( empty($newoptions['title']) ) $newoptions['title'] = __('Related Posts');
			if ( empty($newoptions['number']) ) $newoptions['number'] = 5;
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_sf_ptrp', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
	?>
				<p><label for="sf_ptrp-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="sf_ptrp-title" name="sf_ptrp-title" type="text" value="<?php echo $title; ?>" /></label></p>
				<input type="hidden" id="sf_ptrp-submit" name="sf_ptrp-submit" value="1" />
	<?php
	}

	if ( function_exists( 'wp_register_sidebar_widget' ) ) {
		wp_register_sidebar_widget( 'sf_ptrp', 'Page Tags Related Posts', 'widget_sf_ptrp', null, 'sf_ptrp');
		wp_register_widget_control( 'sf_ptrp', 'Page Tags Related Posts', 'widget_sf_ptrp_control', null, 75, 'sf_ptrp');
	} else {
		register_sidebar_widget('Page Tags Related Posts', 'widget_sf_ptrp', null, 'sf_ptrp');
		register_widget_control('Page Tags Related Posts', 'widget_sf_ptrp_control', null, 75, 'sf_ptrp');
	}
	//if ( is_active_widget('widget_sf_ptrp') )
	//	add_action('wp_head', 'widget_sf_ptrp_style');
	endif;
}

add_action('init', 'widget_sf_ptrp_register');
*/