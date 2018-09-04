<div class="modal fade" id="EditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clear_form()">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-pencil-square-o fa-fw" aria-hidden="true"></i> Update Project Type</h4>
            </div>
            <form name="project_edit" id="project_edit" action="/projects/saveProject" method="POST" data-remote="data-remote">
                <div class="modal-body">
                    <div role="form" class="row">

                    {% if form_edit is not empty %}
                        {% for field in form_edit %}
                            <div class="form-group {{ field.getUserOption('group-req') }} {{ field.getUserOption('ishidden') }}">
                                <label for="{{ field.getName() }}" class="control-label {{ field.getUserOption('label-width') }}">{{ field.getLabel() }}</label>
                                <div class="{{ field.getUserOption('input-width') }}">
                                    {% if field.getUserOption('group-addon-prefix') %}
                                        <div class="input-group">
                                            <span class="input-group-addon">{{field.getUserOption('group-addon-prefix')}}</span>
                                            {{ field }}
                                        </div>
                                    {% else if field.getUserOption('group-addon-postfix') %}
                                        <div class="input-group">
                                            <span class="input-group-addon">{{field.getUserOption('group-addon-postfix')}}</span>
                                            {{ field }}
                                        </div>
                                    {% else %}
                                        {{ field }}
                                    {% endif %}
                                </div>
                            </div>
                        {% endfor %}   
                    {% endif %}   
                    <p>&nbsp;</p>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" onclick="clear_form()"><i class="fa fa-times-circle" aria-hidden="true"></i> Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save Update</button>
                </div>
            </form>
        </div>
    </div>
</div>