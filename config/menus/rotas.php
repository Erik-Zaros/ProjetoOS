<?php
$rotas = [
    "usuarios" => [
        "titulo" => "Usuários Admin",
        "icone" => "bi bi-person-circle",
        "link" => "usuarios"
    ],
    "dashboard" => [
        "titulo" => "Dashboard Geral",
        "icone" => "bi bi-pie-chart-fill",
        "link" => "menu",
    ],
    "clientes" => [
        "titulo" => "Clientes",
        "icone" => "bi bi-people-fill",
        "submenus" => [
            [
                "titulo" => "Cadastrar Cliente",
                "link" => "cliente",
            ]
        ]
    ],
    "produtos" => [
        "titulo" => "Produtos",
        "icone" => "bi bi-box-fill",
        "submenus" => [
            [
                "titulo" => "Cadastrar Produto",
                "link" => "produto",
            ]
        ]
    ],
    "pecas" => [
        "titulo" => "Peças",
        "icone" => "bi bi-wrench",
        "submenus" => [
            [
                "titulo" => "Cadastrar Peças",
                "link" => "peca",
            ]
        ]
    ],
    "estoque" => [
        "titulo" => "Estoque",
        "icone" => "bi bi-box2-fill",
        "submenus" => [
            [
                "titulo" => "Lança Movimentação",
                "link" => "cadastra_movimentacao",
            ],
            [
                "titulo" => "Consulta Estoque",
                "link" => "consulta_estoque",
            ]
        ]
    ],
    "ordem_servico" => [
        "titulo" => "Ordem de Serviço",
        "icone" => "bi bi-tools",
        "submenus" => [
            [
                "titulo" => "Cadastrar OS",
                "link" => "cadastra_os",
            ],
            [
                "titulo" => "Consultar OS",
                "link" => "consulta_os",
            ]
        ]
    ]
];
