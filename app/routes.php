<?php

Cavesman\Router::middleware("GET", "/*", function () {
    if (!\src\Modules\User\Session::isLogged())
        \Cavesman\Http::response(\Cavesman\Smarty::partial('login.tpl'), 200, 'text/html');
});

Cavesman\Router::get("/", function () {
    //Something to /
});

\Cavesman\Menu::addItem([
    "name" => "home",
    "items" => [
        [
            "order" => 1,
            "name" => "users",
            "label" => self::trans("Usuarios", [], "user"),
            "icon" => "fa fa-users",
            "link" => _PATH_ . self::trans("slug-users", [], "user"),
            "permission" => [
                "action" => "view_users",
                "group" => "ACCESS_PERMISSION",
            ],
            "childs" => []
        ],
        [
            "order" => 2,
            "name" => "employee",
            "label" => self::trans("Employee", [], "employee"),
            "icon" => "fa fa-briefcase",
            "link" => _PATH_ . self::trans("employee-slug", [], "employee"),
            "permission" => [
                "action" => "view_employee",
                "group" => "ACCESS_PERMISSION",
            ],
            "childs" => []

        ],
        [
            "order" => 3,
            "name" => "contact",
            "label" => self::trans("Contact", [], "contact"),
            "icon" => "fa fa-id-card",
            "link" => _PATH_ . self::trans("contact-slug", [], "contact"),
            "permission" => [
                "action" => "view_contact",
                "group" => "ACCESS_PERMISSION",
            ],
            "childs" => []

        ]
    ]
]);
