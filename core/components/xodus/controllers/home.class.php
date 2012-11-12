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

class XodusHomeManagerController extends XodusManagerController {
    public function process(array $scriptProperties = array()) {
 
    }
    public function getPageTitle() { return $this->modx->lexicon('exodus'); }
    public function loadCustomCssJs() {
    $this->addJavascript($this->xodus->config['jsUrl'].'mgr/widgets/xodus.panel.js');
        $this->addLastJavascript($this->xodus->config['jsUrl'].'mgr/sections/index.js');
    }
    public function getTemplateFile() { return $this->xodus->config['templatesPath'].'home.tpl'; }
	
	public static function canWrite($path) {
// check tmp file for read/write capabilities
    $rm = file_exists($path);
    $f = @fopen($path.'test.txt', 'a');
    if ($f===false)
        return false;
    fclose($f);
    if (!$rm)
        unlink($path.'test.txt');
    return true;
}
}