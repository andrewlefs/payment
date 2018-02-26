<?php

class Common_hook {

    public function hook_exception_handler() {
        set_error_handler('_hook_exception_handler');
    }

}

function _hook_exception_handler($severity, $message, $filepath, $line) {
    if ($severity == E_STRICT) {
        return;
    }
    $_error = & load_class('Exceptions', 'core');
    if (($severity & error_reporting()) == $severity) {
        for ($i = ob_get_level(); $i > 0; $i--) {
            @ob_end_clean();
        }
        $_error->show_php_error($severity, $message, $filepath, $line);
    }
    if (config_item('log_threshold') == 0) {
        return;
    }
    if (($severity & error_reporting()) == $severity) {
        $_error->log_exception($severity, $message, $filepath, $line);
    }
}
