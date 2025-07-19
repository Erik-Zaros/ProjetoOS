<?php
$imports = [
    "global" => [
        "css" => [
            "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css",
            "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css",
            "https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css",
            "https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css",
            "../public/css/style.css",
            "../public/css/form.css",
            "../public/css/button.css"
        ],
        "js" => [
            "https://code.jquery.com/jquery-3.6.0.min.js",
            "https://code.jquery.com/ui/1.13.3/jquery-ui.min.js",
            "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js",
            "https://cdn.jsdelivr.net/npm/sweetalert2@11",
            "https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js",
            "../public/js/main.js",
            "../public/js/logAuditor.js"
        ]
    ],
    "usuario" => [
        "css" => ["../public/css/usuario.css"],
        "js" => ["../public/js/usuario.js"]
    ],
    "menu" => [
        "css" => ["../public/css/menu.css"],
        "js" => ["../public/js/menu.js",
                 "https://code.highcharts.com/9.1.2/highcharts.js"],
    ],
    "cliente" => [
        "css" => ["../public/css/cliente.css"],
        "js" => ["../public/js/cliente.js"]
    ],
    "produto" => [
        "css" => ["../public/css/produto.css"],
        "js" => ["../public/js/produto.js"]
    ],
    "peca" => [
        "css" => ["../public/css/peca.css"],
        "js" => ["../public/js/peca.js"]
    ],
    "cadastra_os" => [
        "css" => ["../public/css/cadastraOS.css",
                  "https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css",
                  "https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"],
        "js" => ["../public/js/os.js",
                 "https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"]
    ],
    "consulta_os" => [
        "css" => ["../public/css/consultaOS.css"],
        "js" => ["../public/js/os.js"]
    ],
    "os_press" => [
        "css" => ["../public/css/osPress.css"],
        "js" => ["../public/js/os.js"]
    ]
];
?>
