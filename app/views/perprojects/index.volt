<!-- include CSS -->
{{partial("perprojects/partials/css")}}  

<section class="content-header animated fadeIn">
    <h1>Per Projects</h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-cog fa-fw"></i> Management</li>
        <li class="active">per projects</li>
    </ol>
</section>

<section class="content animated fadeIn">
    <div class="row">

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-list-ul" aria-hidden="true"></i> Per Projects</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th align="center"><b>ID</b></th>
                                    <th align="center"><b>Project Name</b></th>
                                    <th align="center"><b>Median PSF</b></th>
                                    <th align="center"><b>No. of Transaction</b></th>
                                    <th align="center"><b>Unit Type</b></th>
                                    <th align="center"><b>View Details</b></th>
                                </tr>
                            </thead>
                            <tbody id="listView">
                                {% if perprojects is not empty %}
                                    {% for x in perprojects %}
                                        <tr id="del{{ x.id }}">
                                            <td align="center">{{ x.id }}</td>
                                            <td>{{ x.PerProjects_Project.project_name }}</td>   
                                            <td>{{ x.median_psf }}</td>                                         
                                            <td>{{ x.no_transactions }}</td>
                                            <td>{{ x.PerProjects_PropertyUnits.name }}</td>
                                            <td><a href="#" class="btn btn-xs btn-info ajax-modal" data-toggle="modal" title="Details Info" data-target="#modal-ajax-handler" data-action="Details Info" data-id="{{ x.id }}" data-name="{{ x.PerProjects_Project.project_name }}" ><i class="fa fa-eye fa-fw"></i></a> </td>
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

<!-- MODAL AJAX HANDLER -->
{{ajax_modal}}
<!-- ./MODAL AJAX HANDLER -->

<!-- include Js -->
{{partial("perprojects/partials/js")}}  