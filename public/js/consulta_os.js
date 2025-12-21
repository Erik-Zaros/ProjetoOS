$(document).ready(function () {

    carregarOs();

    function carregarOs() {
        if ($.fn.DataTable.isDataTable('#osTable')) {
            $('#osTable').DataTable().destroy();
        }
        $.ajax({
            url: '../public/consulta_os/listar.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#osTable tbody').empty();
                data.forEach(function (os) {
                    if (os.finalizada != true) {
                        var canceladaButton = os.cancelada ? '' : '<button class="btn btn-danger btn-sm cancelar-os" data-os="' + os.os + '">Cancelar</button>';
                    }

                    if (os.cancelada != true) {
                        var finalizarButton = os.finalizada ? '' : '<button class="btn btn-success btn-sm finalizar-os" data-os="' + os.os + '">Finalizar</button>';
                    }

                    var alterarButton = '<a href=cadastra_os?os='+ os.os +' class="btn btn-warning btn-sm">Alterar</a>';
                    var canceladaBadge = os.cancelada ? '<span class="badge bg-danger">Cancelada</span>' : '';
                    var finalizadaBadge = os.finalizada ? '<span class="badge bg-success">Finalizada</span>' : '';

                    $('#osTable tbody').append(`
                        <tr>
                            <td><a href="os_press?os=${os.os}" class="link">${os.os}</a></td>
                            <td>${os.cliente}</td>
                            <td>${os.cpf}</td>
                            <td>${os.produto}</td>
                            <td>${os.data_abertura}</td>
                            <td>
                                ${canceladaBadge}
                                ${os.finalizada == false && os.cancelada == false ? alterarButton : ''}
                                ${os.finalizada != true ? canceladaButton : ''}
                                ${os.cancelada != true ? finalizarButton : ''}
                                ${finalizadaBadge}
                            </td>
                        </tr>
                    `);
                });
                if (data.length > 0) {
                    $('#osTable').DataTable({
                        responsive: true,
                        scrollX: true,
                        autoWidth: false,
                        language: {
                            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                        }
                    });
                }
            }
        });
    }

    $('#filtroForm').on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: '../public/consulta_os/filtrar.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (data) {
                $('#osTable tbody').empty();
                data.forEach(function (os) {
                    if (os.finalizada != true) {
                        var canceladaButton = os.cancelada ? '' : '<button class="btn btn-danger btn-sm cancelar-os" data-os="' + os.os + '">Cancelar</button>';
                    }

                    if (os.cancelada != true) {
                        var finalizarButton = os.finalizada ? '' : '<button class="btn btn-success btn-sm finalizar-os" data-os="' + os.os + '">Finalizar</button>';
                    }

                    var alterarButton = '<a href=cadastra_os?os='+ os.os +' class="btn btn-warning btn-sm">Alterar</a>';
                    var canceladaBadge = os.cancelada ? '<span class="badge bg-danger">Cancelada</span>' : '';
                    var finalizadaBadge = os.finalizada ? '<span class="badge bg-success">Finalizada</span>' : '';

                    $('#osTable tbody').append(`
                            <tr>
                                <td><a href="cadastra_os?os=${os.os}">${os.os}</a></td>
                                <td>${os.cliente}</td>
                                <td>${os.cpf}</td>
                                <td>${os.produto}</td>
                                <td>${os.data_abertura}</td>
                                <td>
                                    ${canceladaBadge}
                                    ${os.finalizada == false && os.cancelada == false ? alterarButton : ''}
                                    ${os.finalizada != true ? canceladaButton : ''}
                                    ${os.cancelada != true ? finalizarButton : ''}
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
                    url: '../public/consulta_os/finalizar.php',
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

    $(document).on('click', '.cancelar-os', function () {
        var os = $(this).data('os');
        Swal.fire({
            title: `Deseja cancelar a ordem de serviço ${os}?`,
            text: 'Essa ação não pode ser desfeita.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, cancelar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../public/consulta_os/cancelar.php',
                    method: 'POST',
                    data: { os: os },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cancelado!',
                            text: response.message,
                        }).then(() => {
                            carregarOs();
                        });
                    },
                    error: function (xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Erro ao cancelar a OS!',
                        });
                    }
                });
            }
        });
    });
});