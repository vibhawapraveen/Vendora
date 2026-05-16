<?php

class Admin extends Controller
{
    public function __construct($PREV_URL,$URL,$SLUG_DATA=NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $this->view("admin/login");
    }
}

?>