(function( $ ) {
	'use strict';
	let $editOfficeModal,
		usersJSON,
		officersJSON;
		
	function parseJSONElement(elementId)
	{
		return JSON.parse(document.getElementById(elementId).innerHTML);
	}
	
	$(()=>{
		usersJSON = parseJSONElement('allUsersMeta');
		officersJSON = parseJSONElement('officerHeirarchy');
		$editOfficeModal = $('#editOfficeModal');
		bindEventHandlers();
		setUpAutoComplete();
		
	});
	
	function bindEventHandlers()
	{
		// event handlers
		$('.btn_edit_office').click(editOffice);
		$('#updateModalButton').click(updateOffice);
	}
	
	function setUpAutoComplete()
	{
		let $autoComplete = $("#office_edit_office-member");
		var options = {
			data:usersJSON,
			adjustWidth:0,
			getValue: function(element){return `${element.first_name} ${element.last_name} (${element.membership_number})`;},
			list: {
				match: {
					enabled: true
				},
				onSelectItemEvent: function()
				{
					let data = $autoComplete.getSelectedItemData();
					$('#office_edit_id_users').val(data.id);
					$('#office_edit_first_name').val(data.first_name);
					$('#office_edit_last_name').val(data.last_name);
					$('#office_edit_membership_number').val(data.membership_number);
				}
			}
		};
		$autoComplete.easyAutocomplete(options);
	}
	
	function editOffice()
	{
		let $button = $(this),
			$tr = $button.closest('tr'),
			officeData = $tr.data(),
			officerList = officersJSON[officeData.idDomains],
			$select = $('#office_edit_id_superior').empty().append(
				$('<option value="">No superior</optoin>')
			);
		$editOfficeModal.modal('show');
		for(let officer of officerList)
		{
			$(`<option value="${officer.id_officers}">${officer.title}</option>`).appendTo($select);
		}
		$('#office_edit_id_domains').val(officeData.idDomains);
		$('#office_edit_id_officers').val(officeData.idOfficers);
		$('#office_edit_id_users').val(officeData.idUsers);
		$('#office_edit_first_name').val(officeData.firstName);
		$('#office_edit_last_name').val(officeData.lastName);
		$('#office_edit_membership_number').val(officeData.membershipNumber);
		$('#office_edit_email').val(officeData.email);
		$('#office_edit_office-title').val(officeData.title);
		$('#office_edit_office-member').val(`${officeData.firstName} ${officeData.lastName} (${officeData.membershipNumber})`);
		$('#office_edit_office-date').val(officeData.dateAppointed);
	}
	
	function updateOffice(evt)
	{
		let data = {
			action:			'update_office',
			id:				$('#office_edit_id_officers').val(),
			id_users:		$('#office_edit_id_users').val(),
			title:			$('#office_edit_office-title').val(),
			email:			$('#office_edit_email').val(),
			date_appointed:	$('#office_edit_office-date').val(),
			id_superior:	$('#office_edit_id_superior').val(),
			id_domains:		$('#office_edit_id_domains').val()
		};
		
		$.post(
			ajaxurl,
			data,
			function(response)
			{
				console.log(response);
			}
		);
	}
	
})( jQuery );