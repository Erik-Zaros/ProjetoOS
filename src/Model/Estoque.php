<?php

namespace App\Model;

use App\Core\Db;
use App\Auth\Autenticador;

class Estoque
{
    private $dados;
    private $posto;

    public function __construct(array $dados, $posto)
    {
        $this->dados = $dados;
        $this->posto = $posto;
    }

    private static function pgErrorMessage($con): string
    {
        $err = pg_last_error($con);
        if (!$err) {
            return 'Erro ao processar.';
        }

        if (preg_match('/ERROR:\s*(.+?)(?:\n|$)/i', $err, $m)) {
            return trim($m[1]);
        }

        $err = preg_replace('/^ERROR:\s*/i', '', $err);
        $err = preg_replace('/\nCONTEXT:.*/is', '', $err);
        return trim($err) ?: 'Erro ao processar a operação.';
    }

    public function lancar()
    {
        $con     = Db::getConnection();
        $usuario = Autenticador::getUsuario();

        $posto  = $this->posto;
        $tipo   = isset($this->dados['tipo']) ? substr($this->dados['tipo'], 0, 1) : '';
        $qtde   = isset($this->dados['qtde']) ? $this->dados['qtde'] : 0;

        $produto = (isset($this->dados['produto']) && $this->dados['produto'] !== '') ? $this->dados['produto'] : null;
        $peca    = (isset($this->dados['peca'])    && $this->dados['peca']    !== '') ? $this->dados['peca']    : null;

        $os      = (isset($this->dados['os']) && $this->dados['os'] !== '') ? $this->dados['os'] : null;
        $motivo  = isset($this->dados['motivo']) && $this->dados['motivo'] !== '' ? pg_escape_string($this->dados['motivo']) : null;

        $p_posto   = $posto;
        $p_produto = ($produto === null ? "NULL" : $produto);
        $p_peca    = ($peca    === null ? "NULL" : $peca);
        $p_tipo    = ($tipo === '' ? "NULL" : "'" . pg_escape_string($tipo) . "'");
        $p_qtde    = $qtde;
        $p_os      = ($os      === null ? "NULL" : $os);
        $p_motivo  = ($motivo  === null ? "NULL" : "'" . $motivo . "'");
        $p_usuario = ($usuario === null ? "NULL" : $usuario);

        $sql = "SELECT fn_lanca_movimentacao_estoque(" .
               $p_posto   . "," .
               $p_produto . "," .
               $p_peca    . "," .
               $p_tipo    . "," .
               $p_qtde    . "," .
               $p_os      . "," .
               $p_motivo  . "," .
               $p_usuario .
               ") AS mov";

        $res = @pg_query($con, $sql);

        if (!$res) {
            $msg = self::pgErrorMessage($con);
            return ['status' => 'error', 'title' => 'Erro', 'message' => $msg];
        }

        if (pg_num_rows($res) === 0) {
            return ['status' => 'error', 'title' => 'Erro', 'message' => 'Sem retorno da função.'];
        }

        $mov = pg_fetch_result($res, 0, 'mov');

        return [
            'status'  => 'success',
            'title'   => 'Movimentação lançada',
            'message' => 'Movimentação registrada com sucesso!',
            'mov'     => $mov
        ];
    }

}
