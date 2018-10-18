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
								<th>Officer</th>
								<th>Member</th>
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
									<?php if(isset($viewing_own_log) && $viewing_own_log){?>
									&nbsp;
									<?php }else {?>
									<select id="prestige_record_id_officers" class="form-control">
									</select>
									<?php } ?>
								</td>
								<td></td>
								<td><?php if(isset($viewing_own_log) && $viewing_own_log){?>
									&nbsp;
									<?php }else {?>
									<select id="prestige_record_approved">
										<option>Submitted</option>
										<option>Approved</option>
										<?php if($can_audit) { ?><option>Audited</option> <?php } ?>
										<option>Rejected</option>
									</select>
									<!--
									<div class="btn-group btn-group-toggle btn-group-vertical" data-toggle="buttons">
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
										<label class="prestige-record-approved btn btn-danger">
											<input type="radio" name="prestige_record_approved" id="prestige_record_approved_rejected" value="Rejected"/> Rejected
										</label>
									</div>
									-->
									<?php } ?></td>
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