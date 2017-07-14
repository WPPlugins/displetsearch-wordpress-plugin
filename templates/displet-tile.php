<!--[if lt IE 9]>
	<style>
		.tileoverlay div {
			/* IE8+ - must be on one line, unfortunately */
			-ms-filter: "progid:DXImageTransform.Microsoft.Matrix(M11=0.7071067811865474, M12=-0.7071067811865477, M21=0.7071067811865477, M22=0.7071067811865474, SizingMethod='auto expand')";
			/* IE6 and 7 */
			filter: progid:DXImageTransform.Microsoft.Matrix(
				M11=0.7071067811865474,
				M12=-0.7071067811865477,
				M21=0.7071067811865477,
				M22=0.7071067811865474,
				SizingMethod='auto expand');
			margin-left: 20px !important;
			margin-top: 3px !important;
			width: 100px;
		}
	</style>
<![endif]-->

<div id="displet-tile">
	<?php if (count($model['results']) > 0) : ?>
		
		<div class="displet-tile-controls">
			<div class="displet-tile-counts">
				<span class="displet-tile-counts-first">1</span> &ndash;
				<span class="displet-tile-counts-last"><?php echo (int) $model['rpp'] ?></span> of
				<span class="displet-tile-counts-total"><?php echo (int) $model['count'] ?></span>
			</div>

			<a href="#prev" class="displet-tile-control-prev-link">
				<span class="displet-tile-control-prev-icon">&laquo;</span>
				<span class="displet-tile-control-prev">Prev</span>
			</a>
			<a href="#next" class="displet-tile-control-next-link">
				<span class="displet-tile-control-next">Next</span>
				<span class="displet-tile-control-next-icon">&raquo;</span>
			</a>

			<select class="displet-tile-sortby" name="sort_by">
				<option value="">Sort By</option>
				<option value="list-price-asc">Price Low to High</option>
				<option value="list-price-desc">Price High to Low</option>
				<?php
					if (count($results) > 24) {
						echo '<option value="">--------------</option>';
						echo '<option value="" displetminprice="0" displetmaxprice="999999999">All Prices</option>';
						$nav_positions = $model['nav_elements'];
						if (isset($nav_positions)) {
							foreach ($nav_positions as $nav_position) {
								echo '<option value="" displetminprice="' . $nav_position['min'] . '" displetmaxprice="' . $nav_position['max'] . '">$' . number_format($nav_position['min']) . ' - $' . number_format($nav_position['max']) . '</option>';
							}
						}
					}
				?>
			</select>
		</div>

		<div class="displet-tile-listings">

		<?php foreach ($model['results'] as $result) : ?>
			<?php
				$thumb_src = (empty($result->images->thumb[0])) ? $model['nothumb_url'] : $result->images->thumb[0];
				$cat_address = $result->{'street-number'} . ' '	. $result->{'street-name'};
				if ((string) $result->unit) {
					$cat_address .= ' #' . ((string) $result->unit);
				}
				$cat_address .= ' ' . $result->city . ', ' . $result->state . ' ' . $result->zip;
				if (!$model['use_pro']) {
					$url_tokens = explode("/", $result->details);
					$details_page = get_permalink($model['landing_page']) . '?property='. end($url_tokens);
				}
				else {
					$details_page = esc_url($result->details);
				}
			?>
			<a class="displet-tile-listing" href="<?php echo $details_page ?>" title="<?php echo esc_attr($cat_address) ?>">
				<div class="displet-tile-hovertrans">
					<div class="displet-inner">
						<div class="displet-inner2"><!-- --></div>
					</div>
				</div>
				<div class="displet-tile-thumb-container">
						<img value="<?php echo esc_url($thumb_src) ?>" alt="<?php echo esc_attr($cat_address) ?>" />
						<?php $status = $result->status; if (stripos($status,'pen')!==false || stripos($status,'und')!==false) : ?>
							<div class="tileoverlay"><div>Under Contract</div></div>
						<?php elseif (stripos($status,'conti')!==false) : ?>
							<div class="tileoverlay"><div>Contingency</div></div>
						<?php endif; ?>
				</div>

				<div class="displet-tile-price-container">
					<span class="displet-dollar">$</span>
					<span class="displet-tile-listing-price">
						<?php echo esc_html(number_format((int) $result->{'list-price'})) ?>
					</span>
					<div class="displet-clear"><!-- --></div>
				</div>

				<div class="displet-tile-info">
					<div class="displet-tile-address">
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
					<div class="displet-tile-specs">
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
			</a>
		<?php endforeach; ?>
		</div>
		<div class="displet-clear"><!-- --></div>

		<div class="displet-tile-controls">
			<div class="displet-tile-counts">
				<span class="displet-tile-counts-first">1</span> &ndash;
				<span class="displet-tile-counts-last"><?php echo (int) $model['rpp'] ?></span> of
				<span class="displet-tile-counts-total"><?php echo (int) $model['count'] ?></span>
			</div>
			<a href="#prev" class="displet-tile-control-prev-link">
				<span class="displet-tile-control-prev-icon">&laquo;</span>
				<span class="displet-tile-control-prev">Prev</span>
			</a>
			<a href="#next" class="displet-tile-control-next-link">
				<span class="displet-tile-control-next">Next</span>
				<span class="displet-tile-control-next-icon">&raquo;</span>
			</a>
		</div>
		<div class="displet-powered">
			Powered by
			<?php if ($model['remove_link'] != 'yes') : ?>
				<a href="http://displet.com/wordpress-plugins/displet-rets-idx-plugin/" target="_blank">Displet RETS / IDX Plugin</a>
			<?php else : ?>
				Displet RETS / IDX Plugin
			<?php endif; ?>
		</div>
		<?php if (!empty($model['disclaimer'])) : ?>
			<div class="displet-tile-disclaimer">
				<?php echo $model['disclaimer'] ?>
			</div>
		<?php endif; ?>
		
	<?php else : ?>
		<div class="displet-tile-no-results"><span>No listings available</span></div>
	<?php endif; ?>
</div>