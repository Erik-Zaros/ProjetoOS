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
    "cadastros" => [
        "titulo" => "Cadastros",
        "icone" => "bi bi-folder-fill",
        "submenus" => [
            [
                "titulo" => "Clientes",
                "link" => "cliente",
            ],
            [
                "titulo" => "Produtos",
                "link" => "produto",
            ],
            [
                "titulo" => "Peças",
                "link" => "peca",
            ],
            [
                "titulo" => "Serviço Realizado",
                "link" => "servico_realizado",
            ],
            [
                "titulo" => "Lista Básica",
                "link" => "lista_basica",
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
