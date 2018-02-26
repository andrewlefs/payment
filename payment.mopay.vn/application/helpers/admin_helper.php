<?php
//ChinhLD
function printr($data){
    echo '<pre>';
        print_r($data);
    echo '</pre>';
    exit;
}

/**
 * Convert ement to string in array
 * @param $value
 * @param $key
 */
function callback_convert_string(&$value,$key){
    settype($value,'string');
}

function _script_tag($src) {
    $CI = & get_instance();
    $script = "<script language='javascript' type='text/javascript' ";
    if (strpos($src, '://') !== false)
        $script .= " src='" . $src . "' ";
    else
        $script .= " src='" . $CI->config->base_url($src) . "' ";
    $script .= " ></script>\n";
    return $script;
}

function _link_tag($src) {
    $CI = & get_instance();
    $link = '<link rel="stylesheet" ';
    if (strpos($src, '://') !== false)
        $link .= " href='" . $src . "' ";
    else
        $link .= " href='" . $CI->config->base_url($src) . "' ";
    $link .= " >\n";
    return $link;
}

function _icon_get($span_class = '', $title = '', $url = '', $span_string = '', $li_string = '', $a_string = '') {
    return _icon("", $span_class, $title, "get", $url, "", $span_string, $li_string, $a_string);
}

function _icon_post($li_class = "", $span_class = '', $title = '', $form_action = '', $value_action = "", $span_string = '', $li_string = '') {
    return _icon($li_class, $span_class, $title, "post", $form_action, $value_action, $span_string, $li_string, "");
}

function _icon($li_class, $span_class, $title, $method, $form_action, $value_action, $span_string, $li_string, $a_string) {
    $string = '';
    if ($method == "get") {
        $string .= "<a href='" . $form_action . "' " . $a_string . " >";
        $form_action = "";
    }
    if ($method == "post") {
        $li_class .= " post ";
    }
    $string .= '<li class="ui-state-default ui-corner-all icon ' . $li_class . '" ' . $li_string . ' value_action="' . $value_action . '" form_action="' . $form_action . '" ><span title="' . $title . '" ' . $span_string . ' class="ui-icon ' . $span_class . '"></span></li>';
    if ($method == "get") {
        $string .= "</a>";
    }
    return $string;
}

/* <div control> */

function _div_text($config, $value) {
    $label = $config [LABEL];
    $string = "<div class='div_control'><label>$label</label><div class='ctrl " . CTRL_TEXT . "'>" . _render_text($config, $value) . "</div>" . DIV_CLEAR . "</div>";
    return $string;
}

function _div_input_type_hidden($config, $value) {
    $label = $config [LABEL];
    $string = "<div class='div_control'><div class='ctrl " . CTRL_INPUT_TYPE_HIDDEN . "'>" . _render_input_type_hidden($config, $value) . "</div>" . DIV_CLEAR . "</div>";
    return $string;
}

function _div_textarea($config, $value) {
    $label = $config [LABEL];
    $field = $config [FIELD];
    $class = $config [CLASS_CTRL];
    $string = "<div class='div_control $class'><label for='$field'>$label</label><div class='ctrl " . CTRL_TEXTAREA . "'>" . _render_textarea($config, $value) . "</div>" . DIV_CLEAR . "</div>";
    return $string;
}

function _div_ckfinder($config, $value) {
    $field = $config [FIELD];
    $label = $config [LABEL];
    $class = $config [CLASS_CTRL];
    $string = "<div class='div_control $class'><label for='$field'>" . $label . "</label><div class='ctrl " . CTRL_CKFINDER . "'>" . _render_ckfinder($config, $value) . "</div>" . DIV_CLEAR . "</div>";
    return $string;
}

//test
function _div_datepicker($config, $value) {
    $field = $config [FIELD];
    $label = $config [LABEL];
    $string = "<div class='div_control'><label for='$field'>$label</label><div class='ctrl " . CTRL_DATEPICKER . "'>" . _render_datepicker($config, $value) . "</div>" . DIV_CLEAR . "</div>";
    return $string;
}

//test
function _div_timepicker($config, $value) {
    $field = $config [FIELD];
    $label = $config [LABEL];
    $string = "<div class='div_control'><label for='$field'>$label</label><div class='ctrl " . CTRL_TIMEPICKER . "'>" . _render_timepicker($config, $value) . "</div>" . DIV_CLEAR . "</div>";
    return $string;
}

function _div_input_type_text($config, $value) {
    $label = $config [LABEL];
    $field = $config[FIELD];
    $class = $config [CLASS_CTRL];
    $string = "<div class='div_control $class'><label for='$field'>" . $label . "</label><div class='ctrl " . CTRL_INPUT_TYPE_TEXT . "'>" . _render_input_type_text($config, $value) . "</div>" . DIV_CLEAR . "</div>";
    return $string;
}

