<?php

class Language {

    public $langdb;
    private $lang_choice;
    private $app_choice;

    public function init($language_code, $app = 'default') {
        if (empty($this->langdb[$app]) === TRUE) {
            if (!@include_once APPPATH . 'third_party/MEAPI/Languages/language_' . $app . '.php') {
                @include_once APPPATH . 'third_party/MEAPI/Languages/language_default' . '.php';
            }

            $this->langdb[$app] = $language;

        }
        $this->lang_choice = empty($this->langdb[$app][$language_code]) ? 'default' : $language_code;
        $this->app_choice = $app;
    }

    public function item($item_id, $data = NULL) {
        $msg = $this->langdb[$this->app_choice][$this->lang_choice][$item_id];
        if (empty($data) === FALSE) {
            foreach ($data as $key => $value) {
                $msg = str_replace('{' . $key . '}', $value, $msg);
            }
        }
        //$check = preg_match_all('/\\{([a-zA-Z0-9_]+)\\}/ims', $msg, $matches);
        return $msg;
    }
}