(function( $ ) {
	'use strict';
	let $editOfficeModal,
		usersJSON,
		officersJSON,
		$tr;
		
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
		$('#reset_permissions_button').click(resetPermissions);
	}
	
	function resetPermissions()
	{
		$.post(
			ajaxurl,
			{action:'reset_permissions'},
			function(response)
			{
				console.log(response);
			}
		);
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
		let $button = $(this);
		$tr = $button.closest('tr');
		let	officeData = $tr.data(),
			officerList = officersJSON[officeData.idDomains],
			$select = $('#office_edit_id_superior').empty().append(
				$('<option value="">No superior</optoin>')
			);
		$editOfficeModal.modal('show');
		
		for(let officer of officerList)
		{
			$(`<option value="${officer.id_officers}">${officer.title}</option>`).appendTo($select);
		}
		$('#office_edit_chain').val(officeData.chain);
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
			action:				'update_office',
			id:					$('#office_edit_id_officers').val(),
			id_users:			$('#office_edit_id_users').val(),
			title:				$('#office_edit_office-title').val(),
			email:				$('#office_edit_email').val(),
			date_appointed:		$('#office_edit_office-date').val(),
			id_superior:		$('#office_edit_id_superior').val(),
			id_domains:			$('#office_edit_id_domains').val(),
			chain:				$('#office_edit_chain').val(),
			first_name:			$('#office_edit_first_name').val(),
			last_name:			$('#office_edit_last_name').val(),
			membership_number:	$('#office_edit_membership_number').val()
		};
		
		$.post(
			ajaxurl,
			data,
			function(response)
			{
				if(response.success === true)
				{
					updateOfficeUIElement(data);
				}
				else
				{
					console.log(response);
				}
			}
		);
	}
	
	function updateTRData(data)
	{
		$tr.data({
			idOfficers:data.id,
			idUsers:data.id_users,
			title:data.title,
			email:data.email,
			dateAppointed:data.date_appointed,
			idSuperior:data.id_superior?parseInt(data.id_superior):'',
			idDomains:parseInt(data.id_domains),
			chain:data.chain,
			firstName:data.first_name,
			lastName:data.last_name,
			membershipNumber:data.membership_number
		});
	}
	
	function updateRow(data)
	{
		let userIndex = 0, searching = true, officerName = null;
		
		while(searching && userIndex < usersJSON.length)
		{
			let user = usersJSON[userIndex];
			if(parseInt(user.id) === parseInt(data.id_users))
			{
				officerName = `${user.first_name} ${user.last_name}`;
				searching = false;
			}
			userIndex++;
		}
		
		$('.tr_office_title', $tr).text(data.title);
		$('.tr_office_name', $tr).text(officerName);
		$('.tr_office_membership_number', $tr).text(data.membership_number);
		$('.tr_office_date_appointed', $tr).text(data.date_appointed);
		$editOfficeModal.modal('hide');
		
	}
	
	function updateOfficeUIElement(data)
	{
		updateTRData(data);
		updateRow(data);
	}
	
})( jQuery );