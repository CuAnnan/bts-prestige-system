<h3><?php _e("BTS Required Information", "blank"); ?></h3>
<table class="form-table">
	<tr>
		<th><?php _e("Membership Number"); ?></th>
		<td><?php echo esc_attr(get_the_author_meta('membership_number', $user->ID)); ?></td>
	</tr>
	<tr>
		<th><?php _e("Domain");?></th>
		<td><?php echo esc_attr(get_the_author_meta('domain', $user->ID));?></td>
	</tr>
	<tr>
		<th><?php _e("Membership Class");?></th>
		<td><?php echo esc_attr(get_the_author_meta('membership_class', $user->ID));?></td>
	</tr>
	<tr>
		<th><?php _e("Membership due date");?></th>
		<td><?php echo esc_attr(get_the_author_meta('membership_renewal_date', $user->ID));?></td>
	</tr>
	<tr>
		<th><label for="address1"><?php _e("Address Line 1");?></label></th>
		<td><input type="text" name="address1" id="address1" value="<?php echo esc_attr( get_the_author_meta( 'address1', $user->ID ) ); ?>" class="regular-text" /></td>
	</tr>
	<tr>
		<th><label for="address2"><?php _e("Address Line 2");?></label></th>
		<td><input type="text" name="address2" id="address2" value="<?php echo esc_attr( get_the_author_meta( 'address2', $user->ID ) ); ?>" class="regular-text" /></td>
	</tr>
	<tr>
		<th><label for="zip"><?php _e("Zip Code");?></label></th>
		<td><input type="text" name="zip" id="zip" value="<?php echo esc_attr(get_the_author_meta('zip', $user->ID)); ?>" class="regular-text"/></td>
	</tr>
	<tr>
		<th><label for="city"><?php _e("City")?></label></th>
		<td><input type="text" id="city" name="city" value="<?php echo esc_attr(get_the_author_meta('city', $user->ID));?>" class="regular-text"/></td>
	</tr>
	<tr>
		<th><label for="state"><?php _e("State")?></label></th>
		<td>
			
			<select id="state" name="state"><?php
				$states = ["N/A", "ACT", "NSW", "NT", "QLD", "SA", "TAS", "VIC", "WA"];
				foreach($states as $state)
				{
					?><option <?php echo "value=\"{$state}\""; echo esc_attr(get_the_author_meta('state')) == $state ? ' disabled="disabled"':'';?>><?php echo $state;?></option>
				<?php
				}	
			?></select>
		</td>
	</tr>
	<tr>
		<th><label for="country"><?php _e("Country, if not Australia") ?></th>
		<td><input type="text" name="country" id="country" value="<?php echo esc_attr(get_the_author_meta('country', $user->ID)); ?>" class="regular-text"/></td>
	</tr>
	<tr>
		<th><label for="phone"><?php _e("Phone number");?></label></th>
		<td><input type="text" name="phone" id="phone" value="<?php echo esc_attr(get_the_author_meta('phone', $user->ID));?>" class="regular-text"/></td>
	</tr>
</table>