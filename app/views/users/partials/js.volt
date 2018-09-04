<script>

//Simplefied Ajax Modal Actions
function handleAjaxError(type, body='', footer='') {
    switch(type) {
        case 'success':
            $('.modal-header').addClass("bg-success");
            titleHtml = '<i class="fa fa-check-circle fa-fw" aria-hidden="true"></i> Success';
        break;
        case 'error':
        default:
            $('.modal-header').addClass("bg-red");
            titleHtml = '<i class="fa fa-exclamation-circle fa-fw" aria-hidden="true"></i> Error';
        break;    
    }    
    if(body=="") { bodyHtml = "<p>No item is selected. Please select atleast one.</p>"; } 
    else { bodyHtml = "<p>"+body+"</p>"; }
    if(footer=="") { footerHtml = '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'; } 
    else { footerHtml = footer+'<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'; }

    $('#modal-ajax-handler').attr("data-id", type);
    $('#modal-ajax-handler').find(".modal-title").html(titleHtml);
    $('#modal-ajax-handler').find(".modal-body").html(bodyHtml);
    $('#modal-ajax-handler').find(".modal-footer").html(footerHtml);
    $('#modal-ajax-handler').modal('show');
}// ./Simplefied Ajax Modal Actions

$(function () {
    //$('form[name="users_list"]').find('.select2').select2({
    $('.select2').select2({
        theme: "bootstrap", 
        placeholder:'- Select Profile -', 
        width: "100%"
    });
    //$("#input-b5").fileinput({showCaption: false, dropZoneEnabled: false});
    $('#kvFileinputModal').appendTo('body');

    $('#table').DataTable({
        "processing": true,
        "serverSide": false,
        "responsive": false,
        "autoWidth": false,
        "scrollX": false,
        "sPaginationType": "full_numbers",
        "dom": "<'row mbox-mb-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12'B><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging-top'f>>rt<'row mbox-pt-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-info'i><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging'p>>",
        "lengthMenu": [
            [5, 25, 50, 100, 250, 500, -1],
            ["5 Rows", "25 Rows", "50 Rows", "100 Rows", "250 Rows", "500 Rows", "All Rows"]
        ],
        "buttons": [
            {
                background:true,
                extend: 'pageLength',
            },
        ],
        "columnDefs": [
            { "width": "25%", "targets": [0] },
            { "width": "20%", "targets": [1,2,3] },
            { "class": "text-center", "targets": [-1] },
            { "orderable": false, "width": "15%", "targets": [-1] },
        ],        
        "order": [[ 1, "asc" ]],
    });


});

$(document).ready(function () {
    $("#{{form_name}}").validate({ 
        rules: {
            name: {
                required: true
            },     
            email: {
                required: true,
                email: true
            },     
            password: {
                required: true,
                minlength: 8
            }, 
            confirmPassword: {
                required: true,
                minlength: 8,
                equalTo: "input[name='password']"
            },
            mobile: {
                required: true,
                number: true
            },
            "usergroup[]": {
                required: true
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
                dataType:'json',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
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
    url: '{{ url('users/list') }}',
    dataType:'html',
    success: function(response){
      $('#listView').html(response);
    }
  });
}

function deleted(id, users) {
  $('input#id_delete').val(id);
  $('span#users').text(users);
}

function updated(id) {
    
    //$.fn.modal.Constructor.prototype.enforceFocus = function() {};

    var form = $('form[name="user_edit"]').attr('action', '{{ url('users/updated/') }}' + id);
  
    $.ajax({
        type: 'GET',
        url: '{{ url('users/detail/') }}'+id,
        dataType:'json',
        success: function(response){

            var $form = $('form#user_edit');      
            $.each(response, function(key, value) {               
                $form.find('input[name="'+key+'"]')
                .not('input[name="usergroup[]"]')
          		.not('input[name="image"]')
                .not('input[name="password"]')
                .val(value);
                if (key == 'image') {
                    $('input[name="remove_image"]').val(value);
                    $('#uploadPreview2').attr('src', 'img/users/'+value);
                }
            });
            var str = response.usergroup;
            var res = str.split(",");
            var cleanRes = res.filter(function(v){return v!==''});
            $('form#user_edit :input[name="usergroup[]"]').val(cleanRes.join(',').split(',')).trigger('change');
        }
    });
}

function clear_form(id){
    $('form[name="users_list"]').find('[name]').not('[name="usergroup"]', '[name="image"]').val('');
    $('form[name="user_edit"]').find('[name]').not('[name="usergroup"]', '[name="image"]').val('');

    $('form#users_list :input[name="usergroup[]"]').val('').trigger('change');
    $('form#user_edit :input[name="usergroup[]"]').val('').trigger('change');
    $('form#users_list :input[type="file"]').val('').trigger('change');
    $('form#user_edit :input[type="file"]').val('').trigger('change');
    $("#imgPreview1").empty();
    
}

function status(id, status) {
    $.ajax({
        type: 'POST',
        url: 'users/status',
        dataType:'json',
        data: 'id='+id+'&active='+status,
        success: function(response){
console.log(response);            
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

$(document).on('click', '.fileinput-remove', function(e) {
    $('#uploadImage1, uploadImage2').focus();
});

</script>