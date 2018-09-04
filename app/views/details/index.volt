<!-- include CSS -->
{{partial("details/partials/css")}}  

<section class="content-header animated fadeIn">
    <h1>Project Details</h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-cog fa-fw"></i> Management</li>
        <li class="active">details</li>
    </ol>
</section>

<section class="content animated fadeIn">
    <div class="row">

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-list-ul" aria-hidden="true"></i> Project Details</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="dt_table" class="table table-striped table-condensed table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    {% if NameCols is not empty %}
                                        {% for NameCol in NameCols %}
                                            <th>{{NameCol}}</th>
                                        {% endfor %}
                                    {% endif %}
                                </tr>
                                <tr id="filterrow">
                                    {% if NameCols is not empty %}
                                        {% for NameCol in NameCols %}
                                            <th class="input-filter">{{NameCol}}</th>
                                        {% endfor %}
                                    {% endif %}
                                </tr>
                            </thead>
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

 <div id="usernameModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Change Username</h4>
            </div>
            <div class="modal-body">

                <p>Some text in the modal.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ./MODAL AJAX HANDLER -->

<!-- include Js -->
{{partial("details/partials/js")}}  