(function($){
	"use strict";
	let $claimModal = null,
		$notesModal = null,
		$row = null;
		
	$(function(){
		$claimModal = $('#newPrestigeRecordModalDialog');
		$notesModal = $('#prestigeNotesModalDialog');
	});
	
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
	
	class Prestige
	{
		static showClaimModal()
		{
			let now = new Date(),
			day = ("0" + now.getDate()).slice(-2),
			month = ("0" + (now.getMonth() + 1)).slice(-2),
			year = now.getFullYear();
		
			let $form = $('#newPrestigeRecordForm');
			$('input[type=text]', $form).val('');
			$('select', $form).val('');
			$('#prestige_amount').val('');
			$('.prestige-type').removeClass('active');
			$('#prestige_reward_open')
				.prop('checked', true)
				.closest('.prestige-type')
				.addClass('active');
			$('.prestige-reward-approved').removeClass('active');
			$('#prestige_reward_approve_submitted')
				.prop('checked', true)
				.closest('.prestige-reward-approved')
				.addClass('active');
			$('#claim_date').val(`${year}-${month}-${day}`);
			
			let $offices = $('#prestige_reward_acting_officer');
			if($offices)
			{
				
			}
		
			$claimModal.modal('show');
		}
		
		static showNotesModal()
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
					.append($('<td/>').text(note.note_officer_title?`${note.note_officer_title} ${note.note_domain_name}`:''))
					.append($('<td/>').text(note.member))
					.append($('<td/>').text(note.status))
					.append($('<td/>').text(note.note_date))
					.appendTo($notesTable);
			}
			$('#prestigeNotesModalDialog').modal('show');
			$notesModal.modal('show');
		}
		
		static bindNotesButtons()
		{
			$('.prestige-note-button').off().click(Prestige.showNotesModal);
		}
	}
	
	window.Prestige = Prestige;
})(jQuery);