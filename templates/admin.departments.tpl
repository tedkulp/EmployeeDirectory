<h3>Departments</h3>

<table cellspacing="0" class="pagetable">
	<thead>
		<tr>
			<th width="75%">Name</th>
			<th>Code</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$departments item='one_department'}
			<tr>
				<td>{$one_department.name}</td>
				<td>{$one_department.code}</td>
				<td>{$one_department.edit_link} {$one_department.delete_link}</td>
			</tr>
		{foreachelse}
			<tr>
				<td colspan="3"><strong>No Departments!</strong></td>
			</tr>
		{/foreach}
	</tbody>
</table>

<p>
	{$add_department}
</p>
