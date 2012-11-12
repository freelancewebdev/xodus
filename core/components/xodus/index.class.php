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

require_once dirname(__FILE__) . '/model/xodus/xodus.class.php';
abstract class XodusManagerController extends modExtraManagerController {
    /** @var Doodles $xodus */
    public $xodus;
    public function initialize() {
		$this->xodus = new Xodus($this->modx);
		if(!$this->canWrite(dirname(__file__).'/tmp/')){
			$this->modx->lexicon->load('xodus:default');
        
			echo '<h2>'.$this->modx->lexicon('xodus.error').'</h2>';
				echo '<p>'.$this->modx->lexicon('xodus.fix_write').': '.$this->modx->getOption('core_path').'components/xodus/tmp/.</p>';
					
		}
        $this->addJavascript($this->xodus->config['jsUrl'].'mgr/xodus.js');
        
		$this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            Xodus.config = '.$this->modx->toJSON($this->xodus->config).';
        });
        </script>');
        return parent::initialize();
    }
    public function getLanguageTopics() {
        return array('xodus:default');
    }
    public function checkPermissions() { return true;}
	
	
}
class IndexManagerController extends XodusManagerController {
    public static function getDefaultController() { return 'home'; }
}