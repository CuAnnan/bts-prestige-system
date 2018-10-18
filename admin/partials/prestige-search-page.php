<script type="text/json" id="users_json"><?php echo json_encode($users); ?></script>
<script type="text/json" id="officers_json"><?php echo json_encode($offices); ?></script>
<script type="text/javascript">
	var user_id = <?php echo get_current_user_id(); ?>;
</script>

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
<?php require_once (plugin_dir_path(__FILE__)."prestige-notes.modal.php"); ?>