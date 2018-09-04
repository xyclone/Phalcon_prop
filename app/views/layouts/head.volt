<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- <title>All New Property</title> -->
  {{ get_title() }}
  {{ stylesheet_link(["rel":"icon", "href":"img/master/logo.jpg", "type":"image/x-icon"]) }}
  {{ stylesheet_link('css/bootstrap.min.css') }}
  {{ stylesheet_link('plugins/font-awesome/css/font-awesome.min.css') }}
  {{ stylesheet_link('css/AdminLTE.css') }}
  {{ stylesheet_link('css/skins/_all-skins.min.css') }}
  {{ stylesheet_link('css/animate.css') }}
  {{ stylesheet_link('css/core/style.css') }}

  <!-- JS -->
  {{ javascript_include('plugins/jQuery/jquery-2.2.3.min.js') }}
  {{ javascript_include('js/bootstrap.min.js') }}
  {{ javascript_include('plugins/file-style/bootstrap-filestyle.min.js') }}

  <!-- iCheck -->
  {{ stylesheet_link('plugins/iCheck/all.css') }}
  {{ javascript_include('plugins/iCheck/icheck.min.js') }}

  <!-- Pnotify -->
  {{ stylesheet_link('plugins/pnotify/pnotify.min.css') }}
  {{ javascript_include("plugins/pnotify/pnotify.core.js") }}
  {{ javascript_include("plugins/pnotify/pnotify.buttons.js") }}
  {{ javascript_include("plugins/pnotify/pnotify.nonblock.js") }}

  <!-- Jquery Fileinput -->
  {{ stylesheet_link('plugins/fileinput/build.css') }}
  {{ stylesheet_link('plugins/fileinput/fileinput.min.css') }}
  {{ javascript_include('plugins/fileinput/fileinput.min.js') }}

  <!-- Html5LightBox -->
{{ stylesheet_link('plugins/html5lightbox/html5lightbox.css') }}
{{ javascript_include('plugins/html5lightbox/html5lightbox.js') }}

<!-- all -->
{{ javascript_include('plugins/slimScroll/jquery.slimscroll.min.js') }}
{{ javascript_include('plugins/fastclick/fastclick.js') }}
{{ javascript_include('plugins/input-mask/jquery.inputmask.js') }}
{{ javascript_include("plugins/select2/select2.full.min.js") }}
{{ javascript_include('js/app.min.js') }}

<!-- bootstrap datepicker -->
<!-- { { stylesheet_link("plugins/datepicker/datepicker3.css") }} -->

<!-- bootstrap datepicker -->
<!-- { { javascript_include("plugins/datepicker/bootstrap-datepicker.js") }} -->

<!-- select2 -->
{{ stylesheet_link('plugins/select2/4.0.5/css/select2.min.css') }}
{{ stylesheet_link('plugins/select2/4.0.5/css/select2-bootstrap.min.css') }}
{{ javascript_include('plugins/select2/4.0.5/js/select2.full.min.js') }}  
<!-- jQuery Validation -->
{{ javascript_include('plugins/jquery-validation/jquery.validate.min.js') }}
<!-- input mask -->
{{ javascript_include("js/jquery.mask.js") }}

<!-- sweetalert -->
{{ stylesheet_link('css/sweetalert.css') }}
{{ stylesheet_link('css/sweetalert2.css') }}
{{ stylesheet_link('css/sweetalert2.min.css') }}
{{ javascript_include('js/sweetalert2.js') }}
{{ javascript_include('js/sweetalert.min.js') }}
{{ javascript_include('js/sweetalert2.min.js') }}

{{ javascript_include("plugins/autocomplete/jquery.autocomplete.js") }}

<!-- Hor Scroll -->
{{ stylesheet_link("plugins/horizontalscroll/li-scroller.css") }}
{{ javascript_include("plugins/horizontalscroll/jquery.li-scroller.1.0.js") }}

<!-- DateTimepicker -->
{{ stylesheet_link('plugins/datetimepicker/jquery.datetimepicker.css') }}
{{ javascript_include('plugins/datetimepicker/jquery.datetimepicker.full.min.js') }}
<!-- Moment -->
{{ javascript_include('plugins/moment/moment.min.js') }}
<!-- DateRangepicker -->
{{ stylesheet_link('plugins/daterangepicker/daterangepicker.css') }}
{{ javascript_include('plugins/daterangepicker/daterangepicker.min.js') }}

<!-- Toogle Bootstrap -->
{{ stylesheet_link('plugins/bootstrap-toggle/bootstrap-toggle.min.css') }}
{{ javascript_include('plugins/bootstrap-toggle/bootstrap-toggle.min.js') }}
<!-- Checkbox Funky Style -->
{{ stylesheet_link('plugins/funky/funky.css') }}

<!-- DataTables styles-->
{{ stylesheet_link('plugins/datatables/media/css/responsive.dataTables.min.css') }}
{{ stylesheet_link('plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }} 
{{ stylesheet_link('plugins/datatables.net-buttons/css/buttons.dataTables.min.css') }}
{{ stylesheet_link('plugins/datatables/media/css/custom.css') }}
{{ stylesheet_link('plugins/datatables.net-select/css/select.dataTables.min.css') }}
{{ stylesheet_link('plugins/datatables.net-fixedheaders/css/fixedHeader.dataTables.min.css') }}
{{ stylesheet_link('plugins/datatables.net-fixedcolumns/css/fixedColumns.dataTables.min.css') }}
<!-- DataTables scripts -->
{{ javascript_include('plugins/datatables/media/js/jquery.dataTables.min.js') }}
{{ javascript_include('plugins/datatables.net-bs/js/dataTables.bootstrap.min.js') }} 
{{ javascript_include('plugins/datatables/media/js/dataTables.responsive.min.js') }} 
{{ javascript_include('plugins/datatables/media/js/fnReloadAjax.js') }}
{{ javascript_include('plugins/pdfmake/build/pdfmake.min.js') }} 
{{ javascript_include('plugins/pdfmake/build/vfs_fonts.js') }} 
{{ javascript_include('plugins/datatables.net-buttons/js/buttons.html5.min.js') }} 
{{ javascript_include('plugins/datatables.net-buttons/js/buttons.print.min.js') }} 
{{ javascript_include('plugins/datatables.net-buttons/js/dataTables.buttons.min.js') }} 
{{ javascript_include('plugins/datatables.net-buttons/js/buttons.colVis.min.js') }}
<!-- { { javascript_include('plugins/datatables.net-select/js/dataTables.select.min.js') }} -->
{{ javascript_include('plugins/datatables.net-fixedheaders/dataTables.fixedHeader.min.js') }}
{{ javascript_include('plugins/datatables.net-fixedcolumns/dataTables.fixedColumns.min.js') }}
</head>