<?php
$groups = $this->config->item('nav');
//if(DEV)
//	$groups=array_merge($groups,$this->config->item('dev_nav'));
$expand = 0;
if ($expand == 1) {
    ?>
    <div expand='1' class="accordion ui-accordion ui-widget ui-helper-reset">
        <?php foreach ($groups as $group => $value) { ?>
            <div class="group ">
                <h3 class="ui-state-hover ui-accordion-header ui-helper-reset ui-state-default ui-accordion-icons ui-corner-tl ui-corner-tr">
                    <span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span>
                    <a href="#"><?php echo $group ?></a>
                </h3>
                <div class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" style="display: block;" >
                    <?php
                    foreach ($value as $ref_link => $label) {
                        $link = base_url($ref_link)
                        ?>
                        <p>
                            <a <?php echo strstr(base_url($this->uri->uri_string()), $link) !== false ? "class='current'" : ""; ?> href="<?php echo $link; ?>"><?php echo $label ?></a>
                        </p>
        <?php } ?>
                </div>
            </div>
    <?php } ?>
    </div>
    <?php } else { ?>
    <div expand='0' class="accordion"> 
    <?php foreach ($groups as $group => $value) { ?>
            <div class="group">
                <h3 ><?php echo $group ?></h3>
                <div>
                    <?php
                    foreach ($value as $ref_link => $label) {
                        $link = base_url($ref_link);
                        ?>
                        <p>
                            <a <?php echo strstr(base_url($this->uri->uri_string()), $link) !== false ? "class='current'" : ""; ?> href='<?php echo $link; ?>'><?php echo $label ?></a>
                        </p>
            <?php } ?>
                </div>
            </div>
    <?php } ?>
    </div>
    <?php
}?>