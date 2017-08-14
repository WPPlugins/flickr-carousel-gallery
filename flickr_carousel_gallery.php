<?php
/*
Plugin Name: Flickr Carousel Gallery
Plugin URI: http://wordpress.org/#
Description: This plugin is a gallery generator / lightbox view combo. Very easy to add to your post or page using short tags or the text editor
Author: Ramandeep Singh	
Licence:GPL 3
Version: 1.0
Author URI: www.designaeon.com
*/








function add_jquery_lightbox_scripts(){
	$path = WP_PLUGIN_URL.'/flickr-carousel-gallery/';
	wp_enqueue_script('jquery');
	$opts = mcg_get_options();
	$format = $opts['mcg_thumbformat'];
	echo '<script type="text/javascript">
			var theblogurl ="'.get_bloginfo('url').'";
			var flickr_mini_gallery_img_format ="'.$format.'";
			
		</script>';
	

	wp_register_style( 'flickr-carousel-gallery', plugins_url('css/flickr-carousel-gallery.css', __FILE__) );
	wp_enqueue_style( 'flickr-carousel-gallery' );
	
	wp_enqueue_script('flickrcarousel', $path.'js/flickrcarousel.js', array('jquery'),'0.1');
	
}

add_action('wp_head', 'add_jquery_lightbox_scripts',5);

//add ContentFlow Scripts and Styles
function jquery_content_flow_script(){
$path = WP_PLUGIN_URL.'/flickr-carousel-gallery/';
$src=$path.'assets/ContentFlow/contentflow.js';
wp_enqueue_script('jqueryContentFlow',$src,array('jquery'));
/*echo' <script type="text/javascript">
        //var myNewFlow = new ContentFlow("myFantasicFlow",{onclickActiveItem:function(obj item){return false;}});
    </script>';*/
}
add_action('wp_head', 'jquery_content_flow_script',5);

