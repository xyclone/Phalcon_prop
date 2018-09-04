<!-- include CSS -->
{{partial("projimages/partials/css")}}  


<section class="content-header animated fadeIn">
    <h1>Upload Images</h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-cog fa-fw"></i> Tools</li>
        <li class="active">Project Images</li>
    </ol>
</section>

<section class="content animated fadeIn">
    <div class="row">

        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-upload fa-fw" aria-hidden="true"></i> Projects Images</h3>
                </div>
                {{ form(link_action, 'name':form_name, 'id':form_name, 'class': 'form-control-static', 'role':'form', 'autocomplete':'off', 'enctype':'multipart/form-data', 'data-remote':'data-remote' ) }}
                    <div class="box-body">
                        {% if form is not empty %}
                            {% for field in form %}
                                <div class="form-group {{ field.getUserOption('group-req') }} {{ field.getUserOption('ishidden') }}">
                                    <label for="{{ field.getName() }}" class="control-label {{ field.getUserOption('label-width') }}">{{ field.getLabel() }}</label>
                                    <div class="{{ field.getUserOption('input-width') }}">
                                        {% if field.getUserOption('funkyCheckbox') %}                                     
                                            <div class="funkyradio">
                                                <div class="funkyradio-success">
                                                    {{ field }}
                                                    <label for="{{ field.getName() }}">{{ field.getLabel() }}</label>
                                                </div>
                                            </div>
                                        {% elseif field.getUserOption('postfix-addon') %}
                                            <div class="input-group">
                                                {{ field }}
                                                <span class="input-group-addon"><b>{{field.getUserOption('postfix-label')}}</b></span>
                                            </div>
                                        {% else %}
                                            {{ field }}
                                        {% endif %}
                                    </div>
                                </div>
                            {% endfor %}   
                        {% endif %}
                        <br>
                    </div>
                    <div class="box-footer">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <div class="col-sm-5">
                                    <button type="button" id="reset" name="reset" class="btn btn-default btn-block"><i class="fa fa-undo fa-fw" aria-hidden="true"></i> Reset</button>
                                </div>
                                <div class="col-sm-5 pull-right">
                                    <input type="hidden" name="{{ tokenKey }}" value="{{ token }}" />
                                    <button type="submit" class="btn btn-info btn-block"><i class="fa fa-upload fa-fw" aria-hidden="true"></i> Upload</button>
                                </div>
                            </span>
                        </div>
                    </div>                
                {{ end_form() }}
            </div>
        </div>

        <!-- Images Source -->
        <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-picture-o fa-fw" aria-hidden="true"></i> Images Source</h3>
                </div>
				<div class="box-body">
					<div class='list-group gallery'>
                        <div id='response'></div>
                    </div>
				</div>
			</div>
        </div><!-- // ./Images Source -->
    </div>
</section>

<!-- MODAL AJAX HANDLER -->
{{ajax_modal}}
<!-- ./MODAL AJAX HANDLER -->

<!-- LOADING MODAL -->
<div class="modal fade" id="processmodal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title"><i class='fa fa-spinner fa-spin fa-fw'></i><span class='sr-only'></span> Processing...</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">Currently updating database, please do not close the window.</div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ./LOADING MODAL -->

<!-- include Js -->
{{partial("projimages/partials/js")}}  