function _div_ckeditor($config, $value) {
    $label = $config [LABEL];
    $string = "<div class='div_control'><label>$label</label><div class='ctrl " . CTRL_CKEDITOR . "' >" . _render_ckeditor($config, $value) . "</div>" . DIV_CLEAR . "</div>";
    return $string;
}

function _div_input_type_checkbox($config, $value) {
    $field = $config [FIELD];
    $label = $config [LABEL];
    $string = "<div class='div_control' ><label for='$field'>$label</label><div class='ctrl " . CTRL_INPUT_TYPE_CHECKBOX . "'>" . _render_input_type_checkbox($config, $value) . "</div>" . DIV_CLEAR . "</div>";
    return $string;
}

function _div_input_type_radio($config, $value) {
    $field = $config [FIELD];
    $label = $config [LABEL];
    $class = $config [CLASS_CTRL];
    $string = "<div class='div_control $class' ><label for='$field'>$label</label><div class='ctrl " . CTRL_INPUT_TYPE_RADIO . "'>" . _render_input_type_radio($config, $value) . "</div>" . DIV_CLEAR . "</div>";
    return $string;
}

function _div_spinner($config, $value) {
    $field = $config [FIELD];
    $label = $config [LABEL];
    $class = $config [CLASS_CTRL];
    $string = "<div class='div_control $class' ><label for='$field'>$label</label><div class='ctrl spinner' >" . _render_spinner($config, $value) . "</div>" . DIV_CLEAR . "</div>";
    return $string;
}

function _div_tag($config, $value) {
    $field = $config [FIELD];
    $label = $config [LABEL];
    $validate = _isset_or($config [CTRL_TAG_VALIDATE], CTRL_TAG_VALIDATE_UNCHECK);
    $string = "<div class='div_control' ><label for='$field'>$label</label><div class='ctrl tag' " . CTRL_TAG_VALIDATE . "='" . $validate . "' >" . _render_tag($config, $value) . "</div>" . DIV_CLEAR . "</div>";
    return $string;
}

function _div_autocomplete($config, $value) {
    $field = $config [FIELD];
    $label = $config [LABEL];
    $validate = _isset_or($config [CTRL_AUTOCOMPLETE_VALIDATE], CTRL_AUTOCOMPLETE_VALIDATE_UNCHECK);
    $string = "<div class='div_control' ><label for='$field'>$label</label><div class='ctrl autocomplete' " . CTRL_AUTOCOMPLETE_VALIDATE . "='" . $validate . "' >" . _render_autocomplete($config, $value) . "</div>" . DIV_CLEAR . "</div>";
    return $string;
}

function _div_input_type_password($config) {
    $field = $config [FIELD];
    $label = $config [LABEL];
    $string = "<div class='div_control' ><label for='$field'>" . $label . "</label><div class='ctrl input_type_password'>" . _render_input_type_password($config) . "</div>" . DIV_CLEAR . "</div>";
    return $string;
}

/* function _li_note(&$note)
  {
  $string = "";
  if (isset ( $note ))
  {
  $string .= '<li class="ui-state-highlight tooltip" tooltip="' . $note . '"><span class="ui-icon ui-icon-info"></span></li>';
  }
  return $string;
  } */
/* </div control> */

/* </control> */

function _render_text($config, $value) {
    $field = $config [FIELD];
    $string = set_value($config [FIELD], $value);
    return $string;
}

function _render_spinner($config, $value) {
    $step = _isset_or($config [CTRL_SPINNER_STEP], 1);
    $field = $config [FIELD];
    $string = "<input id='$field' name='$field' type='text' value='" . set_value($field, $value) . "' " . CTRL_SPINNER_STEP . "='" . $step . "' />";
    return $string;
}

function _render_ckeditor($config, $value) {
    $field = $config [FIELD];
    $type = _isset_or($config [CTRL_CKEDITOR_TYPE], CTRL_CKEDITOR_TYPE_BASIC);
    $type_html = CTRL_CKEDITOR_TYPE . "='" . $type . "'";
    $string = "<textarea id='$field' $type_html name='$field' >" . set_value($field, $value) . "</textarea>";
    return $string;
}

function _render_ckfinder($config, $value) {
    $field = $config [FIELD];
    $note = $config [NOTE];
    $startup_path = _isset_or($config [CTRL_CKFINDER_PATH], CTRL_CKFINDER_PATH_IMAGES);
    $string = "<input type='text' id='$field' tabindex='-1' " . ($note == "" ? "" : " class='tooltip' title=\"$note\" ") . " name='$field' value='" . set_value($field, $value) . "'/><div for='$field' class='tooltip'></div><input type='button' " . CTRL_CKFINDER_PATH . "='" . $startup_path . "' for='$field' value='" . LABEL_BROWSE . "'/>";
    return $string;
}

function _render_input_type_hidden($config, $value) {
    $field = $config [FIELD];
    $string = "<input id='$field' name='$field' type='hidden' value='" . set_value($field, $value) . "' />";
    return $string;
}

