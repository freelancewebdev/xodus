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

class XodusGetfileManagerController extends XodusManagerController {
    public function process(array $scriptProperties = array()) {
 
    }
	
	public function render(){
		$this->failure('');
		$this->loadHeader = false;
		$this->loadFooter = false;
		$this->isFailure = false;
		$this->failureMessage = '';
		$this->content = '';
		$this->modx->lexicon->load('xodus:default');
		$core_path = dirname(dirname(__FILE__));
		$file_path = $core_path.'/tmp/'.$_GET['f'];
		if(file_exists($file_path)){
			$filepts = explode('.',$_GET['f']);
			$ext = $filepts[1];
			$mimetype = '';
			switch($ext){
				case 'csv':
					$mimetype = 'text/csv';
					break;
				case 'xls':
					$mimetype = 'application/excel';
					break;
				case 'xlsx':
					$mimetype = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
					break;	
			}
			header("Content-type: application/force-download");  
            header('Content-Disposition: inline; filename="'.$_GET['f'].'"');  
            header("Content-Transfer-Encoding: Binary"); 
		 	header("Content-length: ".filesize($file_path));  
            header('Content-Type: '.$mimetype);  
            header('Content-Disposition: attachment; filename="'.$_GET['f'].'"');  
            readfile($file_path);  
			unlink($file_path);
			return ' ';
		}else{
			$this->modx->lexicon->load('xodus:default');
			$this->content = '<html><title>'.$this->modx->lexicon('xodus').' '.$this->modx->lexicon('xodus.error').'</title><head></head><body>
			<h1>'.$this->modx->lexicon('xodus').' '.$this->modx->lexicon('xodus.error').'</h1>
			<p>'.$this->modx->lexicon('xodus.error_text').'. <a href="javascript:history.go(-1);">'.$this->modx->lexicon('xodus.return_link_text').'</a>.</p>
			</body></html>';
			
		return $this->content;
		}
	}
	
	public function failure($message){
		$this->isFailure = false;
		$this->failureMessage = '';	
	}
	
}