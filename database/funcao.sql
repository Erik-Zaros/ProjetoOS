CREATE OR REPLACE FUNCTION fn_lanca_movimentacao_estoque(
    p_posto   INTEGER,
    p_produto INTEGER DEFAULT NULL,
    p_peca    INTEGER DEFAULT NULL,
    p_tipo    CHAR(1) DEFAULT NULL,
    p_qtde    INTEGER DEFAULT NULL,
    p_os      BIGINT  DEFAULT NULL,
    p_motivo  TEXT    DEFAULT NULL,
    p_usuario INTEGER DEFAULT NULL
) RETURNS BIGINT AS $$
DECLARE
    v_delta    INTEGER;
    v_saldo    INTEGER;
    v_mov_id   BIGINT;
    v_motivo   TEXT;
BEGIN
    IF (p_produto IS NOT NULL AND p_peca IS NOT NULL)
       OR (p_produto IS NULL AND p_peca IS NULL) THEN
        RAISE EXCEPTION 'Informe apenas PRODUTO ou apenas PEÇA.';
    END IF;

    IF p_qtde IS NULL OR p_qtde <= 0 THEN
        RAISE EXCEPTION 'Quantidade deve ser > 0.';
    END IF;

    IF p_tipo IS NULL OR p_tipo NOT IN ('E','S') THEN
        RAISE EXCEPTION 'Tipo inválido: %. Use E (entrada) ou S (saída).', p_tipo;
    END IF;

    v_delta := CASE WHEN p_tipo = 'E' THEN p_qtde ELSE -p_qtde END;

    v_motivo := COALESCE(
        p_motivo,
        CASE
          WHEN p_os IS NOT NULL THEN
            (CASE WHEN p_produto IS NOT NULL THEN 'Produto' ELSE 'Peça' END)
            || ' lançada na OS ' || p_os::text
        END
    );

    IF p_produto IS NOT NULL THEN

        IF p_tipo = 'S' THEN
            SELECT qtde INTO v_saldo
            FROM tbl_estoque
            WHERE posto = p_posto AND produto = p_produto;

            IF NOT FOUND THEN
                RAISE EXCEPTION 'Produto não encontrado no estoque do posto.';
            END IF;

            IF v_saldo < p_qtde THEN
                RAISE EXCEPTION 'Saldo insuficiente para produto. Saldo atual: %, tentativa de saída: %.', v_saldo, p_qtde;
            END IF;
        END IF;

        LOOP
            UPDATE tbl_estoque e
               SET qtde = e.qtde + v_delta,
                   data_input = NOW()
             WHERE e.posto = p_posto AND e.produto = p_produto
             RETURNING e.qtde INTO v_saldo;
            EXIT WHEN FOUND;

            BEGIN
                INSERT INTO tbl_estoque (posto, produto, qtde)
                VALUES (p_posto, p_produto, v_delta)
                RETURNING qtde INTO v_saldo;
                EXIT;
            EXCEPTION WHEN unique_violation THEN

            END;
        END LOOP;

        INSERT INTO tbl_estoque_movimento (posto, produto, tipo, qtde, os, motivo, usuario)
        VALUES (p_posto, p_produto, p_tipo, p_qtde, p_os, v_motivo, p_usuario)
        RETURNING estoque_movimento INTO v_mov_id;

    ELSE

        IF p_tipo = 'S' THEN
            SELECT qtde INTO v_saldo
            FROM tbl_estoque
            WHERE posto = p_posto AND peca = p_peca;

            IF NOT FOUND THEN
                RAISE EXCEPTION 'Peça não encontrada no estoque do posto.';
            END IF;

            IF v_saldo < p_qtde THEN
                RAISE EXCEPTION 'Saldo insuficiente para peça. Saldo atual: %, tentativa de saída: %.', v_saldo, p_qtde;
            END IF;
        END IF;

        LOOP
            UPDATE tbl_estoque e
               SET qtde = e.qtde + v_delta,
                   data_input = NOW()
             WHERE e.posto = p_posto AND e.peca = p_peca
             RETURNING e.qtde INTO v_saldo;
            EXIT WHEN FOUND;

            BEGIN
                INSERT INTO tbl_estoque (posto, peca, qtde)
                VALUES (p_posto, p_peca, v_delta)
                RETURNING qtde INTO v_saldo;
                EXIT;
            EXCEPTION WHEN unique_violation THEN

            END;
        END LOOP;

        INSERT INTO tbl_estoque_movimento (posto, peca, tipo, qtde, os, motivo, usuario)
        VALUES (p_posto, p_peca, p_tipo, p_qtde, p_os, v_motivo, p_usuario)
        RETURNING estoque_movimento INTO v_mov_id;
    END IF;

    RETURN v_mov_id;
END;
$$ LANGUAGE plpgsql;
