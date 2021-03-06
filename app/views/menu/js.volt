<script>
$(function () {
    $('#table').DataTable();
});

(function() {

    $('form[data-remote]').on('submit', function(e) {
        var form = $(this);
        var url = form.prop('action');

        $.ajax({
            type: 'POST',
            url: url,
            dataType:'json',
            data: form.serialize(),
            success: function(response){
                new PNotify({
                    title: response.title,
                    text: response.text,
                    type: response.type
                });
                if (response.close === 1) {
                    $('#Tambah').modal('hide');
                    list();
                } else if (response.close === 2) {
                    $('#Delete').modal('hide');
                    $('#del'+response.id).fadeOut(1000);
                } else {
                    list();
                    clear_form();
                }
            }
        });
        e.preventDefault();
    });
})();

function list() {
    $.ajax({
        type: 'GET',
        url: '{{ url('work/list') }}',
        dataType:'html',
        success: function(response){
            $('#listView').html(response);
        }
    });
}

function deleted(id, pekerjaan) {
    $('input#id_delete').val(id);
    $('span#pekerjaan').text(pekerjaan);
}

function updated(id, pekerjaan, description) {
    var form = $('form[name="pekerjaan"]').attr('action', '{{ url('Work/updated/') }}' + id);
    form.find('[name="pekerjaan"]').val(pekerjaan);
}

function clear_form(id){
    $('form[name="pekerjaan"]').find('[name]').val('');
    $('form[name="group"]').find('[name]').not('[name^=usergroup]').val('');
}

function status(id, status) {
    $.ajax({
        type: 'POST',
        url: 'Work/status',
        dataType:'json',
        data: 'id='+id+'&actived='+status,
        success: function(response){
            new PNotify({
                title: response.title,
                text: response.text,
                type: response.type
            });
            if (response.i === 'text-success' && response.bg === 'bg-green') {
                $("td i#text"+id)
                .attr("onclick", "status("+id+", '"+response.active+"')")
                .toggleClass('text-danger text-success');

                $("td span#status"+id)
                .toggleClass('bg-red bg-green')
                .text(response.status);
            } else if (response.i === 'text-danger' && response.bg === 'bg-red') {
                $("td i#text"+id)
                .attr("onclick", "status("+id+", '"+response.active+"')")
                .toggleClass('text-success text-danger');

                $("td span#status"+id)
                .toggleClass('bg-green bg-red')
                .text(response.status);
            }
        }
    });
}
</script>