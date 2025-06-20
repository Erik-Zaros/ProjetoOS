document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const osParam = urlParams.get('os');
    const page = window.location.pathname.split("/").pop();

    if (page === 'cadastra_os.php') {
        if (osParam) {
            carregarDadosOS(osParam);
        } else {
            carregarProdutos();
        }
    }

function carregarDadosOS(os) {
    $.ajax({
        url: '../public/os/buscar.php',
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

            carregarProdutos(data.produto);

            $('#osForm').off('submit').on('submit', function (e) {
                e.preventDefault();
                editaOS(os);
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
        url: '../public/produto/listar.php',
        method: 'GET',
        cache: false,
        success: function (data) {
            try {
                var produtos = JSON.parse(data);
                $('#produto').empty();
                $('#produto').append('<option value="">Selecione o Produto</option>');

                produtos.forEach(function (produto) {
                    if (produto.ativo == 't') {
                        var selected = (produtoSelecionado && produto.produto == produtoSelecionado) ? 'selected' : '';
                        $('#produto').append(`<option value="${produto.produto}" ${selected}>${produto.codigo} - ${produto.descricao}</option>`);
                    }
                });

                $('#produto').select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: 'Selecione o Produto',
                    allowClear: true
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

function editaOS(os) {
    $.ajax({
        url: '../public/os/editar.php',
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
                    window.location.href = 'consulta_os.php';
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
                url: '../public/os/cadastrar.php',
                method: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: response.message,
                        }).then(() => {
                            $('#osForm')[0].reset();
                            carregarProdutos();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: response.message,
                        });
                    }
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

    if (window.location.pathname.includes('consulta_os.php')) {
        carregarOs();
    }

    function carregarOs() {
        $.ajax({
            url: '../public/os/listar.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#osTable tbody').empty();
                data.forEach(function (os) {
                    var finalizadaBadge = os.finalizada ? '<span class="badge bg-success">Finalizada</span>' : '';
                    var finalizarButton = os.finalizada ? '' : '<button class="btn btn-success btn-sm finalizar-os" data-os="' + os.os + '">Finalizar</button>';
                    var alterarButton = os.finalizada ? '' : `<a href="cadastra_os.php?os=${os.os}" class="btn btn-warning btn-sm">Alterar</a>`;

                    $('#osTable tbody').append(`
                        <tr>
                            <td><a href="os_press.php?os=${os.os}" class="text-primary">${os.os}</a></td>
                            <td>${os.cliente}</td>
                            <td>${os.cpf}</td>
                            <td>${os.produto}</td>
                            <td>${os.data_abertura}</td>
                            <td>
                                ${alterarButton}
                                ${finalizarButton}
                                ${finalizadaBadge}
                            </td>
                        </tr>
                    `);
                });
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro ao carregar ordens de serviço!',
                });
            }
        });
    }

    carregarOs();

    function carregarDetalhesOS(os) {

        $.ajax({
            url: '../public/os/press.php',
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

                $('#osNumero').text(data.os);
                $('#dataAbertura').text(new Date(data.data_abertura).toLocaleDateString('pt-BR'));
                $('#nomeConsumidor').text(data.nome_consumidor);
                $('#cpfConsumidor').text(data.cpf_consumidor);
                $('#produto').text(data.produto);
                $('#status').html((data.finalizada === true || data.finalizada === 't') ? '<span class="badge bg-success">Finalizada</span>' : '<span class="badge bg-warning">Em Aberto</span>');
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

    $(document).ready(function () {
        const urlParams = new URLSearchParams(window.location.search);
        const osParam = urlParams.get('os');

        if (osParam) {
            carregarDetalhesOS(osParam);
        }
    });

    $('#filtroForm').on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: '../public/os/filtrar.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (data) {
                $('#osTable tbody').empty();
                data.forEach(function (os) {
                    var finalizadaBadge = os.finalizada ? '<span class="badge bg-success">Finalizada</span>' : '';
                    var finalizarButton = os.finalizada ? '' : '<button class="btn btn-success finalizar-os btn-sm" data-os="' + os.os + '">Finalizar</button>';
                    var alterarButton = os.finalizada ? '' : `<a href="cadastra_os.php?os=${os.os}" class="btn btn-warning btn-sm">Alterar</a>`;

                    $('#osTable tbody').append(`
                            <tr>
                                <td><a href="cadastra_os.php?os=${os.os}">${os.os}</a></td>
                                <td>${os.cliente}</td>
                                <td>${os.cpf}</td>
                                <td>${os.produto}</td>
                                <td>${os.data_abertura}</td>
                                <td>
                                    ${alterarButton}
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
                    url: '../public/os/finalizar.php',
                    method: 'POST',
                    data: { os: os },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Finalizado!',
                            text: response.message,
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

$(function() {
  $("#nome_consumidor").autocomplete({
    minLength: 2,
    source: function(request, response) {
      $.ajax({
        url: "../public/cliente/autocomplete.php",
        dataType: "json",
        data: {
          term: request.term
        },
        success: function(data) {
          response(data);
        }
      });
    },
    select: function(event, ui) {
      $("#nome_consumidor").val(ui.item.value);
      $("#cpf_consumidor").val(ui.item.cpf);
      return false;
    }
  });
});

});
