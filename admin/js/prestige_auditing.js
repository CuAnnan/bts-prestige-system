(function( $ ) {
	'use strict';
	
	let prestigeCategories = null,
		prestigeActions = null,
		domains = null,
		venues = null,
		offices = null,
		prestigeRecords = null,
		$row = null,
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
		populateSelect('#id_prestige_categories', prestigeCategories, 'name', populateActions);
		populateSelect('#id_domains', domains, 'name', populateVenues);
		
	}
	
	function populateActions()
	{
		populateSelect('#id_prestige_actions', prestigeActions.filter(action => action.id_prestige_category === $('#id_prestige_categories').val()), 'description');
	}
	
	function populateVenues()
	{
		populateSelect('#id_venues', venues.filter(venue => venue.id_domains === $('#id_domains').val()), 'genre');
	}
	
	function bindEvents()
	{
		$('#prestige_record_note_btn').click(addPrestigeNote);
		bindNotesButtons();
	}
	
	function bindNotesButtons()
	{
		$('.prestige-note-button').off().click(showNotes);
	}
	
	function buildDataTable()
	{
		/*
		 * A function to concatenate the user's firstname, last name, and membership number
		 */
		let user = (data)=>
		{
			return `${data.first_name} ${data.last_name} (${data.number})`;
		};
		
		$dataTable = $('#prestige_record_table').DataTable({
			data:prestigeRecords,
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
				{data:null, orderable:false, defaultContent:`<button class="btn btn-primary prestige-note-button">Notes</button>`}
			],
			createdRow:function(row, data, dataIndex)
			{
				$(row).data({notes:data.notes, id:data.id});
			},
			order:[[4, 'desc']]
		});
	}
	
	function showNotes()
	{
		let $button = $(this);
		$row = $button.closest('tr');
		let	data = $row.data(),
			notes = data.notes,
			$notesTable =$('#prestige-notes').empty(),
			currentStatus = notes[notes.length - 1].note_status,
			$status_button = $(`input[name='prestige_record_approved'][value='${currentStatus}']`);
		$('.prestige-record-approved').removeClass('active');
		$status_button.prop('checked', true);
		$status_button.closest('.prestige-record-approved').addClass('active');
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
				'status':				$('input[name=prestige_record_approved]:checked').val(),
				'id_prestige_record':	$('#notes_prestige_record_id').val()
			},
			$dtRow = $dataTable.row($row),
			rowData = $dtRow.data();
			rowData.status = data.status;
		
		$.post(
			ajaxurl,
			data,
			function(response)
			{
				if(data.status === 'Audited')
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
	
})(jQuery);