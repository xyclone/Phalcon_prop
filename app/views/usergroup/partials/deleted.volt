<div class="modal fade" id="Delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-red">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete Usergroup</h4>
            </div>

            <form name="delete" action="{{ url('usergroup/deleted') }}" method="POST" data-remote="data-remote">
                <div class="modal-body">
                    <input type="hidden" name="id" id="id_delete" value="">
                    <p>Are you sure you want to delete Usergroup "<span id="usergroup" class="text-red"></span>" ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default close_btn" data-dismiss="modal"><i class="fa fa-times-circle" aria-hidden="true"></i> Close</button><button type="submit" class="btn btn-danger m-l-md"><i class="fa fa-check" aria-hidden="true"></i> Confirm</button>
                </div>
            </form>

        </div>
    </div>
</div>