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

class Xodus {
    public $modx;
    public $config = array();
    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;
 
        $basePath = $this->modx->getOption('xodus.core_path',$config,$this->modx->getOption('core_path').'components/xodus/');
        $assetsUrl = $this->modx->getOption('xodus.assets_url',$config,$this->modx->getOption('assets_url').'components/xodus/');
        $this->config = array_merge(array(
            'basePath' => $basePath,
            'corePath' => $basePath,
            'modelPath' => $basePath.'model/',
            'processorsPath' => $basePath.'processors/',
            'templatesPath' => $basePath.'templates/',
            'chunksPath' => $basePath.'elements/chunks/',
            'jsUrl' => $assetsUrl.'js/',
            'cssUrl' => $assetsUrl.'css/',
            'assetsUrl' => $assetsUrl,
            'connectorUrl' => $assetsUrl.'connector.php',
        ),$config);
 
    }

public function getChunk($name,$properties = array()) {
    $chunk = null;
    if (!isset($this->chunks[$name])) {
        $chunk = $this->_getTplChunk($name);
        if (empty($chunk)) {
            $chunk = $this->modx->getObject('modChunk',array('name' => $name));
            if ($chunk == false) return false;
        }
        $this->chunks[$name] = $chunk->getContent();
    } else {
        $o = $this->chunks[$name];
        $chunk = $this->modx->newObject('modChunk');
        $chunk->setContent($o);
    }
    $chunk->setCacheable(false);
    return $chunk->process($properties);
}
 
private function _getTplChunk($name,$postfix = '.chunk.tpl') {
    $chunk = false;
    $f = $this->config['chunksPath'].strtolower($name).$postfix;
    if (file_exists($f)) {
        $o = file_get_contents($f);
        $chunk = $this->modx->newObject('modChunk');
        $chunk->set('name',$name);
        $chunk->setContent($o);
    }
    return $chunk;
}
}