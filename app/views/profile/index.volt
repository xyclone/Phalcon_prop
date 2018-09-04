<!-- include CSS -->
{{partial("propstatus/partials/css")}}  

<section class="content-header animated fadeIn">
    <h1>My Profile</h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-wrench fa-fw"></i> Management</li>
        <li class="active">profile</li>
    </ol>
</section>

<section class="content animated fadeIn" style="margin-top: 20px;">
    <div class="row">

        <div class="col-lg-10 col-lg-offset-1 col-md-8 col-md-offset-2 col-sm-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-pencil-square-o fa-fw" aria-hidden="true"></i> Update My Profile</h3>
                </div>
                <form name="propstatus" action="{{ url(link_action) }}" role="form" method="POST" data-remote="data-remote">
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
                                    {{ link_to(link_back, "<i class='fa fa-undo fa-fw' aria-hidden='true'></i> Back", "class": "btn btn-default pull-left") }}
                                    <button type="submit" id="submit" name="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i> Save Changes</button>
                                </div>
                            </span>
                        </div>
                    </div>                
                </form>
            </div>

        </div>
    </div>
</section>