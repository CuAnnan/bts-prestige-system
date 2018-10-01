<div class="wrap">
<script type="text/json" id="prestige_records_requiring_approval"><?php echo json_encode($prestige_records); ?></script>
<script type="text/json" id="prestige_categories_json"><?php echo json_encode($prestige_categories); ?></script>
<script type="text/json" id="prestige_actions_json"><?php echo json_encode($prestige_actions); ?></script>
<script type="text/json" id="domains_json"><?php echo json_encode($domains);?></script>
<script type="text/json" id="venues_json"><?php echo json_encode($venues);?></script>
<script type="text/json" id="offices_json"><?php echo json_encode($offices);?></script>
<h1>Prestige Auditing</h1>

<h2>Records requiring attention</h2>

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