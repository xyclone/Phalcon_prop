<!-- include CSS 
{ {partial("index/partials/css")}}  -->
<style>
/*div.dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
    }*/
th, td { white-space: nowrap; }
div.dataTables_wrapper {
    width: 95vw;
    margin: 0 auto;
}
/*div.dataTables_scrollHeadInner table thead tr th  { 
    white-space: pre !important; 
}
div.dataTables_scrollHeadInner table tbody tr td  { 
    white-space: pre !important; 
}
div.DTFC_LeftBodyLiner table thead tr th  { 
    white-space: pre !important; 
}*/
</style>

<section class="content-header animated fadeIn">
    <h1>Result</h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-cog fa-fw"></i> Search</li>
        <li class="active">results</li>
    </ol>
</section>

<section class="content animated fadeIn">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-list-ul" aria-hidden="true"></i> List of Projects</h3>
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
{{partial("index/partials/pjs")}}  