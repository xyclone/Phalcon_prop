<script>
    function handleAjaxError( xhr, textStatus, error ) {
        if ( textStatus === 'timeout' ) {
            var errormessage = 'The server took too long to send the data.' +'\n<br>';
            $('#modal-ajax-handler').find('.modal-body div#displayResponse').html(errormessage);
            $('#modal-ajax-handler').modal('show');
        } else {
            var errormessage = 'Invalid data table format.' +'\n<br>';
            $('#modal-ajax-handler').find('.modal-title').html("<i class='fa fa-exclamation-circle fa-fw' id='searchinfo' aria-hidden='true'> </i> Error");
            $('#modal-ajax-handler').find('.modal-body div#displayResponse').html(errormessage);
            $('#modal-ajax-handler').modal('show');
        }  
    }

    // (function() {
    //     $('form[data-remote]').on('submit', function(e) {
    //         var form = $(this);
    //         var url = form.prop('action');   
    //         $.ajax({
    //             type: 'POST',
    //             url: url,
    //             dataType:'json',
    //             data: new FormData(this),
    //             contentType: false,
    //             cache: false,
    //             processData: false,
    //             success: function(response){
    //                 new PNotify({
    //                   title: response.title,
    //                   text: response.text,
    //                   type: response.type
    //                 });
    //                 if (response.close === 2) {
    //                     $('#table').DataTable().fnReloadAjax();
    //                 } else {
    //                     //clear_form();
    //                     $('#table').DataTable().fnReloadAjax();
    //                 }
    //           }
    //         });
    //         e.preventDefault();
    //     });
    // })();


    // (function() {
    //     $('form[data-remote]').on('submit', function(e) {
    //         var form = $(this);
    //         var url = form.prop('action');  
    //         $.ajax({
    //             type: 'POST',
    //             url: url,
    //             dataType:'json',
    //             data: new FormData(this),
    //             contentType: false,
    //             cache: false,
    //             processData: false,
    //             success: function(response){
    //                 new PNotify({
    //                   title: response.title,
    //                   text: response.text,
    //                   type: response.type
    //                 });
    //                 if (response.close === 1) {
    //                     $('#modal-ajax-handler').modal('hide');
    //                     //list();
    //                     $('#table').DataTable().fnReloadAjax();
    //                 // } else if (response.close === 2) {
    //                 //     $('#Delete').modal('hide');
    //                 //     $('#table').DataTable().row('#del'+response.id).remove().draw( false );
    //                 } else {
    //                     $('#table').DataTable().fnReloadAjax();
    //                 }
    //           }
    //         });
    //         e.preventDefault();
    //     });
    // })();

    // Document Ready
    $(document).ready(function() {
        'use strict';

        $('.select2').select2({theme: "bootstrap", width: "100%"});

        //Replace thead filterRow to textfield search
        $('#dt_table thead tr#filterrow th').each( function () {
            var title = $('#dt_table thead th').eq( $(this).index() ).text();
            switch(title) {
                case "Action":
                    $(this).html('<div class="input-group"><input type="text" class="form-control input-sm searchfield-sm" disabled style="width:100px;" ><span class="input-group-btn">');
                    break;
                case "":
                case "Id":
                    $(this).html('');
                    break;
                case "Creation by":
                    $(this).html('<div class="input-group"><input type="text" class="form-control input-sm searchfield-sm" style="width:150px;" autocomplete="off" disabled><span class="input-group-btn"></span></div>');
                    $('#dt_table thead th').eq($(this).index()).text('Action');
                    break;
                default:
                    $(this).html('<div class="input-group"><input type="text" class="form-control input-sm searchfield-sm" style="width:150px;" placeholder="Search '+title+'" autocomplete="off"><span class="input-group-btn"><button type="button" class="btn btn-sm btn-default clear-text"><i class="fa fa-times-circle fa-fw" aria-hidden="true"></i></button></span></div>');
                    break;
            }
        }); // ./Replace thead filterRow to textfield search
        var oTable = $('#dt_table').DataTable({
            "processing": true,
            "serverSide": true,
            //"bSortCellsTop": true,
            "orderCellsTop": true,
            "scrollCollapse": true,
            "scrollY": "62vh",
            "responsive": false,
            "scrollX": true,
            "scrollCollapse": true,
            "autoWidth": true,
            "ajax": {
                "url": "{{JsonUrl}}",
                "type": "POST",
                "dataType":"json",
                "data": function ( d ) { },
                "error": handleAjaxError,
                "warning": handleAjaxError
            },
            "columns": {{JsonCols}},
            "oLanguage": {
                "sProcessing": "<div id='ajax_loader' style='position: absolute; left: 50%; top: 30%; z-index: 1002'><i class='fa fa-spinner fa-spin fa-5x fa-fw text-success'></i><span class='sr-only'></span></div>",
                buttons: {
                    pageLength: {
                        _: '<i class="fa fa-list fa-fw" aria-hidden="true"></i> %d Rows',
                        '-1': '<i class="fa fa-list fa-fw" aria-hidden="true"></i> Show All'
                    }
                }
            },
            "sPaginationType": "full_numbers",
            "dom": "<'row mbox-mb-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12'B><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging-top'p>>rt<'row mbox-pt-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-info'i><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging'p>>",
            "lengthMenu": [
                [50, 100, 200, 500, 900, 1000, -1],
                ["50 Rows", "100 Rows", "200 Rows", "500 Rows", "900 Rows", "1000 Rows","All Rows"]
            ],
            "buttons": [
                {
                    background:false,
                    extend: 'colvis',
                    //columns: [0,2,":gt(4)"],
                    text: '<i class="fa fa-eye fa-fw" aria-hidden="true"></i> Columns',
                },
                {
                    background:false,
                    extend: 'pageLength',
                },
            ],
            "columnDefs": [
                {   "data": "project_name", 
                    "targets": [{{array_search('project_name',DataCols)}}], 
                    "render": function(data, type, row) {
                        return '<a href="#" data-toggle="modal" title="Project Transactions" data-target="#modal-ajax-handler" data-action="Project Transactions" data-id="'+row['id']+'" data-name="'+row['project_name']+'" ><b>'+row['project_name']+'</b></a>';
                    },
                    //"width": "15%",
                },
                {   "data": "creation_by", 
                    "targets": [{{array_search('creation_by',DataCols)}}], 
                    "render": function(data, type, row) {
                        var response = '<button type="button" class="btn btn-xs btn-info" title="Project Info" data-toggle="modal" data-target="#modal-ajax-handler" data-action="Project Info" data-id="'+row['id']+'" data-name="'+row['project_name']+'"><i class="fa fa-building fa-fw"></i><span class="sr-only"></span></button>';
                        //var response = '<button class="btn btn-sm btn-primary" id="buttonCrudGroupMenu"><i class="fa fa-edit fa-fw cursor iconCrud" data-toggle="modal" data-target="#EditModal" onclick="updated('+row['id']+')"></i></button>';
                        response += '<button type="button" class="btn btn-xs btn-success m-l-xs" title="Project Images" data-toggle="modal" data-target="#modal-ajax-handler" data-action="Project Images" data-id="'+row['id']+'" data-name="'+row['project_name']+'"><i class="fa fa-picture-o fa-fw"></i><span class="sr-only"></span></button>';
                        response += '<button type="button" class="btn btn-xs btn-danger m-l-xs" title="Project Delete" data-toggle="modal" data-target="#modal-ajax-handler" data-action="Project Delete" data-id="'+row['id']+'" data-name="'+row['project_name']+'"><i class="fa fa-trash fa-fw cursor iconCrud" ></i></button>';
                        return response;
                    },
                    "width": "10%",
                    "class": "text-center",
                },
                { "targets": [{{hiddenCols}}], "visible": false, "searchable": true },

            ],  
            "order": [[ 0, "asc" ]],
        });
        oTable.columns.adjust().draw();
        //Search field
        $.each($('.input-filter', oTable.table().header()), function () {
            //var test = oTable.columns($(this).index());
//console.log(test);
            var column = oTable.columns($(this).index()+":visible");
            $( 'input', this).keypress( function (event) {
                if (event.which == 13) {
                    //console.log(this.value);
                    $(this).val(this.value);
                    column.search(this.value).draw();
                }
            });
            $('.clear-text', this).click( function (event) {
                event.preventDefault();
                $(this).parent().parent().find("input[type=text]:first").val("");
                column.search('').draw();
            });
        }); // ./Apply the search 

        //Modal Display Action
        $("#modal-ajax-handler").on('hidden.bs.modal', function () {
            $(this).find(".modal-title").empty();
            $(this).find(".modal-body").empty();
            $(this).find(".modal-footer").empty(); 
            $(this).removeClass("hmodal-default hmodal-primary hmodal-info hmodal-warning hmodal-danger hmodal-success");
        });
        $("#modal-ajax-handler").on("shown.bs.modal", function(event) {
            var modalId = $("#modal-ajax-handler");
            $("#modal-ajax-handler").removeClass("hmodal-default hmodal-primary hmodal-info hmodal-warning hmodal-danger hmodal-success");
            var titleHtml=''; var bodyHtml=''; var footerHtml='';
            var dataId = $(event.relatedTarget).data('action');       
            switch(dataId) {
                case 'Project Info':
                    $('#modal-ajax-handler').addClass("hmodal-info");
                    modalId.find(".modal-header").addClass("bg-primary");
                    modalId.find("#dialog-box").addClass("modal-xl");  
                    var projectid = $(event.relatedTarget).data('id');          
                    var projectname = $(event.relatedTarget).data('name');
                    if(projectname !== undefined || projectname !== null && projectid !== undefined || projectid !== null) {
                        titleHtml = '<i class="fa fa-building fa-fw" aria-hidden="true"></i> '+dataId+': '+projectname;
                        bodyHtml = "";
                        footerHtml = '<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o fa-fw" aria-hidden="true"></i> Close</button><a class="btn btn-info" id="save"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i> Save Project</a>';
                        $.ajax({
                            type: 'GET',
                            url: '/projects/details/'+projectid,
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
                    $(this).find('.modal-footer a').click(function() {
                        var form = modalId.find("#postprojects");
                        var url = $(form).prop('action'); 
                        $(form).attr("data-remote", "data-remote");
                        $(form).attr("method", "POST");                            
                        var mdata = $(form).serialize();                
                        $.ajax({
                            type: 'POST',
                            url: url,
                            dataType:'json',
                            data: mdata,
                            cache: false,
                            processData: true,
                            success: function(response){
                                new PNotify({
                                  title: response.title,
                                  text: response.text,
                                  type: response.type
                                });
                                if (response.close === 1) {
                                    $('#modal-ajax-handler').modal('hide');
                                    $("#dt_table").DataTable().ajax.reload();
                                } else {
                                    $('#modal-ajax-handler').modal('hide');
                                    $("#dt_table").DataTable().ajax.reload();
                                }
                          }
                        });

                    }); 
                    break;
                case 'Project Images':
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
                            url: '/projects/imagesHtml/'+projectid,
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
                case 'Project Delete':
                    $('#modal-ajax-handler').addClass("hmodal-info");
                    modalId.find(".modal-header").addClass("bg-primary");
                    modalId.find("#dialog-box").addClass("modal-md");  
                    var projectid = $(event.relatedTarget).data('id');          
                    var projectname = $(event.relatedTarget).data('name');
                    if(projectname !== undefined || projectname !== null && projectid !== undefined || projectid !== null) {
                        titleHtml = '<i class="fa fa-trash-o fa-fw" aria-hidden="true"></i> '+dataId;
                        bodyHtml = "";
                        bodyHtml = "<p>Do you want to delete <strong>"+projectname+"</strong>?</p>";
                        footerHtml = '<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o fa-fw" aria-hidden="true"></i> Close</button><a class="btn btn-danger" id="disable"><i class="fa fa-trash-o fa-fw" aria-hidden="true"></i> Confirm</a>';
                        var form = $(document.createElement('form'));
                        $(form).attr("action", "/projects/deleted");
                        $(form).attr("data-remote", "data-remote");
                        $(form).attr("method", "POST");  
                        form.appendTo(document.body);
                    }
                    modalId.find(".modal-title").html(titleHtml);
                    modalId.find(".modal-body").html(bodyHtml);
                    modalId.find(".modal-footer").html(footerHtml);
                    $(this).find('.modal-footer a').click(function(e) {
                        var url = $(form).prop('action');   
                        var mdata = 'projectid='+projectid;                   
                        $.ajax({
                            type: 'POST',
                            url: url,
                            dataType:'json',
                            data: mdata,
                            cache: false,
                            processData: true,
                            success: function(response){
                                new PNotify({
                                  title: response.title,
                                  text: response.text,
                                  type: response.type
                                });
                                if (response.close === 2) {
                                    $('#modal-ajax-handler').modal('hide');
                                    $("#dt_table").DataTable().ajax.reload();
                                } else {
                                    $('#modal-ajax-handler').modal('hide');
                                    $("#dt_table").DataTable().ajax.reload();
                                }
                          }
                        });
                    });   
                    break;
                case 'Project Transactions':
                    modalId.addClass("hmodal-warning");
                    modalId.find(".modal-header").addClass("bg-primary");
                    modalId.find("#dialog-box").addClass("modal-xl"); 
                    var projectid = $(event.relatedTarget).data('id');          
                    var projectname = $(event.relatedTarget).data('name');
                    if(projectname !== undefined || projectname !== null && projectid !== undefined || projectid !== null) {
                        titleHtml = '<i class="fa fa-building-o fa-fw" aria-hidden="true"></i> '+dataId+': '+projectname;
                        bodyHtml = "";
                        footerHtml = '<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o fa-fw" aria-hidden="true"></i> Close</button>';
                        $.ajax({
                            type: 'GET',
                            url: '/details/transactions/'+projectid,
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

<!--
                // { "width": "5%", "class": "text-center", "targets": [{ {array_search('id',DataCols)}}] },
                // { "width": "10%", "targets": [{ {array_search('project_type_id',DataCols)}}] },
                // { "width": "10%", "targets": [{ {array_search('proj_property_type_id',DataCols)}}] },
                // { "width": "30%", "targets": [{ {array_search('available_unit_type_id',DataCols)}}] },
                // { "width": "10%", "targets": [{ {array_search('status_id',DataCols)}}] },
                // { "width": "8%", "class": "text-right p-r-md", "targets": [{ {array_search('status_date',DataCols)}}] },
                // { "width": "10%", "orderable": false, "class": "text-center", "targets": [-1] },
                // { "targets": [{ {hiddenCols}}], "visible": false, "searchable": true },

        // $('#table').DataTable({
        //     "processing": true,
        //     "serverSide": false,
        //     "responsive": false,
        //     "autoWidth": false,
        //     "scrollX": false,
        //     "sPaginationType": "full_numbers",
        //     "dom": "<'row mbox-mb-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12'B><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging-top'f>>rt<'row mbox-pt-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-info'i><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging'p>>",
        //     "lengthMenu": [
        //         [50,-1],
        //         ["50 Rows","All Rows"]
        //     ],
        //     "buttons": [
        //         {
        //             extend: 'colvis',
        //             columns: [0,1,2,":gt(3)"],
        //             text: '<i class="fa fa-eye fa-fw" aria-hidden="true"></i> Columns',
        //         },
        //         {
        //             background:true,
        //             extend: 'pageLength',
        //         },
        //     ],
        //     "columnDefs": [
        //         { "width": "5%", "class": "text-center", "targets": [{ {array_search('id',fieldCols)}}] },
        //         { "width": "10%", "targets": [{ {array_search('project_type_id',fieldCols)}}] },
        //         { "width": "10%", "targets": [{ {array_search('proj_property_type_id',fieldCols)}}] },
        //         { "width": "15%", "targets": [{ {array_search('project_name',fieldCols)}}] },
        //         { "width": "30%", "targets": [{ {array_search('available_unit_type_id',fieldCols)}}] },
        //         { "width": "10%", "targets": [{ {array_search('status_id',fieldCols)}}] },
        //         { "width": "15%", "targets": [{ {array_search('status_date',fieldCols)}}] },
        //         { "width": "10%", "orderable": false, "class": "text-center", "targets": [-1] },
        //         { "targets": [{{hiddenCols}}], "visible": false, "searchable": true },
        //     ],        
        //     "order": [[ 0, "asc" ]],
        // });
-->