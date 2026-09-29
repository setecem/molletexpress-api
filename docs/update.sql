-- appDeCA: papel en el DeCA (TRANSPORTISTA_EFECTIVO | CARGADOR_CONTRACTUAL)
ALTER TABLE albaran ADD transport_role VARCHAR(32) DEFAULT NULL;

-- appDeCA: vinculación con una empresa de DeCA (Configuración → DeCA en app)
CREATE TABLE deca_connection (id INT AUTO_INCREMENT NOT NULL, created_on DATETIME DEFAULT NULL, updated_on DATETIME DEFAULT NULL, deleted_on DATETIME DEFAULT NULL, url VARCHAR(255) NOT NULL, api_key LONGTEXT NOT NULL, key_prefix VARCHAR(64) DEFAULT NULL, enterprise VARCHAR(255) DEFAULT NULL, own_partner_id INT DEFAULT NULL, own_partner_name VARCHAR(255) DEFAULT NULL, last_check DATETIME DEFAULT NULL, last_error LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4;

-- appDeCA: permiso DeCA (Acceso / Editar) para la pantalla app → DeCA.
-- schema-tool:update amplía los ENUM; después hay que crear las filas de permisos:
--   php bin/cavesman sync:roles
-- y asignar DeCA → Acceso / Editar a quien vaya a configurar la vinculación (Empleados → Permisos).
ALTER TABLE employee_role CHANGE `group` `group` ENUM('EMPLOYEE', 'CLIENT', 'INVOICE', 'DELIVERY_NOTE', 'SERVICE', 'CHARGE_ORDER', 'DECA') NOT NULL;
ALTER TABLE role CHANGE `group` `group` ENUM('EMPLOYEE', 'CLIENT', 'INVOICE', 'DELIVERY_NOTE', 'SERVICE', 'CHARGE_ORDER', 'DECA') NOT NULL;

-- appDeCA: borrador de DeCA creado desde el albarán (vía API pública de DeCA)
ALTER TABLE albaran ADD deca_id INT DEFAULT NULL, ADD deca_status VARCHAR(16) DEFAULT NULL, ADD deca_error LONGTEXT DEFAULT NULL;

-- appDeCA: clientes vinculados como terceros de DeCA (app → DeCA → Clientes en DeCA)
ALTER TABLE client ADD deca_partner_id INT DEFAULT NULL, ADD deca_hash VARCHAR(40) DEFAULT NULL, ADD deca_synced_at DATETIME DEFAULT NULL;
