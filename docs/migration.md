## Como migrar

#### 1. Crear bdd vacía
#### 2. Crear estructura con doctrine
#### 3. Importar bdd 
#### 4. Ejecutar bin/cavesman install
#### 5. Ejecutar migraciones:

- bin/cavesman migrate:users:employees
- bin/cavesman migrate:files:contacts 
- bin/cavesman migrate:files:employees 
  - (No hace falta porque no hay ficheros)
- bin/cavesman update:employee:password 
  - Username: admin
