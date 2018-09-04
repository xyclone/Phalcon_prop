{% if users is not empty %}
    {% for x in users %}
        <tr id="del{{ x.id }}">
            <td>{{ x.email }}</td>
            <td>{{ x.name }}</td>   
            <td>{{ x.mobile }}</td>   
            <td>{{ x.groupname }}</td>                                          
            <td>
                <button class="btn btn-sm btn-primary"  id="buttonCrudGroupMenu"><i class="fa fa-edit fa-fw cursor iconCrud" data-toggle="modal" data-target="#EditModal" onclick="updated({{ x.id }})"></i></button>
                <button class="btn btn-sm btn-danger m-l-sm"  id="buttonCrudGroupMenu"><i class="fa fa-trash fa-fw cursor iconCrud" data-toggle="modal" data-target="#Delete" onclick="deleted({{ x.id }}, '{{ x.username }}')"></i></button>
                {% if x.active === 'Y' %}
                    <button class="btn btn-sm btn-success m-l-sm" id="buttonCrudGroupMenu"><i class="fa fa-power-off fa-fw cursor text-default iconCrud" id="text{{ x.id }}" onclick="status({{ x.id }}, 'N')"></i></button>
                {% else %}
                    <button class="btn btn-sm btn-danger m-l-sm"  id="buttonCrudGroupMenu"><i class="fa fa-power-off fa-fw cursor text-default iconCrud"  id="text{{ x.id }}" onclick="status({{ x.id }}, 'Y')"></i></button>
                {% endif %}
            </td>
        </tr>
    {% endfor %}
{% endif %}