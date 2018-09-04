<script>


$(function () {
    $('.select2').select2({
        theme: "bootstrap", 
        placeholder:'- Select -', 
        width: "100%"
    });
   
    
    $('#table').DataTable({
        "processing": true,
        "serverSide": false,
        "responsive": false,
        "autoWidth": false,
        "scrollX": false,
        "sPaginationType": "full_numbers",
        "dom": "<'row mbox-mb-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12'B><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging-top'f>>rt<'row mbox-pt-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-info'i><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging'p>>",
        "lengthMenu": [
            [10, 25, 50, 100, 250, 500, -1],
            ["10 Rows", "25 Rows", "50 Rows", "100 Rows", "250 Rows", "500 Rows", "All Rows"]
        ],
        "buttons": [
            {
                background:true,
                extend: 'pageLength',
            },
        ],
        "columnDefs": [
            { "width": "25%", "targets": [0] },
            { "width": "55%", "targets": [1] },
            { "class": "text-center", "targets": [-1] },
            { "orderable": false, "width": "10%", "targets": [-1] },
        ],        
        "order": [[ 0, "asc" ]],
    });


}); 


$(document).ready(function () {
    $("#{{form_name}}").validate({ 
        rules: {
            usergroup: {
                required: true
            },     
            icon: {
                required: true,
            }
        },
        messages: { },
        ignore: ".ignore, :hidden, input[type='file']",
        errorPlacement: function (error, element) {
        },
        invalidHandler: function(event, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                new PNotify({
                    title: 'Error',
                    text: 'Found ' + errors + ' error fields. They have been highlighted.',
                    type: 'error'
                });
            }
        },
        submitHandler: function (form) {               
        }
    });
});


(function() {
    $('form[data-remote]').on('submit', function(e) {
        e.preventDefault();
        if ($(this).validate().numberOfInvalids() != 0) {
            return false;
        } else {
            var form = $(this);
            var url = $(form).prop('action');
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                data:  form.serialize(),
                success: function(response){
                    new PNotify({
                        title: response.title,
                        text: response.text,
                        type: response.type
                    });
                    if (response.close === 1) {
                        $('#EditModal').modal('hide');
                        list();
                    } else if (response.close === 2) {
                        $('#Delete').modal('hide');
                        $('#table').DataTable().row('#del'+response.id).remove().draw( false );
                    } else {
                        list();
                        clear_form();
                        if(response.close!==undefined) {
                            $("#table").ajax.reload();
                            //$('#table').dataTable().fnReloadAjax();
                        }
                    }
                }
            });
        }

    });
})();

function list() {
    $.ajax({
        type: 'GET',
        url: '{{ url('usergroup/list') }}',
        dataType:'html',
        success: function(response){
            $('#listView').html(response);
        }
    });
}

function deleted(id, usergroup) {
    $('input#id_delete').val(id);
    $('span#usergroup').text(usergroup);
}

function updated(id, usergroup, description, icon) {
    var form = $('form[name="usersgroup"]').attr('action', '{{ url('usergroup/updated/') }}' + id);
    form.find('[name="usergroup"]').val(usergroup);
    form.find('[name="description"]').val(description);
    form.find('[name="icon"]').val(icon).trigger('change');;
}

function clear_form(id){
    $('form[name="usersgroup"]').find('[name]').val('');
    $('form[name="group"]').find('[name]').val('');
}

function status(id, status) {
    $.ajax({
        type: 'POST',
        url: 'usergroup/status',
        dataType:'json',
        data: 'id='+id+'&active='+status,
        success: function(response){
            new PNotify({
                title: response.title,
                text: response.text,
                type: response.type
            });
            if (response.i === 'text-success' && response.bg === 'bg-green') {
                $("td i#text"+id)
                    .attr("onclick", "status("+id+", '"+response.active+"')").parent()
                    .toggleClass('btn-success btn-danger');
            } else if (response.i === 'text-danger' && response.bg === 'bg-red') {
                $("td i#text"+id)
                    .attr("onclick", "status("+id+", '"+response.active+"')").parent()
                    .toggleClass('btn-danger btn-success');
            }
        }
    });
}
</script>