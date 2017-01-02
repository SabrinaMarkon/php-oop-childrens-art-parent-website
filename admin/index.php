<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
if (!isset($_SESSION))
{
    session_start();
}
require_once "../config/Database.php";
require_once "../config/Settings.php";
require_once "../config/Layout.php";

function sabrina_autoloader($class) {
    require $class . ".php";
}
spl_autoload_register("sabrina_autoloader");

$sitesettings = new Settings();
$settings = $sitesettings->getSettings();
foreach ($settings as $key => $value)
{
    $$key = $value;
}

######################################
if (isset($_POST['login']))
{
    $_SESSION['username'] = $_REQUEST['username'];
    $_SESSION['password'] = $_REQUEST['password'];
    $logincheck = new User();
    $newlogin = $logincheck->userLogin($_SESSION['username'],$_SESSION['password']);
    if ($newlogin === false)
    {
        $logout = new User();
        $logout->userLogout();
    }
    else
    {
        # returned member details.
        foreach ($newlogin as $key => $value)
        {
            $$key = $value;
            $_SESSION[$key] = $value;
        }
        $showgravatar = $logincheck->getGravatar($_SESSION['username'],$_SESSION['email']);
    }
}
if (isset($_POST['forgotlogin']))
{
    $forgot = new User();
    $showforgot = $forgot->forgotLogin($sitename,$domain,$adminemail);
}
if (isset($_POST['contactus']))
{
    $contact = new Contact();
    $sentcontact = $contact->sendContact($settings);
}
if (isset($_POST['register']))
{
    $register = new User();
    $showregistration = $register->newSignup($settings);
}
if (isset($_POST['saveprofile']))
{
    $update = new User();
    $showupdate = $update->saveProfile($_SESSION['username']);
}
if (isset($_GET['page']) && ($_GET['page'] == "logout"))
{
    $logout = new User();
    $logout->userLogout();
    $logoutpage = new PageContent();
    $showlogout = $logoutpage->showPage('Logout Page');
}
######################################

$Layout = new Layout();
$Layout->showAdminHeader();

//echo $_GET['page'] . "<br>";
if ((!empty($_GET['page'])) and ((file_exists($_GET['page'] . ".php") and ($_GET['page'] != "index"))))
{
    $page = $_REQUEST['page'];
    include $page . ".php";
}
else
{
    include "main.php";
}
$Layout->showAdminFooter();