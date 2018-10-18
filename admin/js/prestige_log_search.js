(function( $ ) {
	'use strict';
	let users_json = null,
		offices = null,
		$row = null;
	
	$(function(){
		parseJSONElements();
		buildAutoComplete();
		bindEventHandlers();
		
	});
	
	function parseJSONElement(id)
	{
		return JSON.parse(document.getElementById(id).innerHTML);
	}
	
	function parseJSONElements()
	{
		users_json = parseJSONElement('users_json');
		offices = parseJSONElement('officers_json');
	}
	
	function bindEventHandlers()
	{
		$('#prestige_search_button').click(fetchPrestigeLog);
		$('#prestige_record_note_btn').click(addPrestigeNote);
	}
	
	function bindNotesButtons()
	{
		$('.prestige-note-button').off().click({offices:offices}, Prestige.showNotesModal);
	}
	
	function fetchPrestigeLog()
	{
		let data = {
			action:'fetch_user_prestige',
			id_users:$('#prestige_id_users').val()
		};
		$.post(
			ajaxurl,
			data,
			function(response)
			{
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