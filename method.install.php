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

$db = $this->GetDb();

$dict = NewDataDictionary($db);

// table schema description
$flds = "id I KEY AUTO,
		 department_id I,
		 first_name C(25),
		 last_name C(50),
		 middle_initial C(1),
		 position C(50),
		 office_num C(10),
		 extension C(10),
		 email C(100),
		 website C(255),
		 image_path C(255),
		 admin I1 default 0,
		 create_date ".CMS_ADODB_DT.",
		 modified_date ".CMS_ADODB_DT."
";

$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_employee_directory_employees", $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

$flds = "id I KEY AUTO,
		 name C(50),
		 code C(25),
		 website C(255),
		 description X2,
		 image_path C(255),
		 create_date ".CMS_ADODB_DT.",
		 modified_date ".CMS_ADODB_DT."
";

$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_employee_directory_departments", $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

# Setup summary template
$fn = cms_join_path(dirname(__FILE__),'templates','orig_summary_template.tpl');
if( file_exists( $fn ) )
{
	$template = file_get_contents( $fn );
	$this->SetPreference(EMP_DIR_PREF_NEWSUMMARY_TEMPLATE,$template);
	$this->SetTemplate('summary_Sample',$template);
	$this->SetPreference(EMP_DIR_PREF_DFLTSUMMARY_TEMPLATE,'Sample');
}

// put mention into the admin log
$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('installed', $this->GetVersion()));
