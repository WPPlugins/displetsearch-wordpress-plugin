<div id="displet-price-navigation">
	<?php if (isset($model['caption'])) : ?>
		<div class="displet-title">
			<?php echo $model['caption']; ?> by Price
		</div>
	<?php endif; ?>
	<table>
		<tbody>
			<tr>
				<td>
					<a class="displet-price-navigation active" displetminprice="0" displetmaxprice="999999999">All Prices</a>
				</td>
				<?php
					$nav_positions = $model['nav_elements'];
					$i = 2;
					foreach ($nav_positions as $nav_position) {
						if ($i % 3 == 1) {
							echo '</tr><tr>';
						}
						if ($nav_position['min'] == 0){
							echo '<td><a class="displet-price-navigation" displetminprice="' . $nav_position['min'] . '" displetmaxprice="' . $nav_position['max'] . '">Under $' . number_format($nav_position['max']) . '</a></td>';
						}
						else if ($nav_position['max'] == 999999999){
							echo '<td><a class="displet-price-navigation" displetminprice="' . $nav_position['min'] . '" displetmaxprice="' . $nav_position['max'] . '">Over $' . number_format($nav_position['min']) . '</a></td>';
						}
						else{
							echo '<td><a class="displet-price-navigation" displetminprice="' . $nav_position['min'] . '" displetmaxprice="' . $nav_position['max'] . '">$' . number_format($nav_position['min']) . ' - $' . number_format($nav_position['max']) . '</a></td>';
						}
						$i++;
					}
				?>
			</tr>
		</tbody>
	</table>
</div>