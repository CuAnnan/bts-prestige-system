<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://wing.so-4pt.net
 * @since      1.0.0
 *
 * @package    Bts_Prestige_System
 * @subpackage Bts_Prestige_System/admin/partials
 * 
 * Dear person who comes next
 *  I'd much rather be using a templating engine. But wordpress doesn't use one.
 */

function convert_officer_row_to_data($officer)
{
	$dataStrings = [];
	foreach($officer as $field=>$value)
	{
		$fieldAsData = str_replace('_','-', $field);
		$dataStrings[] = "data-{$fieldAsData}=\"{$value}\"";
	}
	return join(' ', $dataStrings);
}

$users = get_users(['fields'=>['id']]);
$usersMetaData = [];
foreach($users as $user_id)
{
	$meta = get_user_meta($user_id->id);
	$meta['id'] = $user_id->id;
    $usersMetaData[] = $meta;
}
echo '<script type="text/json" id="allUsersMeta">'.json_encode($usersMetaData).'</script>';
?>
	<table>
	<?php
		foreach($managed_domains as $managed_domain)
		{
	?>
		<tbody class="domain" data-id-domains="<?php echo $managed_domain->id; ?>">
			<tr>
				<th colspan="7">
					<?php echo $managed_domain->name;?> -
					<?php echo $managed_domain->location;?> -
					<?php echo $managed_domain->number; ?>
				</th>
			</tr>
			<?php
				if(isset($officers[$managed_domain->id]))
				{
					foreach($officers[$managed_domain->id] as $officer){ ?>
					<tr <?php echo convert_officer_row_to_data($officer); ?>>
						<td><?php echo $officer->title ?></td>
						<td><?php echo $officer->genre_name?$officer->genre_name:'&nbsp;'; ?></td>
						<td><?php echo $officer->first_name.' '.$officer->last_name; ?></td>
						<td><?php echo $officer->membership_number; ?></td>
						<td><?php echo $officer->date_appointed; ?></td>
						<td><button class="btn btn-sm btn-primary btn_edit_office">Edit</button></td>
						<td><button class="btn btn-sm btn-danger btn_delete_office">Delete</button></td>
					</tr>
			<?php	
					}
				}
			?>
		</tbody>
	<?php
		}
	?>
	</table>

<div class="modal" id="editOfficeModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Edit Office</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="container">
					<div class="row">
						<label for="office-genre" class="col-sm-4 col-form-label">Genre</label>
						<div class="col">
							<select id="office-genre" class="form-control">
								<option value="">Domain level office</option>
								<?php 
									foreach($genres as $genre)
								{?>
								<option value="<?php echo $genre->id?>?"><?php echo $genre->name; ?></option>
								<?php
								}
								?>
							</select>
						</div>
					</div>
					<div class="row">
						<label for="office-title" class="col-sm-4 col-form-label">Position</label>
						<div class="col"><input type="text" class="form-control" id="office-title"/></div>
					</div>
					<div class="row">
						<label for="office-member" class="col-sm-4 col-form-label">Position holder</label>
						<div class="col"><input type="text" class="form-control" id="office-member"/></div>
					</div>
					<div class="row">
						<label for="office-date" class="col-sm-4 col-form-label">Date appointed</label>
						<div class="col"><input type="date" class="form-control" id="office-date"/></div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="updateModalButton">Save changes</button>
			</div>
		</div>
	</div>
</div>