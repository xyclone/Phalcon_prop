<!-- include CSS -->
{{partial("districts/partials/css")}}  

<section class="content-header animated fadeIn">
    <h1>Districts</h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-wrench fa-fw"></i> Management</li>
        <li class="active">districts</li>
    </ol>
</section>

<section class="content animated fadeIn">
    <div class="row">

        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-plus-square-o" aria-hidden="true"></i> Add New District</h3>
                </div>
                <form name="districts" action="{{ url(link_action) }}" role="form" method="POST" data-remote="data-remote">
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

        </div>

        <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-list-ul" aria-hidden="true"></i> List Districts</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th align="center"><b>Name</b></th>
                                    <th align="center"><b>Description</b></th>
                                    <th align="center"><b>Action</b></th>
                                </tr>
                            </thead>
                            <tbody id="listView">
                                {% if districts is not empty %}
                                    {% for x in districts %}
                                        <tr id="del{{ x.name }}">
                                            <td>{{ x.name }}</td>   
                                            <td>{{ x.description }}</td>                                         
                                            <td align="center">
                                                <button class="btn btn-sm btn-primary" id="buttonCrudGroupMenu"><i class="fa fa-edit fa-fw cursor iconCrud" data-toggle="modal" data-target="#EditModal" onclick="updated('{{ x.name }}')"></i></button>
                                                <button class="btn btn-sm btn-danger m-l-sm"  id="buttonCrudGroupMenu"><i class="fa fa-trash fa-fw cursor iconCrud" data-toggle="modal" data-target="#Delete" onclick="deleted('{{ x.name }}', '{{ x.name }}')"></i></button>
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
{{partial("districts/partials/form")}}  
{{partial("districts/partials/deleted")}}  
<!-- include Js -->
{{partial("districts/partials/js")}}  