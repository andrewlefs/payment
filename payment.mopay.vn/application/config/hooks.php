<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$hook['pre_controller'][] = array(
    'class' => 'Common_hook',
    'function' => 'hook_exception_handler',
    'filename' => 'Common_hook.php',
    'filepath' => 'hooks');
