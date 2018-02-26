<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MEAPI_Pagination extends CI_Pagination {

    function create_links() {
        // If our item count or per-page total is zero there is no need to continue.
        $total_rows = $this->total_rows;
        if ($total_rows == 0) {
            return '';
        }

        // Calculate the total number of pages
        $CI = & get_instance();
        $_quantity = $CI->_quantity;
        $per_page = $this->per_page;
        $num_pages = ceil($total_rows / $per_page);

        // Is there only one page? Hm... nothing more to do here then.
        if ($num_pages == 1) {
            return '';
        }

        // Determine the current page number.
        $_offset = $CI->_offset;
        $cur_page = $_quantity == -1 ? -1 : floor($CI->_offset / $per_page) + 1;

        // And here we go...
        $output = '';

        // Render the "First" link
        if ($cur_page != 1) {
            $i = 0;
            $output .= $this->first_tag_open . $i . $this->first_tag_close;
        } else {
            $output .= "<li class='null'></li>";
        }

        // Render the "previous" link
        if ($cur_page != 1 && $_quantity != -1) {
            $i = $_offset - $per_page;
            $output .= $this->prev_tag_open . $i . $this->prev_tag_close;
        } else {
            $output .= "<li class='null'></li>";
        }

        // Write the digit links
        $output .= "<select tabindex='-1'>";
        $start = 1;
        $end = $per_page;
        if ($_quantity == -1)
            $output .= '<option selected="selected" > - </option>';
        for ($loop = 1; $loop <= $num_pages; $loop++) {
            if ($end > $total_rows)
                $end = $total_rows;
            $output .= '<option value="' . (($loop - 1) * $per_page) . '" ' . ($loop == $cur_page ? "selected='selected'" : "") . ' >Trang ' . $loop . '/' . $num_pages . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $start . "-" . $end . '/' . $total_rows . '</option>';
            $start+=$per_page;
            $end+=$per_page;
        }
        $output .= "</select>";

        // Render the "next" link
        if ($cur_page != $num_pages && $_quantity != -1) {
            $i = $_offset + $per_page;
            $output .= $this->next_tag_open . $i . $this->next_tag_close;
        } else {
            $output .= "<li class='null'></li>";
        }

        // Render the "Last" link
        if ($cur_page != $num_pages) {
            $i = ($num_pages - 1) * $per_page;
            $output .= $this->last_tag_open . $i . $this->last_tag_close;
        } else {
            $output .= "<li class='null'></li>";
        }

        // Add the wrapper HTML if exists
        $output = $this->full_tag_open . $output . str_replace(array("{class}", "{style}"), array(($_quantity == -1 ? "ui-state-hover" : "icon"), ($_quantity == -1 ? "cursor:default" : "")), $this->full_tag_close);

        return $output;
    }

}
