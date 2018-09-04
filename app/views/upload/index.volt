<!-- include CSS -->
{{partial("upload/partials/css")}}  

<section class="content-header animated fadeIn">
    <h1>Upload Projects</h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-cog fa-fw"></i> Tools</li>
        <li class="active">Uploads</li>
    </ol>
</section>

<section class="content animated fadeIn">
    <div class="row">

        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-upload fa-fw" aria-hidden="true"></i> Upload Projects</h3>
                </div>
                {{ form(link_action, 'name':form_name, 'id':form_name, 'class': 'form-control-static', 'role':'form', 'autocomplete':'off', 'enctype':'multipart/form-data', 'data-remote':'data-remote' ) }}
                    <div class="box-body">
                        {% if form is not empty %}
                            {% for field in form %}
                                <div class="form-group {{ field.getUserOption('group-req') }} {{ field.getUserOption('ishidden') }}">
                                    <label for="{{ field.getName() }}" class="control-label {{ field.getUserOption('label-width') }}">{{ field.getLabel() }}</label>
                                    <div class="{{ field.getUserOption('input-width') }}">
                                        {{ field }}
                                        <span class="text-danger font-bold"><small>{{ field.getUserOption('notes') }}</small></span>
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
                </form>
            </div>
        </div>

        <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-list-ul" aria-hidden="true"></i> List File Uploaded</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th align="center"><b>ID</b></th>
                                    <th align="center"><b>Type</b></th>
                                    <th align="center"><b>Filename</b></th>
                                    <th align="center"><b>Remarks</b></th>
                                    <th align="center"><b>Date Upload</b></th>
                                </tr>
                            </thead>
                            <tbody id="listView">
                                {% if uploads is not empty %}
                                    {% for x in uploads %}
                                        <tr id="del{{ x.id }}">
                                            <td align="center">{{ x.id }}</td>
                                            <td>{{ x.type }}</td>   
                                            <td>{{ x.filename }}</td>                                         
                                            <td>{{ x.remarks }}</td>
                                            <td align="center">{{ x.upload_date }}</td>
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

<!-- LOADING MODAL -->
<div class="modal fade" id="uploadmodal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title"><i class='fa fa-spinner fa-spin fa-fw'></i><span class='sr-only'></span> Uploading...</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">Currently updating database, please do not close the window.</div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ./LOADING MODAL -->

<!-- include popup -->
{{partial("upload/partials/form")}}  
{{partial("upload/partials/deleted")}}  
<!-- include Js -->
{{partial("upload/partials/js")}}  