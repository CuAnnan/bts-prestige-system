(function( $ ) {
	'use strict';
	
	$(function(){
		let data = Object.values(JSON.parse(document.getElementById('prestige_records').innerHTML));
		console.log(data);
		$('#prestige_record_table').DataTable({
			data:data,
			columns:[
				{data:'description'},
				{data:'category'},
				{data:'reward_amount'},
				{data:'reward_type'},
				{data:'date_claimed'},
				{data:'officer_title'},
				{data:'domain_name'},
				{data:'genre_name'},
				{data:'approved'},
				{data:null, orderable:false, defaultContent:`<button class="btn btn-primary prestige-note-button">Notes</button>`}
			],
			createdRow:function(row, data, dataIndex)
			{
				$(row).data({notes:data.notes, id:data.id});
			}
		});
		$('#prestige_record_note_btn').click(addPrestigeNote);
		$('.prestige-note-button').click(showNotes);
	});
	
	function showNotes()
	{
		let $button = $(this),
			$row = $button.closest('tr'),
			data = $row.data(),
			notes = data.notes,
			$notesTable =$('#prestige-notes').empty();
		$('#prestige_record_approved').val('false');
		$('#notes_prestige_record_id').val(data.id);
		for(let note of notes)
		{
			$('<tr/>')
				.append($('<td/>').text(note.note))
				.append($('<td/>').text(parseInt(note.approved) === 1))
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
			'approved':				$('#prestige_record_approved').val(),
			'id_prestige_record':	$('#notes_prestige_record_id').val()
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
	
})(jQuery);