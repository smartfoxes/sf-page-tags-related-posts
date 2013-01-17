<?php
  
function sf_ptrp_add_page_metaboxes() {
    add_meta_box('sf_ptrp_page_tags', 'Posts Options', 'sf_ptrp_page_tags', 'page', 'normal', 'default');       
}
add_action( 'add_meta_boxes', 'sf_ptrp_add_page_metaboxes' , 1000);
   
function sf_ptrp_page_tags() {
    global $post;
    $number = get_post_meta($post->ID, '_sf_ptrp_number', true);    
    $tags = get_post_meta($post->ID, '_sf_ptrp_tags', true);
    
    echo '<input type="hidden" name="sf_ptrp_noncename" id="sf_ptrp_noncename" value="' .
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
    
    echo '<div class="_sf_ptrp_field">';
    echo '<label class="_sf_ptrp_label" for="_sf_ptrp_number">Number of Posts</label>';       
    echo '<div class="_sf_ptrp_controls"><input class="_sf_ptrp_input" type="text" name="_sf_ptrp_number" id="_sf_ptrp_number" value="' . $number  . '" size=5 class="small-text" /><br><small>Leave empty to use default value defined in the widget</small></div>';
    echo '</div>';
   
    echo '<div class="_sf_ptrp_field">';
    echo '<label class="_sf_ptrp_label" for="_sf_ptrp_tags">Tags</label>';
    echo '<div class="_sf_ptrp_controls"><textarea class="_sf_ptrp_input" name="_sf_ptrp_tags" id="_sf_ptrp_tags" cols="60" rows="3">' . htmlspecialchars($tags)  . '</textarea><br clear="both" \><small>Comma separated list of tags</small><div class="_sf_ptrp_tagcloud" style="padding: 10px 0 0;">Popular tags:';
    
    $tags = wp_tag_cloud(array("echo" => false));
    $tags = preg_replace('/href=[\"\'].*?[\"\']/i', 'href="#" onClick="document.getElementById(\'_sf_ptrp_tags\').value = document.getElementById(\'_sf_ptrp_tags\').value + \',\' + this.innerText;return false;"', $tags);
    echo $tags;
    echo '</div></div>';    
    echo '</div>';
}

function sf_ptrp_save_page_tags($post_id, $post) {
        
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_verify_nonce( $_POST['sf_ptrp_noncename'], plugin_basename(__FILE__) )) {    
        return $post->ID;
    }
    
    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID )) {
        return $post->ID;
    }
    
    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
    $meta['_sf_ptrp_number'] = $_POST['_sf_ptrp_number'];
    $meta['_sf_ptrp_tags'] = $_POST['_sf_ptrp_tags'];
    
    // Add values of $meta as custom fields
    foreach ($meta as $key => $value) { // Cycle through the $events_meta array!
        if( $post->post_type == 'revision' ) return; // Don't store custom data twice
        if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
        if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }
}
add_action('save_post', 'sf_ptrp_save_page_tags', 1, 2); // save the custom fields

