<div class="displet-stats-widget">

<?php if (count($model['results']) > 0) : ?>
	<table>
		<tr>
			<th scope="row">Available Listings:</th>
			<td><?php echo esc_html($model['count']) ?></td>
		</tr>
		<tr>
			<th scope="row">Price Range:</th>
			<td>$<?php echo esc_html(number_format($model['price_range']['min'])) ?> to
				$<?php echo esc_html(number_format($model['price_range']['max'])) ?>
			</td>
		</tr>
		<tr>
			<th scope="row">Size Range:</th>
			<td><?php echo esc_html(number_format($model['size_range']['min'])) ?> to
				<?php echo esc_html(number_format($model['size_range']['max'])) ?> Sq. Ft.
			</td>
		</tr>
	</table>
<?php else : ?>
	<div class="displet-stats-widget-no-results">
		<span>No listings available</span>
	</div>
<?php endif ?>
</div>
