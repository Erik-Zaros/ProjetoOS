<?php

namespace App\Repository;

use App\Core\Db;
use App\Model\Posto;

class PostoRepository
{
    public function buscarPorId(int $postoId): ?Posto
    {
        $con = Db::getConnection();

        $sql = "SELECT posto,
                       nome,
                       ativo,
                       modulo
                FROM tbl_posto
                WHERE posto = {$postoId}
                LIMIT 1";

        $res = pg_query($con, $sql);

        if (!$res || pg_num_rows($res) === 0) {
            return null;
        }

        return $this->tratar(pg_fetch_assoc($res));
    }

    public function buscarNome(int $postoId): ?string
    {
        $con = Db::getConnection();

        $sql = "SELECT trim(upper(fn_retira_especiais(nome))) AS nome_posto
                FROM tbl_posto
                WHERE posto = {$postoId}
                LIMIT 1";

        $res = pg_query($con, $sql);

        if (!$res || pg_num_rows($res) === 0) {
            return null;
        }

        return pg_fetch_result($res, 0, 'nome_posto');
    }

    public function usaModulo(int $postoId, string $modulo): bool
    {
        $con = Db::getConnection();

        $modulo = pg_escape_string($con, $modulo);

        $sql = "SELECT 1
                FROM tbl_posto
                WHERE posto = {$postoId}
                AND modulo ? '{$modulo}'
                AND trim(modulo->>'{$modulo}') = 't'
                LIMIT 1";

        $res = pg_query($con, $sql);

        return $res && pg_num_rows($res) > 0;
    }

    public function listarTodos(): array
    {
        $con = Db::getConnection();

        $sql = "SELECT posto,
                       nome,
                       ativo,
                       modulo
                FROM tbl_posto
                ORDER BY nome";

        $res = pg_query($con, $sql);

        if (!$res) {
            return [];
        }

        $postos = [];

        while ($row = pg_fetch_assoc($res)) {
            $postos[] = $this->tratar($row)->toArray();
        }

        return $postos;
    }

    private function tratar(array $row): Posto
    {
        return new Posto([
            'posto'  => $row['posto'] ?? null,
            'nome'   => $row['nome'] ?? '',
            'ativo'  => $row['ativo'] ?? false,
            'modulo' => $row['modulo'] ?? [],
        ]);
    }
}
