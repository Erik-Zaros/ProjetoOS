$(document).ready(function () {
    $(document).on('click', '.btn-log-auditor', function () {
        const tabela = $(this).data('tabela');
        const id = $(this).data('id');

        if (!tabela || !id) return;

        Swal.fire({
            title: 'Buscando log...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.post('../public/logAuditor/buscar.php', { tabela: tabela, id: id }, function (response) {
            Swal.close();
            if (response.logs && response.logs.length > 0) {
                let html = '';
                response.logs.forEach(log => {
                    const antesObj = log.antes ? JSON.parse(log.antes) : null;
                    const depoisObj = log.depois ? JSON.parse(log.depois) : null;

                    html += `
                        <tr>
                            <td>${log.data_log}</td>
                            <td>${log.usuario_nome}</td>
                            <td>${traduzirAcao(log.acao)}</td>
                            <td>${formatarTabela(antesObj)}</td>
                            <td>${formatarTabela(depoisObj)}</td>
                        </tr>
                    `;
                });
                $('#logAuditorBody').html(html);
            } else {
                $('#logAuditorBody').html('<tr><td colspan="5" class="text-center">Nenhum log encontrado.</td></tr>');
            }

            $('#logAuditorModal').modal('show');
        }).fail(function () {
            Swal.fire({
                icon: 'error',
                title: 'Erro ao buscar logs',
                text: 'Não foi possível recuperar os dados do Log.',
                confirmButtonText: 'Ok'
            });
        });
    });

    function traduzirAcao(acao) {
        switch (acao) {
            case 'insert': return 'Inserção';
            case 'update': return 'Atualização';
            case 'delete': return 'Exclusão';
            default: return acao;
        }
    }

    function traduzirValor(valor) {
        if (typeof valor !== 'string') return valor;

        const val = valor.trim().toLowerCase();
        if (val === 't' || val === 'true') return 'Sim';
        if (val === 'f' || val === 'false') return 'Não';
        return valor;
    }

    function formatarTabela(obj) {
        if (!obj) return '<em>-</em>';
        let html = '<table class="table table-sm table-bordered mb-0">';
        for (const campo in obj) {
            const valorTraduzido = traduzirValor(obj[campo]);
            html += `<tr>
                        <td><strong>${campo}</strong></td>
                        <td>${valorTraduzido}</td>
                    </tr>`;
        }
        html += '</table>';
        return html;
    }
});
