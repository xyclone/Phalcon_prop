<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>All New Property</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        {{ stylesheet_link(["rel":"icon", "href":"img/master/logo.jpg", "type":"image/x-icon"]) }}
        {{ stylesheet_link('css/bootstrap.min.css') }}
        {{ stylesheet_link('plugins/font-awesome/css/font-awesome.min.css') }}
        {{ stylesheet_link('css/AdminLTE.min.css') }}
    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
            {{ flash.output() }} 
            <div class="login-box-body">
                <p class="login-box-msg">Change Password</p>
                {{  erorrSend }}
                <form action="{{ url("login/changePassword") }}" method="POST">
                    {% if form is not empty %}    
                        {% for field in form %}
                            <div class="form-group {{ field.getUserOption('group-req') }} {{ field.getUserOption('has-feedback') }} {{ field.getUserOption('is-hidden') }}">
                                {% if field.getLabel() is not empty %}
                                    <label for="{{ field.getName() }}" class="control-label {{ field.getUserOption('label-width') }}">{{ field.getLabel() }}</label>
                                    <div class="{{ field.getUserOption('input-width') }}">
                                {% endif %}
                                    {{ field }}
                                    {{ field.getUserOption('has-icon') }}
                                {% if field.getLabel() is not empty %}
                                    </div>
                                {% endif %}
                            </div>
                        {% endfor %}
                    {% endif %}    
                    <div class="row">
                        <div class="col-xs-6">
                            {{ link_to("Login", "<i class='fa fa-undo fa-fw' aria-hidden='true'></i> Back To Login", "class": "btn btn-default btn-block") }}
                        </div>
                        <div class="col-xs-6">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i> Change Password</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        {{ javascript_include('plugins/jQuery/jquery-2.2.3.min.js') }}
        {{ javascript_include('js/bootstrap.min.js') }}
    </body>
</html>