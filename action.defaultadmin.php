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

if (!isset($gCms)) exit;

if (isset($params['active_tab']))
{
	$this->SetCurrentTab($params['active_tab']);
}

echo $this->StartTabHeaders();
echo $this->SetTabHeader('employees', $this->Lang('employees'));
echo $this->SetTabHeader('departments', $this->Lang('departments'));
echo $this->EndTabHeaders();

echo $this->StartTabContent();

echo $this->StartTab('employees');
include(dirname(__FILE__).'/function.admin_employeestab.php');
echo $this->EndTab();

echo $this->StartTab('departments');
include(dirname(__FILE__).'/function.admin_departmentstab.php');
echo $this->EndTab();

echo $this->EndTabContent();
