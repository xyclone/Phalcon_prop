<!-- include CSS -->
{{partial("index/partials/css")}}

<section class="content animated fadeIn" style="padding-top: 0px; margin-bottom: 25px;">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-8 col-md-offset-2 col-sm-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-search fa-fw" aria-hidden="true"></i> Search Property</h3>
                </div>
                <form name="project_search" id="project_search" action="{{ url(link_action) }}" role="form" method="POST" >
                {{ form(link_action, 'name':form_name, 'id':form_name, 'class': 'form-horizontal', 'role':'form', 'autocomplete':'off' ) }}
                    <div class="box-body">
		                {% if form is not empty %}
		                    {% for field in form %}
                                {% if field.getUserOption('funkyCheckbox') %}  
                                    <div class="form-group {{ field.getUserOption('group-req') }} {{ field.getUserOption('width') }} {{ field.getUserOption('ishidden') }}">
                                        <div class="{{ field.getUserOption('input-width') }}">                    
                                            <div class="funkyradio">
                                                <div class="funkyradio-success">
                                                    {{ field }}
                                                    <label for="{{ field.getName() }}">{{ field.getLabel() }}</label>
                                                </div>
                                            </div>
    		                            </div>
    		                        </div>
                                {% elseif field.getUserOption('is-touchspin') %}
                                    <div class="form-group {{ field.getUserOption('group-req') }} {{ field.getUserOption('width') }} {{ field.getUserOption('ishidden') }}" style="display:inline !important;">
                                        <div class="{{ field.getUserOption('input-width') }}">
                                            <div class="input-group">{{ field }}</div>
                                            <script>
                                                $("input[name='{{ field.getName() }}']").TouchSpin({
                                                    min: parseInt("{{field.getUserOption('value_min')}}"),
                                                    max: parseInt("{{field.getUserOption('value_max')}}"),
                                                    step: parseInt("{{ field.getUserOption('value_interval') }}"),
                                                    decimals: 0,
                                                    boostat: 5,
                                                    maxboostedstep: 10,
                                                    prefix: "<b>{{ field.getLabel() }}</b>"
                                                });
                                            </script> 
                                        </div>
                                    </div>
                                {% elseif field.getUserOption('prefix-addon') %}
                                    <div class="form-group {{ field.getUserOption('group-req') }} {{ field.getUserOption('width') }} {{ field.getUserOption('ishidden') }}">
                                        <div class="{{ field.getUserOption('input-width') }}">
                                            <div class="input-group">
                                                <span class="input-group-addon"><b>{{field.getUserOption('prefix-label')}}</b></span>
                                                {{ field }}
                                            </div>
                                        </div>
                                    </div>
                                {% elseif field.getUserOption('postfix-addon') %}
                                    <div class="form-group {{ field.getUserOption('group-req') }} {{ field.getUserOption('width') }} {{ field.getUserOption('ishidden') }}">
                                        <div class="{{ field.getUserOption('input-width') }}">
                                            <div class="input-group">
                                                {{ field }}
                                                <span class="input-group-addon"><b>{{field.getUserOption('postfix-label')}}</b></span>
                                            </div>
                                        </div>
                                    </div>
                                {% else %}
                                    <div class="form-group {{ field.getUserOption('group-req') }} {{ field.getUserOption('width') }} {{ field.getUserOption('ishidden') }}">
                                        <div class="{{ field.getUserOption('input-width') }}">
                                            {{ field }}
                                        </div>
                                    </div>
                                {% endif %}
		                    {% endfor %}   
		                {% endif %}                      
                        <br>
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- <input type="hidden" name="{ { tokenKey }}" value="{ { token }}" /> -->
                                <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-search fa-fw" aria-hidden="true"></i> Filter Result</button>
                            </div>
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-default pull-left" id="ClearItems" value="Reset"><i class='fa fa-refresh fa-fw' aria-hidden='true'></i> Reset</button>
                            </div>
                        </div>
                    </div>                
                {{ end_form() }}
            </div>
		</div>
	</div>
</div>

<!-- include Js -->
{{partial("index/partials/js")}}  