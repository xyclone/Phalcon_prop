<script>

$(document).ready(function () {

    $("#type").select2({
        theme: "bootstrap",
        allowClear: true, 
        placeholder:'- Select Upload Type -', 
        width: "100%"
    }); 

    $("#project_id").select2({
        theme: "bootstrap",
        allowClear: true, 
        placeholder:'- Select Project Name -', 
        width: "100%"
    }); 


    $(document).on("click", "#reset",function() {
        clear_form();
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
            [15, 25, 50, 100, 250, 500, -1],
            ["15 Rows", "25 Rows", "50 Rows", "100 Rows", "250 Rows", "500 Rows", "All Rows"]
        ],
        "buttons": [
            {
                background:true,
                extend: 'pageLength',
            },
        ],
        "columnDefs": [
            { "width": "10%", "class": "text-center", "targets": [0] },
            { "width": "15%", "targets": [1] },
            { "width": "20%", "targets": [2] },
            { "width": "40%", "targets": [3] },
            { "width": "15%", "targets": [4] },
        ],        
        "order": [[ 0, "desc" ]],
    });

    $("#{{form_name}}").validate({ 
        rules: {
            type: {
                required: true
            }
        },
        messages: { },
        ignore: ".ignore, :hidden",
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
                dataType:'json',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log(XMLHttpRequest.responseText);
                },
                beforeSend: function(){
                    $('#uploadmodal').modal({ backdrop: 'static', keyboard: false });
                    $('#uploadmodal').modal('show');
                },
                complete: function() {
                    $('#uploadmodal').modal('hide');
                },
                success: function(response){
                    $('#uploadmodal').modal('hide');
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
    url: '{{ url('upload/list') }}',
    dataType:'html',
    success: function(response){
      $('#listView').html(response);
    }
  });
}

function clear_form(id){
    $("#{{form_name}}").trigger('reset');
    $("#type").val("").trigger("change");
    $("#project_id").val("").trigger("change");
    $(".file").val("").trigger("change");
    $.ajax({
        type: 'GET',
        url: 'upload/newtoken',
        dataType:'json',
        contentType: false,
        cache: false,
        processData: false,
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(XMLHttpRequest.responseText);
        },
        success: function(response){
            $('input[name="{{form_name}}"]').find("input[type='hidden']").remove();
            $('<input>').attr({
                type: 'hidden',
                id: response.tokenKey,
                name: response.tokenKey,
                value: response.token
            }).appendTo("#{{form_name}}");
        }
    });    
}
</script>