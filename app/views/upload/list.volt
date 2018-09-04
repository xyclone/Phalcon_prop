{% if uploads is not empty %}
    {% for x in uploads %}
        <tr id="del{{ x.id }}">
            <td align="center">{{ x.id }}</td>
            <td>{{ x.type }}</td>   
            <td>{{ x.filename }}</td>                                         
            <td>{{ x.remarks }}</td>
            <td align="center">{{ x.upload_date }}</td>
        </tr>
    {% endfor %}
{% endif %}