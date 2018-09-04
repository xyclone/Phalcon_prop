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
            <div class="login-logo">
                <h3>Login</h3>
            </div>

            <div class="login-box-body">
                <p class="login-box-msg">Sign in to start your session</p>
                {{ flash.output() }} 
                <form action="{{ url('login') }}" method="POST">
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
                        <div class="col-xs-12">
                            <input type="hidden" name="{{ tokenKey }}" value="{{ token }}" />
                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-sign-in fa-fw" aria-hidden="true"></i> Sign In</button>
                        </div>
                    </div>
                </form>
                <br>
                <div class="row m-t-md">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <input type="checkbox" id="remember" name="remember" class="flat-blue"> <label class="control-label"> Remember me</label>
                        </div>
                    </div>
                    <div class="col-sm-6 pull-right">
                        {{ link_to("Login/forgotPassword", "<i class='fa fa-question-circle fa-fw' aria-hidden='true'></i> Forgot Password", "class": "pull-right text-warning") }}
                    </div>
                </div>
            </div>
        </div>
        {{ javascript_include('plugins/jQuery/jquery-2.2.3.min.js') }}
        {{ javascript_include('js/bootstrap.min.js') }}
    </body>
</html>
