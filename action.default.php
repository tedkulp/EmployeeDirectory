<?php  /* -*- Mode: PHP; tab-width: 4; c-basic-offset: 2 -*- */
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

$type = get_parameter_value($params, 'type', 'reports');
if ($type == 'entries') $type = 'reports';
$filter_type = get_parameter_value($params, 'filter_type', $type);
$filter = get_parameter_value($params, 'filter', 'all');
$category_name = get_parameter_value($params, 'category_name', '');
$analyst_name = get_parameter_value($params, 'analyst_name', '');
$from = get_parameter_value($params, 'from', 'default');
$comp_only = get_parameter_value($params, 'comp_only', false);

$config = $gCms->GetConfig();

$page = 1;
if (isset($params['page']))
{
	$page = (int)$params['page'];
	if ($page < 1)
		$page = 1;
}

//Make sure this call was intended for the type (for the dashboard page stuff)
//If not, clear out the values
if ($type != $filter_type)
{
	$filter = 'all';
	$category_name = '';
	$analyst_name = '';
	$page = 1;
}

//var_dump($type, $filter, $filter_type, $category_name, $analyst_name);

$sorttype = '';
$countjoins = array();
$joins = array();
$first_letters = array();
$sortfield = 1;
unset($params['assign']);

$table = 'module_employee_directory_employees';

$thetemplate = 'summary_'.$this->GetPreference(EMP_DIR_PREF_DFLTSUMMARY_TEMPLATE);
if( isset($params['summarytemplate'] ) )
{
	$thetemplate = 'summary_'.$params['summarytemplate'];
	if ($type == 'webinars')
		$thetemplate = 'webinar' . $thetemplate;
	else if ($type == 'data')
		$thetemplate = 'data' . $thetemplate;
}

$sortorder = $this->GetPreference('sortorder','desc');
if (isset( $params['sortorder']))
{
	switch ($params['sortorder'])
	{
		case 'asc':
		case 'desc':
			$sortorder = $params['sortorder'];
	}
}

$sortby = $this->GetPreference('sortby', 'create_date');

if (isset($params['sortby']))
{
	$tmp = strtolower(trim($params['sortby']));
	switch( $tmp )
	{
		case 'id':
			$sortby = 'id';
			break;
		case 'name':
			$sortby = 'name';
			break;
		case 'created':
			$sortby = 'create_date';
			break;
		case 'modified':
			$sortby = 'modified_date';
			break;
		case 'random':
			$sortby = 'RAND()';
			$sortorder = '';
			break;
		default:
			break;
	}
}

if ($sortby == 'random')
{
	$sortby = 'RAND()';
	$sortorder = '';
}

$sortby = 'last_name ASC, first_name';
$sortorder = 'ASC';

$limit = $this->GetPreference('summary_pagelimit', 5);
if (isset($params['pagelimit']))
{
	$limit = (int)$params['pagelimit'];
}
$limit = max($limit, 1);
$limit = min($limit, 10000);

$startelement = ($page-1) * $limit;

$category = '';
$inputcat = '';
if (isset($params['category']))
{
	$category = trim($params['category']);
	$category = cms_html_entity_decode($category);
}
else if (isset($params['categoryid']))
{
	$categoryid = $params['categoryid'];
}

//
// Build the pretty urls
//

//
// Build the queries
//
$entryarray = array();
$paramarray = array();
$where = array();
$query = "SELECT c.*, d.name as department_name FROM ".cms_db_prefix().$table." c";
$query2 = "SELECT count(*) as count FROM ".cms_db_prefix().$table." c";
//$where[] = "c.active in (0, 1)";
$where[] = "1 = 1";

if (isset($category_name) && $category_name != '')
{
	$str = " INNER JOIN ".cms_db_prefix()."module_javelin_files_entry_categories cc ON cc.entry_id = c.id";
	if ($type == 'webinars')
		$str = " INNER JOIN ".cms_db_prefix()."module_javelin_files_webinar_categories cc ON cc.webinar_id = c.id";
	$query .= $str;
	$query2 .= $str;
	$str = " INNER JOIN ".cms_db_prefix()."module_javelin_files_categories cs ON cs.id = cc.category_id";
	$query .= $str;
	$query2 .= $str;

	$arr1 = array($category_name);
	$arr2 = array();
	foreach( $arr1 as $xx )
	{
		$arr2[] = "'".$xx."'";
	}
	$txt = implode(',',$arr2);
	$where[] = 'cs.name IN ('.$txt.')';
}

