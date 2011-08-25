<?php  /* -*- Mode: PHP; tab-width: 4; c-basic-offset: 2 -*- */
# JavelinFiles - A CMS Made Simple Module
# Created By: Ted Kulp <ted@shiftrefresh.net>
# Copyright (c) 2011 - Javelin Strategy http://www.javelinstrategy.com
#
# CMS - CMS Made Simple
# (c)2004 by Ted Kulp (ted@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org

if (!isset($gCms)) exit;

$db = $this->GetDb();
$dict = NewDataDictionary($db);
$current_version = $oldversion;

switch($current_version)
{
	case "0.1":
	{
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AlterColumnSQL(cms_db_prefix().'module_employee_directory_employees', "office_num C(50)");
		$dict->ExecuteSQLArray($sqlarray);
	}
}

# vim:ts=4 sw=4 noet
