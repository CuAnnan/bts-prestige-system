<div class="modal" id="newPrestigeRecordModalDialog" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<form id="newPrestigeRecordForm" onsubmit="return false;">
				<div class="modal-header">
					<h5 id="prestige_claim_h5">New Prestige Record</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<div class="container">
						<input type="hidden" id="id_prestige_record"/>
						<?php if(!isset($viewing_own_log)){?>
						<div class="form-group row">
							<label class="col-form-label col-4" for="prestige_reward_id_officers">Acting Office:</label>
							<div class="col"><select id="prestige_reward_id_officers" class="form-control"></select></div>
						</div>
						<div class="form-group row">
							<label class="col-form-label col-4" for="prestige_reward_user_search">Member</label>
							<div class="col">
								<input type="text" class="form-control" id="prestige_reward_user_search" required/>
								<small class="form-text text-muted">
									The user field is searchable based on first name, last name, and/or membership number
								</small>
							</div>
							<input type="hidden" id="prestige_reward_id_user"/>
							<input type="hidden" id="prestige_reward_first_name"/>
							<input type="hidden" id="prestige_reward_last_name"/>
							<input type="hidden" id="prestige_reward_membership_number"/>
						</div>
						<?php } ?>
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
										<input type="radio" name="prestige_type" id="prestige_type_open" value="Open" checked/> Open
									</label>
									<label class="prestige-type btn btn-secondary">
										<input type="radio" name="prestige_type" id="prestige_type_regional" value="Regional"/> Regional
									</label>
									<label class="prestige-type btn btn-secondary">
										<input type="radio" name="prestige_type" id="prestige_type_national" value="National"/> National
									</label>
								</div>
							</div>
						</div>
						<?php if(!isset($viewing_own_log)){?>
						<div class="form-group row">
							<label class="col-4 col-form-label" fro="prestige_reward_approved">Status</label>
							<div class="col">
								<div class="btn-group btn-group-toggle" data-toggle="buttons">
									<label class="prestige-reward-approved btn btn-secondary active">
										<input type="radio" name="prestige_reward_approved" id="prestige_reward_approve_submitted" value="Submitted"/> Submitted
									</label>
									<label class="prestige-reward-approved btn btn-secondary">
										<input type="radio" name="prestige_reward_approved" id="prestige_reward_approved_approved" value="Approved"/> Approved
									</label>
									<?php if($can_audit) { ?>
									<label class="prestige-reward-approved btn btn-secondary">
										<input type="radio" name="prestige_reward_approved" id="prestige_reward_approved_audited" value="Audited"/> Audited
									</label>
									<?php } ?>
									<label class="prestige-reward-approved btn btn-danger">
										<input type="radio" name="prestige_reward_approved" id="prestige_reward_approved_rejected" value="Rejected"/> Rejected
									</label>
								</div>
							</div>
						</div>
						<?php } ?>
						<div class="form-group row">
							<label class="col-form-label col-4" for="claim_date">Date of Claim:</label>
							<div class="col">
								<input type="date" id="claim_date" class="form-control"/>
							</div>
						</div>
						<div id="prestige_claim_chain_and_office"<?php if(!isset($viewing_own_log)) { ?> style="display:none"<?php } ?>>
							<div class="form-group row">
								<label class="col-form-label col-4" for="chain">Chain</label>
								<div class="col">
									<select class="form-control" id="chain">
										<option>Coordinator</option>
										<option>Storyteller</option>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-4" for="id_domains">Domain</label>
								<div class="col"><select class="form-control" id="id_domains" required></select></div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-4" for="id_officers">Office</label>
								<div class="col">
									<select class="form-control" id="id_officers" required></select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-primary" id="newPrestigeRecordButton">Add Record</button>
					<button class="btn btn-primary" display="style:none" id="editPrestigeRecordButton">Update Record</button>
					<button class="btn btn-warning" data-dismiss="modal">Cancel</button>
				</div>
			</form>
		</div>
	</div>
</div>