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
				{data:'approved'}
			]
		});
	});
	
	
	
})(jQuery);