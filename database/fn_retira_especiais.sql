CREATE EXTENSION IF NOT EXISTS unaccent;

CREATE OR REPLACE FUNCTION fn_retira_especiais(p_texto TEXT)
RETURNS TEXT AS
$$
DECLARE
    v_resultado TEXT;
BEGIN
    IF p_texto IS NULL THEN
        RETURN NULL;
    END IF;

    v_resultado := unaccent(p_texto);

    v_resultado := regexp_replace(v_resultado, '[^a-zA-Z0-9 ]', '', 'g');

    v_resultado := lower(v_resultado);

    RETURN v_resultado;
END;
$$ LANGUAGE plpgsql IMMUTABLE;
PAULO