function _render_input_type_radio($config, $value) {
    $options = _isset_or($config [CTRL_INPUT_TYPE_RADIO_OPTIONS], array());
    $field = $config [FIELD];
    $string = "";
    if (count($options) > 1) {
        $i = 0;
        $value = set_value($field, $value);
        foreach ($options as $k => $v) {
            $checked = (isset($value) && $value == $k) ? "checked='checked'" : "";
            $string .= "<label class='tooltip' title='" . $v . "'><input $checked type='radio' name='" . $field . "' value='" . $k . "' id='" . $field . "_$i' />" . $v . "</label>";
            $i ++;
        }
    }
    return $string;
}

function _render_input_type_checkbox($config, $value) {
    $options = _isset_or($config [CTRL_INPUT_TYPE_CHECKBOX_OPTIONS], array());
    $field = $config [FIELD];
    $string = "";
    if (count($options) == 1) {
        foreach ($options as $key => $value)
            $string .= "<label class='tooltip' title='" . $value . "' style='width:100%;padding-right:0px'><input type='checkbox' name='" . $field . "' value='" . $key . "' id='$field' />" . $value . "</label>";
    } else {
        $i = 0;
        foreach ($options as $key => $value) {
            $string .= "<label class='tooltip' title='" . $value . "'><input type='checkbox' name='" . $field . "[$i]' value='" . $key . "' id='" . $field . "_$i' />" . $value . "</label>";
            $i ++;
        }
    }
    return $string;
}

function _render_input_type_text($config, $value) {
    $note = $config [NOTE];
    $field = $config [FIELD];
    $string = "<input id='$field' name='$field' " . ($note == "" ? "" : " class='tooltip' title=\"$note\" ") . " type='text' value='" . set_value($field, $value) . "' />";
    return $string;
}

function _render_autocomplete($config, $value) {
    $field = $config [FIELD];
    $options = _isset_or($config [CTRL_AUTOCOMPLETE_OPTIONS], array());
    $string = '<select id="' . $field . '" name="' . $field . '">';
    foreach ($options as $k => $v) {
        $string .= '<option ' . ($k == set_value($field, $value) ? "selected='selected'" : "") . ' value="' . $k . '">' . $v . '</option>';
    }
    $string .= '</select>';
    return $string;
}

function _render_tag($config, $value) {
    $field = $config [FIELD];
    $options = _isset_or($config [CTRL_TAG_OPTIONS], array());
    $script = "<script>";
    $script .= 'availableTags["' . $field . '"]=' . json_encode(array_values($options));
    $script .= ";</script>";
    $CI = & get_instance();
    $CI->prepend_script($script);
    $string = '<input id="' . $field . '" name="' . $field . '" value="' . set_value($field, $value) . '"/>';
    return $string;
}

function _render_textarea($config, $value) {
    $field = $config [FIELD];
    $string = "<textarea id='$field' name='$field' >" . set_value($field, $value) . "</textarea>";
    return $string;
}

function _render_input_type_password($config) {
    $field = $config [FIELD];
    $note = $config [NOTE];
    $string = "<input id='$field' name='$field' " . ($note == "" ? "" : " class='tooltip' title=\"$note\" ") . " type='password' value='' autocomplete='off'/>";
    return $string;
}

function _render_timepicker($config, $value) {
    $field = $config [FIELD];
    $rules = $config [RULES];
    $required = strpos($config [RULES], "required") === false ? 0 : 1;
    $date = set_value($field, _parse_utc_to_date($value, FORMAT_DATE));
    if ($date == "") {
        $date = date(FORMAT_DATE);
        $hour = date("H");
        $minute = date("i");
        $check = "";
    } else {
        $hour = set_value($field . "__hour", _parse_utc_to_date($value, "H"));
        $minute = set_value($field . "__minute", _parse_utc_to_date($value, "i"));
        $check = "checked='checked'";
    }
    if (!$required) {
        $string = "<input type='checkbox' name='" . $field . "__check' value='1' $check />";
    }
    $string .= '<input id="' . $field . '" name="' . $field . '" type="text" class="date" value="' . $date . '"/><li class="icon ui-state-default" for="' . $field . '"><span class="ui-icon ui-icon-calendar"></span></li>';
    $string .= '<input type="text" class="hour" for="' . $field . '" name="' . $field . '__hour" value="' . $hour . '"/><input type="text" name="' . $field . '__minute" value="' . $minute . '" class="minute" for="' . $field . '"/>';
    return $string;
}

