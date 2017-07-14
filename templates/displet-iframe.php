<?php
/*
 * Note: All the inline styles are because of excessive specificity in WordPress themes
 * (including twentyten)
 */
?>
<div class="ondisplet_frame_container">
	<iframe src="<?php echo $model['iframe_url']; ?>" class="ondisplet_frame" style="height:<?php echo ($model['height'] ? $model['height'] : "6000px")?>; width:<?php echo ($model['width'] ? $model['width'] : "100%")?>;"/>
		<p>Your browser does not support frames. Please consider updating to a modern browser like <a href="http://www.mozilla.org/en-US/firefox/new/">Firefox</a></p>
	</iframe>

	<?php if (!empty($model['disclaimer'])) : ?>
		<div class="displet-listings-disclaimer">
			<?php echo $model['disclaimer'] ?>
		</div>
	<?php endif ?>

</div>