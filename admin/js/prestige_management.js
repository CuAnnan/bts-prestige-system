(function( $ ) {
	'use strict';
	
	let $prestigeModal = $('#newPrestigeRecordModalDialog'),
		prestigeCategories = null,
		prestigeActions = null,
		domains = null,
		venues = null,
		offices = null,
		prestigeLog = null,
		$dataTable = null;
	
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
		populateSelect('#id_venues', offices.filter(office=> (office.id_domains === $('#id_domains').val() && office.chain === $('#chain').val())), 'full_title');
		//populateSelect('#id_venues', venues.filter(venue => venue.id_domains === $('#id_domains').val()), 'genre');
	}
	
	function bindEvents()
	{
		$('#prestige_record_note_btn').click(addPrestigeNote);
		$('#prestige_claim_button').click(showPrestigeClaimForm);
		$('#newPrestigeRecordForm').on('submit', ()=>{validateAndSubmitPrestigeClaimForm(); return false;});
		$('#chain').change(populateOffices);
		Prestige.bindNotesButtons();
	}
	
	function bindNotesButtons()
	{
		$('.prestige-note-button').off().click(Prestige.showNotesModal);
	}
	
	function validateAndSubmitPrestigeClaimForm()
	{
		let idDomains = $('#id_domains').val(),
			idVenues = $('#id_venues').val(),
			chain = $('#chain').val(),
			officer = null;
		if(idVenues)
		{
			officer = offices.filter(office=>office.id_venues === idVenues && office.chain === chain)[0];
		}
		else
		{
			officer = offices.filter(office=>office.id_domains === idDomains && office.chain === chain)[0];
		}
		let data = {
				action: 'add_prestige_record',
				id_officers:parseInt(officer.id),
				id_prestige_actions:$('#id_prestige_actions').val(),
				prestige_amount:parseInt($('#prestige_amount').val()),
				prestige_type:$('input[name=prestige_type]:checked').val(),
				reason:$('#prestige_reason').val(),
				date:$('#claim_date').val()
			},
			domainName = $('#id_domains option:selected').text(),
			genreName = idVenues?$('#id_venues option:selected').text():null;
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
		let now = new Date(),
			day = ("0" + now.getDate()).slice(-2),
			month = ("0" + (now.getMonth() + 1)).slice(-2),
			year = now.getFullYear();
		
		$('#id_prestige_categories').val('');
		$('#id_domains').val('');
		$('#id_prestige_actions').empty();
		$('.prestige-type').removeClass('active');
		$('input[name=prestige_type][value=Open]')
			.prop("checked", true)
			.closest('.prestige-type')
			.addClass('active');
		$('#claim_date').val(`${year}-${month}-${day}`);
		$('#id_venues').empty();
		$('#prestige_reason').val('');
		$('#prestige_amount').val('');
		$prestigeModal.modal('show');
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
	}
	
	function showNotes()
	{
		let $button = $(this),
			$row = $button.closest('tr'),
			data = $row.data(),
			notes = data.notes,
			$notesTable =$('#prestige-notes').empty();
		$('#prestige_record_approved').val('Submitted');
		$('#notes_prestige_record_id').val(data.id);
		for(let note of notes)
		{
			$('<tr/>')
				.append($('<td/>').text(note.note))
				.append($('<td/>').text(note.status))
				.append($('<td/>').text(note.note_date))
				.appendTo($notesTable);
		}
		$('#prestigeNotesModalDialog').modal('show');
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
				$('#prestigeNotesModalDialog').modal('close');
			}
		);
	}
	
})(jQuery);