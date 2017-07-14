<div id="displet-property-type-navigation">
	<?php if (isset($model['caption'])) : ?>
		<div class="displet-title">
			<?php echo $model['caption']; ?> by Property Type
		</div>
	<?php endif; ?>
	<table>
		<tbody>
			<tr>
				<?php
					$property_types = array('<a class="displet-property-type-navigation active" displetpropertytype="property-type-all" displetminprice="0" displetmaxprice="999999999">All Property Types</a>');
					if ($model['has_houses']) {
						$property_types[] = '<a class="displet-property-type-navigation" displetpropertytype="property-type-house" displetminprice="0" displetmaxprice="999999999">Houses</a>';
					}
					if ($model['has_condos']) {
						$property_types[] = '<a class="displet-property-type-navigation" displetpropertytype="property-type-condo" displetminprice="0" displetmaxprice="999999999">Condos</a>';
					}
					if ($model['has_townhomes']) {
						$property_types[] = '<a class="displet-property-type-navigation" displetpropertytype="property-type-townhome" displetminprice="0" displetmaxprice="999999999">Townhome</a>';
					}
					if ($model['has_land']) {
						$property_types[] = '<a class="displet-property-type-navigation" displetpropertytype="property-type-land" displetminprice="0" displetmaxprice="999999999">Land</a>';
					}
					$i = 1;
					foreach ($property_types as $property_type) {
						if ($i % 3 == 1) {
							echo '</tr><tr>';
						}
						echo '<td>' . $property_type . '</td>';
						$i++;
					}
				?>
			</tr>
		</tbody>
	</table>
</div>