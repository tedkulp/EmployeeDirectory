<?php
#-------------------------------------------------------------------------
# Module: Employee Directory
# Author: Ted Kulp <ted@shiftrefresh.net>
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (ted@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http:	//www.gnu.org/licenses/licenses.html#GPL#
#
#-------------------------------------------------------------------------
#-------------------------------------------------------------------------
if (!isset($gCms)) exit;

$employee = $this->GetEmployee($params['settingsdata_id']);

$form_settings = new CMSForm($this->GetName(), $id.'settings', 'edit_employee', $returnid);
$form_settings->setLabel('submit', $this->lang('save'));

$form_settings->setWidget('data_first_name', 'text', array('value' => $employee['first_name']));
$form_settings->setWidget('data_last_name', 'text', array('value' => $employee['last_name']));
$form_settings->setWidget('data_middle_initial', 'text', array('value' => $employee['middle_initial']));
$form_settings->setWidget('data_position', 'text', array('value' => $employee['position']));
$form_settings->setWidget('data_extension', 'text', array('value' => $employee['extension']));
$form_settings->setWidget('data_office_num', 'text', array('value' => $employee['office_num']));
$form_settings->setWidget('data_email', 'text', array('value' => $employee['email']));
$form_settings->setWidget('data_website', 'text', array('value' => $employee['website']));
$form_settings->setWidget('data_department_id', 'select', array('value' => $employee['department_id'], 'values' => $this->GetDepartmentsForSelect()));
$form_settings->setWidget('data_id', 'hidden', array('value' => $employee['id']));

if ($form_settings->isPosted())
{
	$form_settings->process();
	foreach ($form_settings->getWidgets() as $widget)
	{
		if (startswith($widget->getName(), 'data_'))
		{
			$employee[str_replace('data_', '', $widget->getName())] = $widget->getValue();
		}
	}

	$this->SaveEmployee($employee);
	return $this->Redirect($id, 'defaultadmin', $returnid, array('active_tab' => 'employees'));
}
else if ($form_settings->isCancelled())
{
	return $this->Redirect($id, 'defaultadmin', $returnid, array('active_tab' => 'employees'));
}

$this->smarty->assign('form_settings', $form_settings);
echo $this->ProcessTemplate('admin.edit_form.tpl');
