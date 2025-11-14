<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- open graph meta tag s-->
    <!--<meta property="og:title" content="CRM"/>-->
    <!--<meta property="og:description" content="CRM Workspace"/>-->
    <!--<meta property="og:image" content="./items/images/opengraphimage.png"/>-->
    <!--<meta property="og:image:source_url" content="./items/images/opengraphimage.png" />-->
    <!--<meta property="og:image:type" content="image/png" />-->
    <!--<meta property="og:type" content="article" />-->

    <!--<link rel="shortcut icon" type="image/png" href="assets/images/logo/icon.png">-->
    <!--<link rel="shortcut icon" type="image/png" href="./items/images/opengraphimage.png">-->

    <title>CRM</title>

    {css file="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"}
    {css file="login.css"}

</head>

<body>

<section class="login-section">
    <h3>CRM</h3>
    <form id="loginForm" method="post" class="form-horizontal" action="/user/login">
        <input type=text name="user" autocomplete="user" autofocus="autofocus" placeholder="Usuario">
        <input type="password" autocomplete="current-password" name="password" placeholder="Contraseña">
        <small class="error-message"></small>
        <button type="submit">
            Submit
        </button>
    </form>
</section>

{js file="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"}
{js file="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"}
{js file="login.js"}
</body>

</html>
