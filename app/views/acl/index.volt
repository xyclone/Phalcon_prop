<!-- include CSS -->
{{partial("acl/partials/css")}}  

<section class="content-header animated fadeIn">
    <h1>Acl</h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-cogs"></i> Access Control</li>
        <li class="active">ACL</li>
    </ol>
</section>

<section class="content animated fadeIn">
    <div class="row">
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-object-group" aria-hidden="true"></i> Input Group Menu</h3>
                </div>
                <form name="group" action="{{ url('acl/menugroup/input') }}" method="POST" data-remote>
                <div class="box-body">
                    <div class="form-group">
                        <label>Name Menu Group</label>
                        <input type="text" name="menu_group" class="form-control" placeholder="Menu Group">
                    </div>
                    <div class="form-group usergroup">
                        <label>Usergroup</label><br>
                        {% for ug in usergroup %}
                            <td align="center">
                                <label class="usergroup">
                                <input type="checkbox" name="usergroup[]" value="{{ ug.id }}"> {{ ug.usergroup }}
                                </label><br>
                            </td>
                        {% endfor %}
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-flat btn-block">Submit</button>
                    </div>
                </div>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-th-list" aria-hidden="true"></i> List Group Menu</h3>
                </div>
                <div class="box-body">
                    <table id="table1" class="table table-bordered table-hover">
                        <thead> 
                            <tr>
                                <th width="25">No</th>
                                <th>Menu Group</th>
                                <th>Usergroup</th>
                                <th align="center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="listViewGroup">
                            {% set no = 1 %}
                            {% for x in group %}
                            <tr id="groupdel{{ x.id }}">
                                <td>{{ no }}</td>
                                <td>{{ x.menu_group }}</td>
                                <td>{{ x.usergroup }}</td>
                                <td>
                                    <button class="btn btn-danger btn-flat"  id="buttonCrudGroupMenu">
                                        <i class="fa fa-trash fa-fw cursor iconCrud" data-toggle="modal" data-target="#groupDelete" onclick="deletedGroup({{ x.id }}, '{{ x.menu_group }}')"></i>  
                                    </button>
                                   {% if x.active === 'Y' %}
                                        <button class="btn btn-sm btn-success m-l-sm" id="buttonCrudGroupMenu"><i class="fa fa-power-off fa-fw cursor text-default iconCrud" id="grouptext{{ x.id }}" onclick="statusGroup({{ x.id }}, 'N')"></i></button>
                                    {% else %}
                                        <button class="btn btn-sm btn-danger m-l-sm"  id="buttonCrudGroupMenu"><i class="fa fa-power-off fa-fw cursor text-default iconCrud"  id="grouptext{{ x.id }}" onclick="statusGroup({{ x.id }}, 'Y')"></i></button>
                                    {% endif %}
                                </td>
                            </tr>
                            {% set no = no + 1 %}
                            {% endfor %}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-list" aria-hidden="true"></i> List Acl</h3>
                    <div class="box-tools pull-right" style="margin-top:2px;">
                        <button type="button" class="btn btn-success btn-sm btn-block m-b-sm" data-toggle="modal" data-target="#CreateModel" onclick="clear_form()">
                            <i class="fa fa-plus-circle"></i> Create New ACL
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="table2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="50" align="center">ID</th>
                                    <th width="25">Icon</th>
                                    <th>Url</th>
                                    {% for ug in usergroup %}
                                    <th><div class="text-center">{{ ug.usergroup }}</div></th>
                                    {% endfor %}
                                    <th width="100">Except</th>
                                    <th width="163">Action</th>
                                </tr>
                            </thead>
                            <tbody id="listView">
                            {% for x in acl %}
                                <tr id="del{{ x.id }}">
                                    <td align="center">{{ x.id }}</td>

                                    <td align="center">
                                    {% if x.icon is not empty %}
                                        <i class="fa {{ x.icon }}"></i></td>
                                    {% endif %}

                                    <td>{{ x.url }}</td>
                                    {% for ug in usergroup %}
                                        <td align="center">
                                            <input type="checkbox" id="check" class="flat-blue" value="{{ x.id }},{{ ug.id }}" {% if ug.id in Helpers.usergroup(x.usergroup) %} checked="true" {% endif %} >
                                        </td>
                                    {% endfor %}
                                    <td style="padding: 0px;">
                                        <div ondblclick="return except(this)" style="padding: 10px;" acl="{{ x.id }}">{{ x.except }}</div>
                                    </td>

                                    <td>
                                        <button class="btn btn-primary" style="padding: 4px 4px 0px 4px">
                                            <i class="fa fa-edit fa-fw cursor" style="font-size:18px;" data-toggle="modal" data-target="#CreateModel" onclick="updated('{{ x.id }}', '{{ x.url }}', '{{ x.controller }}', '{{ x.action }}', '{{ x.except }}')"></i> 
                                        </button>

                                        <button class="btn btn-danger" style="padding: 4px 4px 0px 4px">
                                            <i class="fa fa-trash fa-fw cursor" style="font-size:18px;" data-toggle="modal" data-target="#Delete" onclick="deleted({{ x.id }}, '{{ x.url }}')"></i>
                                        </button> 

                                        <!-- <button class="btn btn-default btn-flat"  id="buttonCrudGroupMenu">
                                            <i class="fa fa-power-off cursor text-success" style="font-size:18px;" id="text{{ x.id }}" onclick="status({{ x.id }}, 'N')"></i>
                                        </button> -->
                                       {% if x.active === 'Y' %}
                                            <button class="btn btn-sm btn-success m-l-sm" id="buttonCrudGroupMenu"><i class="fa fa-power-off fa-fw cursor text-default iconCrud" id="text{{ x.id }}" onclick="status({{ x.id }}, 'N')"></i></button>
                                        {% else %}
                                            <button class="btn btn-sm btn-danger m-l-sm"  id="buttonCrudGroupMenu"><i class="fa fa-power-off fa-fw cursor text-default iconCrud"  id="text{{ x.id }}" onclick="status({{ x.id }}, 'Y')"></i></button>
                                        {% endif %}

                                    </td>
                                </tr>
                            {% endfor %}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- include Js -->
{{partial("acl/partials/js")}}
{{partial("menu/deleted")}}  
{{partial("acl/partials/form")}}  
{{partial("acl/partials/deleted")}}  