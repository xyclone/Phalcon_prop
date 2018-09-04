
<section class="content animated fadeIn" style="padding-top: 40px;">
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
		                        <div class="form-group {{ field.getUserOption('group-req') }} {{ field.getUserOption('width') }} {{ field.getUserOption('ishidden') }}">
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
                                <div class="col-sm-12">
                                    <div class="col-sm-6">
                                    <button type="button" class="btn btn-default pull-right" id="ClearItems" value="Reset"><i class='fa fa-refresh fa-fw' aria-hidden='true'></i> Reset</button>
                                    </div>
                                    <div class="col-sm-6">
                                    <input type="hidden" name="{{ tokenKey }}" value="{{ token }}" />
                                    <button type="submit" class="btn btn-primary pull-left"><i class="fa fa-search fa-fw" aria-hidden="true"></i> Filter Result</button>
                                    </div>
                                </div>
                            </span>
                        </div>
                    </div>                
                {{ end_form() }}
            </div>
		</div>
	</div>
</div>

<!-- include Js -->
{{partial("index/partials/js")}}  