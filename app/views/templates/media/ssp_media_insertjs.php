<?php
# cannot call this file directly
if ( strpos( basename( $_SERVER['PHP_SELF']) , __FILE__ ) !== false ) exit;
?>  
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php _e('Uploads'); ?> &#8212; <?php _e('WordPress'); ?></title>
<script type="text/javascript">
//<![CDATA[
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
var userSettings = {'url':'<?php echo SITECOOKIEPATH; ?>','uid':'<?php if ( ! isset($current_user) ) $current_user = wp_get_current_user(); echo $current_user->ID; ?>','time':'<?php echo time(); ?>'};
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>', pagenow = 'media-upload-popup', adminpage = 'media-upload-popup';
//]]>
</script>
<?php
do_action('admin_enqueue_scripts', 'media-upload-popup');
do_action('admin_print_styles-slideshowpro-media-popup');
do_action('admin_print_styles');
do_action('admin_print_scripts-slideshowpro-media-popup');
do_action('admin_print_scripts');

?>
</head>
<body<?php if ( isset($GLOBALS['body_id']) ) echo ' id="' . $GLOBALS['body_id'] . '"'; ?>>
	<script>post_id = <?php echo $calling_post_id; ?>;</script>
	<div id="ssp-loading"></div>
	<div id="ssp-navigation">
		<h3 class="media-title">Add image/video</h3>
		<?php 
		if(isset($error)):
			echo $error;
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($){
			$('#ssp-navigation').show();
			});
		</script>
		<?php else: ?>
		<p id="ssp-albums-select">
			<label>Select Album:</label>&nbsp;
			<select name="album" id="ssp_albums">
				<option value="0">Recently Uploaded</option>
			</select>
		</p>
		<p id="ssp-albums-sort">
			<label>Sort:</label>&nbsp;
			<select name="ssp_select_sort" id="ssp_select_sort">
				<option value="id">Date Uploaded</option>
				<option value="captured_on">Date Captured</option>
				<option value="modified">Date Modified</option>
				<option value="title">Filename</option>
			</select>
		</p>
	</div>
		<div style="display:block;clear:both;height:1px;">&nbsp;</div>
		 <div id="ssp_album">
			 <ul id="album_images"></ul>
		 </div>
		 <div id="ssp-temp"><ul></ul></div>
		 <div id="ssp-pages"></div>
	  	<div style="display:block;clear:both;">&nbsp;</div>
	  	<div id="ssp-insert-form">	
		</div>
		
	<?php /*  insert image Template
		 variables from the image object are accessed like ${image.attribute}
		 These can be moved around as much as necessary. */ ?>
		
		<script id="ssp-insert-table-tmpl" type="text/x-jquery-tmpl">
			<div style="display:none;" id="ssp-insert-table">
					<a href="#" id="ssp-close" title="Close window" class="ssp-close-win"></a>	
					<h3 class="title">Insert ${image.media_type}</h3>
		  				<table cellpadding="0" cellspacing="5" class="outer-table">
					  	<tr>
					  		<td valign="top">
					  			<div id="ssp-thumb-image">
					  			{{if image.is_video }}<div class="ssp-vid-overlay">&nbsp;</div>{{/if}}
					  			</div></td>
					  		<td valign="top">
								<table cellpadding="0" cellspacing="0" class="inner-table">
									<tr>
										<td>
											<label>File:</label> <span id="ssp-filename">${image.src}</span>
										</td>
									</tr>
									{{if !image.is_video}}
									<tr id="ssp-insert-row-dims">
										<td>
											<label>Dimensions:</label> <span id="ssp-dimensions">${image.original.dimensions}</span>
						  				</td>
									</tr>
									{{/if}}
									<tr>
										<td style="padding-top:10px;">
										<?php if( current_theme_supports( 'post-thumbnails' ) ): ?>
											{{if image.show_featured }}
											<a id="ssp-featured" class="button wp-post-thumbnail" title="Use as Featured Image" style="margin:0;">Use as Featured Image</a>
											{{/if}}
										<?php endif; ?>
										</td>
									</tr>
								</table>
							</td>
					  	</tr>
					  	<tr>
					  		<td valign="top"><label for="ssp-image-title">Title</label></td><td><input id="ssp-image-title" class="wide-input" type="text" name="image[title]" value="${image.title}" /></td>
					  	</tr>
					  	<tr>
					  		<td valign="top"><label for="ssp-image-caption">Caption</label></td><td><input id="ssp-image-caption" class="wide-input" type="text" name="image[caption]" value="${image.caption}" /></td>
					  	</tr>
					  	<tr>
					  		<td></td>
					  		<td>
					  			<input id="ssp-image-display-title" type="checkbox" name="image[display_title]" value="1" /> <label for="ssp-image-display-title" class="sub-opt">Display title in post</label>&nbsp;&nbsp;
					  			<input id="ssp-image-display-caption" type="checkbox" name="image[display_caption]" value="1" /> <label for="ssp-image-display-caption" class="sub-opt">Display caption in post</label>
					  		</td>
					  	</tr>
						<tr>
					  		<td valign="top"><label for="ssp-image-link">Link</label></td><td><input id="ssp-image-link" class="wide-input" type="text" name="image[link]" value="${image.link}" /></td>
					  	</tr>
					  	{{if !image.is_video}}
					  	<tr id="ssp-insert-row-size-image">
					  		<td valign="top"><label>Size</label></td>
					  		<td>
					  			<table cellpadding="0" cellspacing="0" class="inner-table">
					  			<tr>
					  				<td>
					  					<input id="ssp-image-size-auto" type="radio" checked name="image[size]" value="large" /> <label id="ssp-image-size-auto-label" for="ssp-image-size-auto">${large_lbl}</label>&nbsp;<label for="ssp-image-size-auto" id="ssp-auto-dim" class="help">(${image.large.dimensions})</label>
					  				</td>
					  				<td class="col-2">
										<input id="ssp-image-size-thumbnail" type="radio" name="image[size]" value="thumb" /> <label id="ssp-image-size-thumbnail-label" for="ssp-image-size-thumbnail">Thumbnail</label>&nbsp;<label for="ssp-image-size-thumbnail" id="ssp-thumbnail-dim" class="help">(${image.thumbnail.dimensions})</label>
					  				</td>
					  			</tr>
					  			<tr>
					  				<td>
					  					<input id="ssp-image-size-medium" type="radio" name="image[size]" value="medium" /> <label id="ssp-image-size-medium-label" for="ssp-image-size-medium">Medium</label>&nbsp;<label for="ssp-image-size-medium" id="ssp-medium-dim" class="help">(${image.medium.dimensions})</label>
					  				</td>
					  				<td class="col-2">
					  					<input id="ssp-image-size-full" type="radio" name="image[size]" value="original" /> <label id="ssp-image-size-full-label" for="ssp-image-size-full">Full</label>&nbsp;<label for="ssp-image-size-full" id="ssp-full-dim" class="help">(${image.original.dimensions})</label>
					  				</td>
					  			</tr>
					  			<tr>
					  				<td colspan="2">
					  					<input id="ssp-image-size-custom" type="radio" name="image[size]" value="custom" /> <label for="ssp-image-size-custom">Custom</label>
					  					<input type="text" id="ssp-image-size-custom-width" size="2" name="image[custom_width]" value="" class="small-input" style="margin-bottom:-3px;" />&nbsp;x&nbsp;<input type="text" id="ssp-image-size-custom-height" name="image[custom_height]" size="2" value="" class="small-input" style="margin-bottom:-3px;" />&nbsp;<input id="ssp-constrain-img" name="ssp-constrain-img" type="checkbox" checked="checked" /> <label for="ssp-constrain-img" class="sub-opt">Constrain proportions</label>
					  					<div id="ssp-scale-warn" class="ssp-form-warn" style="display:none;"><span>Dimensions are larger than original image.</span></div>
					  				</td>
					  			</tr>
					  			</table>
					  		</td>
					  	</tr>
					  	{{else}}
						<tr id="ssp-insert-row-size-video">
							<td valign="top"><label>Width</label></td>
							<td>
								<table cellpadding="0" cellspacing="0" class="inner-table">
								<tr>
									<td>
										<input id="ssp-video-size-auto" type="radio" checked name="video[size]" value="video" />&nbsp;<label id="ssp-video-size-auto-label" for="ssp-video-size-auto">${large_lbl}</label>&nbsp;<label for="ssp-video-size-auto" id="ssp-video-size-auto" class="help">(${image.width})</label>
									</td>
									<td>
					  					<input id="ssp-video-size-custom" type="radio" name="video[size]" value="custom" /> <label for="">Custom</label>
					  					<input type="text" id="ssp-video-size-custom-width" size="2" name="ssp-video-size-custom-width" value="" class="small-input" />
										
					  				</td>
								</tr>
							
								</table>
								<span class="description">Height will be calculated automatically for proportional scale.</span>
							</td>
						</tr>
						<tr id="ssp-insert-row-options-video">
							<td valign="top"><label>Options</label></td>
							<td>
								<table cellpadding="0" cellspacing="0" class="inner-table">
					  			<tr>
					  				<td>
										<input id="ssp-video-options-auto" type="checkbox" <?php echo $auto; ?> />&nbsp;<label id="ssp-video-options-auto-label" for="ssp-video-options-auto">Auto play</label>
									</td>
									<td>
										<input id="ssp-video-options-controls" type="checkbox" <?php echo $controls; ?> />&nbsp;<label id="ssp-video-options-controls-label" for="ssp-video-options-controls">Show controls</label>
									</td>
								</tr>
								</table>
							</td>
						</tr>
						{{/if}}
					  	<tr class="align" id="ssp-insert-row-align">
				  			<td valign="top"><label>Alignment</label></td>
				  			<td class="options">
				  				<input name="image[align]" id="ssp-image-align-none" checked="checked" value="" type="radio" /><label for="ssp-image-align-none" class="align image-align-none-label">None</label>
								<input name="image[align]" id="ssp-image-align-left" value="alignleft" type="radio" /><label for="ssp-image-align-left" class="align image-align-left-label">Left</label>
								<input name="image[align]" id="ssp-image-align-center" value="aligncenter" type="radio" /><label for="ssp-image-align-center" class="align image-align-center-label">Center</label>
								<input name="image[align]" id="ssp-image-align-right" value="alignright" type="radio" /><label for="ssp-image-align-right" class="align image-align-right-label">Right</label>	  		
							</td>
						</tr>
						<tr>
							<td colspan="2" align="right" class="bttns">
								<button id="ssp-cancel" class="button" title="Cancel">Cancel</button>
								<button id="ssp-insert-continue" class="button" title="Insert and Continue">Insert and Continue</button>
								<button id="ssp-insert" class="button-primary" title="Insert">Insert</button>

							</td>
						</tr>
					</table>			
				</div> <!-- /ssp-insert-table -->
			</script>

