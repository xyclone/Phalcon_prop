<!-- include CSS -->
{{partial("allprojects/partials/css")}}

<section class="content-header animated fadeIn">
    <h1>All Projects</h1>
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
                    <div class="table-responsive"><!-- stripe row-border order-column table table-striped table-condensed table-hover-->
                        <table id="dt_table" class="table table-striped table-condensed table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    {% if NameCols is not empty %}
                                        {% for NameCol in NameCols %}
                                            <th style="width:300px!important;">{{NameCol}}</th>
                                        {% endfor %}
                                    {% endif %}
                                </tr>
                                <tr id="filterrow">
                                    {% if NameCols is not empty %}
                                        {% for NameCol in NameCols %}
                                            <th class="input-filter" style="width:300px!important;">{{NameCol}}</th>
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
<!-- ./MODAL AJAX HANDLER -->

<div class="modal fade" id="modal-image-handler" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="vertical-alignment-helper">
        <div id="dialog-box" class="modal-dialog vertical-align-center">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn btn-xs pull-right" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times fa-fw text-info" aria-hidden="true"></i></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer bg-default">
                    <button type="button" id="btnClose" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o fa-fw" aria-hidden="true"></i> Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- include Js -->
{{partial("allprojects/partials/js")}}  