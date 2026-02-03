-- =========================
-- Renombrado de columnas de fechas
-- =========================

ALTER TABLE albaran
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE albaran_linea
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE albaran_linea_certificada
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE alert
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE client
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();         

ALTER TABLE departament
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE employee
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE factura
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE factura_linea
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE factura_linea_certificada
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE orden_cobro
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE pedido
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE pedido_linea
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE pedido_linea_certificada
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE presupuesto
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE presupuesto_linea
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();
 
ALTER TABLE presupuesto_linea_certificada
	CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
	CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE producto
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();
 
ALTER TABLE profile
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE profile_action
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE profile_action_group
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE profile_action_rel
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE user
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();

ALTER TABLE user_departament
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();
 
ALTER TABLE user_type
 CHANGE COLUMN date_created created_on DATETIME NULL DEFAULT current_timestamp(),
 CHANGE COLUMN date_modified updated_on DATETIME NULL DEFAULT current_timestamp();
 
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS pedido;
DROP TABLE IF EXISTS pedido_linea;
DROP TABLE IF EXISTS pedido_linea_certificada;
DROP TABLE IF EXISTS presupuesto;
DROP TABLE IF EXISTS presupuesto_linea;
DROP TABLE IF EXISTS presupuesto_linea_certificada;
DROP TABLE IF EXISTS profile;
DROP TABLE IF EXISTS profile_action;
DROP TABLE IF EXISTS profile_action_group;
DROP TABLE IF EXISTS profile_action_rel;
DROP TABLE IF EXISTS user_departament;
DROP TABLE IF EXISTS user_hours;
DROP TABLE IF EXISTS user_type;
SET FOREIGN_KEY_CHECKS = 1;