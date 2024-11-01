<?php
	
	# cannot call this file directly
	if ( strpos( basename( $_SERVER['PHP_SELF']) , __FILE__ ) !== false ) exit;
	
		
	?>
<script>
jQuery(document).ready(function($){
	$('input[type=text]').each(function(){
		var me = $(this);
		var p = me.attr('placeholder');
		var v = me.val() || p;
		if (Modernizr.input.placeholder) {
 		 // your placeholder text should already be visible!
		} else {
  			me.val(v);
  			me.focus(function(){
  				me.val('');
  			}).blur(function(){
  				v = me.val() || p;
  				me.val(v);
  			});
  		}

	});
});
//]]>
</script>

	<div class="wrap">
		
		<div id="ssp-settings-head">
			<div class="ssp-icon">&nbsp;</div>
			<h2><?php echo $title; ?></h2>
		</div>
		
		
	
		<?php echo form_open('options.php', array('id' => 'slideshowpro_form')); ?>
		
		<?php settings_fields('slideshowpro'); ?>

		<!-- Director Settings -->  
		
			<p>
				Need help? Open the <a href="http://wiki.slideshowpro.net/SSPdir/AC-WordPressPlugin" target="_blank" title="WordPress Plugin Instructions">plugin instructions</a>.
						</p>
								
			<table class="form-table ssp_form-table" style="clear:left;">
				<tr valign="top">
	  				<th scope="row"><label for="ssp_director_apikey">API Key</label></th>
	  					<td>
	  						<input type="text" placeholder="Enter API Key" id="ssp_director_apikey" name="slideshowpro_settings[ssp_director_apikey]" value="<?php echo ($options['ssp_director_apikey'] != 'Enter API Key') ? $options['ssp_director_apikey'] : ''; ?>" size="50" />
	  					</td>
				</tr>
				<tr valign="top">
	  				<th scope="row"><label for="ssp_director_apipath">API Path</label></th>
	  					<td>
	  						<input type="text" placeholder="Enter SlideShowPro Director Path" id="ssp_director_apipath" name="slideshowpro_settings[ssp_director_apipath]" value="<?php echo ($options['ssp_director_apipath'] != '') ? $options['ssp_director_apipath'] : ''; ?>" size="50" />
	  					</td>			
					</tr>
					<tr>
						<th scope="row">
							Thumbnail Previews
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>Thumbnail previews</span>
								</legend>
								<?php 
								$v = ( $options['ssp_director_tscale'] == 0 ) ? 0 : 1;
								echo form_checkbox('slideshowpro_settings[ssp_director_tscale]', 1, $v, 'id="ssp_director_tscale"' ); ?>
								<label for="ssp_director_tscale">Crop and display uniformly</label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							Images
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>Image Hyperlinks</span>
								</legend>
								<?php 
								$l = ( $options['ssp_img_links'] == 0 ) ? 0 : 1;
								echo form_checkbox('slideshowpro_settings[ssp_img_links]', 1, $l, 'id="ssp_img_links"' ); ?>
								<label for="ssp_img_links">Link to original</label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							Videos
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>Video settings</span>
								</legend>
								<?php 
								$a = ( $options['ssp_director_vid_auto'] == 0 ) ? 0 : 1;
								echo form_checkbox('slideshowpro_settings[ssp_director_vid_auto]', 1, $a, 'id="ssp_director_vid_auto"' ); ?>
								<label for="ssp_director_vid_auto">Auto play</label>
								<br />
								<?php 
								$c = ( $options['ssp_director_vid_controls'] == 0 ) ? 0 : 1;
								echo form_checkbox('slideshowpro_settings[ssp_director_vid_controls]', 1, $c, 'id="ssp_director_vid_controls"' ); ?>
								<label for="ssp_director_vid_controls">Show controls</label>
							</fieldset>
						</td>
					</tr>
			</table>

			<!-- / Director Settings -->
		
	
		
	
	
		<p class="submit">
			<?php echo form_submit(array('name'  => 'Submit', 
										 'value' => 'Save Changes', 
										 'class' => 'button-primary' )); ?>
		</p>
		
		<?php echo form_close(); ?>
		
		<br class="clear"/>
		<br class="clear"/>
		
		</div>
		
	</div> <!-- / wrap -->