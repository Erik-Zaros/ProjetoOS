document.addEventListener("DOMContentLoaded", function () {
    const page = window.location.pathname.split("/").pop();
    const navLinks = document.querySelectorAll(".nav-link");
    navLinks.forEach(link => {
        if (link.getAttribute("href") === page) {
            link.classList.add("active");
        }
    });

    document.getElementById("sidebarToggle").addEventListener("click", function () {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("d-none");
    });

    const urlParams = new URLSearchParams(window.location.search);
    const osParam = urlParams.get('os');

    if (page === 'cadastra_os.html') {
        if (osParam) {
            carregarDadosOS(osParam);
        } else {
            carregarProdutos();
        }
    }
});

function carregarDadosOS(os) {
    $.ajax({
        url: '../controller/os/buscar_os.php',
        method: 'GET',
        data: { os: os },
        dataType: 'json',
        success: function (data) {
            if (data.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro ao carregar OS: ' + data.error,
                });
                return;
            }

            $('#os').val(data.os);
            $('#data_abertura').val(data.data_abertura);
            $('#nome_consumidor').val(data.nome_consumidor);
            $('#cpf_consumidor').val(data.cpf_consumidor);

            carregarProdutos(data.produto_codigo);

            $('#osForm').off('submit').on('submit', function (e) {
                e.preventDefault();
                editarOS(os);
            });
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao carregar dados da OS!',
            });
        }
    });
}

function carregarProdutos(produtoSelecionado = null) {
    $.ajax({
        url: '../controller/produto/listar_produtos.php',
        method: 'GET',
        cache: false,
        success: function (data) {
            try {
                var produtos = JSON.parse(data);
                $('#produto_id').empty();
                $('#produto_id').append('<option value="">Selecione o Produto</option>');

                produtos.forEach(function (produto) {
                    if (produto.ativo == 1) {
                        var selected = (produtoSelecionado && produto.id == produtoSelecionado) ? 'selected' : '';
                        $('#produto_id').append(`<option value="${produto.id}" ${selected}>${produto.descricao}</option>`);
                    }
                });
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro ao processar os dados dos produtos.',
                });
            }
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao carregar produtos!',
            });
        }
    });
}

function editarOS(os) {
    $.ajax({
        url: '../controller/os/editar_os.php',
        method: 'POST',
        data: $('#osForm').serialize(),
        dataType: 'json',
        success: function (response) {
            if (response.status === "error") {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: response.message,
                });
            } else if (response.status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso',
                    text: response.message,
                }).then(() => {
                    window.location.href = 'consulta_os.html';
                });
            }
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao tentar editar a ordem de serviço.',
            });
        }
    });
}

$(document).ready(function () {
    if (!window.location.search.includes('os=')) {
        $('#osForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: '../controller/os/cadastrar_os.php',
                method: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Ordem de Serviço Cadastrada!',
                        text: response,
                    }).then(() => {
                        $('#osForm')[0].reset();
                        carregarProdutos();
                    });
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Erro ao cadastrar OS!',
                    });
                }
            });
        });
    }

    if (window.location.pathname.includes('consulta_os.html')) {
        carregarOs();
    }

    function carregarOs() {
        $.ajax({
            url: '../controller/os/listar_os.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#osTable tbody').empty();
                data.forEach(function (os) {
                    var finalizadaBadge = os.finalizada ? '<span class="badge bg-success">Finalizada</span>' : '';
                    var finalizarButton = os.finalizada ? '' : '<button class="btn btn-success finalizar-os" data-os="' + os.os + '">Finalizar</button>';
                    $('#osTable tbody').append(`
                        <tr>
                            <td><a href="cadastra_os.html?os=${os.os}">${os.os}</a></td>
                            <td>${os.cliente}</td>
                            <td>${os.produto}</td>
                            <td>${os.data_abertura}</td>
                            <td>
                                ${finalizarButton}
                                ${finalizadaBadge}
                            </td>
                        </tr>
                    `);
                });
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro ao carregar ordens de serviço!',
                });
            }
        });
    }

    carregarOs();

    $('#filtroForm').on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: '../controller/os/filtrar_os.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (data) {
                $('#osTable tbody').empty();
                data.forEach(function (os) {
                    var finalizadaBadge = os.finalizada ? '<span class="badge bg-success">Finalizada</span>' : '';
                    var finalizarButton = os.finalizada ? '' : '<button class="btn btn-success finalizar-os" data-os="' + os.os + '">Finalizar</button>';
                    $('#osTable tbody').append(`
                            <tr>
                                <td><a href="cadastra_os.html?os=${os.os}">${os.os}</a></td>
                                <td>${os.cliente}</td>
                                <td>${os.produto}</td>
                                <td>${os.data_abertura}</td>
                                <td>
                                    ${finalizarButton}
                                    ${finalizadaBadge}
                                </td>
                            </tr>
                        `);
                });
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro ao aplicar os filtros!',
                });
            }
        });
    });

    $('#limparFiltros').on('click', function () {
        $('#filtroForm')[0].reset();
        carregarOs();
    });

    $(document).on('click', '.finalizar-os', function () {
        var os = $(this).data('os');
        Swal.fire({
            title: `Deseja finalizar a ordem de serviço ${os}?`,
            text: 'Essa ação não pode ser desfeita.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, finalizar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../controller/os/finalizar_os.php',
                    method: 'POST',
                    data: { os: os },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Finalizado!',
                            text: response,
                        }).then(() => {
                            carregarOs();
                        });
                    },
                    error: function (xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Erro ao finalizar a OS!',
                        });
                    }
                });
            }
        });
    });
});
