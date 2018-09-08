<script>
    //Handle Ajax Modal Error
    function handleAjaxError( xhr, textStatus, error ) {
        if ( textStatus === 'timeout' ) {
            var errormessage = '<p>The server took too long to send the data.' +'\n<br></p>';
            $('#modal-ajax-handler').find('.modal-body').html(errormessage);
            $('#modal-ajax-handler').modal('show');
        } else {
            var errormessage = '<p>Invalid data table format.' +'\n<br></p>';
            $('#modal-ajax-handler').find('.modal-title').html("<i class='fa fa-exclamation-circle fa-fw' id='searchinfo' aria-hidden='true'> </i> Error");
            $('#modal-ajax-handler').find('.modal-body').html(errormessage);
            $('#modal-ajax-handler').modal('show');
        }
        var cleartable = $('#data_table').DataTable();
        $("#data_table_processing").css("visibility","hidden");
    }// ./Handle Ajax Modal Error

    $(document).on('click', '#btnPrint', function(e) {
        printElement(document.getElementById("printThis"));
    });

    function printElement(elem) {
        var domClone = elem.cloneNode(true);
        var $printSection = document.getElementById("printSection");
        if (!$printSection) {
            var $printSection = document.createElement("div");
            $printSection.id = "printSection";
            document.body.appendChild($printSection);
        }
        $printSection.innerHTML = "";
        $printSection.appendChild(domClone);
        window.print();
    }

    // Document Ready
    $(document).ready(function() {
        'use strict';

        //Auto Close Alert 
        $(".alert-success, .alert-info, .alert-warning, .alert-danger").fadeTo(2000, 500).slideUp(500, function(){
            $(".alert-success, .alert-info, .alert-warning, .alert-danger").slideUp(500);
        }); // ./Auto Close Alert 

        $.fn.dataTable.ext.buttons.reload = {
            text: 'Reload',
            action: function ( e, dt, node, config ) {
                location.reload();
            }
        };

        //Replace thead filterRow to textfield search
        $('#dt_table thead tr#filterrow th').each( function () {
            var title = $('#dt_table thead th').eq( $(this).index() ).text();
            switch(title) {
                case "":
                case "Action":
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
            // "processing": true,
            // "serverSide": true,
            // "orderCellsTop": true,
            // "scrollCollapse": true,
            // "scrollY": "62vh",
            // "responsive": false,
            // "autoWidth": true,
            // "scrollX": true,
            // "scrollCollapse": true,
            // "autoWidth": false,
            // "lengthChange": true,
            // "fixedColumns": {
            //     "leftColumns": 1
            // },
            "processing": true,
            "serverSide": true,
            "orderCellsTop": true,
            "scrollCollapse": true,
            "scrollY": "62vh",
            "responsive": false,
            "scrollX": true,
            "scrollCollapse": true,
            "autoWidth": false,
            "lengthChange": true,
            "fixedColumns": {
                "leftColumns": 2
            },
            "ajax": {
                "url": "{{JsonUrl}}",
                "type": "POST",
                "dataType":"json",
                "data": function ( d ) {
                    d.sale_date = $('#sale_date').val();
                },
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
                    text: '<i class="fa fa-eye fa-fw" aria-hidden="true"></i> Columns',
                },
                {
                    background:false,
                    extend: 'pageLength',
                },
            ],
            "columnDefs": [
                {   "data": "area_sqft", 
                    "targets": [{{array_search('area_sqft',DataCols)}}], 
                    "class": "text-center",
                },
                {   "data": "creation_by", 
                    "targets": [{{array_search('creation_by',DataCols)}}], 
                    "render": function(data, type, row) {
                        var response = '<button type="button" class="btn btn-xs btn-info" title="Per Project Info" data-toggle="modal" data-target="#modal-ajax-handler" data-action="Per Project Info" data-id="'+row['id']+'" data-name="'+row['project_name']+'"><i class="fa fa-building fa-fw cursor iconCrud"></i><span class="sr-only"></span></button> ';
                        response += '<button type="button" class="btn btn-xs btn-danger m-l-xs" title="Delete" data-toggle="modal" data-target="#modal-ajax-handler" data-action="Delete" data-id="'+row['id']+'" data-name="'+row['project_name']+'"><i class="fa fa-trash fa-fw cursor iconCrud" ></i></button> ';
                        return response;
                    },
                    "width": "10%",
                    "class": "text-center",
                },
                { "targets": [{{hiddenCols}}], "visible": false, "searchable": true }
            ],
            "order": [[ 1, "asc" ]],
        });
        oTable.columns.adjust().draw();
        //Search field
        $.each($('.input-filter', oTable.table().header()), function () {
            var column = oTable.columns($(this).index()+":visible");
            $( 'input', this).keypress( function (event) {
                if (event.which == 13) {
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
            var titleHtml=''; var bodyHtml=''; var footerHtml='';
            var dataAction = $(event.relatedTarget).data('action');       
            switch(dataAction) {
                case 'Per Project Info':
                    $('#modal-ajax-handler').addClass("hmodal-info");
                    modalId.find(".modal-header").addClass("bg-primary");
                    modalId.find("#dialog-box").addClass("modal-xl");  
                    var perprojectid = $(event.relatedTarget).data('id');          
                    var projectname = $(event.relatedTarget).data('name');
                    if(projectname !== undefined || projectname !== null && perprojectid !== undefined || perprojectid !== null) {
                        titleHtml = '<i class="fa fa-building fa-fw" aria-hidden="true"></i> '+dataAction+': '+projectname;
                        bodyHtml = "";
                        footerHtml = '<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o fa-fw" aria-hidden="true"></i> Close</button>';
                        $.ajax({
                            type: 'GET',
                            url: '/perprojects/details/'+perprojectid,
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
                case 'Delete':
                    $('#modal-ajax-handler').addClass("hmodal-info");
                    modalId.find(".modal-header").addClass("bg-primary");
                    modalId.find("#dialog-box").addClass("modal-md");  
                    var perprojectid = $(event.relatedTarget).data('id');          
                    var projectname = $(event.relatedTarget).data('name');
                    if(projectname !== undefined || projectname !== null && perprojectid !== undefined || perprojectid !== null) {
                        titleHtml = '<i class="fa fa-trash-o fa-fw" aria-hidden="true"></i> '+dataAction+': '+projectname;
                        bodyHtml = "<p>Do you want to delete <strong>"+projectname+" ("+perprojectid+")</strong>?</p>";
                        footerHtml = '<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o fa-fw" aria-hidden="true"></i> Close</button><a class="btn btn-danger" id="disable"><i class="fa fa-trash-o fa-fw" aria-hidden="true"></i> Confirm</a>';
                        var form = $(document.createElement('form'));
                        $(form).attr("action", "/perprojects/deleted");
                        $(form).attr("data-remote", "data-remote");
                        $(form).attr("method", "POST");  
                        form.appendTo(document.body);
                    }
                    modalId.find(".modal-title").html(titleHtml);
                    modalId.find(".modal-body").html(bodyHtml);
                    modalId.find(".modal-footer").html(footerHtml);
                    $(this).find('.modal-footer a').click(function(e) {
                        var url = $(form).prop('action');   
                        var mdata = 'id='+perprojectid;                   
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