function saveInvoice($form,$url) {
	// AJAX code to submit form.
	$.ajax({
        type: 'POST',
        url: $url,
        data: $form.serialize()
    })
    .done(function(data){
        var d = JSON.parse(data);
        alert(d.message);
				if(d.error === false)
				{
					window.location.href = './list-invoices.php';
				}
				else {
					return false;
				}

    })
    .fail(function() {
        alert('Add new invoice request failed');
    });

}

function editInvoice($form,$url) {
	// AJAX code to submit form.
	$.ajax({
        type: 'POST',
        url: $url,
        data: $form.serialize()
    })
    .done(function(data){
        var d = JSON.parse(data);
        alert(d.message);
        if(d.error === false)
				{
					window.location.href = './list-invoices.php';
				}
				else {
					return false;
				}
    })
    .fail(function() {
        alert('Update request failed');
    });
}


function deleteInvoice($form,$url) {
	// AJAX code to submit form.
	$.ajax({
        type: 'POST',
        url: $url,
        data: $form.serialize()
    })
    .done(function(data){
        var d = JSON.parse(data);
    		alert(d.message);
				window.location.href = './list-invoices.php';
    })
    .fail(function() {
        alert('Delete request failed');
    });
}


function sendMail($form,$url) {
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
        alert('Sending mail request failed');
    });
}
