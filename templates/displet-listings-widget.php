<?php if (count($model['results']) > 0) : ?>

<?php $i=1; foreach ($model['results'] as $result) : if ($i <= intval($model['max_listings'])) : ?>

<?php
$thumb_src = (empty($result->images->thumb[0])) ? $model['nothumb_url'] : $result->images->thumb[0];
$cat_address = $result->{'street-number'} . ' '
	. $result->{'street-name'};
if ((string) $result->unit) {
	$cat_address .= ' #' . ((string) $result->unit);
}
$cat_address .= ' ' . $result->city . ', ' . $result->state . ' ' . $result->zip;
if (!$model['use_pro']) {
	$url_tokens = explode("/", $result->details);
	$details_page = get_permalink($model['landing_page']) . '?property='. end($url_tokens);
} else {
	$details_page = esc_url($result->details);
}
?>

<a class="displet-listing-widget-listing"
	href="<?php echo $details_page ?>"
	title="<?php echo esc_attr($cat_address) ?>">

	<div class="displet-listing-widget-thumb-container">
		<img src="<?php echo esc_url($thumb_src) ?>"/>
	</div>

	<div class="displet-listing-widget-price-container">
		<span class="displet-listing-widget-price">
			$<?php echo esc_html(number_format((int) $result->{'list-price'})) ?>
		</span>
	</div>

	<div class="displet-listing-widget-bottom">
		<div class="displet-listing-widget-address-container">
			<div class="displet-listing-widget-address">
				<?php echo esc_html($result->{'street-number'}) ?>&nbsp;
				<?php echo esc_html($result->{'street-name'}) ?>&nbsp;
		<?php if ((string) $result->unit) : ?>
				#<?php echo esc_html($result->{'unit'}) ?>
		<?php endif ?>
				<br/>
				<?php echo esc_html($result->city) ?>,&nbsp;<?php echo esc_html($result->state)?>&nbsp;<?php echo esc_html($result->zip) ?>
				<br/>
		<?php if ((string) $result->subdivision) : ?>
			<?php echo esc_html($result->subdivision) ?>
		<?php endif ?>
			</div>
		</div>

		<div class="displet-listing-widget-info-container">
			<b>Property Type:&nbsp;</b><?php echo esc_html($result->{'property-type'}) ?><br/>
			<b>Beds:&nbsp;</b><?php echo esc_html($result->{'num-bedrooms'}) ?>
			<b>Baths:&nbsp;</b><?php echo esc_html($result->{'full-baths'}) ?>
			<br/>
			<b>Size:&nbsp;</b><?php echo esc_html($result->{'square-feet'}) ?> Sq. Ft.
		</div>
<!--
		<div class="displet-listing-widget-details-container">
			<a href="<?php echo esc_url($result->{'details'}) ?>">View Details</a>
		</div>
-->
	</div>
</a>

<?php $i++; endif; endforeach; ?>

<?php else : ?>
	<div class="displet-listing-widget-no-results"><span>No listings available</span></div>
<?php endif ?>
