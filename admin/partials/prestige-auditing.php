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
		var is_admin = <?php echo boolval(array_intersect(['administrator', BTS_NATIONAL_OFFICE_ROLE],  wp_get_current_user()->roles)); ?>;
	</script>
	<h1>Prestige Auditing</h1>

	<h2>Records requiring attention</h2>
	
	<div id="recordsSection">
		<input type="hidden" id="id-acting-office"/>
		<ul id="recordsNav" class="nav nav-tabs" role="tablist"></ul>
		<div class="tab-content" id="recordsTabs"></div>
	</div>

	<button class="btn btn-primary" id="addPrestigeReward">Add Prestige Reward</button>
</div>

<?php require_once (plugin_dir_path(__FILE__)."prestige-claim.modal.php"); ?>
<?php require_once (plugin_dir_path(__FILE__)."prestige-notes.modal.php"); ?>