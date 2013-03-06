<?php

/**
 * Xodus
 *
 * Copyright 2012 by Joe Molloy <info@hyper-typer.com>
 *
 * This file is part of Xodus, a simple component MODx Revolution which allows users from a selected user group to be exported to CSV or MS Excel format.
 *
 * Xouds is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Xodus is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Xodus; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package xodus
 */

class XodusGetGroupUserListProcessor extends modObjectGetListProcessor {
    public $classKey = 'modUser';
    public $languageTopics = array('modx:default');
    public $defaultSortField = 'fullname';
    public $defaultSortDirection = 'ASC';
    public $objectType = '';
	private $users = array();
	private $fext = '';
	private $timestamp = '';
	
	public function prepareQueryBeforeCount(xPDOQuery $c) {
        $usergroup = (int)$this->getProperty('group',0);
		$c->innerJoin ('modUserProfile','Profile');
		$c->innerJoin ('modUserGroupMember','UserGroupMembers');
		$c->innerJoin ('modUserGroup','UserGroup','`UserGroupMembers`.`user_group` = 		`UserGroup`.`id`');
		$c->leftJoin ('modUserGroupRole','UserGroupRole','`UserGroupMembers`.`role` = `UserGroupRole`.`id`');
        $c->where(array(
			'active' => true,
			'UserGroupMembers.user_group' => $usergroup,
		));
		$c->select(array('modUser.id','modUser.username','modUser.password'));
		$c->limit(0);
        return $c;
    }
	
	public function prepareRow(xPDOObject $obj){
		$profile = $obj->getOne('Profile');
		$obj->set('fullname', $profile->get('fullname'));
		$obj->set('email', $profile->get('email'));
		$obj->set('phone', $profile->get('phone'));
		$obj->set('mobilephone',$profile->get('mobilephone'));
		$obj->set('blocked',$profile->get('blocked'));
		$obj->set('blockeduntil',$profile->get('blockeduntil'));
		$obj->set('blockedafter',$profile->get('blockedafter'));
		$obj->set('logincount',$profile->get('logincount'));
		$obj->set('lastlogin',$profile->get('lastlogin'));
		$obj->set('failedlogincount',$profile->get('failedlogincount'));
		$obj->set('sessionid',$profile->get('sessionid'));
		$obj->set('dob',$profile->get('dob'));
		$obj->set('gender',$profile->get('gender'));
		$obj->set('address',$profile->get('address'));
		$obj->set('country',$profile->get('country'));
		$obj->set('city',$profile->get('city'));
		$obj->set('state',$profile->get('state'));
		$obj->set('zip',$profile->get('zip'));
		$obj->set('fax',$profile->get('fax'));
		$obj->set('photo',$profile->get('photo'));
		$obj->set('comment',$profile->get('comment'));
		$obj->set('website',$profile->get('website'));
		$obj->set('extended',$profile->get('extended'));
		$ta = $obj->toArray('',false,true);
		return $ta;
	}
	
	public function afterIteration(array $users) {
		$this->modx->log(modX::LOG_LEVEL_INFO,'Child afteriteration called');
        $this->users = $users;
		if(count($this->users) > 0){
			$this->timestamp = time();
        	$this->doExportFile($this->users,$this->timestamp);
		}
		return $users;
    }
	
	public function outputArray(array $users, $count = false){
		if ($count === false) { $count = count($users);}
		if($count == 0){
			return '{"success":true,"message":"'.$this->modx->lexicon('xodus.export_no_users').'","object":{"total":"0"},"data":[]}';	
		}else{
			$action_url = $this->getActionURL();
			if($this->fext == 'csv'){
				return '{"success":true,"message":"'.count($users).' '.$this->modx->lexicon('xodus.export_users').'","object":{"file":"'.$this->timestamp.'.csv","action_url":"'.$action_url.'","total":"'.count($users).'"},"data":[]}';
			}else{
				return '{"success":true,"message":"'.count($users).' '.$this->modx->lexicon('xodus.export_users').'","object":{"file":"'.$this->timestamp.'.'.$this->fext.'","action_url":"'.$action_url.'","total":"'.count($users).'"},"data":[]}';	
			}
		}
	}
	
	private function doExportFile($users,$timestamp){
		$format = $this->getProperty('format');
		if($format == 0){
			$this->doCSV($users,$timestamp);
		}else{
			$this->doExcel($users,$timestamp);	
		}
		
	}
	
