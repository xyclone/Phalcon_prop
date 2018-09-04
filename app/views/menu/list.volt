{% set no = 1 %}
{% for x in group %}
  <tr id="groupdel{{ x.id }}">
      <td>{{ no }}</td>
      <td>{{ x.menu_group }}</td>
      <td>{{ x.usergroup }}</td>
      <td>
          <button class="btn btn-danger btn-flat"  id="buttonCrudGroupMenu">
              <i class="fa fa-trash fa-fw cursor iconCrud" data-toggle="modal" data-target="#groupDelete" onclick="deletedGroup({{ x.id }}, '{{ x.menu_group }}')"></i>  
          </button>
         {% if x.active === 'Y' %}
              <button class="btn btn-sm btn-success m-l-sm" id="buttonCrudGroupMenu"><i class="fa fa-power-off fa-fw cursor text-default iconCrud" id="grouptext{{ x.id }}" onclick="statusGroup({{ x.id }}, 'N')"></i></button>
          {% else %}
              <button class="btn btn-sm btn-danger m-l-sm"  id="buttonCrudGroupMenu"><i class="fa fa-power-off fa-fw cursor text-default iconCrud"  id="grouptext{{ x.id }}" onclick="statusGroup({{ x.id }}, 'Y')"></i></button>
          {% endif %}
      </td>
  </tr>
{% set no = no + 1 %}
{% endfor %}