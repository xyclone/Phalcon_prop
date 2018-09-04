<!-- include CSS -->
{{partial("usergroup/partials/css")}}  

<section class="content-header animated fadeIn">
    <h1>Usergroup</h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-cogs"></i> Access Control</li>
        <li class="active">usergroup</li>
    </ol>
</section>

<section class="content animated fadeIn">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Add New Usergroup</h3>
                </div>
                <form name="group" action="{{ url('usergroup/input') }}" method="POST" data-remote="data-remote">
                <div class="box-body">
                    
                    {% if form is not empty %}
                        {% for field in form %}
                            <div class="form-group {{ field.getUserOption('group-req') }} {{ field.getUserOption('ishidden') }}">
                                <label for="{{ field.getName() }}" class="control-label {{ field.getUserOption('label-width') }}">{{ field.getLabel() }}</label>
                                <div class="{{ field.getUserOption('input-width') }}">
                                    {{ field }}
                                </div>
                            </div>
                        {% endfor %}   
                    {% endif %}      
                    
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
        </div>

        <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">List Usergroup</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th align="center" width="20%">
                                        <b>Usergroup</b>
                                    </th>
                                    <th align="center">
                                        <b>Description</b>
                                    </th>
                                    <th align="center" width="150">
                                        <b>Action</b>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="listView">
                                {% if usergroup is not empty %}
                                    {% for x in usergroup %}
                                        <tr id="del{{ x.id }}">
                                            <td>{{ x.usergroup }}</td>
                                            <td>{{ x.description }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary"  id="buttonCrudGroupMenu"><i class="fa fa-edit fa-fw cursor iconCrud" data-toggle="modal" data-target="#EditModal" onclick="updated('{{ x.id }}', '{{ x.usergroup }}', '{{ x.description }}', '{{ x.icon }}')"></i></button>
                                                <button class="btn btn-sm btn-danger m-l-sm"  id="buttonCrudGroupMenu"><i class="fa fa-trash fa-fw cursor iconCrud" data-toggle="modal" data-target="#Delete" onclick="deleted({{ x.id }}, '{{ x.usergroup }}')"></i></button>

                                                {% if x.active is 'Y' %}
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

<!-- include popup -->
{{partial("usergroup/partials/form")}}  
{{partial("usergroup/partials/deleted")}}  
<!-- include Js -->
{{partial("usergroup/partials/js")}}  
