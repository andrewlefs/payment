<div class='toolbar'>
    <?php if ($this->session->userdata('is_login')) { ?>
        <!-- <div class="static">
                <button tabindex='-1' class='popup_button'>&nbsp;</button>
        </div>
        <ul>
                <li><a class='export_to_excel'>Xuất excel</a></li>	
        </ul> -->
    <?php } ?>

    <div class="dynamic">
        <?php if (isset($this->toolbar)) { ?>
            <?php echo _render_toolbar($this->toolbar, POS_IN_TOP_BAR) ?>
        <?php } ?>
    </div>
</div>

