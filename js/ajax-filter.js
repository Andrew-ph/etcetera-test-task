jQuery(function($){

//Filtering CPT list in div#response
    function filter_CPT() {
        var filter = $('#filter');
        $.ajax({
            url:filter.attr('action'),
            data:filter.serialize(), // form data
            type:filter.attr('method'), // POST
            beforeSend:function(xhr){
                filter.find('button').text('Processing...'); // changing the button label
            },
            success:function(data){
                filter.find('button').text('Apply filter'); // changing the button label back
                $('#response').html(data); // insert data
            }
        });
        return false;
    };

//Reset filter and filter the list by button "reset" list
    $("#reset-button").on('click', function(){
        this.form.reset(); // forcing reset event
        filter_CPT();
    });

//Filter CPT on dropdown and radio button change
    $('.jquery-filter').on('change', function() {
        filter_CPT();
    });

});