function _render_datepicker($config, $value) {
    $field = $config [FIELD];
    $rules = $config [RULES];
    $required = strpos($config [RULES], "required") === false ? 0 : 1;
    $date = set_value($field, _parse_utc_to_date($value, FORMAT_DATE));
    if ($date == "") {
        $date = date(FORMAT_DATE);
        $check = "";
    } else {
        $check = "checked='checked'";
    }
    if (!$required) {
        $string = "<input type='checkbox' name='" . $field . "__check' value='1' $check />";
    }
    $string = '<input id="' . $field . '" name="' . $field . '" type="text" class="date" value="' . $date . '"/><li class="icon ui-state-default" for="' . $field . '"><span class="ui-icon ui-icon-calendar"></span></li>';
    return $string;
}

//href
//onclick
//submit ajax/other method
function _render_input_type_button($config) {
    $field = $config [FIELD];
    $label = $config [LABEL];
    $value_action = _isset_or($config [CTRL_INPUT_TYPE_BUTTON_VALUE], $label);
    $href = _isset_or($config [CTRL_INPUT_TYPE_BUTTON_HREF], '');
    $onclick = _isset_or($config [CTRL_INPUT_TYPE_BUTTON_ONCLICK], '');
    $class = _isset_or($config [CTRL_INPUT_TYPE_BUTTON_CLASS], '');
    $class = $class == "" ? "" : " class='$class' ";
    $method = _isset_or($config [CTRL_INPUT_TYPE_BUTTON_METHOD], '');
    if ($method != "") {
        $CI = & get_instance();
        $method = " method='" . base_url($_GET['control'] . "/" . $method) . "' ";
    }
    if ($href != '') {
        $string = "<a href='$href'><input name='$field' type='button' value='$label' /></a>";
    } else if ($onclick != '') {
        $string = "<input name='$field' type='button' value='$label' onclick=\"$onclick\" />";
    } else {
        $string = "<input name='$field' $class $method type='button' value_action='$value_action' value='$label' />";
    }
    return $string;
}

function _render_input_type_submit($config) {
    $label = $config [LABEL];
    $class = _isset_or($config [CTRL_INPUT_TYPE_SUBMIT_CLASS], '');
    $class = $class == "" ? "" : " class='$class' ";
    $string = "<input name='action' type='submit' value='" . $label . "' $class />";
    return $string;
}

function _render_input_type_reset($config) {
    $label = _isset_or($config [LABEL], LABEL_RESET);
    $string = "<input type='reset' value='" . $label . "' />";
    return $string;
}

function _render_back($config) {
    $config [LABEL] = _isset_or($config [LABEL], LABEL_BACK);
    $CI = & get_instance();
    $class = $_GET['control'];
    $config [CTRL_INPUT_TYPE_BUTTON_HREF] = _isset_or($config [CTRL_BACK_HREF], base_url($class . "/index"));
    return _render_input_type_button($config);
}

function _render_close($config) {
    $config [LABEL] = _isset_or($config [LABEL], LABEL_CLOSE);
    $CI = & get_instance();
    $class = $_GET['control'];
    $config [CTRL_INPUT_TYPE_BUTTON_ONCLICK] = "window.close();";
    return _render_input_type_button($config);
}

function _render_insert($config) {
    $CI = & get_instance();
    $class = $_GET['control'];
    $config [LABEL] = _isset_or($config [LABEL], LABEL_INSERT);
    $config [CTRL_INPUT_TYPE_BUTTON_HREF] = _isset_or($config [CTRL_INSERT_HREF], base_url($class . "/insert"));
    return _render_input_type_button($config);
}

function _render_delete($config) {
    $config [LABEL] = _isset_or($config [LABEL], LABEL_DELETE);
    $config [CTRL_INPUT_TYPE_BUTTON_CLASS] = CTRL_INPUT_TYPE_BUTTON_CLASS_CONFIRM . " " . CTRL_INPUT_TYPE_BUTTON_CLASS_VALID_CHECK . " " . CTRL_INPUT_TYPE_BUTTON_CLASS_SUBMIT;
    $config [CTRL_INPUT_TYPE_BUTTON_METHOD] = CTRL_INPUT_TYPE_BUTTON_METHOD_DELETE;
    return _render_input_type_button($config);
}

function _render_update($config) {
    $CI = & get_instance();
    $class = $CI->router->class;
    $config [LABEL] = _isset_or($config [LABEL], LABEL_UPDATE);
    $config [CTRL_INPUT_TYPE_BUTTON_HREF] = _isset_or($config [CTRL_UPDATE_HREF], base_url($class . "/update/" . $CI->uri->segment(4)));
    return _render_input_type_button($config);
}

function _render_search($config) {
    $config [LABEL] = _isset_or($config [LABEL], LABEL_SEARCH);
    return _render_input_type_submit($config);
}

function _render_save_insert($config) {
    $config [LABEL] = _isset_or($config [LABEL], LABEL_SAVE_INSERT);
    return _render_input_type_submit($config);
}

function _render_save_update($config) {
    $config [LABEL] = _isset_or($config [LABEL], LABEL_SAVE_UPDATE);
    return _render_input_type_submit($config);
}