//builds the gallery
function build_carousel_gallery($atts, $content='Loading...Celebrity Images') {
	$opts = mcg_get_options();
	$usr = $opts['mcg_userid'];
	$lang = $opts['mcg_language'];
	$format = $opts['mcg_thumbformat'];
	$hover = $opts['mcg_hover'];
	$scroller = $opts['mcg_showscroller'];
	$captioncolor = $opts['mcg_captioncolor'];
	$showscroller = $opts['mcg_showscroller'];
	$description = $opts['mcg_description'];
	$galformat = $opts['mcg_galleryformat'];
	extract(shortcode_atts(array(
		'photoset_id' 		=>'',
		'lang' 				=>'',
		'user_id' 			=> $usr,
		'tags' 				=>'',
		'tag_mode'			=>'',
		'min_upload_date'	=>'',
		'max_upload_date'	=>'', 
		'min_taken_date'	=>'',
		'max_taken_date'	=>'',
		'license'			=>'',
		'sort'				=>'',
		'bbox'				=>'',
		'accuracy'			=>'',
		'safe_search'		=>'',
		'content_type'		=>'',
		'machine_tags'		=>'',
		'group_id'			=>'',
		'lat'				=>'',
		'lon'				=>'',
		'radius_units'		=>'',
		'per_page'			=>'30',
		'extras'			=>'',
		'content'			=>$content,
		'hover'				=>$hover,
		'captioncolor'		=>	$captioncolor,
		'showscroller'		=>	$showscroller,
	), $atts));
	//echo($hover);
	$lang = "{$lang}";
	$photoset_id ="{$photoset_id}";
	if(function_exists(xlanguage_current_language_code)){
		$code = xlanguage_current_language_code();
	}else{
		$code = $lang;
	}
	if($hover == "yes"){
		//$class = "FCG-hover-image";
	}else{
		$class = "";
	}
	if($description == "yes"){
		$desc = ",description";
	}else{
		$desc = "";
	}
	if($code == $lang or $lang==''){
		//$flickr_gal = "<div class='ContentFlow'><div class='flow'><div class='flickr-mini-gallery ".$class."' lang=\"$format&$galformat\" rel=\"user_id={$user_id}&tags={$tags}&min_upload_date={$min_upload_date}&max_upload_date={$max_upload_date}&min_taken_date={$min_taken_date}&max_taken_date={$max_taken_date}&license={$license}&sort={$sort}&bbox={$bbox}&accuracy={$accuracy}&safe_search={$safe_search}&content_type={$content_type}&machine_tags={$machine_tags}&group_id={$group_id}&lat={$lat}&lon={$lon}&radius_units={$radius_units}&per_page={$per_page}&extras={$extras}$desc\" longdesc='photosearch'>{$content}</div></div></div>";
		$flickr_gal = "<div class='ContentFlow'>   <div class='loadIndicator'><div class='indicator'></div></div> <div class='flow flickr-carousel-gallery ".$class."' lang=\"$format&$galformat\" rel=\"user_id={$user_id}&tags={$tags}&min_upload_date={$min_upload_date}&max_upload_date={$max_upload_date}&min_taken_date={$min_taken_date}&max_taken_date={$max_taken_date}&license={$license}&sort={$sort}&bbox={$bbox}&accuracy={$accuracy}&safe_search={$safe_search}&content_type={$content_type}&machine_tags={$machine_tags}&group_id={$group_id}&lat={$lat}&lon={$lon}&radius_units={$radius_units}&per_page={$per_page}&extras={$extras}$desc\" longdesc='photosearch'>";
			require_once("lib/phpFlickr-3.1/phpFlickr.php");
				$f = new phpFlickr("fabf60447b1521f3175375e30b580059");
				$fargs=array(
					"user_id"=>$user_id,
					"tags"=>$tags,
					"min_upload_date"=>$min_upload_date,
					"max_upload_date"=>$max_upload_date,
					"min_taken_date"=>$min_taken_date,
					"max_taken_date"=>$max_taken_date,
					"license"=>$license,	
					"sort"=>$sort,
					"bbox"=>$bbox,
					"accuracy"=>$accuracy,
					"safe_search"=>$safe_search,
					"content_type"=>$content_type,
					"machine_tags"=>$machine_tags,
					"group_id"=>$group_id,
					"lat"=>$lat,
					"radius_units"=>$radius_units,
					"per_page"=>$per_page,
					"extras"=>$extras.$desc
					);
				$images=$f->photos_search($fargs);
				
				foreach($images['photo'] as $photo){
					//echo"<pre>";
					//print_r($photo);
					//echo"</pre>";
					$flickr_gal.= '<a class="item" rel="'.$photo['owner'].'" alt="'.$photo['id'].'" href="' . $f->buildPhotoURL($photo, "Medium") . '" title="'.$photo['title'].'"><img class="content" title="'.$photo['title'].'" alt="'.$photo['title'].'" longdesc="http://www.flickr.com/photos/'.$photo['owner'].'/'.$photo['id'].'" rel="" src="'.$f->buildPhotoURL($photo,"Square").'"  /> <div class="caption">'.$photo['title'].'</div></a>';
					//echo '<a class="item" href="' . $f->buildPhotoURL($photo, "Medium") . '" title="'.$photo['title'].'"><img class="content" src="'.$f->buildPhotoURL($photo,"Square").'"  /></a>';
					
				}	
			$flickr_gal.= "</div> ";
		
			$flickr_gal .= "<div class='globalCaption' style='color:".$captioncolor."'></div>";
				if($showscroller=="yes"){
				$flickr_gal.="<div class='scrollbar'><div class='slider'><div class='position'></div></div></div>";	
				}
		$flickr_gal.=" </div>";
	}else{
		$flickr_gal ="";
	}
	if($photoset_id != ''){
		//$flickr_gal = "<div class='flickr-mini-gallery ".$class."' lang=".$format.'&'.$galformat." rel=\"photoset_id={$photoset_id}&extras={$extras}$desc\" longdesc='photoset'>{$content}</div>";
		$flickr_gal = "<div class='ContentFlow'>  <div class='loadIndicator'><div class='indicator'></div></div> <div class='flow flickr-carousel-gallery ".$class."' lang=".$format.'&'.$galformat." rel=\"photoset_id={$photoset_id}&extras={$extras}$desc\" longdesc='photoset'>{$content}";
			require_once("lib/phpFlickr-3.1/phpFlickr.php");
				$f = new phpFlickr("fabf60447b1521f3175375e30b580059");
				$fargs=array(
					"photoset_id"=>$photoset_id,
					"privacy_filter"=>null,
					"per_page"=>$per_page,
					"extras"=>$extras.$desc
					);
				$images=$f->photosets_getPhotos($fargs['photoset_id'],$fargs['extras'],$fargs['privacy_filter'],$fargs['per_page']);
				
				foreach($images['photoset']['photo'] as $photo){
					//$flickr_gal.= "<div class='item'>";
					$flickr_gal.= '<a class="item" rel="'.$photo['owner'].'" alt="'.$photo['id'].'" href="' . $f->buildPhotoURL($photo, "Medium") . '" title="'.$photo['title'].'"><img class="content" title="'.$photo['title'].'" alt="'.$photo['title'].'" longdesc="http://www.flickr.com/photos/'.$photo['owner'].'/'.$photo['id'].'" rel="" src="'.$f->buildPhotoURL($photo,"Square").'"  /> <div class="caption">'.$photo['title'].'</div></a>';
					//$flickr_gal.= "</div>";
				}
		$flickr_gal.="</div> ";
		
		$flickr_gal .= "<div class='globalCaption' style='color:".$captioncolor."'></div>";
			if($showscroller=="yes"){
			$flickr_gal.="<div class='scrollbar'><div class='slider'><div class='position'></div></div></div>";	
			}
		$flickr_gal.=" </div>";
	}
	return $flickr_gal;
}
add_shortcode('flickrcarousel', 'build_carousel_gallery');


