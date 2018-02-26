<!doctype html>
<html>
    <head>
        <title><?php echo $this->config->item('title'); ?></title>
        <link type="image/x-icon" rel="shortcut icon" href="<?php echo A_IMAGES . "fav.jpg" . REV ?>">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <?php echo _link_tag(JS . 'jquery-ui-1.11.0.custom/jquery-ui.css' . REV); ?>
        <?php echo _link_tag(A_CSS . 'main.css' . REV); ?>
        <?php echo _link_tag(A_CSS . 'full.css' . REV); ?>
        <?php echo _link_tag(A_CSS . 'flick.css' . REV); ?>				
        <?php //echo $css;?>
        <?php echo _script_tag(JS . 'jquery-ui-1.11.0.custom/external/jquery/jquery.js' . REV); ?>		
        <?php echo _script_tag(JS . 'jquery-ui-1.11.0.custom/jquery-ui.js' . REV); ?>
        <?php echo $this->_js; ?>	
        <?php echo $_scripts; ?>		


    </head>
    <body>
        <div class='row_header'>		

            <?php $this->load->view('MEAPI/admin/blocks/logo') ?>

            <?php if ($this->session->userdata('is_login')) { ?>
                <?php $this->load->view('MEAPI/admin/blocks/user_toolbar') ?>
            <?php } ?>
        </div>
        <div class='clear'></div>	
        <div class='col_left'>				
            <?php if ($this->session->userdata('is_login')) { ?>
                <?php $this->load->view('MEAPI/admin/blocks/accordion') ?>
            <?php } ?>
        </div>
        <div class='col_right'>
            <form accept-charset="utf-8" method="post" action="" id='form_main' current_class='<?php echo $_GET['control']; ?>' current_method='<?php echo $_GET['action']; ?>'>
                <div class="manager">
                    <?php $this->load->view('MEAPI/admin/blocks/toolbar') ?>			
                    <?php $this->load->view('MEAPI/admin/blocks/message') ?>    				    
                    <div class='clear'></div>
                </div>
                <div class='container'>
                    <?php echo $content; ?>
                </div>
            </form>
        </div>
        <?php $this->load->view('MEAPI/admin/blocks/bottom_bar') ?>
    </body>
</html>