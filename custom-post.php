<?php

/**
 * Plugin Name: Add Jobs
 * Description: To add jobs in the frontend.
 * Version: 1.0
 * Author: Noob
" */

class admin_end{
	function __construct() {
		add_action( 'admin_enqueue_scripts',array($this,'load_jquery_datepicker' ));
		add_action( 'init', array($this,'job_cpt' ));
		add_action("add_meta_boxes", array($this, "job_box"));
		add_action( 'add_meta_boxes',array($this,'exp_date_metabox' ));
		add_action( 'save_post', array($this, 'save_exp_date_meta' ));
		add_filter( 'the_content', array($this,'display_post' ));
		add_action('admin_menu', array($this,'add_cpt_submenu'));
		add_action( 'admin_init', array($this,'custom_settings'));
		
	}

	function load_jquery_datepicker() {
	wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
    }
    function job_cpt() {
    	$supports = array(
	    'title', // post title
	    'editor', // post content
	    'author', // post author
	    'thumbnail', // featured images
	    'comments', // post comments
	    'revisions', // post revisions
	    'post-formats', // post formats
	);
    	$args = array(
    		'label'                => 'Jobs',
	        'public'               => true,
	        'supports'             =>$supports,
	    );
	    register_post_type( 'jobs', $args );
	}
	function job_box() //to create a custom metabox.
	{
		add_meta_box("job_meta_box", "Job Data", array($this,"custom_meta_box_markup"),"jobs", "advanced", "high", null);
	}
	function custom_meta_box_markup($object)  //to show the fields and contents of the custom metabox created.
	{
		wp_nonce_field(basename(__FILE__), "meta-box-nonce");
		$var1=get_post_meta( $object->ID, 'company_name', true );
		$var2=get_post_meta($object->ID, "company_tag", true);
		$var3=get_post_meta($object->ID, "company_email", true);
		?>
		<div>
			<label for="entered_company_name">Company Name</label>
    		<input id="c_name" name="entered_company_name" type="text" value="<?php echo esc_attr($var1); ?>" required="">

    		<br><br>
    		<label for="entered_company_tag">Company Tagline</label>
    		<input id="c_tag" name="entered_company_tag" type="text" value="<?php echo esc_attr($var2);?>" >
    		<br><br>
    		<label for="entered_company_email">Company Email</label>
    		<input id="c_email" name="entered_company_email" type="email" value="<?php echo esc_attr($var3);?>" >
    	</div>
    	<?php 
    }
    function exp_date_metabox() {
    	add_meta_box('exp_date_metabox','Expiry Date',array($this,'exp_date_metabox_callback'),'jobs', 'side','core');
    }
    function exp_date_metabox_callback( $post ) { ?>
    	<form action="" method="post"> 
    		<?php        
        //retrieve metadata value if it exists
    		$expiry_date = get_post_meta( $post->ID, 'expires', true );
    		?>
    		<label for expiry_date><?php ('Expiry Date'); ?></label>
    		<input type="text" class="MyDate" name="m_expiry_date" 
    		value=<?php echo esc_attr( $expiry_date ); ?> > 
    		<script type="text/javascript">
    			jQuery(document).ready(function() {
    				jQuery('.MyDate').datepicker({
    					dateFormat : 'dd-mm-yy'
    				});
    			});
    		</script>           
    	</form>
    	<?php
    }
	function save_exp_date_meta( $post_id ) {

// Check if the current user has permission to edit the post. */
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;
		if ( isset( $_POST['m_expiry_date'] ) ) {        
			$new_expiry_date = ( $_POST['m_expiry_date'] );
			update_post_meta( $post_id, 'expires', $new_expiry_date );      
		}
    	if(isset($_POST["entered_company_name"]))
    	{
    		$meta_box_company_name = sanitize_text_field($_POST["entered_company_name"]);
    		update_post_meta($post_id, "company_name", $meta_box_company_name);
    	} 
    	
    	if(isset($_POST["entered_company_tag"]))
    	{
    		$meta_box_company_tag = $_POST["entered_company_tag"];
    		update_post_meta($post_id, "company_tag", $meta_box_company_tag);
    	}  
    	if(isset($_POST["entered_company_email"]))
    	{
    		$meta_box_company_email = $_POST["entered_company_email"];
    		update_post_meta($post_id, "company_email", $meta_box_company_email);
    	}  
    	
	}
	
	
	function display_post( $content ) {
    //to display the contents on the frontend.
		global $post;
		$title = get_the_title($post);
		$company_name = esc_attr(get_post_meta($post->ID, "company_name", true));
	    $company_tag = esc_attr(get_post_meta($post->ID, "company_tag", true));
	    $post_expire=esc_attr(get_post_meta( $post->ID, 'expires', true ));
	    $settings_text=esc_textarea(get_option( 'my_setting_textfield' )); 
	    $setting_options =esc_attr(get_option( 'checkbox_field' ));
	    $setting_radio_option =esc_attr(get_option( 'radio_field'));
	    $settings_job=esc_attr(get_option( 'jobs_number' ));
	    $settings_text_area_option = esc_attr(get_option( 'text_area_field'));
	    $settings_update_date= esc_attr(get_option( 'date_area_field' ));
	    $post1 = "<div class='sp_display_post'>Company Name : $company_name</div>";
	    $post2 = "<div class='sp_display_post'>Company tag : $company_tag</div>";
	    $post3 = "<div class='sp_display_post'>Post Expires on $post_expire</div>";
	    $post4 = "<div class='sp_display_post'> $settings_text</div>";
	    $post5 = "<div class='sp_display_post'> $settings_job</div>";
	    $post6 = "<div class='sp_display_post'> $settings_text_area_option</div>";
	    if($setting_options=="true"){
	    	$company_email = esc_attr(get_post_meta($post->ID, "company_email", true));
	    	$post4 = "<div class='sp_display_post'>$company_email</div>";

	    }
	    else{
	    	$post4 = "";
	    }
	    if($setting_radio_option=="show_title"){
	    return $title;	    	
	    }
	    else{
	    	return $title . $content . $post1 . $post2  . $post3 . $post4 . $post5 . $post6;
	    }
	}

