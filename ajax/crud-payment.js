function getPayments($form,$url) {
	// AJAX code to submit form.
	$.ajax({
        type: 'GET',
        url: $url,
        data: $form.serialize()
    })
    .done(function(data){
        var data = JSON.parse(data);

				$('#listPayments tbody').empty();
				if(data !== null)
		    {
					jQuery.each(data, function(i, payment){
						var payDate = new Date(payment.date);
						day = payDate.getDate()>9?payDate.getDate():'0'+payDate.getDate(),
						year = payDate.getFullYear(),
						month = payDate.getMonth()>9?payDate.getMonth():'0'+payDate.getMonth(),
						date = year + '-' + month + '-' + day,
						publicNote = payment.publicnote,
						privateNote = payment.privatenote,
						id = payment.id,
						amount = payment.amount,
						method = payment.method;

						var actions = '<ul class="list-inline actions">'+
				    '<li>' +
				    '<a href="#paymentModal" data-paymentid="' + id + '" data-method="' + method + '" data-amount="' + amount + '" data-date="' + date + '" data-privatenote="' + privateNote + '" data-publicnote="' + publicNote + '" data-toggle="modal" class="btn btn-success" style="margin: 2px">' +
				    '<i class="fa fa-fw fa-edit"></i></a></li>' +
				    '<li>' +
				    '<a href="#deletePaymentModal" data-id="' + payment.id + '" data-toggle="modal" class="btn btn-danger" style="margin: 2px">' +
				    '<i class="fa fa-fw fa-trash"></i></a></li></ul>';
						var tr = "<tr><td>"+date+"</td>"+
			      "<td>"+payment.amount+"</td>"+
			      "<td>"+payment.publicnote+"</td>"+
						"<td>"+actions+"</td></tr>";
			      $('#listPayments tbody').append(tr);
			    });
				}
				else {
					var tr = "<tr><td colspan='4' align='center'>There is no payment for this invoice</td></tr>";
					$('#listPayments tbody').append(tr);
				}
    })
    .fail(function() {
        alert('Retrieving payments request failed');
    });
}

function savePayment($form,$url) {
	// AJAX code to submit form.
	$.ajax({
        type: 'POST',
        url: $url,
        data: $form.serialize()
    })
    .done(function(data){
        var d = JSON.parse(data);
        alert(d.message);
    })
    .fail(function() {
        alert('Add payment request failed');
    });

}

function editPayment($form,$url) {
	// AJAX code to submit form.
	$.ajax({
        type: 'POST',
        url: $url,
        data: $form.serialize()
    })
    .done(function(data){
        var d = JSON.parse(data);
        alert(d.message);
    })
    .fail(function() {
        alert('Update request failed');
    });
}


function deletePayment($form,$url) {
	// AJAX code to submit form.
	$.ajax({
        type: 'POST',
        url: $url,
        data: $form.serialize()
    })
    .done(function(data){
        var d = JSON.parse(data);
    		alert(d.message);
    })
    .fail(function() {
        alert('Delete request failed');
    });
}
