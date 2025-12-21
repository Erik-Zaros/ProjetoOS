<?php

$tabela = $_GET['tabela'] ?? '';
$id = $_GET['id'] ?? '';

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Histórico de Alterações</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      padding: 16px;
      background-color: #f8f9fa;
      font-size: 0.80rem;
    }
    table td, table th {
      padding: 0.4rem !important;
      vertical-align: middle;
    }

    table.table-sm td, table.table-sm th {
      padding: 0.3rem !important;
    }

    .scroll-area {
      max-height: 95vh;
      overflow-y: auto;
    }

    th {
      background-color: #3c3c3c !important;
    }
  </style>
</head>
<body>
  <div class="scroll-area">
    <table class="table table-bordered table-striped table-hover nowrap">
      <thead class="table-dark">
        <tr>
          <th>Data</th>
          <th>Usuário</th>
          <th>Ação</th>
          <th>Antes</th>
          <th>Depois</th>
        </tr>
      </thead>
      <tbody id="logAuditorBody">
        <tr><td colspan="5" class="text-center">Carregando...</td></tr>
      </tbody>
    </table>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>

    const tabela = <?= json_encode($tabela) ?>;
    const id     = <?= json_encode($id) ?>;

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
      if (!obj) return '<em>Vazio</em>';
      let html = '<table class="table table-sm table-bordered mb-0">';
      for (const campo in obj) {
        const valorTraduzido = traduzirValor(obj[campo]);
        html += `<tr><td><strong>${campo}</strong></td><td>${valorTraduzido}</td></tr>`;
      }
      html += '</table>';
      return html;
    }

    $.post('../public/logAuditor/buscar.php', { tabela, id }, function (response) {
      let linhas = '';
      if (response.logs && response.logs.length > 0) {
        response.logs.forEach(log => {
          const antesObj  = log.antes  ? JSON.parse(log.antes)  : null;
          const depoisObj = log.depois ? JSON.parse(log.depois) : null;

          linhas += `
            <tr>
              <td>${log.data_log}</td>
              <td>${log.usuario_nome}</td>
              <td>${traduzirAcao(log.acao)}</td>
              <td>${formatarTabela(antesObj)}</td>
              <td>${formatarTabela(depoisObj)}</td>
            </tr>`;
        });
      } else {
        linhas = '<tr><td colspan="5" class="text-center">Nenhum log encontrado.</td></tr>';
      }

      $('#logAuditorBody').html(linhas);
    }).fail(function () {
      $('#logAuditorBody').html('<tr><td colspan="5" class="text-danger text-center">Erro ao buscar logs.</td></tr>');
    });
  </script>
</body>
</html>
