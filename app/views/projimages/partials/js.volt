<script>
    //Simplefied Ajax Modal Actions
    function handleAjaxError(type, body='', footer='') {
        switch(type) {
            case 'success':
                $('#modal-ajax-handler').addClass("hmodal-success");
                titleHtml = '<i class="fa fa-check-circle fa-fw" aria-hidden="true"></i> Success';
            break;
            case 'error':
            default:
                $('#modal-ajax-handler').addClass("hmodal-danger");
                titleHtml = '<i class="fa fa-exclamation-circle fa-fw" aria-hidden="true"></i> Error';
            break;    
        }    
        if(body=="") { bodyHtml = "<p>No item is selected. Please select atleast one.</p>"; } 
        else { bodyHtml = "<p>"+body+"</p>"; }
        if(footer=="") { footerHtml = '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'; } 
        else { footerHtml = footer+'<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'; }

        $('#modal-ajax-handler').attr("data-id", type);
        $('#modal-ajax-handler').find(".modal-title").html(titleHtml);
        $('#modal-ajax-handler').find(".modal-body").html(bodyHtml);
        $('#modal-ajax-handler').find(".modal-footer").html(footerHtml);
        $('#modal-ajax-handler').modal('show');
    }// ./Simplefied Ajax Modal Actions

    function DTLoadFiles(projType=null,projPtype=null,projectid) {

        var $loader = "<div id='ajax_loader' style='position: absolute; left: 50%; top: 45%; z-index: 9999; opacity: 1;'><i class='fa fa-spinner fa-spin fa-5x fa-fw text-success'></i><span class='sr-only'></span></div>";
        $('.gallery').css('min-height', '300px');
        $(".gallery").css("opacity",0.4);
        $('.gallery #response').append($loader);           
        if(projectid!=""&&projType!=""&&projPtype!="") {
            var http = new XMLHttpRequest();
            var url = "{{link_repo}}";
            var params = "projType="+projType+"&projPtype="+projPtype+"&projectId="+projectid;
            http.open("POST", url, true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.onreadystatechange = function() {
                if(http.readyState == 4 && http.status == 200) {
                    $(".gallery").css("opacity",1);
                    $('#response').html(http.responseText);
                    $("#storagepanel").addClass('showpanel');
                } else {
                    $('.gallery #response').find($("#ajax_loader")).remove();
                }
            }
            http.send(params);
        } else {
            $(".gallery").css("opacity",1);
            $('#response').html("<center><h3>No project selected.</h3></center>");
            $('.gallery #response').find($("#ajax_loader")).remove();
        }
    }

    
    // (function() {
    //     $('form[data-remote]').on('submit', function(e) {
    //         e.preventDefault();
    //         var selProjId = $("#project_id option[selected]" ).val();
    //         var form = $(this);
    //         var url = $(form).prop('action');
    //         $.ajax({
    //             type: 'POST',
    //             url: url,
    //             dataType:'json',
    //             data: new FormData(this),
    //             contentType: false,
    //             cache: false,
    //             processData: false,
    //             error: function(XMLHttpRequest, textStatus, errorThrown) {
    //                 console.log(XMLHttpRequest.responseText);
    //             },
    //             beforeSend: function(){
    //                 $('#processmodal').modal({ backdrop: 'static', keyboard: false });
    //                 $('#processmodal').modal('show');
    //             },
    //             complete: function() {
    //                 $('#processmodal').modal('hide');
    //             },
    //             success: function(response){
    //                 $('#processmodal').modal('hide');
    //                 new PNotify({
    //                   title: response.title,
    //                   text: response.text,
    //                   type: response.type
    //                 });
    //                 DTLoadFiles(selProjId);
    //             }
    //         });
    //     });
    // })();        

    $(document).ready(function() {
        'use strict';

        //Auto Close Alert 
        $(".alert-success, .alert-info, .alert-warning, .alert-danger").fadeTo(2000, 500).slideUp(500, function(){
            $(".alert-success, .alert-info, .alert-warning, .alert-danger").slideUp(500);
        }); // ./Auto Close Alert 

        //Project Property Types
        $("#project_property_type").select2({ placeholder:'- Select Project Property Type -', theme: "bootstrap", width: "100%"});
        // var pprop_options = JSON.parse($("#prop_type_fieldoptions").val());
        // var selProjType = $("#project_type option[selected]" ).val();   
        // if(selProjType!==undefined) {
        //     $('#project_property_type').empty();
        //     $('#project_property_type').prepend('<option></option>');
        //     $('#project_property_type').select2({   
        //         theme: "bootstrap",
        //         allowClear: true,
        //         data: pprop_options[selProjType],
        //         placeholder:'- Select Project Property Type -',
        //         width:'100%',
        //     });
        // }
        $('#project_type').on('change', function() {
            var projType = $(this).val();
            var projpType = $("#project_property_type option:selected").val();
            if(projType!=undefined && projpType!=undefined) {
                $.ajax({
                    type:"POST",
                    url:"projimages/getprojects",
                    data: {
                        "projtype": projType, 
                        "projptype": projpType
                    },
                    async: true,
                    cache: false,
                    dataType:'json',
                    beforeSend: function (data) {
                    },
                    error:function(XMLHttpRequest, textStatus, errorThrown) {
                    },
                    success:function(response) {
                        $('#project_id').empty();
                        $('#project_id').prepend('<option></option>');
                        $('#project_id').select2({
                            theme: "bootstrap",
                            allowClear: true,
                            data: response,
                            placeholder:'- Select Project -',
                            width:'100%',
                        });
                    },
                });

                // $('#project_id').empty();
                // $('#project_id').prepend('<option></option>');
                // $('#project_id').select2({
                //     theme: "bootstrap",
                //     allowClear: true,
                //     placeholder:'- Select Project -',
                //     width:'100%',
                // });

                // $('#project_property_type').empty();
                // $('#project_property_type').prepend('<option></option>');
                // $('#project_property_type').select2({   
                //     theme: "bootstrap",
                //     allowClear: true,
                //     data: pprop_options[$(this).val()],
                //     placeholder:'- Select Project Property Type -',
                //     width:"100%",
                // });
            }
        });

        $('#project_property_type').on('change', function() {
            //DTLoadFiles('','','');
            var projType = $("#project_type option:selected" ).val();
            var projpType = $(this).val();
            if(projType!=undefined && projpType!=undefined) {
                $.ajax({
                    type:"POST",
                    url:"projimages/getprojects",
                    data: {
                        "projtype": projType, 
                        "projptype": projpType
                    },
                    async: true,
                    cache: false,
                    dataType:'json',
                    beforeSend: function (data) {
                    },
                    error:function(XMLHttpRequest, textStatus, errorThrown) {
                    },
                    success:function(response) {
                        $('#project_id').empty();
                        $('#project_id').prepend('<option></option>');
                        $('#project_id').select2({
                            theme: "bootstrap",
                            allowClear: true,
                            data: response,
                            placeholder:'- Select Project -',
                            width:'100%',
                        });
                    },
                });
            }
        });


        //$(".select2").select2({ theme: "bootstrap", width: "100%" });
        $("#project_id").select2({ placeholder:'- Select Project -', theme: "bootstrap", width: "100%"});
        var selProj = $("#project_id option[selected]" ).val();
        if(selProj!=undefined) DTLoadFiles(selProj);
        else DTLoadFiles('','','');
        $(document).on('change', '#project_id', function(e) {
            var projType = $("#project_type option:selected").val();
            var projPtype = $("#project_property_type option:selected").val();
            var projectId = $("#project_id option:selected").val();
// console.log(projType);
// console.log(projPtype);
// console.log(projectId);
            $("#project_id").val(projectId);
            DTLoadFiles(projType,projPtype,projectId);
        });

        $(document).on('click', '.fileinput-remove', function(e) {
            $('#imageUpload').focus();
        });

        //Simplefied Ajax Modal Actions
        $("#modal-ajax-handler").on('hidden.bs.modal', function (e) {
            $('body').removeClass("modal-open-noscroll");
            $(this).find(".modal-dialog").removeClass("modal-lg");
            $(this).find(".modal-title").empty();
            $(this).find(".modal-body").empty();
            $(this).find(".modal-footer").empty(); 
            $(this).removeClass("hmodal-default"); $(this).removeClass("hmodal-primary"); $(this).removeClass("hmodal-info");
            $(this).removeClass("hmodal-warning"); $(this).removeClass("hmodal-danger");
        });
        $("#modal-ajax-handler").on("shown.bs.modal", function(event) {
            if ($(document).height() > $(window).height()) { $('body').addClass("modal-open-noscroll"); }
            else { $('body').removeClass("modal-open-noscroll"); }
            var modalId = $("#modal-ajax-handler");
            var titleHtml = '<i class="fa fa-exclamation-circle fa-fw" aria-hidden="true"></i> Error';
            var bodyHtml = "<p>No item selected.</p>";
            var footerHtml = '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
            var dataAction = $(event.relatedTarget).data('action');
            switch(dataAction) {
                case 'Delete Image':
                    modalId.find(".modal-header").addClass("bg-primary");
                    var dataId = $(event.relatedTarget).data('id');
                    var dataName = $(event.relatedTarget).data('name');
                    if(dataId !== undefined || dataId !== null) {
                        titleHtml = '<i class="fa fa-trash-o fa-fw" aria-hidden="true"></i> '+dataAction;
                        bodyHtml = "<p>Do you want to "+dataAction+" <strong>"+dataName+"</strong>?</p>";
                        footerHtml = '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><a class="btn btn-danger">Confirm</a>';
                        //var form = $("#{{form_delete}}");
                        var form = $(document.createElement('form'));
                        //$(form).attr("method", "POST");
                        $(form).attr("data-remote", "data-remote");
                        $("<input>").attr("type","hidden").attr("name","id").val(dataId).appendTo($(form));
                        $("<input>").attr("type","hidden").attr("name","projtype").val($("#project_type option:selected").val()).appendTo($(form));
                        $("<input>").attr("type","hidden").attr("name","projptype").val($("#project_property_type option:selected").val()).appendTo($(form));
                        $("<input>").attr("type","hidden").attr("name","projid").val($("#project_id option:selected").val()).appendTo($(form));
                    }
                    modalId.find(".modal-title").html(titleHtml);
                    modalId.find(".modal-body").html(bodyHtml);
                    modalId.find(".modal-footer").html(footerHtml);
                    $(this).find('.modal-footer a').click(function() {
                        //$(this).attr('disabled', 'disabled');
                        $("#modal-ajax-handler").modal('hide');
                        $.ajax({
                            type:"POST",
                            url:"{{link_delete}}",
                            data: $(form).serialize(),
                            async: true,
                            dataType:'json',
                            beforeSend: function (data) {
                                //$('#processmodal').modal('show');
                            },
                            error:function(XMLHttpRequest, textStatus, errorThrown) {
                                //$('#processmodal').modal('hide');
                            },
                            success:function(response) {
                                //$(".file").val("").trigger("change");
                                
                                new PNotify({
                                    title: response.title,
                                    text: response.text,
                                    type: response.type
                                });
                                DTLoadFiles(response.projtype,response.projptype,response.projid);
                                
                            },
                        });
                    });
                    break;
                default:
                    break;
            }
            $(this).find($(".modal-footer button")).focus(); 
        });// ./When modal is display


        $("#{{form_name}}").validate({ 
            rules: {
                type: {
                    required: true
                }
            },
            messages: { },
            ignore: ".ignore, :hidden",
            errorPlacement: function (error, element) {
            },
            invalidHandler: function(event, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    new PNotify({
                        title: 'Error',
                        text: 'Found ' + errors + ' error fields. They have been highlighted.',
                        type: 'error'
                    });
                }
            },
            submitHandler: function (form) {               
            }
        });
    });


    (function() {
        $('form[data-remote]').on('submit', function(e) {
            e.preventDefault();
            if ($(this).validate().numberOfInvalids() != 0) {
                return false;
            } else {
                var form = $(this);
                var url = $(form).prop('action');
                $.ajax({
                    type: 'POST',
                    url: url,
                    dataType:'json',
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        console.log(XMLHttpRequest.responseText);
                    },
                    beforeSend: function(){
                    },
                    complete: function() {
                    },
                    success: function(response){
                        $('button.fileinput-remove').trigger("click");
                        new PNotify({
                          title: response.title,
                          text: response.text,
                          type: response.type
                        });
                        DTLoadFiles(response.projtype,response.projptype,response.projid);
                    }
                });
            }
        });
    })();

</script>