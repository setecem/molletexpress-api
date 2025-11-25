<header class="header body-pd" id="header">
    <div class="d-flex">
        <div class="header_toggle me-3">
            <i class='bx bx-menu' id="header-toggle"></i>
        </div>
        {block name="topbar_buttons"}{/block}
    </div>
   <div class="d-flex">
        <b class="d-flex align-items-center me-3 ">{trans s="Hola"}, {$user->getFirstname()}</b>
       <div class="header_img">
           <i class="fa fa-user-circle fa-3x"></i>
       </div>

   </div>
</header>
<div class="l-navbar show" id="nav-bar">
    <nav class="nav">
        <div>
            <!-- Title site -->
            <a href="{$base}" class="nav_logo">
                <i class='fa fa-address-book nav_logo-icon'></i>
                <span class="nav_logo-name">MolletExpress</span>
            </a>

            <!-- Menu -->
            <div class="nav_list">
                {menu}
            </div>
        </div>
        <a href="{$base}user/logout" class="nav_link">
            <i class="fa fa-sign-out nav_icon"></i>
            <span class="nav_name">Salir</span>
        </a>
    </nav>
</div>
<!--Container Main end-->
