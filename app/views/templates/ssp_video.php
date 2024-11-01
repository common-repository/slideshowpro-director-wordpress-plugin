<?php
global $post;
# cannot call this file directly
if ( strpos( basename( $_SERVER['PHP_SELF']) , __FILE__ ) !== false ) die("Can't load this file directly!");

?>
	<!-- SlideShowPro Embed -->
	<div id="ssp-video-<?php echo $unique_id; ?>"></div>
	<script> 
		var video = document.createElement('video');
		var vid = jQuery('#ssp-video-<?php echo $unique_id; ?>');
		if (typeof(video.canPlayType) == 'undefined' || video.canPlayType('video/mp4') == '' || <?php echo $flv; ?>) {
			var flashvars_<?php echo $unique_id; ?> = {
				xmlFileType: 'Single Content',
				xmlFilePath: '<?php echo $url; ?>',
				videoPreviewURL:'<?php echo $preview; ?>',
				navAppearance: 'Hidden',
				displayMode: 'Manual',
				videoAutoStart: <?php echo $autostart; ?>,
				mediaNavAppearance: <?php echo $controls; ?>
			}
			var params_<?php echo $unique_id; ?> = {
				bgcolor: "#333333",
				allowfullscreen: "true",
				allowscriptaccess: 'always'
			}                
			var attributes_<?php echo $unique_id; ?> = {}
			swfobject.embedSWF( "<?php echo $directorSWF; ?>", "ssp-video-<?php echo $unique_id; ?>", "<?php echo $width; ?>", "<?php echo $height; ?>", "9.0.0", false, flashvars_<?php echo $unique_id; ?>, params_<?php echo $unique_id; ?>, attributes_<?php echo $unique_id; ?>);
		} else {
			video.src = '<?php echo $url; ?>';
			video.width = <?php echo $width; ?>;
			video.height = <?php echo $height; ?>;
			video.controls = <?php echo $controls; ?>;
			video.autoplay = <?php echo $autostart; ?>;
			video.poster = '<?php echo $preview; ?>';
			vid.append(video);
		}
	</script>  
	<!-- SlideShowPro Embed Ends -->
	