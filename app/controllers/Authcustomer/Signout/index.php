<?php

class Signout extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        Session::logout();
        header("Location: " . ROOT . $_GET['redirect_store'] ?? "");
    }
}
