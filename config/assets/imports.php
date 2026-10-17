<?php

use App\Service\PostoService;

$imports = [
    "global" => [
        "css" => [
            "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css",
            "https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css",
            "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css",
            "https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css",
            "../plugins/flatpickr/flatpickr.min.css",
            "https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css",
            "https://code.jquery.com/ui/1.14.2/themes/base/jquery-ui.css",
            "../public/adminlte/dist/css/adminlte.min.css",
            "../public/css/layout.css",
            "../public/css/form.css",
            "../public/css/button.css",
            "../public/css/link.css",
            "../public/css/icone.css"
        ],
        "js" => [
            "https://code.jquery.com/jquery-3.7.1.min.js",
            "https://code.jquery.com/ui/1.14.2/jquery-ui.min.js",
            "https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js",
            "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js",
            "https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js",
            "../public/adminlte/dist/js/adminlte.min.js",
            "https://cdn.jsdelivr.net/npm/sweetalert2@11",
            "../plugins/flatpickr/flatpickr.min.js",
            "../plugins/flatpickr/l10n/pt.js",
            "../public/js/alerta.js",
            "../public/js/dark-theme.js",
            "../public/js/lupa.js",
            "../public/js/layout.js",
            "../public/js/log_auditor.js"
        ]
    ],
    "usuario" => [
        "css" => ["../public/css/usuario.css", "../plugins/dataTable/dataTable.css"],
        "js" => ["../public/js/usuario.js", "../plugins/dataTable/dataTable.js"]
    ],
    "menu" => [
        "css" => ["../public/css/menu.css", "../plugins/dataTable/dataTable.css"],
        "js" => [
            "https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js",
            "../public/js/menu.js",
            "../plugins/dataTable/dataTable.js"
        ],
    ],
    "cliente" => [
        "css" => ["../public/css/cliente.css", "../plugins/dataTable/dataTable.css"],
        "js" => ["../public/js/cliente.js", "../plugins/dataTable/dataTable.js"]
    ],
    "produto" => [
        "css" => ["../public/css/produto.css", "../plugins/dataTable/dataTable.css"],
        "js" => ["../public/js/produto.js", "../plugins/dataTable/dataTable.js"]
    ],
    "peca" => [
        "css" => ["../public/css/peca.css", "../plugins/dataTable/dataTable.css"],
        "js" => ["../public/js/peca.js", "../plugins/dataTable/dataTable.js"]
    ],
    "servico_realizado" => [
        "css" => ["../public/css/servico_realizado.css", "../plugins/dataTable/dataTable.css"],
        "js" => ["../public/js/servico_realizado.js", "../plugins/dataTable/dataTable.js"]
    ],
    "lista_basica" => [
        "css" => ["../public/css/lista_basica.css", "../plugins/dataTable/dataTable.css"],
        "js" => ["../public/js/lista_basica.js", "../plugins/dataTable/dataTable.js"]
    ],
    "cadastra_movimentacao" => [
        "css" => ["../public/css/estoque.css"],
        "js" => ["../public/js/estoque_lancar.js"]
    ],
    "consulta_estoque" => [
        "css" => ["../public/css/estoque.css", "../plugins/dataTable/dataTable.css"],
        "js" => ["../public/js/consulta_estoque.js", "../plugins/dataTable/dataTable.js"]
    ],
    "cadastra_os" => [
        "css" => [
            "../public/css/cadastraOS.css",
            "https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css",
            "https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
        ],
        "js" => [
            "../public/js/tdocs.js",
            "../public/js/cadastra_os.js",
            "https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"
        ]
    ],
    "consulta_os" => [
        "css" => ["../public/css/consultaOS.css", "../plugins/dataTable/dataTable.css"],
        "js" => ["../public/js/consulta_os.js", "../plugins/dataTable/dataTable.js"]
    ],
    "os_press" => [
        "css" => ["../public/css/osPress.css"],
        "js" => ["../public/js/consulta_os.js"]
    ],
    "tipo_atendimento" => [
        "css" => ["../public/css/tipo_atendimento.css", "../plugins/dataTable/dataTable.css"],
        "js" => ["../public/js/tipo_atendimento.js", "../plugins/dataTable/dataTable.js"]
    ],
    "relatorio_cliente" => [
        "css" => ["../public/css/relatorio_cliente.css", "../plugins/dataTable/dataTable.css"],
        "js" => ["../public/js/relatorio_cliente.js", "../plugins/dataTable/dataTable.js"]
    ]
];

$posto_service = new PostoService();
$usa_modulo_estoque = $posto_service->usaModulo("estoque");

if ($usa_modulo_estoque == false) {
    unset($imports["cadastra_movimentacao"]);
    unset($imports["consulta_estoque"]);
}

?>
