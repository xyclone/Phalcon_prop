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
                    <h3 class="box-title"><i class="fa fa-list-ul" aria-hidden="true"></i> Per Project</h3>
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
<!-- ./MODAL AJAX HANDLER -->

<!-- include Js -->
{{partial("perprojects/partials/js")}}  