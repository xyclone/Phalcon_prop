<!-- include CSS -->
{{partial("users/partials/css")}}  

<section class="content-header animated fadeIn">
    <h1>Users</h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-cogs"></i> Access Control</li>
        <li class="active">users</li>
    </ol>
</section>

<section class="content animated fadeIn">
    <div class="row">

        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-user" aria-hidden="true"></i> Add New User</h3>
                </div>
                <form name="{{form_name}}" id="{{form_name}}" action="{{ url(link_action) }}" role="form" method="POST" data-remote="data-remote">
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
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-check-square-o fa-fw" aria-hidden="true"></i> Submit</button>
                                </div>
                            </span>
                        </div>
                    </div>                
                </form>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload Users</h3>
                </div>
                {{ form(link_upload, 'name':'users_upload', 'id':'users_upload', 'class': 'form-control-static', 'role':'form', 'autocomplete':'off', 'enctype':'multipart/form-data', 'data-remote':'data-remote' ) }}
                    <div class="box-body">
                        {% if formUpload is not empty %}
                            {% for field in formUpload %}
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
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-upload fa-fw" aria-hidden="true"></i> Upload</button>
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
                    <h3 class="box-title"><i class="fa fa-list-ul" aria-hidden="true"></i> List Users</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th align="center"><b>Email</b></th>
                                    <th align="center"><b>Name</b></th>
                                    <th align="center"><b>Mobile</b></th>
                                    <th align="center"><b>Profile</b></th>
                                    <th align="center" width="148"><b>Action</b></th>
                                </tr>
                            </thead>
                            <tbody id="listView">
                                {% if users is not empty %}
                                    {% for x in users %}
                                        <tr id="del{{ x.id }}">
                                            <td>{{ x.email }}</td>
                                            <td>{{ x.name }}</td>   
                                            <td>{{ x.mobile }}</td>   
                                            <td>{{ x.groupname }}</td>                                          
                                            <td>
                                                <button class="btn btn-sm btn-primary"  id="buttonCrudGroupMenu"><i class="fa fa-edit fa-fw cursor iconCrud" data-toggle="modal" data-target="#EditModal" onclick="updated({{ x.id }})"></i></button>
                                                <button class="btn btn-sm btn-danger m-l-sm"  id="buttonCrudGroupMenu"><i class="fa fa-trash fa-fw cursor iconCrud" data-toggle="modal" data-target="#Delete" onclick="deleted({{ x.id }}, '{{ x.username }}')"></i></button>
                                                {% if x.active === 'Y' %}
                                                    <button class="btn btn-sm btn-success m-l-sm" id="buttonCrudGroupMenu"><i class="fa fa-power-off fa-fw cursor text-default iconCrud" id="text{{ x.id }}" onclick="status({{ x.id }}, 'N')"></i></button>
                                                {% else %}
                                                    <button class="btn btn-sm btn-danger m-l-sm"  id="buttonCrudGroupMenu"><i class="fa fa-power-off fa-fw cursor text-default iconCrud"  id="text{{ x.id }}" onclick="status({{ x.id }}, 'Y')"></i></button>
                                                {% endif %}
                                            </td>
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

<!-- include popup -->
{{partial("users/partials/form")}}  
{{partial("users/partials/deleted")}}  
<!-- include Js -->
{{partial("users/partials/js")}}  