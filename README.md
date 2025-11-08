# Compagni di Viaggi ✈️

**Piattaforma WordPress per trovare compagni di viaggio e organizzare avventure insieme.**

Una community viva di viaggiatori con funzionalità complete: profili utente, sistema di recensioni, chat di gruppo, badge e REST API per app mobile.

🌐 **Sito Live**: [www.compagnidiviaggi.com](https://www.compagnidiviaggi.com)

---

## 🌟 Caratteristiche Principali

### Per gli Utenti
- **Profili Viaggiatori Completi**: Bio, foto, stili di viaggio, lingue parlate e preferenze
- **Sistema di Verifica**: Badge di verifica identità per aumentare la fiducia
- **Badge e Reputazione**: Sistema di gamification con badge ottenibili e punteggio reputazione
- **Ricerca Avanzata**: Filtra viaggi per destinazione, date, tipo di viaggio e budget

### Gestione Viaggi
- **Custom Post Type "Viaggio"**: Sistema completo per creare e gestire viaggi
- **Tassonomie**: Tipi di viaggio (Avventura, Mare, Montagna, etc.) e destinazioni
- **Sistema Partecipanti**: Richieste di partecipazione con approvazione organizzatore
- **Stati Viaggio**: Aperto, Completo, In Corso, Completato, Annullato

### Sistema Recensioni
- **Valutazioni Multi-dimensionali**:
  - Puntualità
  - Spirito di gruppo
  - Rispetto degli altri
  - Capacità di adattamento
- **Commenti Opzionali**: Feedback dettagliato
- **Calcolo Automatico Reputazione**: Aggiornamento real-time del punteggio

### Chat Integrata
- **Chat di Gruppo per Viaggio**: Comunicazione tra partecipanti
- **AJAX Real-time**: Aggiornamenti automatici
- **Anti-spam**: Protezione contro messaggi eccessivi
- **Controllo Accessi**: Solo organizzatore e partecipanti accettati

### REST API per App Mobile
- **Autenticazione JWT**: Token sicuri per app mobile
- **Endpoint Completi**: Viaggi, profili, chat, recensioni, partecipanti
- **Database Condiviso**: Web e mobile usano lo stesso database WordPress
- **Documentazione API**: Endpoint pronti per React Native, Flutter, etc.

---

## 🛠 Stack Tecnologico

- **CMS**: WordPress 6.0+
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Frontend**: HTML5, CSS3 (CSS Variables), Vanilla JavaScript + jQuery
- **Design**: Responsive, Mobile-First
- **REST API**: WordPress REST API + Custom Endpoints
- **Autenticazione**: JWT per app mobile
- **Sicurezza**: Password hashing, CSRF protection, SQL injection prevention

---

## 📋 Requisiti di Sistema

### Server Requirements
- **PHP**: 7.4 o superiore
- **Database**: MySQL 5.7+ o MariaDB 10.3+
- **Web Server**: Apache 2.4+ o Nginx 1.18+
- **HTTPS**: Certificato SSL (Let's Encrypt consigliato)

### PHP Extensions Richieste
- `pdo`
- `pdo_mysql`
- `mysqli`
- `mbstring`
- `json`
- `curl`
- `gd` o `imagick` (per manipolazione immagini)
- `xml`
- `zip`

### Raccomandazioni
- **PHP Memory Limit**: 256M o superiore
- **Max Upload Size**: 64M o superiore
- **Max Execution Time**: 300 secondi

---

## 🚀 Installazione su CloudPanel

### 1. Preparazione Database

Accedi a CloudPanel e crea un nuovo database MySQL:

```
Nome Database: compagni_di_viaggi
Charset: utf8mb4
Collation: utf8mb4_unicode_ci
```

Annota le credenziali del database (host, nome, username, password).

### 2. Scarica WordPress

Sul tuo server CloudPanel:

```bash
cd /home/cloudpanel/htdocs/www.compagnidiviaggi.com
wget https://wordpress.org/latest.tar.gz
tar -xzf latest.tar.gz
mv wordpress/* .
rmdir wordpress
rm latest.tar.gz
```

### 3. Clona il Repository

```bash
cd /home/cloudpanel/htdocs/www.compagnidiviaggi.com

# Rimuovi i file wp-content predefiniti
rm -rf wp-content/plugins/akismet wp-content/plugins/hello.php
rm -rf wp-content/themes/twenty*

# Clona il repository
git clone https://github.com/max74vr/compagni-di-viaggi.git temp
mv temp/wp-content/* wp-content/
rm -rf temp
```

### 4. Configura WordPress

Crea il file `wp-config.php`:

```bash
cp wp-config-sample.php wp-config.php
nano wp-config.php
```

Modifica le credenziali del database:

```php
define( 'DB_NAME', 'compagni_di_viaggi' );
define( 'DB_USER', 'tuo_utente_db' );
define( 'DB_PASSWORD', 'tua_password_db' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', 'utf8mb4_unicode_ci' );
```

Genera le chiavi di sicurezza da [https://api.wordpress.org/secret-key/1.1/salt/](https://api.wordpress.org/secret-key/1.1/salt/) e sostituiscile nel file.

Aggiungi queste righe prima di `/* That's all, stop editing! */`:

```php
// Abilita debug solo in sviluppo
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );

// URL del sito
define( 'WP_HOME', 'https://www.compagnidiviaggi.com' );
define( 'WP_SITEURL', 'https://www.compagnidiviaggi.com' );

// Limiti di memoria
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );
```

### 5. Imposta i Permessi

```bash
cd /home/cloudpanel/htdocs/www.compagnidiviaggi.com

# Proprietario corretto
chown -R clp:clp .

# Permessi directory
find . -type d -exec chmod 755 {} \;

# Permessi file
find . -type f -exec chmod 644 {} \;

# wp-config.php più restrittivo
chmod 600 wp-config.php

# Directory scrivibili
chmod -R 775 wp-content/uploads
```

### 6. Installa WordPress

Visita `https://www.compagnidiviaggi.com/wp-admin/install.php` e completa l'installazione guidata:

1. Scegli la lingua (Italiano)
2. Inserisci:
   - Titolo del sito: **Compagni di Viaggi**
   - Username amministratore
   - Password sicura
   - Email amministratore
3. Clicca su "Installa WordPress"

### 7. Attiva Plugin e Tema

Dopo l'installazione, accedi alla dashboard WordPress:

1. **Attiva il Plugin**:
   - Vai su `Plugin` → `Plugin installati`
   - Trova "Compagni di Viaggi"
   - Clicca su "Attiva"

2. **Attiva il Tema**:
   - Vai su `Aspetto` → `Temi`
   - Trova "Compagni di Viaggi"
   - Clicca su "Attiva"

3. **Verifica Installazione**:
   - Vai su `Compagni di Viaggi` nel menu admin
   - Dovresti vedere la dashboard con le statistiche

### 8. Configurazione Iniziale

#### Impostazioni Plugin
Vai su `Compagni di Viaggi` → `Impostazioni`:
- Max partecipanti default: `10`
- Età minima: `18`
- Abilita chat: ✓
- Abilita recensioni: ✓

#### Permalink
Vai su `Impostazioni` → `Permalink`:
- Seleziona "Nome articolo" per URL friendly
- Clicca "Salva modifiche"

#### Menu di Navigazione
Vai su `Aspetto` → `Menu`:
1. Crea un nuovo menu "Menu Principale"
2. Aggiungi voci:
   - Home
   - Viaggi
   - (opzionale) Dashboard, Profilo, etc.
3. Assegna al menu "Menu Principale"
4. Salva

---

## 📱 Configurazione REST API per App Mobile

### Endpoint Disponibili

Base URL: `https://www.compagnidiviaggi.com/wp-json/cdv/v1/`

#### Autenticazione

**Registrazione**
```
POST /auth/register
Body: {
  "username": "string",
  "email": "string",
  "password": "string",
  "display_name": "string"
}
Response: {
  "token": "jwt_token",
  "user": { ... }
}
```

**Login**
```
POST /auth/login
Body: {
  "username": "string",
  "password": "string"
}
Response: {
  "token": "jwt_token",
  "user": { ... }
}
```

**Validazione Token**
```
POST /auth/validate
Body: {
  "token": "jwt_token"
}
```

#### Viaggi

**Lista Viaggi**
```
GET /travels?per_page=12&page=1&tipo_viaggio=avventura&search=roma
```

**Dettaglio Viaggio**
```
GET /travels/{id}
```

**Richiedi Partecipazione**
```
POST /travels/{id}/join
Headers: Authorization: Bearer {token}
Body: {
  "message": "Presentazione"
}
```

**Partecipanti**
```
GET /travels/{id}/participants
```

#### Chat

**Messaggi Chat**
```
GET /chats/{chat_group_id}/messages
POST /chats/{chat_group_id}/messages
Headers: Authorization: Bearer {token}
Body: {
  "message": "Testo messaggio"
}
```

#### Profili

**Profilo Utente**
```
GET /users/{id}/profile
```

**Badge Utente**
```
GET /users/{id}/badges
```

**Recensioni Utente**
```
GET /users/{id}/reviews
```

#### Dashboard

**I Miei Viaggi**
```
GET /dashboard/my-travels
Headers: Authorization: Bearer {token}
Response: {
  "organized": [...],
  "participating": [...]
}
```

### Autenticazione JWT nell'App

**React Native Example:**
```javascript
// Login
const response = await fetch('https://www.compagnidiviaggi.com/wp-json/cdv/v1/auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ username: 'user', password: 'pass' })
});
const { token } = await response.json();

// Store token
await AsyncStorage.setItem('jwt_token', token);

// Use token for authenticated requests
const travels = await fetch('https://www.compagnidiviaggi.com/wp-json/cdv/v1/travels/123/join', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({ message: 'Ciao!' })
});
```

---

## 🎨 Personalizzazione

### Colori del Tema

Modifica `/wp-content/themes/compagni-viaggi/style.css`:

```css
:root {
    --primary-color: #667eea;      /* Colore primario */
    --secondary-color: #764ba2;    /* Colore secondario */
    --success-color: #48bb78;      /* Verde successo */
    --error-color: #f56565;        /* Rosso errore */
    --warning-color: #ed8936;      /* Arancione warning */
}
```

### Logo

1. Vai su `Aspetto` → `Personalizza`
2. Seleziona "Identità del sito"
3. Carica il tuo logo
4. Salva

### Widget Footer

Vai su `Aspetto` → `Widget`:
- Footer 1: Aggiungi widget per prima colonna
- Footer 2: Aggiungi widget per seconda colonna
- Footer 3: Aggiungi widget per terza colonna

---

## 🔒 Sicurezza

### Implementazioni di Sicurezza

- ✅ Password hashing con bcrypt (WordPress default)
- ✅ Prepared statements PDO per prevenire SQL injection
- ✅ Nonce validation per CSRF protection
- ✅ Sanitizzazione input e output
- ✅ Validazione file upload
- ✅ JWT sicuri per autenticazione API
- ✅ HTTPS obbligatorio per produzione

### Raccomandazioni Aggiuntive

1. **Installa un plugin di sicurezza**:
   - Wordfence Security
   - iThemes Security
   - Sucuri Security

2. **Backup Automatici**:
   - UpdraftPlus
   - BackWPup
   - CloudPanel Backup (se disponibile)

3. **Limita tentativi di login**:
   - Limit Login Attempts Reloaded

4. **Firewall**:
   - Cloudflare (consigliato)
   - ModSecurity su server

---

## 📊 Database Schema

Il plugin crea 5 tabelle custom:

- **wp_cdv_travel_participants**: Partecipanti ai viaggi
- **wp_cdv_chat_groups**: Gruppi chat
- **wp_cdv_chat_messages**: Messaggi chat
- **wp_cdv_reviews**: Recensioni utenti
- **wp_cdv_user_badges**: Badge ottenuti

Usa anche tabelle WordPress standard:
- **wp_users**: Utenti
- **wp_usermeta**: Metadati utenti (profili estesi)
- **wp_posts**: Viaggi (custom post type)
- **wp_postmeta**: Metadati viaggi
- **wp_terms**: Tassonomie (tipi viaggio, destinazioni)

---

## 🐛 Troubleshooting

### Errore "Headers already sent"
Controlla che `wp-config.php` non abbia spazi prima di `<?php`

### Errore 404 su pagine viaggi
Vai su `Impostazioni` → `Permalink` e clicca "Salva modifiche"

### Plugin non si attiva
Verifica i requisiti PHP e controlla i log:
```bash
tail -f /var/log/php-fpm/error.log
```

### Upload falliti
```bash
chmod -R 775 /home/cloudpanel/htdocs/www.compagnidiviaggi.com/wp-content/uploads
```

### Database connection error
Verifica credenziali in `wp-config.php` e che il database esista:
```bash
mysql -u root -p
SHOW DATABASES;
```

### REST API non funziona
Verifica che i permalink siano configurati e che `.htaccess` esista:
```bash
ls -la /home/cloudpanel/htdocs/www.compagnidiviaggi.com/.htaccess
```

---

## 📝 To-Do / Roadmap

- [ ] Sistema di notifiche email
- [ ] App mobile (React Native / Flutter)
- [ ] Integrazione pagamenti per viaggi a pagamento
- [ ] Gallery foto per viaggi
- [ ] Blog/storie di viaggio
- [ ] Mappa interattiva con destinazioni
- [ ] Social login (Google, Facebook)
- [ ] Sistema referral/inviti
- [ ] Admin panel moderazione avanzata
- [ ] Messaggi diretti (DM) tra utenti
- [ ] Calendario eventi/viaggi

---

## 🤝 Contribuire

Questo è un progetto open source. Per contribuire:

1. Fork il repository
2. Crea un branch (`git checkout -b feature/AmazingFeature`)
3. Commit le modifiche (`git commit -m 'Add some AmazingFeature'`)
4. Push al branch (`git push origin feature/AmazingFeature`)
5. Apri una Pull Request

---

## 📄 Licenza

GNU General Public License v2 or later

---

## 👥 Autore

**Max74vr**
- GitHub: [@max74vr](https://github.com/max74vr)
- Website: [www.compagnidiviaggi.com](https://www.compagnidiviaggi.com)

---

## 🙏 Supporto

Per domande o supporto:
- Apri un [Issue su GitHub](https://github.com/max74vr/compagni-di-viaggi/issues)
- Email: (la tua email)

---

**Buon viaggio! ✈️🌍**