	private function doCSV($users,$timestamp){
		$this->fext = 'csv';
		$fname = dirname(dirname(dirname(dirname(__file__)))).'/tmp/'.$timestamp;
		$content = '';
		$count = sizeof($users);
		$headings = array($this->modx->lexicon('xodus.full_name'), $this->modx->lexicon('xodus.email'), $this->modx->lexicon('xodus.password'), $this->modx->lexicon('xodus.phone'), $this->modx->lexicon('xodus.mobile'), $this->modx->lexicon('xodus.fax'), $this->modx->lexicon('xodus.address'), $this->modx->lexicon('xodus.city'), $this->modx->lexicon('xodus.state'), $this->modx->lexicon('xodus.zip'), $this->modx->lexicon('xodus.country'), $this->modx->lexicon('xodus.dob'), $this->modx->lexicon('xodus.gender'), $this->modx->lexicon('xodus.website'), $this->modx->lexicon('xodus.comment'), $this->modx->lexicon('xodus.extended_fields'), $this->modx->lexicon('xodus.blocked'), $this->modx->lexicon('xodus.blocked_after'), $this->modx->lexicon('xodus.blocked_until'), $this->modx->lexicon('xodus.last_login'), $this->modx->lexicon('xodus.login_count'), $this->modx->lexicon('xodus.failed_login_count'), $this->modx->lexicon('xodus.session_id'));
		$fp = fopen($fname.'.csv', 'w');
		fputcsv($fp,$headings);
		for($i = 0; $i < $count; $i++){
			$user = array();
			$user[0] = $users[$i]['fullname'];
			$user[1] = $users[$i]['email'];
			$user[2] = $users[$i]['password'];
			$user[3] = $users[$i]['phone'];
			$user[4] = $users[$i]['mobilephone'];
			$user[5] = $users[$i]['fax'];
			$user[6] = $users[$i]['address'];
			$user[7] = $users[$i]['city'];
			$user[8] = $users[$i]['state'];
			$user[9] = $users[$i]['zip'];
			$user[10] = $users[$i]['country'];
			if($users[$i]['dob'] != 0){
				$dob = date('Y-m-d',$users[$i]['dob']);
			}else{
				$dob = '';
			}
			$user[11] = $dob;
			$gender = $this->getGender($users[$i]['gender']);
			$user[12] = $gender;
			$user[13] = $users[$i]['website'];
			$user[14] = $users[$i]['comment'];
			$user[15] = json_encode($users[$i]['extended']);
			if($users[$i]['blocked'] == '' or $users[$i]['blocked'] == 0){
				$blocked = 'No';	
			}else{
				$blocked = 'Yes';	
			}
			$user[16] = $blocked;
			if($users[$i]['blockedafter'] == 0 or $users[$i]['blockedafter'] == ''){
				$blockedafter = '';
			}else{
				$blockedafter = date('Y-m-d H:i:s',$users[$i]['blockedafter']);
			}
			$user[17] = $blockedafter;
			if($users[$i]['blockeduntil'] == 0 or $users[$i]['blockeduntil'] == ''){
				$blockeduntil = '';
			}else{
				$blockeduntil = date('Y-m-d H:i:s',$users[$i]['blockeduntil']);
			}
			$user[18] = $blockeduntil;
			
			if($users[$i]['lastlogin'] != 0){
				$lastlogin = date('Y-m-d H:i:s',$users[$i]['lastlogin']);	
			}else{
				$lastlogin = '';	
			}
			$user[19] = $lastlogin;
			$user[20] = $users[$i]['logincount'];
			$user[21] = $users[$i]['failedlogincount'];
			$user[22] = $users[$i]['sessionid'];
    		fputcsv($fp, $user);
		}
		fclose($fp);
	}
	