$str = ' INNER JOIN ' .cms_db_prefix()."module_employee_directory_departments d ON d.id = c.department_id";
$query .= $str;

if ($type == 'reports' && $filter == 'mine')
{
	$found = $this->GetAvailableEntryIds();
	//If none are found, force the query to fail
	if (empty($found))
	{
		$where[] = "c.id = 0";
	}
	else
	{
		$where[] = "c.id IN (" . implode(',', $found) . ")";
	}
}

if (count($joins))
{
	$query .= ' LEFT JOIN '.implode(' LEFT JOIN ', $joins);
}
if (count($countjoins))
{
	$query2 .= ' LEFT JOIN '.implode(' LEFT JOIN ', $countjoins);
}

$query = $query . ' WHERE ' . implode(' AND ',$where );
$query2 = $query2 . ' WHERE ' . implode(' AND ',$where );
if ($sorttype == '')
{
	$query .= " ORDER BY ".$sortby." ".$sortorder;
}
else
{
	$query .= ' ORDER BY CAST('.$sortby.' AS '.$sorttype.') '.$sortorder;
}

//var_dump('here', $query, $sortby, $sortorder);


// Execute the Queries
$count = $db->GetOne($query2, $paramarray);
if ($count == 0) return;
$dbresult = $db->SelectLimit($query, $limit, $startelement, $paramarray);
if (!$dbresult) 
{
	echo $db->sql.'<br/>'; die($db->ErrorMsg());
}

// Determine the number of pages
$npages = intval($count / $limit);
if ($count % $limit != 0) $npages++;

// build the object list
global $gCms;
$config = $gCms->GetConfig();
while ($dbresult && ($row = $dbresult->FetchRow()))
{
	$onerow = cge_array::to_object($row);
	$first_letter = strtoupper($onerow->last_name[0]);
	$onerow->first_letter = $first_letter;
	if (!in_array($first_letter, $first_letters))
		$first_letters[] = $first_letter;
	//$prettyurl = product_ops::pretty_url($row['id'],($detailpage!='')?$detailpage:$returnid);
	$prettyurl = 'employee/' . $row['id'];
	
	$parms = $params;
	$parms['entry_id'] = $row['id'];
	$action = 'details';
	$onerow->detail_url = $this->CreateLink($id, 'details', ($detailpage!=''?$detailpage:$returnid), '', $parms,
		'', true, $inline, '', false, $prettyurl);
	//$onerow->file_location = $config['uploads_url'] . '/' . $this->GetName() . '/product_' . $row['id'];
	//$onerow->details = $row['details'];

	$entryarray[] = $onerow;
}


//
// Give everything to smarty
//
$smarty->assign('items', $entryarray);
$smarty->assign('first_letters', $first_letters);
$smarty->assign('totalcount', $count);
$smarty->assign('itemcount', count($entryarray));
$smarty->assign('pagetext', $this->Lang('page'));
$smarty->assign('oftext', $this->Lang('of'));
$smarty->assign('pagecount', $npages);
$smarty->assign('curpage', $page);
if( $page == 1 )
{
	$smarty->assign('firstlink',$this->Lang('firstpage'));
	$smarty->assign('prevlink',$this->Lang('prevpage'));
}
else
{
	$parms = $params;
	$parms['page'] = 1;
	$smarty->assign('firstlink',$this->CreateLink($id,$from,$returnid,
		$this->Lang('firstpage'),
		$parms, '', false, false));
	$parms['page'] = $page - 1;
	$smarty->assign('prevlink',$this->CreateLink($id,$from,$returnid,
		$this->Lang('prevpage'),
		$parms, '', false, false));
}

if( $page == $npages )
{
	$smarty->assign('lastlink',$this->Lang('lastpage'));
	$smarty->assign('nextlink',$this->Lang('nextpage'));
}
else
{
	$parms = $params;
	$parms['page'] = $npages;
	$smarty->assign('lastlink',$this->CreateLink($id,$from,$returnid,
		$this->Lang('lastpage'),
		$parms, '', false, false));
	$parms['page'] = $page + 1;
	$smarty->assign('nextlink',$this->CreateLink($id,$from,$returnid,
		$this->Lang('nextpage'),
		$parms, '', false, false));
}

//
// Process the template
//
echo $this->ProcessTemplateFromDatabase($thetemplate);

# vim:ts=4 sw=4 noet
