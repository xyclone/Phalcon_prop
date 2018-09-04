<!DOCTYPE html>
<html>
{% include "layouts/head.volt" %}
    <body class="hold-transition skin-green sidebar-collapse"><!-- sidebar-collapse -->
        <!-- { { image('img/master/baner.png', 'width':'100%') }} -->
        <div class="wrapper">
            {% include "layouts/header.volt" %}
            {% include "layouts/sidebar.volt" %}
            <div class="content-wrapper">

                <section class="content-header">
                    <div class="container-fluid"> 
                        {{ flash.output() }} 
                    </div>
                </section>                

                {{ content() }}
            </div>
            <!-- { % include "layouts/footer.volt" %} -->
        </div>
    
    <div class="footer">
        <div class="row">
            <div class="col-sm-10">
                <div class="pull-left">
                    <ul id="ticker02">
                        {{ ScrollNews.getAllNews() }}
                    </ul>
                </div>
            </div>
            <div class="col-sm-2">
                <span class="pull-right"><b>All New Property &copy;</b></span>
            </div>
        </div>
    </div>
    <script>
        $(function(){
            $("ul#ticker02").liScroll({travelocity: 0.15});
        }); 
    </script>

    </body>
</html>
