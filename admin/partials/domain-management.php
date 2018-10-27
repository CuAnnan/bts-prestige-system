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

echo '<script type="text/json" id="allUsersMeta">'.json_encode($usersMetaData).'</script>'."\n";
echo '<script type="text/json" id="officerHeirarchy">'.json_encode($officers).'</script>';
?>
	<ul class="nav nav-tabs" id="domainsTab" role="tablist"><?php
		$first = true;
		foreach($managed_domains as $domain)
		{
	?>	<li class="nav-item">
			<a class="nav-link<?php echo $first?' active':''?>" id="domain_<?php echo $domain->id?>-tab" data-toggle="tab" href="#domain_<?php echo $domain->id?>" role="tab" aria-controls="domain_<?php echo $domain->id?>" aria-selected="<?php echo $first?'true':'false'?>"><?php echo $domain->name;?></a>
		</li>
	<?php
			$first = false;
		}
	?>
	</ul>
	<div class="tab-content" id="domainsTabContent">
		
	<?php
		$first = true;
		foreach($managed_domains as $domain)
		{
	?>
		<div role="tabpanel" class="tab-pane fade domain<?php echo $first?' show active':''?>" id="domain_<?php echo $domain->id?>" data-id-domains="<?php echo $domain->id ?>">
			<?php if(isset($venues[$domain->id])){?>
			<div class="expansionContainer">
				<h4>Venue List <button class="btn btn-secondary expander">+</button></h4>
				<div class="venueList expandable">
				<?php
					foreach($venues[$domain->id] as $venue)
					{
						$id_venue = $venue->id;
						$venue_offices = array_filter($officers[$domain->id], function($officer) use($id_venue){
							return $officer->id_venues == $id_venue;
						});
				?>
					<div class="row">
						<div class="col">
							<h5><?php echo $venue->genre ?> - (<?php echo $venue->name?>)</h5>
							<?php foreach($venue_offices as $venue_office)
							{
							?>
							<div class="row">
								<div class="col"><?php echo $venue_office->office_title; ?></div>
							</div>
							<?php
							}
							?>
						</div>
					</div>
				<?php
					}
				?>
				</div>
			</div>
			<?php } ?>
			<div class="expansionContainer">
				<h4>Officer list <button class="btn btn-secondary expander">+</button></h4>
				<div class="officerList expandable">
				<?php
					if(isset($officers[$domain->id]))
					{
						foreach($officers[$domain->id] as $officer){ ?>
						<div class="row" <?php echo convert_officer_row_to_data($officer); ?>>
							<div class="col tr_office_title"><?php echo $officer->title ?><?php echo $officer->genre_name?' - '.$officer->genre_name:''; ?></div>
							<div class="col-3 tr_office_name"><?php echo $officer->first_name.' '.$officer->last_name; ?> (<?php echo $officer->membership_number; ?>)</div>
							<div class="col-2">
								<button class="btn btn-sm btn-primary btn_edit_office">Edit</button>
								<button class="btn btn-sm btn-danger btn_delete_office">Delete</button>
							</div>
						</div>
					<?php	
						}
					}
				?>
				</div>
			</div>
		</div>
	<?php
			$first = false;
		}
	?>
	</div>
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