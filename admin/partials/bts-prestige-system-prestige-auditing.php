<div class="wrap">
<script type="text/json" id="prestige_records_requiring_approval"><?php echo json_encode($prestige_records); ?></script>
<script type="text/json" id="prestige_categories_json"><?php echo json_encode($prestige_categories); ?></script>
<script type="text/json" id="prestige_actions_json"><?php echo json_encode($prestige_actions); ?></script>
<script type="text/json" id="domains_json"><?php echo json_encode($domains);?></script>
<script type="text/json" id="venues_json"><?php echo json_encode($venues);?></script>
<script type="text/json" id="offices_json"><?php echo json_encode($offices);?></script>
<script type="text/json" id="users_json"><?php echo json_encode($usersMetaData);?></script>
<script type="text/javascript">
	var user_id = <?php echo get_current_user_id(); ?>;
</script>
<h1>Prestige Auditing</h1>

<h2>Records requiring attention</h2>
<div class="content text-right">
	<strong>Acting Office:</strong><select id="acting_office"></select>
</div>
<table id="prestige_record_table">
	<thead>
		<tr>
			<th>Member</th>
			<th>Action</th>
			<th>Category</th>
			<th>Amount</th>
			<th>Type</th>
			<th>Date Claimed</th>
			<th>Awarding Officer</th>
			<th>Domain</th>
			<th>Venue</th>
			<th>Approved</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
<button class="btn btn-primary" id="addPrestigeReward">Add Prestige Reward</button>
</div>

<div class="modal" id="prestigeAddModalDialog" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Add Prestige Reward</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<form id="newPrestigeRecordForm">
				<div class="modal-body">
					<div class="container">
						<div class="form-group row">
							<label class="col-form-label col-4" for="id_prestige_categories">Prestige Category</label>
							<div class="col"><select class="form-control" id="id_prestige_categories" required></select></div>
						</div>
						<div class="form-group row">
							<label class="col-form-label col-4" for="id_prestige_actions">Prestige Actions</label>
							<div class="col"><select class="form-control" id="id_prestige_actions" required></select></div>
						</div>
						<div class="form-group row">
							<label class="col-form-label col-4" for="prestige_reason">Reason</label>
							<div class="col"><input class="form-control" type="text" id="prestige_reason" required/></div>
						</div>
						<div class="form-group row">
							<label class="col-form-label col-4" for="prestige_amount">Amount</label>
							<div class="col"><input class="form-control" type="number" id="prestige_amount" size="5" required/></div>
						</div>
						<div class="form-group row">
							<label class="col-form-label col-4" for="prestige_type">Type</label>
							<div class="col">
								<div class="btn-group btn-group-toggle" data-toggle="buttons">
									<label class="prestige-type btn btn-secondary active">
										<input type="radio" name="prestige_type" id="option1" value="Open" checked/> Open
									</label>
									<label class="prestige-type btn btn-secondary">
										<input type="radio" name="prestige_type" id="option2" value="Regional"/> Regional
									</label>
									<label class="prestige-type btn btn-secondary">
										<input type="radio" name="prestige_type" id="option3" value="National"/> National
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer"></div>
			</form>
		</div>
	</div>
</div>


<div class="modal" id="prestigeNotesModalDialog" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Log Entry Notes</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="container">
					<input type="hidden" id="notes_prestige_record_id"/>
					<table width="100%">
						<thead>
							<tr>
								<th>Note</th>
								<th>Approved</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody id="prestige-notes">
						</tbody>
						<tfoot>
							<tr>
								<td><input type="text" class="form-control" style="width:100%" id="prestige_record_note"/></td>
								<td>
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="prestige-record-approved btn btn-secondary active">
											<input type="radio" name="prestige_record_approved" id="option1" value="Submitted"/> Submitted
										</label>
										<label class="prestige-record-approved btn btn-secondary">
											<input type="radio" name="prestige_record_approved" id="option2" value="Approved"/> Approved
										</label>
										<?php if($can_audit) { ?>
										<label class="prestige-record-approved btn btn-secondary">
											<input type="radio" name="prestige_record_approved" id="option3" value="Audited"/> Audited
										</label>
										<?php } ?>
									</div>
								</td>
								<td>
									<button class="btn btn-primary" id="prestige_record_note_btn">Add note</button>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>