	private function doExcel($users,$timestamp){
		$library_path = dirname(dirname(dirname(dirname(__file__)))).'/lib/';
		require_once($library_path.'excel/Classes/PHPExcel.php');									
		require_once($library_path.'excel/Classes/PHPExcel/IOFactory.php');
		require_once($library_path.'excel/Classes/PHPExcel/Cell/AdvancedValueBinder.php');	
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->createSheet();
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $this->modx->lexicon('xodus.full_name'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, $this->modx->lexicon('xodus.email'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 1, $this->modx->lexicon('xodus.password'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 1, $this->modx->lexicon('xodus.phone'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, 1, $this->modx->lexicon('xodus.mobile'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, 1, $this->modx->lexicon('xodus.fax'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, 1, $this->modx->lexicon('xodus.address'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, 1, $this->modx->lexicon('xodus.city'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, 1, $this->modx->lexicon('xodus.state'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, 1, $this->modx->lexicon('xodus.zip'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, 1, $this->modx->lexicon('xodus.country'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, 1, $this->modx->lexicon('xodus.dob'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, 1, $this->modx->lexicon('xodus.gender'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, 1, $this->modx->lexicon('xodus.photo'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, 1, $this->modx->lexicon('xodus.website'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, 1, $this->modx->lexicon('xodus.comment'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, 1, $this->modx->lexicon('xodus.extended_fields'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, 1, $this->modx->lexicon('xodus.blocked'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, 1, $this->modx->lexicon('xodus.blocked_after'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19, 1, $this->modx->lexicon('xodus.blocked_until'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20, 1, $this->modx->lexicon('xodus.last_login'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(21, 1, $this->modx->lexicon('xodus.login_count'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(22, 1, $this->modx->lexicon('xodus.failed_login_count'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(23, 1, $this->modx->lexicon('xodus.session_id'));
		$styleArray = array( 
			'font' => array( 'bold' => true, ), 
			'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$objPHPExcel->getActiveSheet()->getStyle('A1:W1')->applyFromArray($styleArray);
		
		for($i = 0; $i < count($users); $i++){
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $i+2, $users[$i]['fullname']);
			
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $i+2, $users[$i]['email']);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $i+2, $users[$i]['password']);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $i+2, $users[$i]['phone']);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $i+2, $users[$i]['mobilephone']);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $i+2, $users[$i]['fax']);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $i+2, $users[$i]['address']);
		$objPHPExcel->getActiveSheet()->getStyle('E'.($i+2))->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $i+2, $users[$i]['city']);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $i+2, $users[$i]['state']);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $i+2, $users[$i]['zip']);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $i+2, $users[$i]['country']);
		if($users[$i]['dob'] != 0){
		$dob = date('Y-m-d',$users[$i]['dob']);
		PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
		$objPHPExcel->getActiveSheet() ->setCellValue('l'.($i+2), $dob); 
		$objPHPExcel->getActiveSheet() ->getStyle('l'.($i+2)) ->getNumberFormat() ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
		}
		$gender = $this->getGender($users[$i]['gender']);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $i+2, $gender);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $i+2, $users[$i]['photo']);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $i+2, $users[$i]['website']);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $i+2, $users[$i]['comment']);
		if($users[$i]['extended'] != null){
			$extended = json_encode($users[$i]['extended']);
		}else{$extended = '';}
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $i+2, $extended);
		if($users[$i]['blocked'] == 1){$blocked = 'Yes';}else{$blocked = 'No';}
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $i+2, $blocked);
		if($users[$i]['blockedafter'] != 0 and $users[$i]['blockedafter'] != ''){
		$blockedafter = $users[$i]['blockedafter'];
		$objPHPExcel->getActiveSheet()
            ->setCellValueByColumnAndRow(18, $i+2, PHPExcel_Shared_Date::PHPToExcel($blockedafter));
$objPHPExcel->getActiveSheet()
            ->getStyle('S'.($i+2))
            ->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
		}else{
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $i+2, '');	
		}
		if($users[$i]['blockeduntil'] != 0 and $users[$i]['blockeduntil'] != ''){
		$blockeduntil = $users[$i]['blockeduntil'];
		$objPHPExcel->getActiveSheet()
            ->setCellValueByColumnAndRow(19, $i+2, PHPExcel_Shared_Date::PHPToExcel($blockeduntil));
$objPHPExcel->getActiveSheet()
            ->getStyle('T'.($i+2))
            ->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
		}else{
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $i+2, '');	
		}
		
		if($users[$i]['lastlogin'] != 0 and $users[$i]['lastlogin'] != ''){
		$lastlogin = $users[$i]['lastlogin'];
		$objPHPExcel->getActiveSheet()
            ->setCellValueByColumnAndRow(20, $i+2, PHPExcel_Shared_Date::PHPToExcel($lastlogin));
$objPHPExcel->getActiveSheet()
            ->getStyle('U'.($i+2))
            ->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
		}else{
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $i+2, '');	
		}
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(21, $i+2, $users[$i]['logincount']);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(22, $i+2, $users[$i]['failedlogincount']);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(23, $i+2, $users[$i]['sessionid']);	
		}
		$objPHPExcel->setActiveSheetIndex(0);
		$fext = '';
		switch($this->getProperty('format',0)){
			case 2:
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
				$this->fext = 'xlsx';
				break;
			case 1:
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
				$this->fext = 'xls';
				break;
		}
		$objWriter->save(dirname(dirname(dirname(dirname(__file__)))).'/tmp/'.$timestamp.'.'.$this->fext);
	}
	
	function getActionURL(){
		$action_url = '';
		$manager_url = $this->modx->getOption('manager_url');
		$version = $this->modx->getOption('settings_version');
		if($version < 2.3){
			$action = $this->modx->getObject('modAction',array('controller'=>'getfile','namespace'=>'xodus'));
			$action_id = $action->get('id');
			$action_url = '?a='.$action_id;
		}else{
			$action_url = '?action=getFile&namespace=xodus';
		}
		//$action_url = str_replace('/','\/',$action_url);
		return $action_url;
	}
	
	function getGender($genderid){
		switch($genderid){
			case 0:
				$gender = '';
				break;
			case 1:
				$gender = 'Male';
				break;
			case 2:
				$gender = 'Female';
				break;	
		}
		return $gender;	
	}
}

return 'XodusGetGroupUserListProcessor';