function _render_inactive($config) {
    $config [LABEL] = _isset_or($config [LABEL], LABEL_INACTIVE);
    $config [CTRL_INPUT_TYPE_BUTTON_CLASS] = CTRL_INPUT_TYPE_BUTTON_CLASS_VALID_CHECK . " " . CTRL_INPUT_TYPE_BUTTON_CLASS_SUBMIT;
    $config [CTRL_INPUT_TYPE_BUTTON_METHOD] = CTRL_INPUT_TYPE_BUTTON_METHOD_ACTIVE;
    return _render_input_type_button($config);
}

function _render_active($config) {
    $config [LABEL] = _isset_or($config [LABEL], LABEL_ACTIVE);
    $config [CTRL_INPUT_TYPE_BUTTON_CLASS] = CTRL_INPUT_TYPE_BUTTON_CLASS_VALID_CHECK . " " . CTRL_INPUT_TYPE_BUTTON_CLASS_SUBMIT;
    $config [CTRL_INPUT_TYPE_BUTTON_METHOD] = CTRL_INPUT_TYPE_BUTTON_METHOD_ACTIVE;
    return _render_input_type_button($config);
}

function _render_reset($config) {
    $config [LABEL] = _isset_or($config [LABEL], LABEL_RESET);
    return _render_input_type_reset($config);
}

/* </control> */

/* <table> */

//NEW
function _itd_switch($config, $value) {
    $config[CLASS_TD].=" icons";
    $field = $config[FIELD];
    $inline = "{" . $field . "}";
    return _itd($config, $inline);
}

function _itd_money($config, $value) {
    return _itd_number($config, $value);
}

function _itd_number($config) {
    $config[CLASS_TD].=" right";
    $field = $config[FIELD];
    $inline = "{" . $field . "}";
    return _itd($config, $inline);
}

function _itd_text(&$config) {
    $config[CLASS_TD].=" left";
    $field = $config[FIELD];
    $inline = "{" . $field . "}";
    return _itd($config, $inline);
}

function _itd_image($config) {
    $field = $config[FIELD];
    $config[CLASS_TD].=" image";
    $inline .= "<div style=\"{_thumbnail_" . $field . "}\" class='tooltip' tooltip=\"{" . $field . "}\"></div>";
    return _itd($config, $inline);
}

function _itd_id($config) {
    $field = $config[FIELD];
    $col_check = _isset_or($config[COL_ID_CHECK], COL_ID_CHECK_ACTIVE);
    $have_check = $col_check == COL_ID_CHECK_ACTIVE;
    $inline = "";
    if ($have_check)
        $inline .= "<label><input type='checkbox' name='id[]' value='{" . $field . "}' />";
    $inline.="{" . $field . "}";
    if ($have_check)
        $inline .= "</label>";
    $config[CLASS_TD].=" no";
    return _itd($config, $inline);
}

function _itd_update($config) {
    $CI = & get_instance();
    $class = $_GET['control'];
    $label = LABEL_UPDATE;
    $method = METHOD_UPDATE;
    $form_action = base_url($class . "/" . $method . "/{" . ID . "}");
    return _icon_get("ui-icon-pencil", $label, $form_action);
}

function _itd_detail($config) {
    $CI = & get_instance();
    $class = $_GET['control'];
    $label = LABEL_DETAIL;
    $method = METHOD_DETAIL;
    $form_action = base_url($class . "/" . $method . "/{" . ID . "}");
    return _icon_get("ui-icon-document", $label, $form_action, '', '', " target='_blank' ");
}

function _itd_get($config) {
    $class = $config[COL_GET_CLASS];
    $label = $config[LABEL];
    $link = $config[COL_GET_LINK];
    return _icon_get($class, $label, $link);
}

function _itd_manage($config) {
    $field = $config[FIELD];
    $config[CLASS_TD].=" icons";
    $inline = "";
    $options = _isset_or($config[COL_MANAGE_OPTIONS], array());
    foreach ($options as $k => $v) {
        $function = "_itd_" . $v[COL];
        $inline.=$function($v);
    }
    return _itd($config, $inline);
}

function _itd($config, $inline) {
    $note = $config[NOTE_TD];
    $class = $config[CLASS_TD];
    $html = $config[HTML_TD];
    $width = $config[WIDTH];
    if ($note != "") {
        $class.=" tooltip";
        $html.=" tooltip=" . $note . "' ";
    }
    if ($class != "") {
        $html.=" class='" . $class . "' ";
    }
    $string = '<td ' . $html . '>' . $inline . '</td>';
    return $string;
}

function _itd_date(&$config) {
    $config[CLASS_TD].=" center";
    $field = $config[FIELD];
    $inline = "{" . $field . "}";
    return _itd($config, $inline);
}

function _itd_time(&$config) {
    $config[CLASS_TD].=" center";
    $field = $config[FIELD];
    $inline = "{" . $field . "}";
    return _itd($config, $inline);
}

