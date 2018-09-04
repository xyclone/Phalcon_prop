{% if propstatus is not empty %}
    {% for x in propstatus %}
        <tr id="del{{ x.name }}">
            <td>{{ x.name }}</td>   
            <td>{{ x.description }}</td>                                         
            <td align="center">
                <button class="btn btn-sm btn-primary"  id="buttonCrudGroupMenu"><i class="fa fa-edit fa-fw cursor iconCrud" data-toggle="modal" data-target="#EditModal" onclick="updated({{ x.name }})"></i></button>
                <button class="btn btn-sm btn-danger m-l-sm"  id="buttonCrudGroupMenu"><i class="fa fa-trash fa-fw cursor iconCrud" data-toggle="modal" data-target="#Delete" onclick="deleted({{ x.name }}, '{{ x.name }}')"></i></button>
            </td>
        </tr>
    {% endfor %}
{% endif %}