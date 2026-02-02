## Como migrar

#### 1. Crear bdd vacía con nombre: molletexpress_api
#### 2. Importar estructura de la bdd
#### 3. Importar datos de la bdd
#### 4. Ejecutar script.sql para:
- cambiar date_created/date_modified por created_on/updated_on
- borrar tablas que doctrine no podrá por foreign keys
#### 5. Crear estructura con doctrine
#### 6. Ejecutar migraciones:
- bin/cavesman migrate:users:employees
- bin/cavesman check:status:albaranes
- bin/cavesman check:status:facturas

#### 7. Opcional (si fuese necesario cambiar la contraseña)
- bin/cavesman update:user:password
    - Username: admin