	function add_cpt_submenu(){
		add_submenu_page(
		'edit.php?post_type=jobs', //$parent_slug
		'Jobs Subpage', //$page_title
		'Settings', //$menu_title
		'manage_options', //$capability
		'jobs_subpage_ex',//$menu_slug
		array($this,'subpage_render_page')//$function
	);
	}
	

//add_submenu_page callback function
	function subpage_render_page($object) {
		echo '<div class="wrap"><h2> Jobs Settings </h2></div>';
		?>
		<form method="POST" action="options.php">
			<?php
			settings_fields( 'sample-page' );
			do_settings_sections( 'sample-page' );
			submit_button();
			?>
		</form>
		<?php
	}
	function custom_settings() {
		add_settings_section(
			'sample_page_setting_section',
			__( 'Custom settings', 'my-textdomain' ),
			'my_setting_section_callback_function',
			'sample-page'
		);
		add_settings_field(
			'my_setting_textfield',
		   __( 'Organization', 'my-textdomain' ),
		   array($this,'my_setting_markup'),
		   'sample-page',
		   'sample_page_setting_section'
		);

		register_setting( 'sample-page', 'my_setting_textfield',array('type'=>'string','santize_callback'=>'sanitize_key',
		'default'=>''));
		add_settings_field(
			'checkbox_field',
			__( 'Show Email', 'my-textdomain' ),
			array($this,'checkbox_element_callback'),
			'sample-page',
			'sample_page_setting_section'
		);
		register_setting( 'sample-page', 'checkbox_field',array('type'=>'string','santize_callback'=>'sanitize_key',
		'default'=>''));
		add_settings_field(
			'radio_field',
			__( 'Show content', 'my-textdomain' ),
			array($this,'radio_element_callback'),
			'sample-page',
			'sample_page_setting_section'
		);
		register_setting( 'sample-page', 'radio_field',array('type'=>'string','santize_callback'=>'sanitize_key',
		'default'=>''));
		add_settings_field(
			'jobs_number',
			__( 'Number of Jobs', 'my-textdomain' ),
			array($this,'number_field_markup'),
			'sample-page',
			'sample_page_setting_section'
		);
		register_setting( 'sample-page', 'jobs_number',array('type'=>'string','santize_callback'=>'sanitize_key',
		'default'=>''));
		add_settings_field(
			'text_area_field',
			__( 'Update Content', 'my-textdomain' ),
			array($this,'textarea_field_markup'),
			'sample-page',
			'sample_page_setting_section'
		);
		register_setting( 'sample-page', 'text_area_field',array('type'=>'string','santize_callback'=>'sanitize_key',
		'default'=>''));
		add_settings_field(
			'date_area_field',
			__( 'Date Filter', 'my-textdomain' ),
			array($this,'datearea_field_markup'),
			'sample-page',
			'sample_page_setting_section'
		);
		register_setting( 'sample-page', 'date_area_field',array('type'=>'string','santize_callback'=>'sanitize_key',
		'default'=>''));
	}
	function my_setting_markup() {
		?>
		<label for="my-input"></label>
		<input type="text" id="my_setting_field_1" name="my_setting_textfield" 
		value="<?php echo get_option( 'my_setting_textfield' ); ?>"><br><br><br>
		<?php
	}
	function checkbox_element_callback() {
		$options = get_option( 'checkbox_field' );
		if($options == ""){
			?>
			<input name="checkbox_field" type="checkbox" value="true" ><br><br><br>
			<?php
		}
		else if($options == "true")
		{
			?>
			<input name="checkbox_field" type="checkbox" value="true" checked><br><br><br>
			<?php
		}
	}
	function radio_element_callback() {
		$radio_option = get_option( 'radio_field' );
		?>
		<input type="radio" id="title" name="radio_field" value="show_title" <?php checked('show_title',$radio_option);?>>
		<label for="title">Show Title Only</label>
		<input type="radio" id="title_content" name="radio_field" value="show_title_content" <?php checked('show_title_content',$radio_option);?>>
		<label for="title_content">Show Title and Content</label><br><br><br>
		<?php
	}
	function number_field_markup() {
		?>
		<label for="num-input"></label>
		<input id="jobs_no" name="jobs_number" type="number"
		value="<?php echo get_option( 'jobs_number' ); ?>"><br><br><br>
		<?php
	}
	function textarea_field_markup() {
		$text_area_option = get_option( 'text_area_field' );
		?>
		<textarea rows="4" cols="80" name="text_area_field" ><?php echo isset($text_area_option) ? esc_textarea($text_area_option):'';?></textarea><br><br><br>
		<?php
	}
	function datearea_field_markup() {
		$update_date= get_option( 'date_area_field' );
		?>
		<label for get_date></label>
		<input type="text" class="MyDate" name="date_area_field" value="<?php echo $update_date ?>"><br><br><br>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('.MyDate').datepicker({
					dateFormat : 'dd-mm-yy'
				});
			});
		</script>
		<?php
	}
}
add_shortcode('wdp-jobs-plugin','__construct()');
 

$my_class = new admin_end();
if (!function_exists('write_log')) {
    function write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}
