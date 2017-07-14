<div id="displet-stats">
	<?php if (count($model['results']) > 0) : ?>
		<?php if (isset($model['caption'])) : ?>
			<div class="displet-title">
				<?php echo $model['caption']; ?>
				<span class="displet-prices" style="display: none;">
					$<span class="displet-lowest-price-title"></span>
					to
					$<span class="displet-highest-price-title"></span>
				</span>
			</div>
		<?php endif; ?>
		<table>
			<tr>
				<th scope="row">Active Listings:</th>
				<td<?php if ($model['show_pending_count']=='no') echo ' colspan="3"'; ?>>
					<span class="displet-active-count">
						<?php echo esc_html($model['active']) ?>
					</span>
				</td>
				<?php if ($model['show_pending_count']!='no') : ?>
				<th scope="row">Pending/Contingent Listings:</th>
				<td>
					<span class="displet-pending-count">
						<?php echo esc_html($model['count']-$model['active']) ?>
					</span>
				</td>
				<?php endif; ?>
			</tr>
			<tr>
				<th scope="row">Price Range:</th>
				<td>
					$<span class="displet-lowest-price"><?php echo esc_html(number_format($model['price_range']['min'])) ?></span>
					to
					$<span class="displet-highest-price"><?php echo esc_html(number_format($model['price_range']['max'])) ?></span>
				</td>
				<th scope="row">Price Average:</th>
				<td>
					$<span class="displet-average-price"><?php echo esc_html(number_format($model['price_mean'])) ?></span>
				</td>
			</tr>
		<?php if ($model['size_range']['max'] > 0) :?>
			<tr>
				<th scope="row">Size Range:</th>
				<td>
					<span class="displet-lowest-square-footage"><?php echo esc_html(number_format($model['size_range']['min'])) ?></span>
					to
					<span class="displet-highest-square-footage"><?php echo esc_html(number_format($model['size_range']['max'])) ?></span>
					Sq. Ft.
				</td>
				<th scope="row">Size Average:</th>
				<td>
					<span class="displet-average-square-footage"><?php echo esc_html(number_format($model['size_mean'])) ?></span>
					Sq. Ft.
				</td>
			</tr>
		<?php endif ?>
		</table>
	<?php else : ?>
		<div class="displet-stats-no-results">
			<span>No listings available</span>
		</div>
	<?php endif ?>
</div>
