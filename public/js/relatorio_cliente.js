$(document).ready(function () {

	function carregarClientes(formData) {
	    if ($.fn.DataTable.isDataTable('#clienteTable')) {
	        $('#clienteTable').DataTable().destroy();
	    }
	    $.ajax({
	        url: '../public/relatorio_cliente/pesquisar.php',
	        method: 'POST',
	        data: formData,
	        dataType: 'json',
	        success: function (data) {

	            if (data.status === 'alert') {
	                Swal.fire({
	                    icon: 'warning',
	                    title: 'Atenção',
	                    text: data.message
	                });
	                return;
	            }

	            $("#btnCSV").show();

	            $('#clienteTable tbody').empty();

	            data.forEach(function (cliente) {
	                $('#clienteTable tbody').append(`
	                    <tr>
	                        <td>${cliente.nome || ''}</td>
	                        <td>${cliente.cpf || ''}</td>
	                        <td>${cliente.cep || ''}</td>
	                        <td>${cliente.endereco || ''}</td>
	                        <td>${cliente.bairro || ''}</td>
	                        <td>${cliente.cidade || ''}</td>
	                        <td>${cliente.estado || ''}</td>
	                        <td>${cliente.data_cadastro || ''}</td>
	                        <td>${cliente.oss || ''}</td>
	                    </tr>
	                `);
	            });

	            $('#clienteTable').DataTable({
	                destroy: true,
	                responsive: true,
	                scrollX: false,
	                language: {
	                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
	                }
	            });

	        },
	        error: function () {
	            Swal.fire({
	                icon: 'error',
	                title: 'Erro',
	                text: 'Erro ao consultar os dados'
	            });
	        }
	    });
	}

	$("#btnCSV").on("click", function () {
	    $("#formCSV input[name=dataInicio]").val($("#dataInicio").val());
	    $("#formCSV input[name=dataFim]").val($("#dataFim").val());
	    $("#formCSV input[name=cpf]").val($("#cpf").val());
	    $("#formCSV input[name=nome]").val($("#nome").val());

	    $("#formCSV").submit();
	});

    $("#pesquisaForm").on('submit', function(e) {
    	e.preventDefault();
    	var formData = $(this).serialize();
    	carregarClientes(formData);
    });

    $('#limparPesquisa').on('click', function () {
        $('#pesquisaForm')[0].reset();
    });

    $("#dataInicio, #dataFim").datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true
    });
});
