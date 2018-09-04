<div class="modal fade" id="EditModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form name="usersgroup" method="POST" role="form" data-remote="data-remote">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clear_form()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><i class="fa fa-pencil-square-o fa-fw" aria-hidden="true"></i> Update Usergroup</h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        {% if form is not empty %}
                            {% for field in form %}
                                <div class="form-group {{ field.getUserOption('group-req') }} {{ field.getUserOption('ishidden') }}">
                                    <label for="{{ field.getName() }}" class="control-label {{ field.getUserOption('label-width') }}">{{ field.getLabel() }}</label>
                                    <div class="{{ field.getUserOption('input-width') }}">
                                        {{ field }}
                                    </div>
                                </div>
                            {% endfor %}   
                        {% endif %}    
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" data-dismiss="modal" onclick="clear_form()"><i class="fa fa-times-circle" aria-hidden="true"></i> Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save Update</button>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>