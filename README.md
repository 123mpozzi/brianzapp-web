# Proci - WebApp (admin)

# Configurazione

- includere il database dal file [createdb.sql](createdb.sql)
  ```MySQL
  create database dbproci;
  use dbproci;
  source C:/xampp/htdocs/WebApp/createdb.sql;
  ```
  
  e aggiungere un account di prova al database (per testare le funzionalità del sito)
  ```MySQL
  insert into utente (user, password) values ('proci', SHA2('password', 256));
  ```
  
- configurare il db e il proprio account SQL nel file [config.ini](config.ini) (crearlo se non è già presente)
  ```ini
  [mysql]
  user = root
  password = admin
  host = localhost
  dbname = dbproci
  ```

# Suddivisione File Progetto

- src - codice
  - . - codice php generale
  - css - codice css
  - js - codice javascript
  - admin - codice php relativo all'admin (webapp)
  - user - codice php relativo all'utente normale, fornisce il JSON all'app android
- res - risorse (immagini, come i favicon)