function _itd_delete($config) {
    $label = LABEL_DELETE;
    $method = METHOD_DELETE;
    $CI = & get_instance();
    $class = $_GET['control'];
    $form_action = base_url($class . "/" . $method);
    return _icon_post("check_current_row confirm", "ui-icon-trash", $label, $form_action);
}

function _td_id($id, $have_check = true, $link = "") {
    $string = "<td class='no'>";
    if ($have_check)
        $string .= "<label><input type='checkbox' name='id[]' value='" . $id . "' />";
    if ($link != "")
        $string .= "<a target='_blank' href='" . $link . "'>";
    $string .= $id;
    if ($link != "")
        $string .= "</a>";
    if ($have_check)
        $string .= "</label>";
    $string .= "</td>";
    return $string;
}

function _td_no($no, $have_check = true) {
    $string = "<td class='no'>";
    if ($have_check)
        $string .= "<label><input type='checkbox' name='id[]' value='" . $no . "' />";
    $string .= $no;
    if ($have_check)
        $string .= "</label>";
    $string .= "</td>";
    return $string;
}

function _td_money($value) {
    $string = "<td class='right'>";
    $string .= number_format($value, 0, "", ".");
    $string .= "</td>";
    return $string;
}

function _td_short_date($value) {
    $string = "<td class='center'>";
    $string .= _parse_utc_to_date($value, FORMAT_DATE);
    $string .= "</td>";
    return $string;
}

function _td_long_date($value) {
    $string = "<td class='center'>";
    $string .= _parse_utc_to_date($value, FORMAT_DATE_TIME);
    $string .= "</td>";
    return $string;
}

function _td_pass($string) {
    $result = "<td class='left'>";
    if ($string != "") {
        for ($i = 0; $i < strlen($string); $i ++) {
            $result .= "*";
        }
    }
    $result .= "</td>";
    return $result;
}

function _td_text($text = "", $note = "") {
    $string = '<td class="tooltip" tooltip="' . strip_tags($note) . '">';
    $string .= $text;
    $string .= "</td>";
    return $string;
}

function _td_image($src) {
    $string = "<td class='image'>";
    if ($src != "") {
        if (strpos($src, "http://") !== false)
            $thumbnail = $src;
        else
            $thumbnail = str_replace("/images/", "/_thumbs/Images/", $src);
        $string .= "<div style=\"background-image:url('$thumbnail');\" class='tooltip' tooltip=\"<img src='$src'/>\"></div>";
    }
    else {
        $string .= '<div class="tooltip" tooltip="NO IMAGE"><li class="ui-state-default"><span class="ui-icon ui-icon-image"></span></li></div>';
    }
    $string .= "</td>";
    return $string;
}

function _th_field($field, $is_have_order = true, $add_html = "", $class = "") {
    $CI = & get_instance();

    //title
    $title = $CI->labels [$field];

    //order
    if ($is_have_order) {
        $tmp = _th_support_for_order($field);
        $add_html .= $tmp[0];
        $class .= $tmp[2];
        $icon = $tmp[1];
    }

    //note
    $note = $CI->notes [$field];
    if ($note != "") {
        $class .= " tooltip";
        $add_html .= " tooltip='" . $note . "' ";
    }

    //
    $class = $class == "" ? "" : " class='$class' ";
    $string = "<th $add_html $class >";
    $string .= $title . $icon;
    $string .= "</th>";
    return $string;
}

function _th_support_for_order($field_to_order_by) {
    $CI = & get_instance();
    $sub_string = "";
    $class = "";
    $icon = "";
    if ($field_to_order_by != "") {
        $class = " orderer";
        $_order = _isset_or($CI->_order, "");
        if ($_order == "" || strpos("," . $_order, "," . $field_to_order_by) === false) {
            $icon = '<div class="div-r ui-state-highlight"><span class="ui-icon ui-icon-carat-1-e"></span></div>';
            $by = "DESC";
        } else {
            if (strpos("," . $_order, "," . $field_to_order_by . " DESC") !== false) {
                $icon = '<div class="div-r"><span class="ui-icon ui-icon-carat-1-s"></span></div>';
                $by = "ASC";
            }
            if (strpos("," . $_order, "," . $field_to_order_by . " ASC") !== false) {
                $icon = '<div class="div-r"><span class="ui-icon ui-icon-carat-1-n"></span></div>';
                $by = "DESC";
            }
        }
        $sub_string .= " order='" . $field_to_order_by . " " . $by . "' ";
    }
    return array($sub_string, $icon, $class);
}

function _th($title = "", $field_to_order_by = "", $width = "", $note = "") {
    $class = "";
    $icon = "";
    $sub_string = "";
    $width = $width == "" ? "" : " style='width:{$width}px' ";
    $tmp = _th_support_for_order($field_to_order_by);
    $sub_string .= $tmp[0];
    $class .= $tmp[2];
    if ($note != "") {
        $class .= " tooltip";
        $sub_string .= " tooltip='" . $note . "' ";
    }
    $class = $class == "" ? "" : " class='$class' ";
    $string = "<th $width $class " . $sub_string . ">";
    $string .= $title . $tmp[1];
    $string .= "</th>";
    return $string;
}

