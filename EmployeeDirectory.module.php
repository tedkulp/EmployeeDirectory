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
# Or read it online: http:	//www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#-------------------------------------------------------------------------

define('EMP_DIR_PREF_NEWSUMMARY_TEMPLATE','emp_dir_pref_newsummary_template');
define('EMP_DIR_PREF_DFLTSUMMARY_TEMPLATE','emp_dir_pref_dfltsummary_template');

class EmployeeDirectory extends CGExtensions
{
	function EmployeeDirectory()
	{
		$this->CMSModule();
	}

	function GetName()
	{
		return 'EmployeeDirectory';
	}

	function GetFriendlyName()
	{
		return $this->Lang('friendlyname');
	}

	function GetVersion()
	{
		return '0.2';
	}

	function GetAuthor()
	{
		return 'Ted Kulp';
	}

	function GetAuthorEmail()
	{
		return 'ted@shiftrefresh.net';
	}

	function HasAdmin()
	{
		return true;
	}

	function MinimumCMSVersion()
	{
		return "1.9";
	}

	function GetAdminSection()
	{
		return 'content';
	}

	function GetDependencies()
	{
		return array('CGExtensions' => '1.19.4', 'CMSForms' => '0.0.20');
	}

	function HasCapability($capability, $params = array())
	{
		switch ($capability)
		{
			default:
				return FALSE;
		}
	}

	function GetDepartments()
	{
		global $gCms;
		$db = $gCms->GetDb();
		$result = $db->GetAll("SELECT * FROM " . cms_db_prefix() . "module_employee_directory_departments ORDER BY name ASC");
		return $result;
	}

	function GetDepartmentsForSelect()
	{
		$result = array();
		foreach ($this->GetDepartments() as $one_department)
		{
			$result[$one_department['id']] = $one_department['name'];
		}
		return $result;
	}

	function GetDepartment($department_id)
	{
		if ($department_id > -1)
		{
			global $gCms;
			$db = $gCms->GetDb();
			$result = $db->GetRow("SELECT * FROM " . cms_db_prefix() . 'module_employee_directory_departments WHERE id = ?', array($department_id));
			return $result;
		}
		else
		{
			return array('id' => -1, 'name' => '', 'code' => '', 'website' => '', 'description' => '');
		}
	}

	function SaveDepartment($department = array())
	{
		global $gCms;
		$db = $gCms->GetDb();

		if (count($department) > 0 && array_key_exists('id', $department))
		{
			$department_id = $department['id'];
			unset($department['id']);
			$department['modified_date'] = $db->DBTimeStamp(time());

			// Do update if we have a valid id
			if ($department_id > -1)
			{
				$fields = array();
				$values = array();
				foreach ($department as $k=>$v)
				{
					$fields[] = $k . '=?';
					$values[] = $v;
				}
				$values[] = $department_id;
				$query = 'UPDATE ' . cms_db_prefix() . 'module_employee_directory_departments SET ' . implode(',', $fields) . ' WHERE id = ?';
				return $db->Execute($query, $values);
			}
			else
			{
				$department['create_date'] = $db->DBTimeStamp(time());
				$filler = implode(',', array_fill(0, count($department), '?'));
				$query = 'INSERT INTO ' . cms_db_prefix() . 'module_employee_directory_departments (' . implode(',', array_keys($department)) . ') VALUES (' . $filler . ')';
				return $db->Execute($query, array_values($department));
			}
		}
	}

	function DeleteDepartment($department_id)
	{
		global $gCms;
		$db = $gCms->GetDb();
		return $db->Execute("DELETE FROM " . cms_db_prefix() . "module_employee_directory_departments WHERE id = ?", array($department_id));
	}

	function GetEmployees($department_id = -1)
	{
		global $gCms;
		$db = $gCms->GetDb();
		$query = "SELECT * FROM " . cms_db_prefix() . "module_employee_directory_employees ";
		if ($department_id > -1)
			$query .= 'WHERE department_id = ' . $department_id . ' ';
		$query .= "ORDER BY last_name, first_name ASC";
		$result = $db->GetAll($query);
		return $result;
	}

	function GetEmployee($employee_id)
	{
		if ($employee_id > -1)
		{
			global $gCms;
			$db = $gCms->GetDb();
			$result = $db->GetRow("SELECT * FROM " . cms_db_prefix() . 'module_employee_directory_employees WHERE id = ?', array($employee_id));
			return $result;
		}
		else
		{
			return array('id' => -1, 'first_name' => '', 'last_name' => '');
		}
	}

	function SaveEmployee($employee = array())
	{
		global $gCms;
		$db = $gCms->GetDb();

		if (count($employee) > 0 && array_key_exists('id', $employee))
		{
			$employee_id = $employee['id'];
			unset($employee['id']);
			$employee['modified_date'] = $db->DBTimeStamp(time());

			// Do update if we have a valid id
			if ($employee_id > -1)
			{
				$fields = array();
				$values = array();
				foreach ($employee as $k=>$v)
				{
					$fields[] = $k . '=?';
					$values[] = $v;
				}
				$values[] = $employee_id;
				$query = 'UPDATE ' . cms_db_prefix() . 'module_employee_directory_employees SET ' . implode(',', $fields) . ' WHERE id = ?';
				return $db->Execute($query, $values);
			}
			else
			{
				$employee['create_date'] = $db->DBTimeStamp(time());
				$filler = implode(',', array_fill(0, count($employee), '?'));
				$query = 'INSERT INTO ' . cms_db_prefix() . 'module_employee_directory_employees (' . implode(',', array_keys($employee)) . ') VALUES (' . $filler . ')';
				return $db->Execute($query, array_values($employee));
			}
		}
	}

	function DeleteEmployee($employee_id)
	{
		global $gCms;
		$db = $gCms->GetDb();
		return $db->Execute("DELETE FROM " . cms_db_prefix() . "module_employee_directory_employees WHERE id = ?", array($employee_id));
	}

} //end class
