<!-- include CSS -->
{{partial("index/partials/css")}}

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
                                        {% elseif field.getUserOption('is-slider') %}
                                            <!-- <div data-role="rangeslider">
                                                <label for="price-min">Total Units:</label>
                                                <input type="range" name="price-min" id="price-min" value="5" min="0" max="1000">
                                                <label for="price-max">Total Units:</label>
                                                <input type="range" name="price-max" id="price-max" value="800" min="0" max="1000">
                                            </div> -->
                                            <!-- <div class="form-group">
                                                <label for="{ { field.getName() }}" class="control-label { { field.getUserOption('label-width') }}"></label>
                                                <label for="{ { field.getName() }}"class="control-label { { field.getUserOption('label-width') }}"><h5>{ { field.getLabel() }}</h5></label>
                                                <div class="{ { field.getUserOption('input-width') }}">
                                                    { { field }}
                                                </div>
                                            </div> -->
                                            <div class="form-group">{{ field }}
                                                <label for="{{ field.getName() }}" class="control-label">{{ field.getLabel() }} <span id="{{ field.getUserOption('div_slider') }}" class="p-3 mb-2 bg-primary text-white" style="padding:0 5px;"></span></label>
                                                <div id="{{ field.getUserOption('div_adv_slide') }}" ></div>
                                            </div>
                                            <script>
                                                $(document).ready(function(){
                                                    'use strict';
                                                    var getOutput = $("#{{ field.getUserOption('div_slider') }}");
                                                    var getSlider = $("#{{ field.getUserOption('div_adv_slide') }}");
                                                    var fieldName = "{{ field.getName() }}";
                                                    if(fieldName!=undefined) {
                                                        switch(fieldName) {
                                                            case 'top_year':
                                                                var min_val = parseInt("{{ field.getUserOption('slider_min') }}");
                                                                var max_val = parseInt("{{ field.getUserOption('slider_max') }}");
                                                                var max_step = 1;
                                                                var slider_val1 = parseInt((new Date()).getFullYear()-10);
                                                                var slider_val2 = parseInt((new Date()).getFullYear());
                                                                break;
                                                            case 'total_units':
                                                            default:
                                                                var min_val = 0;
                                                                var max_val = 1000;
                                                                var max_step = 5;
                                                                var slider_val1 = 10;
                                                                var slider_val2 = 100;
                                                                break;
                                                        }
                                                    }
                                                    getSlider.slider({
                                                        range:true,
                                                        min:min_val,
                                                        max:max_val,
                                                        values:[slider_val1, slider_val2],
                                                        step:max_step,
                                                        slide:function(event, ui){
                                                            getOutput.html(ui.values[0]+' - '+ui.values[1]);
                                                            $("#{{ field.getName() }}").val(ui.values[0]+'-'+ui.values[1]);
                                                        }
                                                    });
                                                    getOutput.html(getSlider.slider("values",0)+' - '+getSlider.slider("values",1));
                                                    $("#{{ field.getName() }}").val(getSlider.slider('values', 0)+'-'+getSlider.slider('values', 1));
                                                });
                                            </script>
                                            
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