//NEW
/* function _ith_no($have_check = true)
  {
  $string = "<th class='no'>";
  if ($have_check)
  $string .= "<label title='Chọn tất cả dòng'><input type='checkbox' />";
  $string .= "#";
  if ($have_check)
  $string .= "</label>";
  $string .= "</th>";
  return $string;
  } */

function _ith_id($config) {
    $field = $config[FIELD];
    $col_check = _isset_or($config[COL_ID_CHECK], COL_ID_CHECK_ACTIVE);
    $have_check = $col_check == COL_ID_CHECK_ACTIVE;
    $sub_string = "";
    $icon = "";
    $class = "";
    $tmp = _th_support_for_order($field);
    $sub_string .= $tmp[0];
    $class .= $tmp[2];
    $string = "<th class='no " . $class . "' " . $sub_string . ">";
    if ($have_check)
        $string .= "<label title='Chọn tất cả dòng'><input type='checkbox' />";
    $string .= "ID";
    if ($have_check)
        $string .= "</label>";
    $string .= $tmp[1];
    $string .= "</th>";
    return $string;
}

function _ith_money($config) {
    return _ith_text($config);
}

function _ith_date($config) {
    return _ith_text($config);
}

function _ith_time($config) {
    return _ith_text($config);
}

function _ith_image($config) {
    $config[CLASS_TH].=" image";
    $config[LABEL] = "";
    return _ith_text($config);
}

function _ith_number($config) {
    return _ith_text($config);
}

function _ith_switch($config) {
    $class = &$config[CLASS_TH];
    $class.= " small";
    return _ith_text($config);
}

function _ith_text($config) {
    $field = $config[FIELD];
    $note = $config[NOTE];
    $label = $config[LABEL];
    $class = $config[CLASS_TH];
    $icon = "";
    $html = $config[HTML_TH];
    $tmp = _th_support_for_order($field);
    $html .= $tmp[0];
    $class .= $tmp[2];
    if ($note != "") {
        $class .= " tooltip";
        $html .= " tooltip='" . $note . "' ";
    }
    if ($class != "") {
        $html.=" class='$class' ";
    }
    $string = "<th $html >";
    $string .= $label . $tmp[1];
    $string .= "</th>";
    return $string;
}

function _ith_manage($config) {
    $string = "<th class='icons'>" . LABEL_MANAGE . "</th>";
    return $string;
}

//stt dùng trong các báo cáo
function _th_no($have_check = true) {
    $string = "<th class='no'>";
    if ($have_check)
        $string .= "<label title='Chọn tất cả dòng'><input type='checkbox' />";
    $string .= "#";
    if ($have_check)
        $string .= "</label>";
    $string .= "</th>";
    return $string;
}

//id dùng trong bảng dữ liệu
function _th_id($have_check = true, $field_to_order_by = "id") {
    $sub_string = "";
    $icon = "";
    $class = "";
    $tmp = _th_support_for_order($field_to_order_by);
    $sub_string .= $tmp[0];
    $class .= $tmp[2];
    $string = "<th class='no " . $class . "' " . $sub_string . ">";
    if ($have_check)
        $string .= "<label title='Chọn tất cả dòng'><input type='checkbox' />";
    $string .= "ID";
    if ($have_check)
        $string .= "</label>";
    $string .= $tmp[1];
    $string .= "</th>";
    return $string;
}

/* </table> */

/* <toolbar> */

function _render_toolbar($toolbar, $position_of_bar) {
    $string = "";
    $CI = & get_instance();
    $method = $CI->router->method;
    foreach ($toolbar as $key => $value) {
        //var_dump($value);
        $control = $value [CTRL];
        switch ($control) {
            case CTRL_INACTIVE :
            case CTRL_ACTIVE :
            case CTRL_DELETE :
            case CTRL_INSERT :
                $position = _isset_or($value [POS], POS_IN_TOP_BAR);
                break;
            case CTRL_SEARCH :
                $position = _isset_or($value [POS], POS_IN_FORM);
                break;
            case CTRL_UPDATE :
            case CTRL_SAVE_UPDATE :
            case CTRL_SAVE_INSERT :
            case CTRL_BACK :
            default :
                $position = _isset_or($value [POS], POS_IN_BOTH);
        }
        if ($position == POS_IN_BOTH || $position_of_bar == $position) {
            $function = "_render_" . $control;
            $string .= $function($value);
        }
    }
    return $string;
}

/* </toolbar> */

function _parse_array_to_list($array, $key, $value) {
    $result = array();
    foreach ($array as $k => $v) {
        $result [$v [$key]] = $v [$value];
    }
    return $result;
}

