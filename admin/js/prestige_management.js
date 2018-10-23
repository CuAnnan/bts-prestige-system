(function( $ ) {
	'use strict';
	
	let $prestigeModal = $('#newPrestigeRecordModalDialog'),
		prestigeCategories = null,
		prestigeActions = null,
		domains = null,
		venues = null,
		offices = null,
		prestigeLog = null,
		$dataTable = null,
		$dataTableRow = null;
	
	$(function(){
		parseJSONElements();
		buildDataTable();
		buildPrestigeModal();
		bindEvents();
	});
	
	function parseJSONElements()
	{
		prestigeCategories = parseJSONElement('prestige_categories_json');
		prestigeActions = parseJSONElement('prestige_actions_json');
		domains = parseJSONElement('domains_json');
		venues = parseJSONElement('venues_json');
		offices = parseJSONElement('offices_json');
		prestigeLog = Object.values(parseJSONElement('prestige_records'));
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
		populateSelect('#id_prestige_categories', prestigeCategories, 'name', populateActions);
		populateSelect('#id_domains', domains, 'name', populateOffices);
	}
	
	function populateActions()
	{
		populateSelect('#id_prestige_actions', prestigeActions.filter(action => action.id_prestige_category === $('#id_prestige_categories').val()), 'description');
	}
	
	function populateOffices()
	{
		// so, this one needs a bit of unpacking: any offices that are held by the admin user should be ignored.
		// Any office that has no venue is a domain office. Any office that has a venue, the venue should be active.
		// other than that, just match the domain to the chosen domain and the chain to the chosen chain.
		let relevantOffices = offices.filter(office=>(office.id_users !== "1" && (!office.venue || (office.active && office.active === '1')) && office.id_domains === $('#id_domains').val() && office.chain === $('#chain').val()));
		populateSelect('#id_officers', relevantOffices, 'full_title');
	}
	
	function bindEvents()
	{
		$('#prestige_record_note_btn').click(addPrestigeNote);
		$('#prestige_claim_button').click(showPrestigeClaimForm);
		$('#newPrestigeRecordButton').on('click', validateAndSubmitPrestigeClaim);
		$('#editPrestigeRecordButton').on('click', editPrestigeClaim);
		$('#chain').change(populateOffices);
		bindNotesButtons();
	}
	
	function bindNotesButtons()
	{
		$('.prestige-note-button').off().click(Prestige.showNotesModal);
		$('.prestige-edit-button').off().click(showEditClaimModal).removeAttr('disabled').each(
			(index, item)=>
			{
				let	$button = $(item),
					$tr = $button.closest('tr');
				if($('.prestigeClaimStatus', $tr).text()==='Audited')
				{
					$button.attr('disabled', 'disabled');
				}
			}
		);
		
	}
	
	function showEditClaimModal()
	{
		$dataTableRow = $dataTable.row($(this).parents('tr'));
		let data = $dataTableRow.data();
		Prestige.showEditClaimModal(offices, data);
	}
	
	function editPrestigeClaim()
	{
		let officer = offices.filter((office)=>office.id === $('#id_officers').val())[0],
			venue = officer.id_venues?venues.filter((venue)=>venue.id == officer.id_venues)[0]:null,
			domainName = $('#id_domains option:selected').text(),
			genreName = venue?venue.genre:null,
			data = {
				action: Prestige.getClaimModalAction(),
				id_officers:$('#id_officers').val(),
				id_prestige_actions:$('#id_prestige_actions').val(),
				reward_amount:parseInt($('#prestige_amount').val()),
				reward_type:$('input[name=prestige_type]:checked').val(),
				date_claimed:$('#claim_date').val(),
				id_prestige_record:$('#id_prestige_record').val(),
			};
			
		$.post(
			ajaxurl,
			data,
			function(response)
			{
				if(response.success)
				{
					let rowData = $dataTableRow.data();
					for(let key in data)
					{
						rowData[key] = data[key];
					}
					let otherFields = {
						domain_name:domainName,
						genre_name:genreName,
						officer_title:officer.title,
						description: $('#id_prestige_actions option:selected').text(),
						category:$('#id_prestige_categories option:selected').text()
					};
					for(let key in otherFields)
					{
						rowData[key] = otherFields[key];
					}
					$dataTableRow.data(rowData).draw();
					bindNotesButtons();
					$prestigeModal.modal('hide');
				}
			}
		);
	}
	
	function validateAndSubmitPrestigeClaim()
	{
		let officer = offices.filter((office)=>office.id === $('#id_officers').val())[0],
			venue = officer.id_venues?venues.filter((venue)=>venue.id == officer.id_venues)[0]:null,
			domainName = $('#id_domains option:selected').text(),
			genreName = venue?venue.genre:null,
			data = {
				action: Prestige.getClaimModalAction(),
				id_officers:$('#id_officers').val(),
				id_prestige_actions:$('#id_prestige_actions').val(),
				prestige_amount:parseInt($('#prestige_amount').val()),
				prestige_type:$('input[name=prestige_type]:checked').val(),
				reason:$('#prestige_reason').val(),
				date:$('#claim_date').val()
			};
		$.post(
			ajaxurl,
			data,
			function(response)
			{
				if(response.success)
				{
					$dataTable.row.add(
						{
							"notes":[{note:data.reason, note_officer_title:officer.title, note_domain_name:domainName, note_genre_name:genreName, approved:"0", note_date:response.date}],
							"id":response.id,
							officer_id_user:officer.id_users,
							officer_title:officer.title,
							reward_type:data.prestige_type,
							reward_amount:data.prestige_amount,
							date_claimed:response.date,
							description: $('#id_prestige_actions option:selected').text(),
							category:$('#id_prestige_categories option:selected').text(),
							domain_name:domainName,
							genre_name:genreName,
							status:'Submitted'
						}).draw();
					bindNotesButtons();
					$prestigeModal.modal('hide');
				}
			}
		);
		
		return false;
	}
	
	function showPrestigeClaimForm()
	{
		Prestige.setClaimModalAction('add_prestige_reward').showClaimModal(offices);
	}
	
	function buildDataTable()
	{
		$dataTable = $('#prestige_record_table').DataTable({
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
				{data:null, orderable:false, defaultContent:`<button class="btn btn-primary prestige-note-button">Notes</button>`},
				{data:null, orderable:false, defaultContent:`<button class="btn btn-primary prestige-edit-button">Edit</button>`}
			],
			columnDefs:[
				{
					targets:4,
					render:function(data, type, row)
					{
						return data.split(" ")[0];
					}
				},
				{
					targets:8,
					className:'prestigeClaimStatus'
				}
			],
			createdRow:function(row, data, dataIndex)
			{
				$(row).data({notes:data.notes, id:data.id});
			},
			order:[[4, 'desc']],
		});
	}
	
	function addPrestigeNote()
	{
		let data = {
			'action':				'add_prestige_note',
			'note_text':			$('#prestige_record_note').val(),
			'status':				$('#prestige_record_approved').val(),
			'id_prestige_record':	$('#notes_prestige_record_id').val()
		};
		$.post(
			ajaxurl,
			data,
			function(response)
			{
				$('#prestigeNotesModalDialog').modal('hide');
			}
		);
	}
	
})(jQuery);