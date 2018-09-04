<script>
$(function () {
    $('#table1').DataTable({   
        "processing": true,
        "serverSide": false,
        "responsive": false,
        "autoWidth": false,
        "scrollX": false,     
        "sPaginationType": "full_numbers",
        "dom": "<'row mbox-mb-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12'B><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging-top'f>>rt<'row mbox-pt-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-info'i><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging'p>>",
        "lengthMenu": [
            [5, 10, 25, 50, -1],
            ["5 Rows", "10 Rows", "25 Rows", "50 Rows", "All Rows"]
        ],
        "buttons": [
            {
                background:true,
                extend: 'pageLength',
            },
        ],
        "columnDefs": [
            { "orderable": false, "width": "10%", "class": "text-center", "targets": [-1] },
        ],   
    });
});

$(function () {
    $('#table2').DataTable({
        "processing": true,
        "serverSide": false,
        "responsive": false,
        "autoWidth": false,
        "scrollX": false,
        "sPaginationType": "full_numbers",
        "dom": "<'row mbox-mb-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12'B><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging-top'f>>rt<'row mbox-pt-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-info'i><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging'p>>",
        "lengthMenu": [
            [10, 25, 50, -1],
            ["10 Rows", "25 Rows", "50 Rows", "All Rows"]
        ],
        "buttons": [
            {
                background:true,
                extend: 'pageLength',
            },
        ],
        "columnDefs": [
            { "width": "5%", "targets": [0, 1] },
            { "width": "35%", "targets": [2] },
            { "width": "10%", "targets": [3,4,5,6] },
            { "orderable": false, "width": "10%", "class": "text-center", "targets": [-1] },
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
            data: form.serialize(),
            success: function(response){
                new PNotify({
                    title: response.title,
                    text: response.text,
                    type: response.type
                });
                if (response.close === 1) {
                    list();
                    clear_form(response.close);
                } else if (response.close === 2) {
                    $('#Delete').modal('hide');
                    list();
                    $('#del'+response.id).fadeOut(1000);
                } else if (response.close === 3) {
                    $('#groupDelete').modal('hide');
                    listGroup();
                    clear_form();
                } else if (response.close === 4) {
                    $('#groupDelete').modal('hide');
                    listGroup();
                    $('#groupdel'+response.id).fadeOut(1000);
                } else {
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
        url: '{{ url('acl/list') }}',
        dataType:'html',
        success: function(response){
            $('#listView').html(response);
            }
    });
}

function listGroup() {
    $.ajax({
        type: 'GET',
        url: '{{ url('acl/menugroup/list') }}',
        dataType:'html',
        success: function(response){
            $('#listViewGroup').html(response);
        }
    });
}

function deleted(id, acl) {
    $('input#id_delete').val(id);
    $('span#acl').text(acl);
}

function updated(id, url, controller, action, except) {
    $('#CreateModel').find('#labelAcl').html('<i class="fa fa-pencil-square-o fa-fw" aria-hidden="true"></i> Update Acl');
    var form = $('form[name="acl"]');
    form.attr('action', '{{ url('acl/updated/') }}' + id);

    $.ajax({
        type: 'GET',
        url: '{{ url('acl/detail/') }}'+id,
        dataType:'json',
        success: function(response){
            $.each(response, function(key, value) {
                form.find('[name="'+key+'"]')
                    .not('input[name="usergroup"]')
                    .val(value);
            });

            form.find('[name="actions"]').val(response.action);
            var str = response.usergroup;
            var res = str.split(",");
            for (var i = 0; i < res.length; i++) {
                $('input[type="checkbox"]#data'+res[i]).prop('checked', true);
            }
        }
    });

    var btn_submit = form.find('button[type="submit"]');
    btn_submit.removeClass('btn-success');
    btn_submit.addClass('btn-primary');
    btn_submit.html('<i class="fa fa-floppy-o" aria-hidden="true"></i> Save Update');
}

function clear_form(id){
    $('#CreateModel').find('#labelAcl').html('<i class="fa fa-pencil-square-o fa-fw" aria-hidden="true"></i> Input Acl');
    var form  = $('form[name="acl"]');
    form.attr('action', '{{ url('acl/input') }}');
    form.find('[name]').not('[name^="usergroup"]').val('');
    form.find('[name^="usergroup"]').prop('checked', false);
    form.find('.usergroup').show();
    $('form[name="group"]').find('[name]').not('[name^=usergroup]').val('');
    $('form[name="group"]').find('[name^="usergroup"]').prop('checked', false);

    var btn_submit = form.find('button[type="submit"]');
    btn_submit.removeClass('btn-primary');
    btn_submit.addClass('btn-success');
    btn_submit.html('<i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i> Save Update');

    if (id === 1) {
        $('#CreateModel').modal('hide');
    }
}

function status(id, status) {
    $.ajax({
        type: 'POST',
        url: 'acl/status',
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

$('input[type="checkbox"]#check').on("click", function() {
    var val = $(this).val();
    var res = val.split(",");
  
    $.ajax({
        type: 'POST',
        url: 'acl/access',
        dataType:'json',
        data: 'id='+res[0]+'&usergroup='+res[1],
        success: function(response){
            new PNotify({
                title: response.title,
                text: response.text,
                type: response.type
            });
        }
    });
});

$('.except').keyup(function(event) {
    newText = event.target.value;
    $('textarea.except').attr('textval', newText);
    console.log(newText);
});

function except(that) {
    var isi = $(that).html().trim();
    var id  = $(that).attr('acl');
    $(that).parent().html('<textarea class="form-control" onblur="return except_back(this)" style="width:100%; height:100%;" acl="'+id+'">'+isi+'</textarea>').click(); 
    $(that).parent().find('textarea').focus();
    return false;
}

function except_back(that){
    var isi = $(that).val();
    var id  = $(that).attr('acl');
    $(that).parent().html('<div ondblclick="return except(this)" style="padding: 10px;" acl="'+id+'">'+isi+'</div>');
    $.ajax({
        method: "POST",
        dataType: "json",
        url: 'acl/except',
        data: 'id='+id+'&except='+isi,
        success: function(response){
            new PNotify({
                title: response.title,
                text: response.text,
                type: response.type
            });
        }
    });
    return false;
}

function menuAsk(that) {
    var val = $(that).val();
    if (val == 'Y') {
        $('#menuShow').collapse('show');
    } else {
        $('#menuShow').collapse('hide');
    }
}

function deletedGroup(id, group) {
    $('input#group_delete').val(id);
    $('span#menuGroup').text(group);
}

function statusGroup(id, status) {
    $.ajax({
        type: 'POST',
        url: 'acl/menugroup/status',
        dataType:'json',
        data: 'id='+id+'&active='+status,
        success: function(response){
            new PNotify({
                title: response.title,
                text: response.text,
                type: response.type
            });
            if (response.i === 'text-success' && response.bg === 'bg-green') {
                $("td i#grouptext"+id)
                .attr("onclick", "statusGroup("+id+", '"+response.active+"')").parent()
                .toggleClass('btn-success btn-danger');
            } else if (response.i === 'text-danger' && response.bg === 'bg-red') {
                $("td i#grouptext"+id)
                .attr("onclick", "statusGroup("+id+", '"+response.active+"')").parent()
                .toggleClass('btn-danger btn-success');
            }
        }
    });
}
</script>