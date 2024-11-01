<?php if ( ! isset( $_GET['iframe'] ) ): ?>
<ul class="subsubsub">
	 <li><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=slidepress" title="Overview">Overview</a> | </li>
	 <li><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=ssp_show_admin_slideshows" title="Slideshows">Slideshows</a> | </li>
	 <li><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=ssp_show_admin_styles" title="Styles">Styles</a> | </li>
	 <li><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=ssp_show_admin_style_editor" title="Style Editor">Style Editor</a> | </li>
	 <li><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=ssp_show_admin_setup" title="Setup">Setup</a> | </li>
	 <li><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=ssp_show_admin_help" title="Help">Help</a> | </li>
	 <li><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=ssp_show_admin_debug_info" title="Bug Report">Bug Report</a></li>
</ul>                      

<br class="clear"/>
<?php endif; ?>