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
echo '<script type="text/json" id="allUsersMeta">'.json_encode($usersMetaData).'</script>'."\n";
echo '<script type="text/json" id="officerHeirarchy">'.json_encode($officers).'</script>';
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
						<td class="tr_office_title"><?php echo $officer->title ?></td>
						<td class="tr_office_genre_name"><?php echo $officer->genre_name?$officer->genre_name:'&nbsp;'; ?></td>
						<td class="tr_office_name"><?php echo $officer->first_name.' '.$officer->last_name; ?></td>
						<td class="tr_office_membership_number"><?php echo $officer->membership_number; ?></td>
						<td class="tr_office_date_appointed"><?php echo $officer->date_appointed; ?></td>
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
	
	<button class="btn btn-primary" id="reset_permissions_button">Reset all permissions</button>

<div class="modal" id="editOfficeModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Edit Office</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="container">
					<input type="hidden" id="office_edit_id_officers"/>
					<input type="hidden" id="office_edit_id_users"/>
					<input type="hidden" id="office_edit_first_name"/>
					<input type="hidden" id="office_edit_last_name"/>
					<input type="hidden" id="office_edit_id_domains"/>
					<input type="hidden" id="office_edit_id_venues"/>
					<input type="hidden" id="office_edit_membership_number"/>
					
					<div class="row">
						<label for="office_edit_office-title" class="col-sm-4 col-form-label">Position</label>
						<div class="col"><input type="text" class="form-control" id="office_edit_office-title"/></div>
					</div>
					<div class="row">
						<label for="office_edit_chain" class="col-sm-4 col-form-label">Chain</label>
						<div class="col">
							<select id="office_edit_chain">
								<option>Coordinator</option>
								<option>Storyteller</option>
							</select>
						</div>
					</div>
					<div class="row">
						<label for="office_edit_office-member" class="col-sm-4 col-form-label">Position holder</label>
						<div class="col"><input type="text" class="form-control" id="office_edit_office-member"/></div>
					</div>
					<div class="row">
						<label for="office_edit_id_superior" class="col-sm-4 col-form-label">Superior</label>
						<div class="col"><select id="office_edit_id_superior" class="form-control"></select></div>
					</div>
					<div class="row">
						<label for="office_edit_email" class="col-sm-4 col-form-label">Email Address</label>
						<div class="col"><input type="text" class="form-control" id="office_edit_email"/></div>
					</div>
					<div class="row">
						<label for="office_edit_office-date" class="col-sm-4 col-form-label">Date appointed</label>
						<div class="col"><input type="date" class="form-control" id="office_edit_office-date"/></div>
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