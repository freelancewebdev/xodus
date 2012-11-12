<?php
$action= $modx->newObject('modAction');
$action->fromArray(array(
    'id' => 1,
    'namespace' => 'xodus',
    'parent' => 0,
    'controller' => 'getfile',
    'haslayout' => true,
    'lang_topics' => 'xodus:default',
    'assets' => '',
),'',true,true);
return $action;