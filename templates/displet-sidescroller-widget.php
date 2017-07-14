<div id="displet-sidescroller-widget" class="displet-listings-carousel">
	<?php if (count($model['results']) > 0) : ?>
		<ul>
		<?php $i=1; foreach ($model['results'] as $result) : if ($i <= intval($model['max_listings'])) : ?>
			<?php 
				$thumb_src = (empty($result->images->thumb[0])) ? $model['nothumb_url'] : $result->images->thumb[0]; 
				if (empty($model['use_pro'])) {
					$url_tokens = explode("/", $result->details);
					$details_page = get_permalink($model['landing_page']) . '?property='. end($url_tokens);
				} else {
					$details_page = esc_url($result->details);
				}
			?>
			<li><a class="displet-sidescroller-widget-listing" href="<?php echo $details_page ?>">
				<div class="displet-sidescroller-widget-thumb-container">
					<img src="<?php echo esc_url($thumb_src) ?>"/>
				</div>
				<div class="displet-sidescroller-widget-price-container">
					<span class="displet-dollar">$</span>
					<span class="displet-sidescroller-widget-listing-price">
						<?php echo esc_html(number_format((int) $result->{'list-price'})) ?>
					</span>
					<div class="displet-clear"><!-- --></div>
				</div>
				<div class="displet-sidescroller-widget-info">
					<div class="displet-sidescroller-widget-address">
						<div class="displet-street-address">
							<?php
								echo esc_html($result->{'street-number'}) . ' ' . esc_html($result->{'street-name'});
								if ((string) $result->unit) {
									echo ' #' . esc_html($result->{'unit'});
								}
							?>
						</div>
						<div class="displet-city-state-zip">
							<?php echo esc_html($result->{'city'}) . ', ' . esc_html($result->{'state'}) . ' ' . esc_html($result->{'zip'}); ?>
						</div>
						<?php if ((string) $result->subdivision) : ?>
							<div class="displet-subdivision">
								<?php echo esc_html($result->{'subdivision'}); ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="displet-sidescroller-widget-specs">
						<?php if ($result->{'property-type'} != 'Land') : ?>
							<div class="displet-beds-baths">
								<?php echo esc_html($result->{'num-bedrooms'}); ?> Beds / <?php echo esc_html($result->{'full-baths'}); ?> Full Bath / <?php echo esc_html($result->{'half-baths'}) ?> Half Bath
							</div>
							<div class="displet-square-feet">
								<span class="displet-square-feet-value"><?php echo number_format(esc_html($result->{'square-feet'})); ?></span>
								Sq. Ft.
							</div>
							<div class="displet-property-type">
								Property Type: <?php echo esc_html($result->{'property-type'}); ?>
							</div>
							<?php if ($model['include_listing_office'] == 'yes') : ?>
								<div class="displet-listing-office">
									<?php echo esc_html($result->{'listing-office-name'}); ?>
								</div>
							<?php endif; ?>
						<?php else : ?>
							<div class="displet-property-type">
								Property Type: Land
							</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="displet-view-details">
					View Details
				</div>
			</a></li>
		<?php $i++; endif; endforeach; ?>
		</ul>
		<div class="displet-navigation">
			<div class="displet-navigation-inner">	
				<a class="displet-navigation-previous">
					Previous
				</a>
				<a class="displet-navigation-next">
					Next
				</a>
			</div>
		</div>
	<?php else : ?>
		<div class="displet-sidescroller-widget-no-results"><span>No listings available</span></div>
	<?php endif ?>
</div>