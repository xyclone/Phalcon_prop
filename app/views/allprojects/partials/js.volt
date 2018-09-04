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

        $("div.DTFC_LeftBodyLiner table thead tr th").removeClass("sorting");

        $('.select2').select2({theme: "bootstrap", width: "100%", 'allowClear': false});

        //Replace thead filterRow to textfield search
        $('#dt_table thead tr#filterrow th').each( function () {
            var title = $('#dt_table thead th').eq( $(this).index() ).text();
            switch(title) {
                case "Action":
                    $(this).html('<div class="input-group"><input type="text" class="form-control input-sm searchfield-sm" disabled style="width:100px;" ><span class="input-group-btn">');
                    break;
                case "Available Date":
                    $(this).html('<div class="input-group"><input name="available_date" id="available_date" type="text" placeholder="'+title+' Range" class="input-sm searchfield-sm" /><span class="input-group-btn"><button type="button" class="btn btn-sm btn-default clear-text"><i class="fa fa-times-circle fa-fw" aria-hidden="true"></i></button></span></div>');
                    break;
                case "Date when Available Unit Type is updated":
                    $(this).html('<div class="input-group"><input name="available_unit_date" id="available_unit_date" type="text" placeholder="'+title+' Range" class="input-sm searchfield-sm" /><span class="input-group-btn"><button type="button" class="btn btn-sm btn-default clear-text"><i class="fa fa-times-circle fa-fw" aria-hidden="true"></i></button></span></div>');
                    break;
                case "Date":
                    $(this).html('<div class="input-group"><input name="status_date" id="status_date" type="text" placeholder="'+title+' Range" class="input-sm searchfield-sm" /><span class="input-group-btn"><button type="button" class="btn btn-sm btn-default clear-text"><i class="fa fa-times-circle fa-fw" aria-hidden="true"></i></button></span></div>');
                    break;
                case "Date2":
                    $(this).html('<div class="input-group"><input name="status2_date" id="status2_date" type="text" placeholder="'+title+' Range" class="input-sm searchfield-sm" /><span class="input-group-btn"><button type="button" class="btn btn-sm btn-default clear-text"><i class="fa fa-times-circle fa-fw" aria-hidden="true"></i></button></span></div>');
                    break;
                case "Enbloc or GLS Sold Date":
                    $(this).html('<div class="input-group"><input name="gls_sold_date" id="gls_sold_date" type="text" placeholder="'+title+' Range" class="input-sm searchfield-sm" /><span class="input-group-btn"><button type="button" class="btn btn-sm btn-default clear-text"><i class="fa fa-times-circle fa-fw" aria-hidden="true"></i></button></span></div>');
                    break;
                case "STB Appln Date":
                    $(this).html('<div class="input-group"><input name="stb_application_date" id="stb_application_date" type="text" placeholder="'+title+' Range" class="input-sm searchfield-sm" /><span class="input-group-btn"><button type="button" class="btn btn-sm btn-default clear-text"><i class="fa fa-times-circle fa-fw" aria-hidden="true"></i></button></span></div>');
                    break;
                case "STB Approval Date":
                    $(this).html('<div class="input-group"><input name="stb_approval_date" id="stb_approval_date" type="text" placeholder="'+title+' Range" class="input-sm searchfield-sm" /><span class="input-group-btn"><button type="button" class="btn btn-sm btn-default clear-text"><i class="fa fa-times-circle fa-fw" aria-hidden="true"></i></button></span></div>');
                    break; 
                case "Completion Date":
                    $(this).html('<div class="input-group"><input name="completion_date" id="completion_date" type="text" placeholder="'+title+' Range" class="input-sm searchfield-sm" /><span class="input-group-btn"><button type="button" class="btn btn-sm btn-default clear-text"><i class="fa fa-times-circle fa-fw" aria-hidden="true"></i></button></span></div>');
                    break; 
                case "Vacant Possession Date":
                    $(this).html('<div class="input-group"><input name="vacant_possession_date" id="vacant_possession_date" type="text" placeholder="'+title+' Range" class="input-sm searchfield-sm" /><span class="input-group-btn"><button type="button" class="btn btn-sm btn-default clear-text"><i class="fa fa-times-circle fa-fw" aria-hidden="true"></i></button></span></div>');
                    break;  
                case "Date Approved":
                    $(this).html('<div class="input-group"><input name="approved_date" id="approved_date" type="text" placeholder="'+title+' Range" class="input-sm searchfield-sm" /><span class="input-group-btn"><button type="button" class="btn btn-sm btn-default clear-text"><i class="fa fa-times-circle fa-fw" aria-hidden="true"></i></button></span></div>');
                    break; 
                case "Date Issued":
                    $(this).html('<div class="input-group"><input name="issue_date" id="issue_date" type="text" placeholder="'+title+' Range" class="input-sm searchfield-sm" /><span class="input-group-btn"><button type="button" class="btn btn-sm btn-default clear-text"><i class="fa fa-times-circle fa-fw" aria-hidden="true"></i></button></span></div>');
                    break; 
                case "DS Date":
                    $(this).html('<div class="input-group"><input name="ds_date" id="ds_date" type="text" placeholder="'+title+' Range" class="input-sm searchfield-sm" /><span class="input-group-btn"><button type="button" class="btn btn-sm btn-default clear-text"><i class="fa fa-times-circle fa-fw" aria-hidden="true"></i></button></span></div>');
                    break;
                case "Date Spreadsheet Updated":
                    $(this).html('<div class="input-group"><input name="date_updated" id="date_updated" type="text" placeholder="'+title+' Range" class="input-sm searchfield-sm" /><span class="input-group-btn"><button type="button" class="btn btn-sm btn-default clear-text"><i class="fa fa-times-circle fa-fw" aria-hidden="true"></i></button></span></div>');
                    break;
                case "":
                case "ID":
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
            //"fixedColumns": true,
            "ajax": {
                "url": "{{JsonUrl}}",
                "type": "POST",
                "dataType":"json",
                "data": function ( d ) { 
                    d.available_date = $('#available_date').val();
                    d.available_unit_date = $('#available_unit_date').val();
                    d.status_date = $('#status_date').val();
                    d.status2_date = $('#status2_date').val();
                    d.gls_sold_date = $('#gls_sold_date').val();
                    d.stb_application_date = $('#stb_application_date').val();
                    d.stb_approval_date = $('#stb_approval_date').val();
                    d.completion_date = $('#completion_date').val();
                    d.vacant_possession_date = $('#vacant_possession_date').val();
                    d.approved_date = $('#approved_date').val();
                    d.issue_date = $('#issue_date').val();
                    d.ds_date = $('#ds_date').val();
                    d.date_updated = $('#date_updated').val();
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

        //Search on Fixed Column
        $(document).on('keypress', '.DTFC_LeftHeadWrapper table thead tr th div :input', function(event) {
            if (event.which == 13) {
                var column = oTable.columns(1);               
                $(this).val(this.value);                
                column.search(this.value).draw();
            }
        });
        $(document).on('click', '.DTFC_LeftHeadWrapper table thead tr th div button', function(event) {
            event.preventDefault();
            var column = oTable.columns(1);    
            $(this).parent().parent().find("input[type=text]:first").val("");
            column.search('').draw();                       
        });// ./Search on Fixed Column

        //Create DateRangePicker
        $("#available_date, #available_unit_date, #status_date, #status2_date, #gls_sold_date, #stb_application_date, #stb_approval_date, #completion_date, #vacant_possession_date, #approved_date, #issue_date, #ds_date, #date_updated").keypress(function(event) {event.preventDefault();});
        $('#available_date, #available_unit_date, #status_date, #status2_date, #gls_sold_date, #stb_application_date, #stb_approval_date, #completion_date, #vacant_possession_date, #approved_date, #issue_date, #ds_date, #date_updated').daterangepicker({
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
        $('input[name="available_date"]').on('apply.daterangepicker', function(ev, picker) {
            var selected = new Date(picker.startDate.format('YYYY-MM-DD')) + ' - ' + new Date(picker.endDate.format('YYYY-MM-DD'));
            oTable.columns(12).search(selected).draw();   
        });
        $('input[name="available_unit_date"]').on('apply.daterangepicker', function(ev, picker) {
            var selected = new Date(picker.startDate.format('YYYY-MM-DD')) + ' - ' + new Date(picker.endDate.format('YYYY-MM-DD'));
            oTable.columns(20).search(selected).draw();   
        });
        $('input[name="status_date"]').on('apply.daterangepicker', function(ev, picker) {
            var selected = new Date(picker.startDate.format('YYYY-MM-DD')) + ' - ' + new Date(picker.endDate.format('YYYY-MM-DD'));
            oTable.columns(30).search(selected).draw();   
        });
        $('input[name="status2_date"]').on('apply.daterangepicker', function(ev, picker) {
            var selected = new Date(picker.startDate.format('YYYY-MM-DD')) + ' - ' + new Date(picker.endDate.format('YYYY-MM-DD'));
            oTable.columns(32).search(selected).draw();   
        });
        $('input[name="gls_sold_date"]').on('apply.daterangepicker', function(ev, picker) {
            var selected = new Date(picker.startDate.format('YYYY-MM-DD')) + ' - ' + new Date(picker.endDate.format('YYYY-MM-DD'));
            oTable.columns(36).search(selected).draw();   
        });
        $('input[name="stb_application_date"]').on('apply.daterangepicker', function(ev, picker) {
            var selected = new Date(picker.startDate.format('YYYY-MM-DD')) + ' - ' + new Date(picker.endDate.format('YYYY-MM-DD'));
            oTable.columns(40).search(selected).draw();   
        });
        $('input[name="stb_approval_date"]').on('apply.daterangepicker', function(ev, picker) {
            var selected = new Date(picker.startDate.format('YYYY-MM-DD')) + ' - ' + new Date(picker.endDate.format('YYYY-MM-DD'));
            oTable.columns(41).search(selected).draw();   
        });
        $('input[name="completion_date"]').on('apply.daterangepicker', function(ev, picker) {
            var selected = new Date(picker.startDate.format('YYYY-MM-DD')) + ' - ' + new Date(picker.endDate.format('YYYY-MM-DD'));
            oTable.columns(42).search(selected).draw();   
        });
        $('input[name="vacant_possession_date"]').on('apply.daterangepicker', function(ev, picker) {
            var selected = new Date(picker.startDate.format('YYYY-MM-DD')) + ' - ' + new Date(picker.endDate.format('YYYY-MM-DD'));
            oTable.columns(43).search(selected).draw();   
        });
        // $('input[name="vacant_date"]').on('apply.daterangepicker', function(ev, picker) {
        //     var selected = new Date(picker.startDate.format('YYYY-MM-DD')) + ' - ' + new Date(picker.endDate.format('YYYY-MM-DD'));
        //     oTable.columns(56).search(selected).draw();   
        // });
        $('input[name="approved_date"]').on('apply.daterangepicker', function(ev, picker) {
            var selected = new Date(picker.startDate.format('YYYY-MM-DD')) + ' - ' + new Date(picker.endDate.format('YYYY-MM-DD'));
            oTable.columns(81).search(selected).draw();   
        });
        $('input[name="issue_date"]').on('apply.daterangepicker', function(ev, picker) {
            var selected = new Date(picker.startDate.format('YYYY-MM-DD')) + ' - ' + new Date(picker.endDate.format('YYYY-MM-DD'));
            oTable.columns(84).search(selected).draw();   
        });
        $('input[name="ds_date"]').on('apply.daterangepicker', function(ev, picker) {
            var selected = new Date(picker.startDate.format('YYYY-MM-DD')) + ' - ' + new Date(picker.endDate.format('YYYY-MM-DD'));
            oTable.columns(88).search(selected).draw();   
        });
        $('input[name="date_updated"]').on('apply.daterangepicker', function(ev, picker) {
            var selected = new Date(picker.startDate.format('YYYY-MM-DD')) + ' - ' + new Date(picker.endDate.format('YYYY-MM-DD'));
            oTable.columns(91).search(selected).draw();   
        });
        // ./Create DateRangePicker

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
                        titleHtml = '<i class="fa fa-building fa-fw" aria-hidden="true"></i> '+dataId+': '+projectname+'<button type="button" class="btn btn-xs pull-right m-r-xs" id="btnPrint" aria-hidden="true"><i class="fa fa-print fa-fw text-info" aria-hidden="true"></i></button>';
                        bodyHtml = "";
                        footerHtml = '<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o fa-fw" aria-hidden="true"></i> Close</button><button type="button" class="btn btn-warning" id="btnPrint" aria-hidden="true"><i class="fa fa-print fa-fw" aria-hidden="true"></i> Print</button><a class="btn btn-info" id="save"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i> Save Project</a>';
                        $.ajax({
                            type: 'GET',
                            url: '/allprojects/details/'+projectid,
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
                            url: '/allprojects/imagesHtml/'+projectid,
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
                        $(form).attr("action", "/allprojects/deleted");
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