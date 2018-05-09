function saveQuote($form,$url) {
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
					window.location.href = './list-quotes.php';
				}
				else {
					return false;
				}
    })
    .fail(function() {
        alert('Add new quote request failed');
    });

}

function editQuote($form,$url) {
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
					window.location.href = './list-quotes.php';
				}
				else {
					return false;
				}
    })
    .fail(function() {
        alert('Update request failed');
    });
}


function deleteQuote($form,$url) {
	// AJAX code to submit form.
	$.ajax({
        type: 'POST',
        url: $url,
        data: $form.serialize()
    })
    .done(function(data){
        var d = JSON.parse(data);
    		alert(d.message);
				window.location.href = './list-quotes.php';
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
