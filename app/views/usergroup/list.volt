{% if usergroup is not empty %}
    {% for x in usergroup %}
        <tr id="del{{ x.id }}">
            <td>{{ x.usergroup }}</td>
            <td>{{ x.description }}</td>
            <td>
                <button class="btn btn-sm btn-primary"  id="buttonCrudGroupMenu"><i class="fa fa-edit fa-fw cursor iconCrud" data-toggle="modal" data-target="#EditModal" onclick="updated('{{ x.id }}', '{{ x.usergroup }}', '{{ x.description }}', '{{ x.icon }}')"></i></button>
                <button class="btn btn-sm btn-danger m-l-sm"  id="buttonCrudGroupMenu"><i class="fa fa-trash fa-fw cursor iconCrud" data-toggle="modal" data-target="#Delete" onclick="deleted({{ x.id }}, '{{ x.usergroup }}')"></i></button>

                {% if x.active is 'Y' %}
                    <button class="btn btn-sm btn-success m-l-sm" id="buttonCrudGroupMenu"><i class="fa fa-power-off fa-fw cursor text-default iconCrud" id="text{{ x.id }}" onclick="status({{ x.id }}, 'N')"></i></button>
                {% else %}
                    <button class="btn btn-sm btn-danger m-l-sm"  id="buttonCrudGroupMenu"><i class="fa fa-power-off fa-fw cursor text-default iconCrud"  id="text{{ x.id }}" onclick="status({{ x.id }}, 'Y')"></i></button>
                {% endif %}
            </td>
        </tr>
    {% endfor %}
{% endif %}