//----------------------------------------------------//
//OPTIONS
//----------------------------------------------------//
function mcg_get_options() {
	$mcg_userid = get_option('mcg_userid');
	$mcg_thumbformat = get_option('mcg_thumbformat');
	$mcg_hover = get_option('mcg_hover');
	$mcg_captioncolor = get_option('mcg_captioncolor');
	$mcg_showscroller = get_option('mcg_showscroller');
	$mcg_description = get_option('mcg_description');
	$mcg_galleryformat = get_option('mcg_galleryformat');
	
	// Extra paranoia:
	if(empty($mcg_userid))
		$mcg_userid = '';
	if(empty($mcg_thumbformat))
		$mcg_thumbformat = '_s';
	if(empty($mcg_hover))
		$mcg_hover = 'no';
	if(empty($mcg_captioncolor))
		$mcg_captioncolor = '#ddd';
	if(empty($mcg_showscroller))
		$mcg_showscroller = 'no';
	if(empty($mcg_description))
		$mcg_description = 'no';
		
	if(empty($mcg_galleryformat))
		$mcg_galleryformat = '';
		
	return array(
		'mcg_userid' => $mcg_userid,
		'mcg_thumbformat' => $mcg_thumbformat,
		'mcg_hover' => $mcg_hover,
		'mcg_captioncolor' => $mcg_captioncolor,
		'mcg_showscroller' => $mcg_showscroller,
		'mcg_description' => $mcg_description,
		'mcg_galleryformat' => $mcg_galleryformat,
		
	);
}




//----------------------------------------------------//
//USER INTERFACE
//----------------------------------------------------//

