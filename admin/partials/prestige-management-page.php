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
		<b>Membership Class: </b><?php echo $mc['title'];?> (<?php echo $mc['level'];?>)<br/>
		<b>Prestige to next MC:</b><?php echo $prestigeLeft;?>
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
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
	<button id="prestige_claim_button" class="btn btn-primary">Claim Prestige</button>
</div>

<?php require_once (plugin_dir_path(__FILE__)."prestige-claim.modal.php"); ?>
<?php require_once (plugin_dir_path(__FILE__)."prestige-notes.modal.php"); ?>