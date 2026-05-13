## Como migrar

#### 1. Crear bdd vacía
#### 2. Importar estructura de la bdd: schema.sql
#### 3. Importar datos de la bdd: export-data.sql (solo inserts)
#### 4. Ejecutar script.sql para:
- cambiar date_created/date_modified por created_on/updated_on
- borrar tablas que doctrine no podrá por foreign keys
#### 5. Crear estructura con doctrine
#### 6. Ejecutar migraciones:
- bin/cavesman migrate:users:employees
#### 7. Ejecutar check-status.sql:
- inicializa el campo status en las tablas albaran y factura
- lógica aplicada:
  - Si number está vacío → DRAFT
  - Si number tiene valor → ACTIVE
#### 8. Opcional (si fuese necesario cambiar la contraseña)
- bin/cavesman update:user:password
    - Username: admin
