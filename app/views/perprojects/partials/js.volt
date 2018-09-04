<script>

$(function () {

    $.fn.dataTable.ext.buttons.reload = {
        text: 'Reload',
        action: function ( e, dt, node, config ) {
            location.reload();
        }
    };

    $('#table').DataTable({
        "processing": true,
        "serverSide": false,
        "responsive": false,
        "autoWidth": false,
        "scrollX": false,
        "sPaginationType": "full_numbers",
        "dom": "<'row mbox-mb-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12'B><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging-top'f>>rt<'row mbox-pt-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-info'i><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging'p>>",
        "lengthMenu": [
            [15, 50, 100, 250, 500, -1],
            ["15 Rows","50 Rows", "100 Rows", "250 Rows", "500 Rows", "All Rows"]
        ],
        "buttons": [
            'reload',
            {
                background:true,
                extend: 'pageLength',
            },
        ],
        "columnDefs": [
            { "width": "10%", "class": "text-center", "targets": [0] },
            { "width": "25%", "targets": [1] },
            { "width": "15%", "targets": [2,3] },
            { "width": "10%", "targets": [4] },
            { "width": "10%", "orderable": false, "class": "text-center", "targets": [-1] },
        ],        
        "order": [[ 0, "asc" ]],
    });
    $.fn.dataTable.ext.buttons.reload = {
        text: 'Reload',
        action: function ( e, dt, node, config ) {
            dt.ajax.reload();
        }
    };

});

// Document Ready
$(document).ready(function() {
    'use strict';

    //Modal Display Action
    $("#modal-ajax-handler").on('hidden.bs.modal', function () {
        $(this).find(".modal-title").empty();
        $(this).find(".modal-body").empty();
        $(this).find(".modal-footer").empty(); 
        $(this).removeClass("hmodal-default hmodal-primary hmodal-info hmodal-warning hmodal-danger hmodal-success");
    });
    $("#modal-ajax-handler").on("shown.bs.modal", function(event) {
        var modalId = $("#modal-ajax-handler");
        var titleHtml=''; var bodyHtml=''; var footerHtml='';
        var dataId = $(event.relatedTarget).data('action');       
        switch(dataId) {
            case 'Details Info':
                $('#modal-ajax-handler').addClass("hmodal-info");
                modalId.find(".modal-header").addClass("bg-primary");
                modalId.find("#dialog-box").addClass("modal-xl");  
                var projectid = $(event.relatedTarget).data('id');          
                var projectname = $(event.relatedTarget).data('name');
                if(projectname !== undefined || projectname !== null && projectid !== undefined || projectid !== null) {
                    titleHtml = '<i class="fa fa-building fa-fw" aria-hidden="true"></i> '+dataId+': '+projectname;
                    bodyHtml = "";
                    footerHtml = '<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o fa-fw" aria-hidden="true"></i> Close</button>';
                    $.ajax({
                        type: 'GET',
                        url: '/perprojects/details/'+projectid,
                        dataType : 'html',
                        success: function(response){
                            modalId.find(".modal-body").html(response);
                        },
                        error : function(XMLHttpRequest, textStatus, errorThrown) {
                            bodyHtml = "<p>Invalid AJAX Request.</p>";
                            modalId.find(".modal-body").html(bodyHtml);
                        }
                    });
                }
                modalId.find(".modal-title").html(titleHtml);
                modalId.find(".modal-footer").html(footerHtml);
                break;
            default:
                modalId.addClass("hmodal-default");
                titleHtml = '<i class="fa fa-exclamation-circle fa-fw" aria-hidden="true"></i> Error';
                bodyHtml = "<p>No item is selected. Please select atleast one.</p>";
                footerHtml = '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
                modalId.find(".modal-title").html(titleHtml);
                modalId.find(".modal-body").html(bodyHtml);
                modalId.find(".modal-footer").html(footerHtml);
                break;            }
        $(this).find($(".modal-footer button")).focus();   
    });// ./Modal Display Action

});

</script>