function _number_format($object, $decimals = 0, $dec_point = ',', $thousands_sep = '.') {
    return number_format($object, $decimals, $dec_point, $thousands_sep);
}

function _parse_utc_to_date($string, $format = FORMAT_DATE_TIME) {
    if (!is_string($string) || $string == "")
        return "";
    $time = strtotime($string);
    return date($format, $time);
}

function _parse_date_to_utc($string = null, $hour_min_sec = null) {
    if (!is_string($string)) {
        $string = date("d/m/Y");
    }
    if (!is_string($hour_min_sec)) {
        $hour_min_sec = date("H:i:s");
    }
    $date = explode("/", $string);
    if (count($date) > 2) {
        $tmp = $date [2] . "-" . $date [1] . "-" . $date [0] . " " . $hour_min_sec;
        return $tmp;
    } else {
        return false;
    }
}

function _print() {
    echo "<pre>";
    foreach (func_get_args() as $k => $v) {
        var_dump($v);
    }
    echo "</pre>";
}

/* function _dump()
  {
  $CI = & get_instance ();
  $dump = "";
  foreach ( func_get_args () as $k => $v )
  {
  $dump .= "\n---------------------------------------\n";
  $dump .= var_export ( $v, true );
  }
  $CI->_dump = _isset_or ( $CI->_dump, "" );
  $CI->_dump .= $dump;
  } */

function _parse_string_to_alias($string) {
    $string = trim($string);
    //$string = str_replace ( array ("\r\n", "\r", "\t", "\n" ), '', $string );
    $string = preg_replace('/\s\s+/', ' ', $string);
    $string = mb_convert_case($string, MB_CASE_LOWER, 'utf-8');
    $chars = array(
        'a' => array(
            'a',
            'á',
            'à',
            'ả',
            'ạ',
            'ã',
            'â',
            'ấ',
            'ầ',
            'ẩ',
            'ậ',
            'ẫ',
            'ă',
            'ắ',
            'ằ',
            'ẳ',
            'ặ',
            'ẵ'
        ),
        'b' => array(
            'b'
        ),
        'c' => array(
            'c'
        ),
        'd' => array(
            'd',
            'đ'
        ),
        'e' => array(
            'e',
            'é',
            'è',
            'ẻ',
            'ẹ',
            'ẽ',
            'ê',
            'ế',
            'ề',
            'ể',
            'ệ',
            'ễ'
        ),
        'f' => array(
            'f'
        ),
        'g' => array(
            'g'
        ),
        'h' => array(
            'h'
        ),
        'j' => array(
            'j'
        ),
        'k' => array(
            'k'
        ),
        'l' => array(
            'l'
        ),
        'm' => array(
            'm'
        ),
        'n' => array(
            'n'
        ),
        'i' => array(
            'i',
            'í',
            'ì',
            'ỉ',
            'ị',
            'ĩ'
        ),
        'o' => array(
            'o',
            'ó',
            'ò',
            'ỏ',
            'ọ',
            'õ',
            'ơ',
            'ớ',
            'ờ',
            'ở',
            'ợ',
            'ỡ',
            'ô',
            'ố',
            'ồ',
            'ổ',
            'ộ',
            'ỗ'
        ),
        'u' => array(
            'u',
            'ú',
            'ù',
            'ủ',
            'ụ',
            'ũ',
            'ư',
            'ứ',
            'ừ',
            'ử',
            'ự',
            'ữ'
        ),
        'y' => array(
            'y',
            'ý',
            'ỳ',
            'ỵ',
            'ỷ',
            'ỹ'
        ),
        'q' => array(
            'q'
        ),
        'w' => array(
            'w'
        ),
        'r' => array(
            'r'
        ),
        't' => array(
            't'
        ),
        'p' => array(
            'p'
        ),
        's' => array(
            's'
        ),
        'z' => array(
            'z'
        ),
        'x' => array(
            'x'
        ),
        'v' => array(
            'v'
        ),
        '-' => array(
            ' '
        )
    );
    foreach ($chars as $k => $v)
        $string = str_replace($v, $k, $string);
    $string = preg_replace('/[^a-z0-9\s-.]/i', '', $string);
    return $string;
}

function _isset_or(&$value, $alternate = null) {
    return isset($value) ? $value : $alternate;
}

function _empty_string_or(&$value, $alternate = null) {
    return $value == "" ? $alternate : $value;
}

function _location($url, $message = null) {
    $CI = & get_instance();
    if ($message != null)
        $CI->session->set_flashdata('message', $message);
    redirect($url, 'location');
}

function _redirect($method, $message = null, $class = null) {
    $CI = & get_instance();
    if ($message != null)
        $CI->session->set_flashdata('message', $message);
    if ($class == null)
        $class = $_GET['control'];
    if ($method == null)
        $method = $_GET['func'];
    $url = base_url($class . "/" . $method);
    redirect($url, 'location');
}

?>