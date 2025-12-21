$(document).ready(function () {
  $(document).on('click', '.btn-log-auditor', function () {

    const tabela = $(this).data('tabela');
    const id = $(this).data('id');

    if (!tabela || !id) return;

    Shadowbox.open({
      player: 'iframe',
      content: `../view/logAuditor.php?tabela=${encodeURIComponent(tabela)}&id=${encodeURIComponent(id)}`,
      title: 'Histórico de Alterações',
      width: 1250,
      height: 600
    });
  });
});
