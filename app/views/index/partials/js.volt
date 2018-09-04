<script>
$('.select2').select2({theme: "bootstrap", width: "100%"});	

$("#mrt").select2({	placeholder:'MRT/LRT', theme: "bootstrap", width: "100%"});
$("#districts").select2({ placeholder:'District', theme: "bootstrap", width: "100%"});
$("#unit_type").select2({ placeholder:'- Select Unit Type -', theme: "bootstrap", width: "100%"});
$("#project_property_type").select2({ placeholder:'- Select Project Property Type -', theme: "bootstrap", width: "100%"});


$(document).ready(function(){
	'use strict';

    $('#table').DataTable({
        "processing": true,
        "serverSide": false,
        "responsive": false,
        "autoWidth": false,
        "scrollX": false,
        "sPaginationType": "full_numbers",
        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-12 col-xs-12'B><'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-paging-top'p>>rt<'row mbox-pt-10'<'col-lg-6 col-md-6 col-sm-12 col-xs-12 mbox-dt-info'i><'col-lg-6 col-md-6 col-sm-12 col-xs-12'p>>",
        "lengthMenu": [
            [50, 100, 200, 500, 900, 1000, -1],
            ["50 Rows", "100 Rows", "200 Rows", "500 Rows", "900 Rows", "1000 Rows","All Rows"]
        ],
        "buttons": [
            {
                background:true,
                extend: 'pageLength',
            },
        ],
        "columnDefs": [
            { "width": "8%", "class": "text-center", "targets": [0] },
            { "width": "37%", "targets": [1] },
            { "width": "15%", "targets": [2,3,4] },
            { "class": "text-center", "targets": [-1] },
            { "orderable": false, "width": "10%", "targets": [-1] },
        ],        
        "order": [[ 0, "asc" ]],
    });

    $('#ClearItems').click(function(){
        console.log("reset");
        $('#project_search')[0].reset();
        $("select.select2").select2('data', {});
        $("select.select2").select2({theme: "bootstrap", width: "100%"});
		$("#mrt").select2({	placeholder:'MRT/LRT', theme: "bootstrap", width: "100%"});
		$("#districts").select2({ placeholder:'District', theme: "bootstrap", width: "100%"});
		$("#unit_type").select2({ placeholder:'- Select Unit Type -', theme: "bootstrap", width: "100%"});
		$("#project_property_type").select2({ placeholder:'- Select Project Property Type -', theme: "bootstrap", width: "100%"});
    });

    //Project Property Types
    var pprop_options = JSON.parse($("#prop_type_fieldoptions").val());
    var selProjType = $("#project_type option[selected]" ).val();   
    if(selProjType!==undefined) {
		$('#project_property_type').empty();
		$('#project_property_type').prepend('<option></option>');
	    $('#project_property_type').select2(
	    	{ 	
	    		theme: "bootstrap",
	    		allowClear: true,
	    		data: pprop_options[selProjType],
	    		placeholder:'- Select Project Property Type -',
	    		width:'100%',
	    	}
	    );
    }
	$('#project_type').on('change', function() {
    	if($(this).val()!=undefined) {
    		$('#project_property_type').empty();
    		$('#project_property_type').prepend('<option></option>');
		    $('#project_property_type').select2(
		    	{ 	
		    		theme: "bootstrap",
		    		allowClear: true,
		    		data: pprop_options[$(this).val()],
		    		placeholder:'- Select Project Property Type -',
		    		width:'100%',
		    	}
		    );
        }
    });

	function resetDisable() {
		$('input, select').each(
		    function(index){  
		        $(this).removeAttr("disabled", "disabled");
		    }
		);
		$("#status, #transaction").prop("checked", false);
	}
    var selvalue = $("#project_type").find('option:selected').val();
		if (selvalue==3) { //Resale
			resetDisable();
			$("#unit_type").css('pointer-events', 'none');
			$("#unit_type, #mrt, #primary_school, #total_units, #status").prop('disabled','disabled');
		} else if (selvalue==4||selvalue==2) { //GLS
			resetDisable();
			$("#unit_type").css('pointer-events', 'auto');
			$("#planning_region, #unit_type, #min_budget, #max_budget, #min_area, #max_area, #tenure, #top, #mrt, #primary_school, #total_units, #transaction").prop('disabled','disabled');
		} else { //New Sale
			resetDisable();
			$("#status").prop('disabled','disabled');
		}
    $(document).on('change', '#project_type', function (e) {
    	var value = $(this).val();
		if (value==3) { //Resale
			resetDisable();
			$("#unit_type").css('pointer-events', 'none');
			$("#unit_type, #mrt, #primary_school, #total_units, #status").prop('disabled','disabled');
		} else if (value==4||value==2) { //GLS
			resetDisable();
			$("#unit_type").css('pointer-events', 'auto');
			$("#planning_region, #unit_type, #min_budget, #max_budget, #min_area, #max_area, #tenure, #top, #mrt, #primary_school, #total_units, #transaction").prop('disabled','disabled');
		} else { //New Sale
			resetDisable();
			$("#status").prop('disabled','disabled');
		}
    });


});
</script>