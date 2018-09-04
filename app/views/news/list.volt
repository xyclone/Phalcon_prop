{% if news is not empty %}
    {% for x in news %}
        <tr id="del{{ x.id }}">
            <td align="center">{{ x.id }}</td>
            <td>{{ x.name }}</td>   
            <td>{{ x.link }}</td>   
            <td>{{ x.news }}</td>
            <td>{{ x.start_date }}</td> 
            <td>{{ x.stop_date }}</td>                                       
            <td align="center">
                <button class="btn btn-sm btn-primary" id="buttonCrudGroupMenu"><i class="fa fa-edit fa-fw cursor iconCrud" data-toggle="modal" data-target="#EditModal" onclick="updated({{ x.id }})"></i></button>
                <button class="btn btn-sm btn-danger m-l-sm"  id="buttonCrudGroupMenu"><i class="fa fa-trash fa-fw cursor iconCrud" data-toggle="modal" data-target="#Delete" onclick="deleted({{ x.id }}, '{{ x.name }}')"></i></button>
            </td>
        </tr>
    {% endfor %}
{% endif %}