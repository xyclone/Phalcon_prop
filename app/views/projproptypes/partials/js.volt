<script>

$(function () {
    //$('form[name="users_list"]').find('.select2').select2({
    $('.select2').select2({
        theme: "bootstrap", 
        placeholder:'- Select Type -', 
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
            { "width": "40%", "targets": [0] },
            { "width": "40%", "targets": [1,2] },
            { "class": "text-center", "targets": [-1] },
            { "orderable": false, "width": "20%", "targets": [-1] },
        ],        
        "order": [[ 0, "asc" ]],
    });
});

(function() {
    $('form[data-remote]').on('submit', function(e) {
        var form = $(this);
        var url = form.prop('action');   
        $.ajax({
            type: 'POST',
            url: url,
            dataType:'json',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {
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
                    $('#table').DataTable().row('#del'+response.name).remove().draw( false );
                } else {
                    list();
                    clear_form();
                    $('#table').DataTable().fnReloadAjax();
                }
            }
        });
        e.preventDefault();
    });
})();

function list() {
    $.ajax({
        type: 'GET',
        url: '{{ url("projproptypes/list") }}',
        dataType:'html',
        success: function(response){
            $('#listView').html(response);
        }
    });
}

function deleted(name, fieldname) {
    $('input#id_delete').val(name);
    $('span#field').text(fieldname);
}

function updated(name) {
    var form = $('form[name="proptype_edit"]').attr('action', '{{ url("projproptypes/updated/") }}' + name);
    $.ajax({
        type: 'GET',
        url: '{{ url("projproptypes/detail/") }}'+name,
        dataType:'json',
        success: function(response) {
            var $form = $('form#proptype_edit');      
            $.each(response, function(key, value) {
                if(key==='project_type_id') {
                    $form.find('#'+key).val(value).trigger('change');
                } else {
                    $form.find('input[name="'+key+'"]').val(value);
                }
                
            });
            
        }
    });
}

function clear_form(name){
    $('form[name="projproptypes"]').find('[name]').val('');
}
</script>