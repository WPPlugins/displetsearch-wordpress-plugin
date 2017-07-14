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

<?php $results = &$model['results']; ?>

<div id="displet-listings">
	<?php if (count($results) > 0) : ?>
	
		<div class="displet-listings-controls">
			<div class="displet-listings-counts">
				<span class="displet-listings-counts-first">1</span> &ndash;
				<span class="displet-listings-counts-last"><?php echo (int) $model['rpp'] ?></span> of
				<span class="displet-listings-counts-total"><?php echo (int) $model['count'] ?></span>
			</div>
		
			<a href="#prev" class="displet-listings-control-prev-link">
				<span class="displet-listings-control-prev-icon">&laquo;</span>
				<span class="displet-listings-control-prev">Prev</span>
			</a>
			<a href="#next" class="displet-listings-control-next-link">
				<span class="displet-listings-control-next">Next</span>
				<span class="displet-listings-control-next-icon">&raquo;</span>
			</a>
		
			<select class="displet-listings-sortby" name="sort_by">
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
	
		<div>
			<?php foreach ($results as $result) : ?>
				<?php
					$thumb_src = (empty($result->images->thumb[0])) ? $model['nothumb_url'] : $result->images->thumb[0];
					$cat_address = $result->{'street-number'} . ' ' . $result->{'street-name'};
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
				<a class="displet-listing" href="<?php echo $details_page ?>" title="<?php echo esc_attr($cat_address) ?>">
					<div class="displet-listing-hovertrans">
						<div class="displet-inner">
							<div class="displet-inner2"><!-- --></div>
						</div>
					</div>
					<div class="displet-listing-thumb-container">
						<img value="<?php echo esc_attr($thumb_src) ?>" alt="<?php echo esc_attr($cat_address) ?>" />
						<?php $status = $result->status; if (stripos($status,'pen')!==false || stripos($status,'und')!==false) : ?>
							<div class="tileoverlay"><div>Under Contract</div></div>
						<?php elseif (stripos($status,'conti')!==false) : ?>
							<div class="tileoverlay"><div>Contingency</div></div>
						<?php endif; ?>
					</div>
					<div class="displet-listing-info">
						<div class="displet-listing-price">
							<span class="displet-listing-dollar">$</span>
							<span class="displet-listing-price-value">
								<?php echo esc_html(number_format((int) $result->{'list-price'})) ?>
							</span>
						</div>
						<div class="displet-listing-address">
								<span class="displet-listing-street-number"><?php echo esc_html($result->{'street-number'}) ?></span>&nbsp;
								<span class="displet-listing-street-name"><?php echo esc_html($result->{'street-name'}) ?></span>&nbsp;
								<?php if ((string) $result->unit) : ?>
									<span class="displet-listing-unit">
										<?php echo '# ' . $result->unit ?>&nbsp;
									</span>
								<?php endif ?>|&nbsp;
								<span class="displet-listing-city"><?php echo esc_html($result->city) ?></span>,&nbsp;
								<span class="displet-listing-state"><?php echo esc_html($result->state) ?></span>&nbsp;
								<span class="displet-listing-zip"><?php echo esc_html($result->zip) ?></span>
						</div>
						<div class="displet-clear"><!-- --></div>
						<table class="displet-listing-specs">
							<thead>
								<tr>
									<th>MLS#</th>
									<th>Property Type</th>
									<th>Beds</th>
									<th>Baths (Full/Half)</th>
									<th>Square Ft</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="displet-listing-mls"><?php echo esc_html($result->{'mls-number'}) ?></td>
									<td class="displet-listing-property-type"><?php echo esc_html($result->{'property-type'}) ?></td>
									<td class="displet-listing-beds"><?php echo esc_html($result->{'num-bedrooms'}) ?></td>
									<td>
										<span class="displet-listing-full-baths"><?php echo esc_html($result->{'full-baths'}) ?></span>/<span class="displet-listing-half-baths"><?php echo esc_html($result->{'half-baths'}) ?></span>
									</td>
									<td><span class="displet-listing-sq-ft"><?php echo esc_html(number_format((int) $result->{'square-feet'})) ?></span>&nbsp;s.f.</td>
								</tr>
							</tbody>
						</table>
						<div class="displet-listing-description">
							<?php echo esc_html($result->{'internet-remarks'}) ?>
						</div>
						<div class="displet-text-overlay"><!-- --></div>
						<div class="displet-text-overlay-hovertrans"><!-- --></div>
					</div>
					<div class="displet-clear"><!-- --></div>
				</a>
			<?php endforeach ?>
		</div>
	
		<div class="displet-listings-controls">
			<div class="displet-listings-counts">
				<span class="displet-listings-counts-first">1</span> &ndash;
				<span class="displet-listings-counts-last"><?php echo (int) $model['rpp'] ?></span> of
				<span class="displet-listings-counts-total"><?php echo (int) $model['count'] ?></span>
			</div>
		
			<a href="#prev" class="displet-listings-control-prev-link">
				<span class="displet-listings-control-prev-icon">&laquo;</span>
				<span class="displet-listings-control-prev">Prev</span>
			</a>
			<a href="#next" class="displet-listings-control-next-link">
				<span class="displet-listings-control-next">Next</span>
				<span class="displet-listings-control-next-icon">&raquo;</span>
			</a>
		</div>
	
		<div class="displet-powered">
			Powered by
			<?php if ($model['remove_link'] != 'yes') : ?>
				<a href="http://displet.com/wordpress-plugins/displet-rets-idx-plugin/" target="_blank">Displet RETS / IDX Plugin</a>
			<?php else : ?>
				Displet RETS / IDX Plugin
			<?php endif; ?>
		</div><!--// .displet-powered -->
		
		<?php if (!empty($model['disclaimer'])) : ?>
			<div class="displet-listings-disclaimer">
				<?php echo $model['disclaimer'] ?>
			</div>
		<?php endif; ?>
		
	<?php else : ?>
		<div class="displet-listings-no-results"><span>No listings available</span></div>
	<?php endif; ?>
</div>