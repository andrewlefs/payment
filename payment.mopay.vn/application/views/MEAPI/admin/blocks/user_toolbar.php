<style>
    .row_header .user_toolbar .avatar{background-image:url('<?php echo $image ?>');}
</style>
<div class='user_toolbar'>
    <div class='dropdown'>
        <button class='popup_button' tabindex='-1' >&nbsp;</button>
    </div>
    <div class='panel'>
        <li><a href='<?php echo base_url("account/change_password") ?>'>Đổi mật khẩu</a></li>
        <li><a href='<?php echo base_url("account/signout") ?>'>Đăng xuất</a></li>		
    </div>
    <div class='usn tooltip' title='<?php echo $this->session->userdata('username'); ?>'><?php echo $this->session->userdata('username') ?> - <b>(Game <?php echo strtoupper(APP); ?>)</b></div>
</div>