<?php
	do_action('admin_print_footer_scripts');
?>
<script type="text/javascript" >
jQuery(document).ready(function($) {
    
    slideshowpro.insert_init({content_width: "<?php echo $content_width[0]; ?>",
        tscale: <?php echo $tscale; ?> ,
        no_thumb:'<?php echo $no_thumb; ?>',
        large_lbl : '<?php echo $content_width[1]; ?>' || 'Large',
        path:'<?php echo $path; ?>',
        link_images:<?php echo $link_images; ?>,
        ajax_url:'<?php echo admin_url("admin-ajax.php"); ?>',
        featured_nonce:'<?php echo wp_create_nonce( "set_post_thumbnail-$calling_post_id" ); ?>',
        hosted:<?php echo $hosted; ?>,
        image_sizes : {
            thumb: {
                w: 112,
                h: 112,
                c: 1,
                q: 70,
                sh: 1,
                sq:<?php echo $tscale; ?>
            },
            thumbnail: {
                w: <?php echo $tw; ?> ,
                h : <?php echo $th; ?> ,
                c : 1,
                q : 70,
                sh : 1,
                sq: 1
            },
            medium: {
                w: <?php echo $mw; ?> ,
                h : <?php echo $mh; ?> ,
                c : 1,
                q : 85,
                sh : 1,
                sq: 0
            },
            large: {
                w: <?php echo $lw; ?> ,
                h : <?php echo $lh; ?> ,
                c : 1,
                q : 85,
                sh : 1,
                sq: 0
            }
            <?php echo $sizes; ?>
        }
       });
});
</script>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
</body>
</html>
<?php endif; ?>