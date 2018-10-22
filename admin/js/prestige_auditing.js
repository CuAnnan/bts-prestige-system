(function( $ ) {
	'use strict';
	
	let prestigeCategories = null,
		prestigeActions = null,
		domains = null,
		venues = null,
		offices = null,
		prestigeRecords = null,
		users = null,
		$row = null,
		$currentDataTable = null;
	
	$(function(){
		parseJSONElements();
		buildOfficeTabs();
		buildPrestigeModal();
		buildUserAutoComplete();
		bindEvents();
	});
	
	function buildOfficeTabs()
	{
		let recordsByOffice = {},
			$recordsNav = $('#recordsNav'),
			$recordsTabs = $('#recordsTabs'),
			firstRecord = true;
		for(let record of prestigeRecords)
		{
			let office = offices.filter((office)=>office.id === record.id_officers)[0];
			if(!recordsByOffice[office.id])
			{
				recordsByOffice[office.id] = {office:office,records:[]};
			}
			recordsByOffice[office.id].records.push(record);
		}
		
		for(let record of Object.values(recordsByOffice))
		{
			let domain = domains.filter((domain)=>domain.id === record.office.id_domains)[0],
				venue = venues.filter((venue)=>venue.id === record.office.id_venues)[0],
				tabTitle = record.office.short_form + (venue?`-${venue.short_name}`:'')+ (domain.nmc_code?` (${domain.nmc_code})`:''),
				officeAndDomain = record.office.title +(venue?`-${venue.genre}`:'') + (domain.name?` (${domain.name})`:''),
				idPrefix = `record_${record.office.id}`,
				$tabContent = $(`<div class="tab-pane fade" id="${idPrefix}" role="tabpanel" aria-labelledby="nav-profile-tab"></div>`).appendTo($recordsTabs),
				$dataTableContainer = $(`<table id="${idPrefix}_prestige_table"><thead><tr><th>Member</th><th>Action</th><th>Category</th><th>Amount</th><th>Type</th><th>Date Claimed</th><th>Awarding Officer</th><th>Domain</th><th>Venue</th><th>Approved</th><th>&nbsp;</th></tr></thead><tbody></tbody></table>`).appendTo($tabContent),
				$dataTable = buildTabDataTable($dataTableContainer, record),
				$tabNav = $(`<li class="nav-item" data-id-officer="${record.office.id}"><a title="${officeAndDomain}" class="nav-link" id="${idPrefix}-tab" data-toggle="tab" href="#${idPrefix}" role="tab" aria-controls="profile" aria-selected="${firstRecord}">${tabTitle}</a></li>`)
							.appendTo($recordsNav)
							.click(function(){
								let $node = $(this);
								$('#id-acting-office').val($node.data('idOfficer'));
								$currentDataTable = $dataTable;
							});
			if(firstRecord)
			{
				$('a', $tabNav).addClass('active');
				$tabContent.addClass('active').addClass('show');
				$('#id-acting-office').val($tabNav.data('idOfficer'));
			}
			firstRecord = false;
		}
	}
	
	function buildTabDataTable($dataTableContainer, record)
	{
		let user = (data)=> `${data.first_name} ${data.last_name} (${data.number})`;
		let $dataTable = $dataTableContainer.DataTable({
			data:record.records,
			autoWidth:false,
			columns:[
				{data:user},
				{data:'description'},
				{data:'category'},
				{data:'reward_amount'},
				{data:'reward_type'},
				{data:'date_claimed'},
				{data:'officer_title'},
				{data:'domain_name'},
				{data:'genre_name'},
				{data:'status'},
				{data:null, orderable:false, defaultContent:`<button class="btn btn-primary prestige-note-button">Notes</button>`},
				{data:null, orderable:false, defaultContent:`<button class="btn btn-primary prestige-edit-button">Edit</button>`}
			],
			createdRow:function(row, data, dataIndex)
			{
				$(row).data({notes:data.notes, id:data.id});
			},
			order:[[4, 'desc']]
		});
		return $dataTable;
	}
	
	function buildUserAutoComplete()
	{
		let $autoComplete = $('#prestige_reward_user_search');
		var options = {
			data:users,
			adjustWidth:0,
			getValue: function(element){return `${element.first_name} ${element.last_name} (${element.membership_number})`;},
			list: {
				match: {
					enabled: true
				},
				onClickEvent: function()
				{
					let data = $autoComplete.getSelectedItemData();
					$('#prestige_reward_id_user').val(data.id);
					$('#prestige_reward_first_name').val(data.first_name);
					$('#prestige_reward_last_name').val(data.last_name);
					$('#prestige_reward_membership_number').val(data.membership_number);
				}
			}
		};
		$autoComplete.easyAutocomplete(options);
	}
	
	function parseJSONElements()
	{
		prestigeCategories = parseJSONElement('prestige_categories_json');
		prestigeActions = parseJSONElement('prestige_actions_json');
		domains = parseJSONElement('domains_json');
		venues = parseJSONElement('venues_json');
		offices = parseJSONElement('offices_json');
		users = parseJSONElement('users_json');
		prestigeRecords = Object.values(parseJSONElement('prestige_records_requiring_approval'));
	}
	
	function parseJSONElement(id)
	{
		return JSON.parse(document.getElementById(id).innerHTML);
	}
	
	/**
	 * Populates a select with a series of values. Option values are always set to the id of the field
	 * @param {type} selector the jQuery selector
	 * @param {type} fields	  the field to search for values
	 * @param {type} fieldTitle the field property to populate the option text with 
	 * @param {type} handler an optional change handler
	 */
	function populateSelect(selector, fields, fieldTitle, handler)
	{
		let $select = $(selector).empty(),
			$options = [$('<option value="">---</option>')];
		
		for(let field of fields)
		{
			$options.push($(`<option value="${field.id}">${field[fieldTitle]}</option>`));
		}
		$select.append(...$options);
		if(handler)
		{
			$select.change(handler);
		}
	}
	
	function buildPrestigeModal()
	{
		populateSelect('#prestige_reward_id_prestige_categories', prestigeCategories, 'name', populateActions);
	}
	
	function populateActions()
	{
		populateSelect('#prestige_reward_id_prestige_actions', prestigeActions.filter(action => action.id_prestige_category === $('#prestige_reward_id_prestige_categories').val()), 'description');
	}
	
	function bindEvents()
	{
		$('#prestige_record_note_btn').click(addPrestigeNote);
		$('#addPrestigeReward').click(showPrestigeClaimModal);
		$('#prestige_reward_form').submit(()=>{handleNewPrestigeReward(); return false;});
		bindNotesButtons();
	}
	
	function bindNotesButtons()
	{
		$('.prestige-note-button').off().click({offices:offices}, Prestige.showNotesModal);
	}
	
	function showPrestigeClaimModal()
	{
		Prestige.setClaimModalAction('add_prestige_reward').showClaimModal(offices);
	}
	
	function addPrestigeNote()
	{
		let data = {
				'action':				'add_prestige_note',
				'note_text':			$('#prestige_record_note').val(),
				'status':				$('#prestige_record_approved').val(),
				'id_prestige_record':	$('#notes_prestige_record_id').val(),
				'id_acting_officer':	$('#prestige_record_id_officers').val()
			},
			$dtRow = $currentDataTable.row($row),
			rowData = $dtRow.data();
			rowData.status = data.status;
		
		$.post(
			ajaxurl,
			data,
			function(response)
			{
				if(data.status === 'Audited' || data.status === 'Rejected')
				{
					$dtRow.remove().draw();
				}
				else
				{
					$dtRow.data(rowData).draw();
				}
				$('#prestigeNotesModalDialog').modal('hide');
			}
		);
	}
	
	function handleNewPrestigeReward()
	{
		let data = {
			'action':Prestige.getClaimModalAction(),
			'id_users':$('#prestige_reward_id_user').val(),
			'id_officers':$('#prestige_reward_id_officers').val(),
			'id_prestige_actions':$('#prestige_reward_id_prestige_actions').val(),
			'reason':$('#prestige_reward_reason').val(),
			'prestige_amount':$('#prestige_reward_amount').val(),
			'prestige_type':$('input[name=prestige_reward_type]').val(),
			'status':$('input[name=prestige_reward_approved]').val(),
			'date':$('#prestige_reward_claim_date').val()
		};
		$.post(
			ajaxurl,
			data,
			function(response)
			{
				if(response.success)
				{
					$('#prestigeAddModalDialog').modal('hide');
				}
			}
		);
	}
	
})(jQuery);