// Options update page:
// action function for above hook
function mcg_add_pages() {
    // Add a new submenu under Options:
    add_options_page('Flickr Carousel Gallery', 'Flickr Carousel Gallery', 8, 'flickrcarouselgallery', 'mcg_options_page');
}
// mcg_options_page() displays the page content for the Options submenu
function mcg_options_page() {
	if($_POST['action'] == 'update'){
		update_option('mcg_userid', $_POST['mcg_userid'] );
		update_option('mcg_thumbformat', $_POST['mcg_thumbformat'] );
		update_option('mcg_captioncolor', $_POST['mcg_captioncolor'] );
		update_option('mcg_hover', $_POST['mcg_hover'] );
		update_option('mcg_showscroller', $_POST['mcg_showscroller'] );
		update_option('mcg_description', $_POST['mcg_description'] );
		update_option('mcg_galleryformat', $_POST['mcg_galleryformat'] );
		
		
		?><div class="updated"><p><strong><?php _e('Options saved.', 'eg_trans_domain' ); ?></strong></p></div><?php
	};

    ?>
	<div class='wrap'>
	<iframe frameborder="0" src="http://www.designaeon.com/wp-content/plugins/wp_pro_ad_system/includes/api/load_adzone.php?zoneID=6" width="748" height="100" scrolling="no"></iframe>
		<h2>Flickr Carousel Gallery Options</h2>
		<form method='post'>
			<?php wp_nonce_field('miniflickrgallery_options'); ?>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="mcg_userid,mcg_thumbformat,mcg_language,mcg_hover,mcg_galleryformat" />
			<table class="form-table">
				<tbody>
					<tr valign="top">
					<th scope="row"><?php _e("Default Flickr User ID:", 'eg_trans_domain' ); ?></th>
						<td>
						<input type="text" name="mcg_userid" value="<?php echo get_option('mcg_userid'); ?>" />
						<br/>
						<a href="http://idgettr.com/">Find your flickr id</a>
						</td>
					</tr>
					
					<tr valign="top">
					<th scope="row"><?php _e("Thumbnail Format:", 'eg_trans_domain' ); ?></th>
						<td>
							<p>
							<?php $img = get_option('mcg_thumbformat'); ?>
								<select name="mcg_thumbformat">
                                    <option value ="_m" <?php if($img == "_m")echo 'selected="selected"'; ?>>Square</option> 
  									<option value ="_t" <?php if($img == "_t")echo 'selected="selected"'; ?>>Thumbnail</option>
								</select>
								<br/>
						Square is 75px x 75px and Thumbnail is 100px max						</p></td>
					</tr>
					
					<tr valign="top">
					<th scope="row"><?php _e("Lightbox image size:", 'eg_trans_domain' ); ?></th>
						<td>
							<p>
							<?php $imgGal = get_option('mcg_galleryformat'); ?>
								<select name="mcg_galleryformat">
									<option value ="" <?php if($imgGal == "")echo 'selected="selected"'; ?>>500px</option>
  									<option value ="_z" <?php if($imgGal == "_z")echo 'selected="selected"'; ?>>640px</option>
  									<option value ="_b" <?php if($imgGal == "_b")echo 'selected="selected"'; ?>>1024px</option>
								</select>
												</p></td>
					</tr>
					
					<!--<tr valign="top">
					<th scope="row"><?php _e("Enlarge image on rolover?", 'eg_trans_domain' ); ?></th>
						<td>
							<p>
							<?php $hover = get_option('mcg_hover'); ?>
									<input type="radio" name="mcg_hover"value ="no" <?php if($hover == "no")echo 'checked'; ?>>No
  									<input type="radio" name="mcg_hover" value ="yes" <?php if($hover == "yes")echo 'checked'; ?>>Yes
								
								<br/>
						choose if you want to show the image enlarge on rollover						</p></td>
					</tr>-->
					<tr valign="top">
					<th scope="row"><?php _e("Choose Caption Color", 'eg_trans_domain' ); ?></th>
						<td>
							<input type="text" name="mcg_captioncolor" value="<?php echo get_option('mcg_captioncolor'); ?>" /> Eg:#fff
						<br/>
						<a href="http://colorpicker.com/" target="new">Find Color Code Here :->></a>
						</td>
					</tr>
					
					<tr valign="top">
					<th scope="row"><?php _e("Show Scroll Bar?", 'eg_trans_domain' ); ?></th>
						<td>
							<p>
							<?php $showscroller = get_option('mcg_showscroller'); ?>
									<input type="radio" name="mcg_showscroller"value ="no" <?php if($showscroller == "no")echo 'checked'; ?>>No
  									<input type="radio" name="mcg_showscroller" value ="yes" <?php if($showscroller == "yes")echo 'checked'; ?>>Yes
								
								<br/>
						The Crawler Scroll bar	</p></td>
					</tr>
					
					
					
					
				</tbody>
			</table>
			
			<p class="submit">
				<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
<?php
}

add_action('admin_menu', 'mcg_add_pages');

//----------------------------------------------------//
//ADD BUTTON TO TINY MCE
//----------------------------------------------------//

class FCG_Buttons {
   function FCG_Buttons(){
    if(is_admin()){
        if ( current_user_can('edit_posts') && current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
        {
           add_filter('tiny_mce_version', array(&$this, 'tiny_mce_version') );
           add_filter("mce_external_plugins", array(&$this, "mce_external_plugins"));
           add_filter('mce_buttons', array(&$this, 'mce_buttons'));
        }
    }
   }
   function mce_buttons($buttons) {
    array_push($buttons, "|", "flickr_carousel_gallery" );
    return $buttons;
   }
   function mce_external_plugins($plugin_array) {
    $plugin_array['flickr_carousel_gallery']  =  plugins_url('/flickr-carousel-gallery/tiny-mce/editor-plugin.js');
    return $plugin_array;
   }
   function tiny_mce_version($version) {
    return ++$version;
   }
}
 
add_action('init', 'FCG_Buttons');
function FCG_Buttons(){
   global $FCG_Buttons;
   $FCG_Buttons = new FCG_Buttons();
}

?>