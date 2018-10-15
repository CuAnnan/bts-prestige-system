<script type="text/json" id="users_json"><?php echo json_encode($users); ?></script>

<div class="wrap">
	<h5>Prestige Log Search</h5>
	<input type="hidden" id="prestige_id_users"/>
	<input type="hidden" id="prestige_first_name"/>
	<input type="hidden" id="prestige_last_name"/>
	<input type="hidden" id="prestige_membership_number"/>
	<div class="form-group row">
		<label class="col-form-label col-1" for="prestige_user_search">Member</label>
		<div class="col">
			<input type="text" class="form-control" id="prestige_user_search" required/>
			<small class="form-text text-muted">
				The user field is searchable based on first name, last name, and/or membership number
			</small>
		</div>
		<div class="col">
			<button class="btn btn-primary" id="prestige_search_button">Search</button>
		</div>
		<div class="col">
			<div class="row">
				<label class="col-form-label col" for="acting_office">Acting office:</label>
				<div class="col"><select id="acting_office"></select></div>
			</div>
		</div>
	</div>
	
	<div id="prestige_log_container">
		<table id="prestige_record_table">
		<thead>
			<tr>
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
									<?php 
										if(!$viewing_own_log){
									?>
									<select id="prestige_record_approved" class="form-control">
										<option>Submitted</option>
										<option>Approved</option>
										<option>Audited</option>
									</select>
									<?php 
									}
									?>
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