<?php
$rotas = [
    "dashboard" => [
        "titulo" => "Dashboard Geral",
        "icone" => "bi bi-pie-chart-fill",
        "link" => "menu.php",
    ],
    "clientes" => [
        "titulo" => "Clientes",
        "icone" => "bi bi-people-fill",
        "submenus" => [
            [
                "titulo" => "Cadastrar Cliente",
                "link" => "cliente.php",
            ]
        ]
    ],
    "produtos" => [
        "titulo" => "Produtos",
        "icone" => "bi bi-box-fill",
        "submenus" => [
            [
                "titulo" => "Cadastrar Produto",
                "link" => "produto.php",
            ]
        ]
    ],
    "ordem_servico" => [
        "titulo" => "Ordem de Serviço",
        "icone" => "bi bi-tools",
        "submenus" => [
            [
                "titulo" => "Cadastrar OS",
                "link" => "cadastra_os.php",
            ],
            [
                "titulo" => "Consultar OS",
                "link" => "consulta_os.php",
            ]
        ]
    ]
];
