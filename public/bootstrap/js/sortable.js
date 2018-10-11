$('#sortable').sortable({
    update: function (event, ui) {
        var data = $(this).sortable('serialize');

        $.ajax({
            data: data,
            type: 'POST',
            success: function (data) {
                console.log(data);
                jQuery(".res").html(data);
                }
        });
    }
});
