<?php if($show_header): ?>
<?php $this->load->view('inc/ssp_header'); ?>
<?php $this->load->view('inc/ssp_subnav'); ?>
<?php endif; ?>
<?php $this->load->view('templates/'.$template); ?>
<?php if($show_footer): ?>
<?php $this->load->view('inc/ssp_footer'); ?>
<?php endif; ?>