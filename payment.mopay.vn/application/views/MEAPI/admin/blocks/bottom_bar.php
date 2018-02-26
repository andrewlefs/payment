<div class='bottom_bar'>
    <div class='wrap'>
        <a class='power_by' href='http://mecorp.vn' target='_blank'>Copyright © 2012 Mobile Entertainment Corporation</a>
        <li class="icon ui-state-default ui-corner-all top" title="Lên đầu trang"><span class="ui-icon ui-icon-carat-1-n"></span></li>
        <?php echo $this->pagination->create_links(); ?>
        <?php echo _isset_or($this->_index, ""); ?>
        <div class='clear'></div>
    </div>
</div>