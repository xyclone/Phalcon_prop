<!-- include CSS 
{ {partial("index/partials/css")}}  -->

<section class="content-header animated fadeIn">
    <h1>Projects</h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-cog fa-fw"></i> Management</li>
        <li class="active">projects</li>
    </ol>
</section>

<section class="content animated fadeIn">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-list-ul" aria-hidden="true"></i> Projects</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    {% if visCols is not empty %}
                                        {% for NameKey,NameCol in visCols %}
                                            <th>{{NameCol}}</th>
                                        {% endfor %}
                                            <th class="text-center">Action</th>
                                    {% endif %}
                                </tr>
                            </thead>
                            <tbody id="listView">
                                {% if projects is not empty %}
                                    {% for x in projects %}
                                        <tr id="del{{ x['id'] }}">
                                            {% if visCols is not empty %}
                                                {% for visKey,visCol in visCols %}
                                                    <td>{{ x[visKey] }}</td> 
                                                {% endfor %}
                                            {% endif %}
                                            <td><a href="#" class="btn btn-sm btn-info ajax-modal" data-toggle="modal" title="Project Details" data-target="#modal-ajax-handler" data-action="Project Details" data-id="{{ x['id'] }}" data-name="{{ x['project_name'] }}" ><i class="fa fa-building-o fa-fw"></i></a> <a href="#" class="btn btn-sm btn-warning ajax-modal" data-toggle="modal" title="Project Transactions" data-target="#modal-ajax-handler" data-action="Project Transactions" data-id="{{ x['id'] }}" data-name="{{ x['project_name'] }}" ><i class="fa fa-briefcase fa-fw"></i></a></td>
                                        </tr>
                                    {% endfor %}
                                {% endif %}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<p>&nbsp;</p>
<p>&nbsp;</p>

<!-- MODAL AJAX HANDLER -->
{{ajax_modal}}
<!-- ./MODAL AJAX HANDLER -->

<!-- include Js -->
<script>
$(document).ready(function(){
    'use strict';

    $('#table').DataTable({
        "processing": true,
        "serverSide": false,
        "responsive": false,
        "autoWidth": false,
        "scrollX": false,
        "sPaginationType": "full_numbers",
        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-12 col-xs-12'B><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging-top'p>>rt<'row mbox-pt-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12 'i><'col-lg-6 col-md-6 col-sm-12 col-xs-12'p>>",
        "lengthMenu": [
            [50, 100, 200, 500, 900, 1000, -1],
            ["50 Rows", "100 Rows", "200 Rows", "500 Rows", "900 Rows", "1000 Rows","All Rows"]
        ],
        "buttons": [
            {
                background:true,
                extend: 'pageLength',
            },
        ],
        "columnDefs": [
            { "width": "8%", "class": "text-center", "targets": [0] },
            { "width": "37%", "targets": [1] },
            { "width": "15%", "targets": [2,3,4] },
            { "class": "text-center", "targets": [-1] },
            { "orderable": false, "width": "10%", "targets": [-1] },
        ],        
        "order": [[ 0, "asc" ]],
    });

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
            case 'Project Details':
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
                break;
            case 'Project Transactions':
                modalId.addClass("hmodal-warning");
                modalId.find(".modal-header").addClass("bg-orange");
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