(function( $ ) {
	'use strict';
	let users_json = null,
		offices = null,
		prestigeCategories = null,
		prestigeActions = null,
		$row = null,
		editable = false;
	
	$(function(){
		$('input[type=hidden]').val('');
		$('#prestige_user_search').val('');
		parseJSONElements();
		buildAutoComplete();
		bindEventHandlers();
		buildPrestigeModal();
	});
	
	function parseJSONElement(id)
	{
		return JSON.parse(document.getElementById(id).innerHTML);
	}
	
	function parseJSONElements()
	{
		users_json = parseJSONElement('users_json');
		offices = parseJSONElement('officers_json');
		prestigeCategories = parseJSONElement('prestige_categories_json');
		prestigeActions = parseJSONElement('prestige_actions_json');
	}
	
	function handleNewPrestigeReward()
	{
		let data = {
			'action':PrestigeSearch.getClaimModalAction(),
			'id_users':$('#prestige_id_users').val(),
			'id_officers':$('#prestige_reward_id_officers').val(),
			'id_prestige_actions':$('#id_prestige_actions').val(),
			'reason':$('#prestige_reason').val(),
			'prestige_amount':$('#prestige_amount').val(),
			'prestige_type':$('input[name=prestige_type]').val(),
			'status':$('input[name=prestige_reward_approved]').val(),
			'date':$('#claim_date').val()
		};
		
		$.post(
			ajaxurl,
			data,
			function(response)
			{
				if(response.success)
				{
					PrestigeSearch.hideClaimModal();
				}
			}
		);
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
		populateSelect('#id_prestige_categories', prestigeCategories, 'name', populateActions);
	}
	
	function populateActions()
	{
		populateSelect('#id_prestige_actions', prestigeActions.filter(action => action.id_prestige_category === $('#id_prestige_categories').val()), 'description');
	}
	
	function bindEventHandlers()
	{
		$('#prestige_search_button').click(fetchPrestigeLog);
		$('#prestige_record_note_btn').click(addPrestigeNote);
		$('#addPrestigeReward').click(showPrestigeClaimModal);
		$('#newPrestigeRecordButton').click(handleNewPrestigeReward);
	}
	
	function showPrestigeClaimModal()
	{
		PrestigeSearch.setClaimModalAction('add_prestige_reward').showClaimModal(offices);
	}
	
	function bindNotesButtons()
	{
		$('.prestige-note-button').off().click({offices:offices}, PrestigeSearch.showNotesModal);
	}
	
	function fetchPrestigeLog()
	{
		let idUsersSearch =$('#prestige_id_users').val(); 
		if(!idUsersSearch)
		{
			return;
		}
		
		let data = {
			action:'fetch_user_prestige',
			id_users:idUsersSearch
		};
		$.post(
			ajaxurl,
			data,
			function(response)
			{
				editable = response.editable;
				showDataTable(Object.values(response.log));
			}
		);
	}
	
	function showDataTable(prestigeLog)
	{
		let $dataTableContainer = $('#prestige_record_table'),
			$dataTable = $dataTableContainer.DataTable();
		$dataTable.destroy();
		$('#prestige_log_container').show();
		$("tbody", $dataTableContainer).empty();
		$dataTable = $dataTableContainer.DataTable({
			data:prestigeLog,
			columns:[
				{data:'description'},
				{data:'category'},
				{data:'reward_amount'},
				{data:'reward_type'},
				{data:'date_claimed'},
				{data:'officer_title'},
				{data:'domain_name'},
				{data:'genre_name'},
				{data:'status'},
				{data:null, orderable:false, defaultContent:`<button class="btn btn-primary prestige-note-button">Notes</button>`}
			],
			columnDefs:[
				{
					targets:4,
					render:function(data, type, row)
					{
						return data.split(" ")[0];
					}
				}
			],
			createdRow:function(row, data, dataIndex)
			{
				$(row).data({notes:data.notes, id:data.id});
			},
			order:[[4, 'desc']],
		});
		bindNotesButtons();
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
			$dtRow = $dataTable.row($row),
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
	
	function buildAutoComplete()
	{
		let $autoComplete = $('#prestige_user_search').val("").easyAutocomplete({
			data:users_json,
			adjustWidth:0,
			getValue: function(element){return `${element.first_name} ${element.last_name} (${element.membership_number})`;},
			list: {
				match: {
					enabled: true
				},
				onClickEvent: function()
				{
					let data = $autoComplete.getSelectedItemData();
					$('#prestige_id_users').val(data.id);
					$('#prestige_first_name').val(data.first_name);
					$('#prestige_last_name').val(data.last_name);
					$('#prestige_membership_number').val(data.membership_number);
				}
			}
		});
	}
	
})(jQuery);