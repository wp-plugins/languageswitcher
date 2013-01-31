<?php 
	require_once('../../../../wp-load.php');
	header("Content-type: text/css; charset: UTF-8");
	$options = get_option('languageswitcher_options');
?>

.languageswitcher.switch {
	display: block;
	max-width: 100%;
	height: 25px;
	cursor: pointer;
	background: <?php echo $options['color_background_inactive']?>;
	color: <?php echo $options['color_text_inactive']?>;;
	margin: 10px 0;
	margin: 0.7142857143rem 0;
}

.languageswitcher.switch.active {
	color: <?php echo $options['color_text_active']?>;
	background: <?php echo $options['color_background_active']?>;
}

<?php if ($options['shadow'] == 'Yes'): ?>
.languageswitcher.switch:hover,
.languageswitcher.switch:active, .languageswitcher.switch:focus {
	box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5) !important;
}
<?php endif; ?>


.languageswitcher.switch > .languageswitcher.arrow {
	margin: 0 10px 0 5px;
}