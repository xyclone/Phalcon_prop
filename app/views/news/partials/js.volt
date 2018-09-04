<script>

$(function () {
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
            { "width": "10%", "class": "text-center", "targets": [0] },
            { "width": "15%", "targets": [1] },
            { "width": "15%", "targets": [2] },
            { "width": "30%", "targets": [3] },
            { "width": "10%", "targets": [4,5] },
            { "class": "text-center", "targets": [-1] },
            { "orderable": false, "width": "10%", "targets": [-1] },
        ],        
        "order": [[ 0, "asc" ]],
    });

    $(document).on('keydown', "#start_date, #stop_date", function(e) {
        var code = (e.keyCode || e.which);
        if(code===8 || code===46 || code===37 || code===38 || code===39) return false;
        e.preventDefault();
    });
    $('input[name="start_date"]').daterangepicker({
        autoApply: false,
        autoUpdateInput: false,
        singleDatePicker: true,
        minDate: moment(),
        maxDate: moment().add(6, 'months'),
        locale: {
          format: 'YYYY-MM-DD'
        }
    }, function(start, end, label) {
        $(this.element[0]).val(start.format('YYYY-MM-DD'));
    });
    $('input[name="stop_date"]').daterangepicker({
        autoApply: false,
        autoUpdateInput: false,
        singleDatePicker: true,
        minDate: moment(),
        maxDate: moment().add(6, 'months'),
        locale: {
          format: 'YYYY-MM-DD'
        }
    }, function(start, end, label) {
        $(this.element[0]).val(start.format('YYYY-MM-DD'));
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
        e.preventDefault();
    });
})();

function list() {
    $.ajax({
        type: 'GET',
        url: '{{ url('news/list') }}',
        dataType:'html',
        success: function(response){
            $('#listView').html(response);
        }
    });
}

function deleted(id, fieldname) {
    $('input#id_delete').val(id);
    $('span#field').text(fieldname);
}

function updated(id) {
    var form = $('form[name="news_edit"]').attr('action', '{{ url('news/updated/') }}' + id);
    $.ajax({
        type: 'GET',
        url: '{{ url('news/detail/') }}'+id,
        dataType:'json',
        success: function(response) {
            console.log(response);
            var $form = $('form#news_edit');      
            $.each(response, function(key, value) {
                if(key=='news') {
                    $form.find('textarea#'+key).html(value);
                } else {
                   $form.find('input[name="'+key+'"]').val(value); 
                }
                
            });
        }
    });
}

function clear_form(id){
    $('form[name="news"]').find('[name]').val('');
}
</script>