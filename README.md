# Proci - WebApp (admin)

# Configurazione

- includere il database dal file [createdb.sql](createdb.sql)
- configurare il file [config.php](src/config.php)
  - DB_USER
  - DB_PASSWORD
  - DB_NAME

# Suddivisione File Progetto

- src - codice
  - . - codice php generale
  - css - codice css
  - js - codice javascript
  - admin - codice php relativo all'admin (webapp)
  - user - codice php relativo all'utente normale, fornisce il JSON all'app android
- res - risorse (immagini, come i favicon)

