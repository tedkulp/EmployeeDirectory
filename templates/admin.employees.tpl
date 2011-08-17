<h3>Employees</h3>

<table cellspacing="0" class="pagetable">
	<thead>
		<tr>
			<th>First Name</th>
			<th>Last Name</tj>
			<th>&nbsp;</tj>
		</tr>
	</thead>
	<tbody>
		{foreach from=$employees item='one_employee'}
			<tr>
				<td>{$one_employee.first_name}</td>
				<td>{$one_employee.last_name}</td>
				<td>{$one_employee.edit_link} {$one_employee.delete_link}</td>
			</tr>
		{foreachelse}
			<tr>
				<td colspan="3"><strong>No Employees!</strong></td>
			</tr>
		{/foreach}
	</tbody>
</table>

<p>
	{$add_employee}
</p>
