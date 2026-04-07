UPDATE albaran SET status = CASE WHEN number IS NULL OR number = '' THEN 'DRAFT' ELSE 'ACTIVE' END;

UPDATE factura SET status = CASE WHEN number IS NULL OR number = '' THEN 'DRAFT' ELSE 'ACTIVE' END;