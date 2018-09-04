(function( $ ) {
	'use strict';
	let $editOfficeModal,
		usersJSON;
	
	$(()=>{
		$('.btn_edit_office').click(editOffice);
		$editOfficeModal = $('#editOfficeModal');
		usersJSON = JSON.parse(document.getElementById('allUsersMeta').innerHTML);
		var options = {
			data:usersJSON,
			adjustWidth:0,
			getValue: function(element){return `${element.first_name} ${element.last_name} (${element.membership_number})`;},
			list: {
				match: {
					enabled: true
				}
			}
		};
		$("#office-member").easyAutocomplete(options);
	});
	
	function editOffice()
	{
		let $button = $(this);
		let $tr = $button.closest('tr');
		let officeData = $tr.data();
		$editOfficeModal.modal('show');
		$('#office-title').val(officeData.title);
		$('#office-member').val(`${officeData.firstName} ${officeData.lastName} (${officeData.membershipNumber})`);
		$('#office-date').val(officeData.dateAppointed);
		$('#updateModalButton').unbind().click(officeData, updateOffice);
	}
	
	function updateOffice(evt)
	{
		let data = evt.data;
		console.log(data);
	}
	
})( jQuery );