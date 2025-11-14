<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>molletexpress | {block name="title"}Home{/block}</title>

    {css file="/fonts/open-sans/open-sans.css"}

    <!-- Font awesome 6.1.1 -->
    {css file="/fonts/fontawesome/css/all.min.css"}

    <!-- Bootstrap 5.0.2 -->
    {css file="lib/bootstrap/css/bootstrap.min.css"}

    <!-- jConfirm 3.3.4-->
    {css file="lib/jquery-confirm.min.css"}

    <!-- Tostr  2.1.4 -->
    {css file="lib/toastr.min.css"}

    <!-- Base CSS -->
    {css file="base.css"}

    {block name="css"}{/block}
</head>
<body class="body-pd">

{include file="sidebar.tpl"}

{block name="content"}
    <div class="container min-vh-100 py-2">
        <div class="row main-menu">
            {menu name="home" file="partial/menu/home.tpl"}
        </div>
    </div>
{/block}


<!-- jQuery 3.6.0 -->
{js file="lib/jquery.min.js"}

<!-- Toastr 2.1.4 -->
{js file="lib/toastr.min.js"}

<!-- Bootstrap 5.0.2 -->
{js file="../css/lib/bootstrap/js/bootstrap.bundle.min.js"}

<!-- jConfirm 3.3.4 -->
{js file="lib/jquery-confirm.min.js"}

<!-- BASE CSS -->
{js file="base.js"}

{block name="js"}{/block}
</body>
</html>
