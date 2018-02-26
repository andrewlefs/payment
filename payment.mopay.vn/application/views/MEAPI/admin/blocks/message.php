<?php

//
$form_message = validation_errors();

//
$direct_message = "";
if (is_array($this->messages))
    foreach ($this->messages as $key => $value)
        $direct_message.="<p>" . $value . "</p>";

//
$flash_message = $this->session->flashdata('message') ? ("<p class='info'>" . $this->session->flashdata('message') . "</p>") : "";

//
$message = $form_message . $direct_message . $flash_message;
echo $message;
?>