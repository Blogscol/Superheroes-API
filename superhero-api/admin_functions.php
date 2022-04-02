<?php
class Admin_Functions {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function init()
	{
		add_shortcode('superhero_api', 'superhero_api_att');
		add_action( 'add_meta_boxes', 'superhero_api_meta_box_add' );
		add_action( 'save_post', 'superhero_api_meta_box_save' );
		add_action( 'edit_form_after_title', 'edit_form_after_title' );

		add_action( 'wp_ajax_nopriv_get_all_superhero_names', 'get_all_superhero_names' );
		add_action( 'wp_ajax_get_all_superhero_names', 'get_all_superhero_names' );

		/*Disable Gutenberg, because this version only add Meta boxes in classical editor, next version will add in Gutenberg*/

		// Disable Gutenberg on the back end.
		add_filter( 'use_block_editor_for_post', '__return_false' );

		// Disable Gutenberg for widgets.
		add_filter( 'use_widgets_blog_editor', '__return_false' );

		add_action( 'wp_enqueue_scripts', function()
		{
		    // Remove CSS on the front end.
		    wp_dequeue_style( 'wp-block-library' );

		    // Remove inline global CSS on the front end.
		    wp_dequeue_style( 'global-styles' );
		}, 20 );
	}
}

function superhero_api_att($atts, $content = null)
{
    $default = array
    (
        'name' => 'Spider',
        'size' => 5,
    );

    $characters = shortcode_atts($default, $atts);
    $content = do_shortcode($content);

	require_once plugin_dir_path( __FILE__ ) . 'views.php';

	return View_Functions::list_characters_shortcode($content, $characters);
}

function superhero_api_meta_box_add()
{
  add_meta_box( 'superhero-api-meta-box', 'SuperHeroes Shortcode Generator', 'superhero_api_meta_box_show', ['post','page'], 'after_title', 'high' );
}

function superhero_api_meta_box_show( $post )
{
	$values = get_post_custom( $post->ID );
	
	$shortcodeNames = array();

	$SuperHeroName = isset( $values['SuperHeroName'] ) ? esc_attr( $values['SuperHeroName'][0] ) : '';

	$SuperHeroObject = json_decode(htmlspecialchars_decode($SuperHeroName));

	if(isset($SuperHeroObject))
	{
		foreach ($SuperHeroObject as $key => $value)
		{
			$shortcodeNames[] = $value->value;
		}		
	}

	$SuperHeroShortcode = "[superhero_api name='". implode(',',$shortcodeNames) ."']SUPERHEROES[/superhero_api]";

    ?>
    
    <div class="">
		<p>
	    	<label for="names"><h2><span style="font-size: 20px;">SuperHeroes Name</span></h2></label>
	    </p>
		<p>
	    	<label for="names"><h2>Insert Names separated by commas to generate Shortcode: <b>Name1</b>, <b>Name2</b></h2></label>
	    </p>
	    <p>
	    	<input type="text" name="SuperHeroName" id="SuperHero_names" value="<?php echo $SuperHeroName; ?>"/>
		</p>
	</div>

    <div class="">
		<p>
	    	<label for="shortcode"><h2><span style="font-size: 20px;">Generated Shortcode</span></h2></label>
	    </p>
		<p>
	    	<label for="shortcode"><h2>This generated Shortcode can be used in the pages or template</h2></label>
	    </p>
	    <p>
	    	<input type="text" name="SuperHeroShortcode" id="SuperHero_Shortcode" value="<?php echo $SuperHeroShortcode; ?>" readonly/>
		</p>
	</div>

	<?php
}

function edit_form_after_title()
{
    global $post, $wp_meta_boxes;

    do_meta_boxes( get_current_screen(), 'after_title', $post );

    // unset 'ai_after_title' context from the post's meta boxes
    unset( $wp_meta_boxes['post']['after_title'] );
}

function superhero_api_meta_box_save( $post_id )
{
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    if( isset( $_POST['SuperHeroName'] ) )
        update_post_meta( $post_id, 'SuperHeroName', $_POST['SuperHeroName'] );
}

function get_all_superhero_names()
{
    // Check for nonce security
    $nonce = sanitize_text_field( $_POST['nonce'] );

    if ( ! wp_verify_nonce( $nonce, 'inputTags-ajax-nonce' ) ) {
        die ( 'Busted!');
    }

    $namesCache = get_transient('superhero_names');

    if (false === $namesCache)
    {
        try
        {
			$json = file_get_contents('https://cdn.jsdelivr.net/gh/akabab/superhero-api@0.3.0/api/all.json');

			$superheroes = json_decode($json,true);

			$namesCache = array();
			
			foreach($superheroes as $key => $superheroe) 
			{
				$namesCache[] = $superheroe['name'];
			}

            set_transient('superhero_names', $namesCache, 86400 );
        }
        catch (Exception $e)
        {
            $namesCache = false;
        }
    }

    echo json_encode($namesCache);
    die();
}
?>