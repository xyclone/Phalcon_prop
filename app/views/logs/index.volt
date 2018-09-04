<!-- include CSS -->
{{partial("logs/partials/css")}}  

<section class="content-header animated fadeIn">
    <h1>Upload Projects</h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-cog fa-fw"></i> Tools</li>
        <li class="active">uploadprokect</li>
    </ol>
</section>

<section class="content animated fadeIn">
    <div class="row">

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-list-ul" aria-hidden="true"></i> Logs</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th align="center"><b>ID</b></th>
                                    <th align="center"><b>Username</b></th>
                                    <th align="center"><b>Access</b></th>
                                    <th align="center"><b>IP</b></th>
                                    <th align="center"><b>Remarks</b></th>
                                    <th align="center"><b>Access Date</b></th>
                                </tr>
                            </thead>
                            <tbody id="listView">
                                {% if logs is not empty %}
                                    {% for x in logs %}
                                        <tr id="del{{ x.id }}">
                                            <td align="center">{{ x.id }}</td>
                                            <td>{{ x.username }}</td>   
                                            <td>{{ x.access }}</td>                                         
                                            <td>{{ x.ip }}</td>
                                            <td>{{ x.remarks }}</td>
                                            <td>{{ x.access_date }}</td>
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

<!-- include Js -->
{{partial("logs/partials/js")}}  