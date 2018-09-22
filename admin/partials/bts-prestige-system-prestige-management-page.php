<script type="text/json" id="prestige_records"><?php echo json_encode($prestige_rewards); ?></script>
<script type="text/json" id="prestige_categories_json"><?php echo json_encode($prestige_categories); ?></script>
<script type="text/json" id="prestige_actions_json"><?php echo json_encode($prestige_actions); ?></script>
<script type="text/json" id="domains_json"><?php echo json_encode($domains);?></script>
<script type="text/json" id="venues_json"><?php echo json_encode($venues);?></script>
<script type="text/json" id="offices_json"><?php echo json_encode($offices);?></script>

<div class="wrap">
	<h5>Member Class</h5>
	<p>
		<b>Total Audited Prestige: </b><?php echo $audited_prestige; ?><br/>
		<b>Membership Class: </b><?php echo $mc['title'];?> (<?php echo $mc['level'];?>)
	</p>
	<h5>Prestige records</h5>
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
	<button id="prestige_claim_button" class="btn btn-primary">Claim Prestige</button>
</div>

<div class="modal" id="newPrestigeRecordModalDialog" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<form id="newPrestigeRecordForm">
				<div class="modal-header">
					<h5>New Prestige Record</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<div class="container">
						<div class="row">
							<label class="col-form-label col-4" for="id_prestige_categories">Prestige Category</label>
							<div class="col"><select class="form-control" id="id_prestige_categories" required></select></div>
						</div>
						<div class="row">
							<label class="col-form-label col-4" for="id_prestige_actions">Prestige Actions</label>
							<div class="col"><select class="form-control" id="id_prestige_actions" required></select></div>
						</div>
						<div class="row">
							<label class="col-form-label col-4" for="prestige_reason">Reason</label>
							<div class="col"><input class="form-control" type="text" id="prestige_reason" required/></div>
						</div>
						<div class="row">
							<label class="col-form-label col-4" for="prestige_amount">Amount</label>
							<div class="col"><input class="form-control" type="number" id="prestige_amount" size="5" required/></div>
						</div>
						<div class="row">
							<label class="col-form-label col-4" for="prestige_type">Type</label>
							<div class="col">
								<select id="prestige_type">
									<option>Open</option>
									<option>Regional</option>
									<option>National</option>
								</select>
							</div>
						</div>
						<div class="row">
							<label class="col-form-label col-4" for="id_domains" required>Domains</label>
							<div class="col"><select class="form-control" id="id_domains"></select></div>
						</div>
						<div class="row">
							<label class="col-form-label col-4" for="id_venues">Venues</label>
							<div class="col"><select class="form-control" id="id_venues"></select></div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" id="newPrestigeRecordButton">Add Record</button>
					<button class="btn btn-warning" data-dismiss="modal">Cancel</button>
				</div>
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