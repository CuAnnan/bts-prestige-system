(function($){
	"use strict";
	let $claimModal = null,
		$notesModal = null,
		$row = null;
	$.fn.exists = function()
	{
		return this.length !== 0;
	}
		
	$(function(){
		$claimModal = $('#newPrestigeRecordModalDialog');
		$notesModal = $('#prestigeNotesModalDialog');
	});
	
	function populateSelect(selector, fields, fieldTitle, handler)
	{
		let $select = $(selector).empty(),
			$options = [$('<option value="">---</option>')];
		$options.push(...arrayToOptions(fields, fieldTitle));
		$select.append(...$options);
		if(handler)
		{
			$select.change(handler);
		}
	}
	
	function arrayToOptions(fields, fieldTitle)
	{
		let $options = [];
		for(let field of fields)
		{
			$options.push($(`<option value="${field.id}">${field[fieldTitle]}</option>`));
		}
		return $options;
	}
	
	class Prestige
	{
		static setClaimModalAction(action)
		{
			this.action = action;
			return this;
		}
		
		static getClaimModalAction()
		{
			return this.action;
		}
		
		
		static showEditClaimModal(officers, claimData)
		{
			this.setClaimModalAction('edit_prestige_record');
			$('#prestige_claim_h5').text('Edit Prestige Claim');
			$('#id_prestige_categories').val(claimData.id_prestige_categories).change();
			$('#id_prestige_actions').val(claimData.id_prestige_actions).change();
			$('#prestige_reason').val(claimData.notes[0].note).change().prop('disabled', 'disabled');
			$('#prestige_amount').val(claimData.reward_amount).change();
			$('.prestige-type').removeClass('active');
			$(`input[name=prestige_type][value=${claimData.reward_type}]`)
				.prop('checked', true)
				.closest('.prestige-type')
				.addClass('active');
			$('#claim_date').val(claimData.date_claimed.split(" ")[0]);
			let officer = officers.filter((officer)=>officer.id === claimData.id_officers)[0];
			$('#chain').val(officer.chain).change();
			$('#id_domains').val(officer.id_domains).change();
			$('#id_officers').val(officer.id).change();
			$('#id_prestige_record').val(claimData.id);
			$('#editPrestigeRecordButton').show();
			$('#newPrestigeRecordButton').hide();
			$claimModal.modal('show');
		}
		
		static showClaimModal(offices)
		{
			$('#prestige_claim_h5').text('New Prestige Record');
			this.setClaimModalAction('add_prestige_reward');
			
			let now = new Date(),
			day = ("0" + now.getDate()).slice(-2),
			month = ("0" + (now.getMonth() + 1)).slice(-2),
			year = now.getFullYear();
			$('#prestige_reason').removeAttr('disabled');
			let $form = $('#newPrestigeRecordForm');
			$('input[type=text]', $form).val('');
			$('select', $form).val('');
			$('#prestige_amount').val('');
			$('.prestige-type').removeClass('active');
			$('#prestige_reward_open')
				.prop('checked', true)
				.closest('.prestige-type')
				.addClass('active');
			$('#claim_date').val(`${year}-${month}-${day}`);
			Prestige.populateOfficesSelect($('#prestige_reward_id_officers'), offices);
			$('#prestige_reward_id_officers').val($("#id-acting-office").val());
			$('#editPrestigeRecordButton').hide();
			$('#newPrestigeRecordButton').show();
			$claimModal.modal('show');
			return this;
		}
		
		static populateOfficesSelect($offices, offices)
		{
			if(!$offices.exists())
			{
				return;
			}
			let userOffices = offices.filter((office)=>office.id_users == user_id),
				relevantOffices = offices.filter((office)=>office.id === $("#id-acting-office").val());
			
			$offices.empty();
			$('<option>---</option>').appendTo($offices);
			$('<optgroup/>').prop('label', 'Original office').append(arrayToOptions(relevantOffices, 'full_title')).appendTo($offices);
			$('<optgroup/>').prop('label', 'My offices').append(arrayToOptions(userOffices, 'full_title')).appendTo($offices);
			
		}
		
		static showNotesModal(event)
		{
			let $button = $(this);
			$row = $button.closest('tr');
			let	data = $row.data(),
				notes = data.notes,
				$notesTable =$('#prestige-notes').empty();
			$('#prestige_record_approved').val('Submitted');
			$('#notes_prestige_record_id').val(data.id);
			for(let note of notes)
			{
				$('<tr/>')
					.append($('<td/>').text(note.note))
					.append($('<td/>').text(note.note_officer_title?`${note.note_officer_title} ${note.note_domain_name}`:''))
					.append($('<td/>').text(note.member))
					.append($('<td/>').text(note.status?note.status:'-'))
					.append($('<td/>').text(note.note_date.split(' ')[0]))
					.appendTo($notesTable);
			}
			$('#prestige_record_note').val('');
			if(event.data)
			{
				Prestige.populateOfficesSelect($('#prestige_record_id_officers'), event.data.offices);
				$('#prestige_record_id_officers').val($("#id-acting-office").val());
			}
			
			$('#prestigeNotesModalDialog').modal('show');
			$notesModal.modal('show');
			return this;
		}
	}
	
	window.Prestige = Prestige;
})(jQuery);