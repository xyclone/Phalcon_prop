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
                case "Sale Date":
                    $(this).html('<div class="input-group"><input name="sale_date" id="sale_date" type="text" placeholder="'+title+' Range" class="input-sm searchfield-sm" /><span class="input-group-btn"><button type="button" class="btn btn-sm btn-default clear-text"><i class="fa fa-times-circle fa-fw" aria-hidden="true"></i></button></span></div>');
                    break;
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
            "processing": true,
            "serverSide": true,
            //"bSortCellsTop": true,
            "orderCellsTop": true,
            "scrollCollapse": true,
            "scrollY": "62vh",
            "responsive": false,
            "autoWidth": true,
            "scrollX": true,
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
                {   "data": "address", 
                    "targets": [{{array_search('address',DataCols)}}], 
                    "render": function(data, type, row) {
                        return '<b>'+row['address']+'</b>';
                    },
                },
                {   "data": "no_units_per_transaction", 
                    "targets": [{{array_search('no_units_per_transaction',DataCols)}}], 
                    "class": "text-center",
                },
                {   "data": "postal_sector", 
                    "targets": [{{array_search('postal_sector',DataCols)}}], 
                    "class": "text-center",
                },
                {   "data": "postal_code", 
                    "targets": [{{array_search('postal_code',DataCols)}}], 
                    "class": "text-center",
                },
                {   "data": "district_id", 
                    "targets": [{{array_search('district_id',DataCols)}}], 
                    "class": "text-center",
                },
                {   "data": "number", 
                    "targets": [{{array_search('number',DataCols)}}], 
                    "class": "text-center",
                },
                {   "data": "level", 
                    "targets": [{{array_search('level',DataCols)}}], 
                    "class": "text-center",
                },
                {   "data": "stack", 
                    "targets": [{{array_search('stack',DataCols)}}], 
                    "class": "text-center",
                },
                {   "data": "creation_by", 
                    "targets": [{{array_search('creation_by',DataCols)}}], 
                    "render": function(data, type, row) {
                        var response = '<button type="button" class="btn btn-xs btn-info" title="Per Unit Info" data-toggle="modal" data-target="#modal-ajax-handler" data-action="Per Unit Info" data-id="'+row['id']+'" data-name="'+row['address']+'"><i class="fa fa-building fa-fw cursor iconCrud"></i><span class="sr-only"></span></button> ';
                        response += '<button type="button" class="btn btn-xs btn-danger m-l-xs" title="Delete" data-toggle="modal" data-target="#modal-ajax-handler" data-action="Delete" data-id="'+row['id']+'" data-name="'+row['address']+'"><i class="fa fa-trash fa-fw cursor iconCrud" ></i></button> ';
                        return response;
                    },
                    "width": "10%",
                    "class": "text-center",
                },
                { "targets": [{{hiddenCols}}], "visible": false, "searchable": true },
            ],
            "order": [[ 0, "asc" ]],
        });

        
        
        //Search field
        $.each($('.input-filter', oTable.table().header()), function () {
            var column = oTable.columns($(this).index()+":visible");
            //console.log(column);
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


        //Create DateRangePicker
        $("#sale_date").keypress(function(event) {event.preventDefault();});
        $('#sale_date').daterangepicker({
            timePicker: false,
            showDropdowns: true,
            autoUpdateInput: false,
            minDate: moment().startOf('year').subtract(50, 'year'),
            maxDate: moment().startOf('year').add(10, 'year'),
            locale: {
                format: 'YYYY-MM-DD'
            },
        }, function(start, end, label) {
            $(this.element[0]).val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'))
        });
        $('input[name="sale_date"]').on('apply.daterangepicker', function(ev, picker) {
            var selected = new Date(picker.startDate.format('YYYY-MM-DD')) + ' - ' + new Date(picker.endDate.format('YYYY-MM-DD'));
            oTable.columns(32).search(selected).draw();   
        }); // ./Create DateRangePicker

        // oTable.columns().every(function (index) {
        //     $('#dt_table thead tr:eq(1) th:eq(' + index + ') input').on('keyup change', function () {
        //         table.column($(this).parent().index() + ':visible')
        //             .search(this.value).draw();
        //     });
        // });



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
                case 'Per Unit Info':
                    $('#modal-ajax-handler').addClass("hmodal-info");
                    modalId.find(".modal-header").addClass("bg-primary");
                    modalId.find("#dialog-box").addClass("modal-xl");  
                    var dataId = $(event.relatedTarget).data('id');          
                    var unitaddress = $(event.relatedTarget).data('name');
                    if(unitaddress !== undefined || unitaddress !== null && dataId !== undefined || dataId !== null) {
                        titleHtml = '<i class="fa fa-building fa-fw" aria-hidden="true"></i> '+dataAction+': '+unitaddress+'<button type="button" class="btn btn-xs pull-right m-r-xs" id="btnPrint" aria-hidden="true"><i class="fa fa-print fa-fw text-info" aria-hidden="true"></i></button>';
                        bodyHtml = "";
                        footerHtml = '<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o fa-fw" aria-hidden="true"></i> Close</button><button type="button" class="btn btn-warning" id="btnPrint" aria-hidden="true"><i class="fa fa-print fa-fw" aria-hidden="true"></i> Print</button><a class="btn btn-info" id="save"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i> Save PerUnit</a>';
                        $.ajax({
                            type: 'GET',
                            url: '/details/details/'+dataId,
                            dataType : 'html',
                            success: function(response){
                                modalId.find(".modal-body").attr('id', 'printThis');
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
                        var form = modalId.find("#postdetails");
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
                case 'Delete':
                    $('#modal-ajax-handler').addClass("hmodal-info");
                    modalId.find(".modal-header").addClass("bg-primary");
                    modalId.find("#dialog-box").addClass("modal-md");  
                    var dataId = $(event.relatedTarget).data('id');          
                    var unitaddress = $(event.relatedTarget).data('name');
                    if((unitaddress !== undefined || unitaddress !== null)&&(dataId !== undefined || dataId !== null)) {
                        titleHtml = '<i class="fa fa-trash-o fa-fw" aria-hidden="true"></i> '+dataAction+': '+unitaddress;
                        bodyHtml = "";
                        bodyHtml = "<p>Do you want to delete <strong>"+unitaddress+"</strong>?</p>";
                        footerHtml = '<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o fa-fw" aria-hidden="true"></i> Close</button><a class="btn btn-danger" id="disable"><i class="fa fa-trash-o fa-fw" aria-hidden="true"></i> Confirm</a>';
                        var form = $(document.createElement('form'));
                        $(form).attr("action", "/details/deleted");
                        $(form).attr("data-remote", "data-remote");
                        $(form).attr("method", "POST");  
                        form.appendTo(document.body);
                    }
                    modalId.find(".modal-title").html(titleHtml);
                    modalId.find(".modal-body").html(bodyHtml);
                    modalId.find(".modal-footer").html(footerHtml);
                    $(this).find('.modal-footer a').click(function(e) {
                        var url = $(form).prop('action');   
                        var mdata = 'id='+dataId;                   
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

<!--

        // $('#table').DataTable({
        //     "processing": true,
        //     "serverSide": false,
        //     "responsive": false,
        //     "autoWidth": false,
        //     "scrollX": false,
        //     "sPaginationType": "full_numbers",
        //     "dom": "<'row mbox-mb-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12'B><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging-top'f>>rt<'row mbox-pt-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-info'i><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging'p>>",
        //     "lengthMenu": [
        //         [50, 100, 250, 500, -1],
        //         ["50 Rows", "100 Rows", "250 Rows", "500 Rows", "All Rows"]
        //     ],
        //     "buttons": [
        //         'reload',
        //         {
        //             background:true,
        //             extend: 'pageLength',
        //         },
        //     ],
        //     "columnDefs": [
        //         { "width": "10%", "class": "text-center", "targets": [0] },
        //         { "width": "25%", "targets": [1] },
        //         { "width": "15%", "targets": [2,3,4] },
        //         { "width": "10%", "targets": [5] },
        //         { "width": "10%", "orderable": false, "class": "text-center", "targets": [-1] },
        //     ],        
        //     "order": [[ 0, "asc" ]],
        // });
-->