<?php
/**
 * Script di diagnostica Email Verification
 *
 * Verifica che il sistema di email verification sia configurato correttamente
 *
 * ISTRUZIONI:
 * 1. Carica questo file nella root di WordPress
 * 2. Visita: https://www.compagnidiviaggi.com/check-email-verification.php
 * 3. Elimina il file dopo l'uso per sicurezza
 */

// Carica WordPress
require_once('wp-load.php');

// Verifica permessi admin
if (!current_user_can('manage_options')) {
    die('Accesso negato. Devi essere amministratore.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Diagnostica Email Verification - Compagni di Viaggi</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #667eea; border-bottom: 3px solid #667eea; padding-bottom: 10px; }
        h2 { color: #333; margin-top: 30px; }
        .check { margin: 15px 0; padding: 15px; border-radius: 5px; }
        .check-ok { background: #d4edda; border-left: 4px solid #28a745; }
        .check-warning { background: #fff3cd; border-left: 4px solid #ffc107; }
        .check-error { background: #f8d7da; border-left: 4px solid #dc3545; }
        .icon { font-size: 20px; margin-right: 10px; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
        .info-box { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 15px 0; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnostica Email Verification</h1>
        <p>Questo script verifica che il sistema di email verification sia configurato correttamente.</p>

        <?php
        global $wpdb;

        // 1. Verifica esistenza tabella
        echo '<h2>1. Database</h2>';
        $table_name = $wpdb->prefix . 'cdv_email_verification';
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;

        if ($table_exists) {
            echo '<div class="check check-ok"><span class="icon">✓</span>Tabella <code>' . $table_name . '</code> esiste</div>';

            // Conta records
            $total_tokens = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            $pending_tokens = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE verified_at IS NULL");
            $verified_tokens = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE verified_at IS NOT NULL");

            echo '<div class="info-box">';
            echo '<strong>Statistiche tokens:</strong><br>';
            echo 'Totale: ' . $total_tokens . '<br>';
            echo 'In attesa di verifica: ' . $pending_tokens . '<br>';
            echo 'Verificati: ' . $verified_tokens;
            echo '</div>';

            // Mostra ultimi 5 tokens
            $recent_tokens = $wpdb->get_results("
                SELECT t.*, u.user_login, u.user_email
                FROM $table_name t
                LEFT JOIN {$wpdb->users} u ON t.user_id = u.ID
                ORDER BY t.created_at DESC
                LIMIT 5
            ");

            if ($recent_tokens) {
                echo '<details><summary>Ultimi 5 tokens generati</summary><pre>';
                foreach ($recent_tokens as $token) {
                    echo 'User: ' . $token->user_login . ' (' . $token->user_email . ')' . "\n";
                    echo 'Token: ' . substr($token->token, 0, 20) . '...' . "\n";
                    echo 'Creato: ' . $token->created_at . "\n";
                    echo 'Scade: ' . $token->expires_at . "\n";
                    echo 'Verificato: ' . ($token->verified_at ? $token->verified_at : 'NO') . "\n";
                    echo "---\n";
                }
                echo '</pre></details>';
            }
        } else {
            echo '<div class="check check-error"><span class="icon">✕</span>Tabella <code>' . $table_name . '</code> NON esiste</div>';
            echo '<div class="info-box"><strong>Soluzione:</strong> Vai su Dashboard → Plugin, disattiva e riattiva il plugin "Compagni di Viaggi"</div>';
        }

        // 2. Verifica pagina conferma-email
        echo '<h2>2. Pagina Conferma Email</h2>';
        $page = get_page_by_path('conferma-email');

        if ($page) {
            echo '<div class="check check-ok"><span class="icon">✓</span>Pagina "Conferma Email" esiste (ID: ' . $page->ID . ')</div>';
            echo '<div class="info-box">';
            echo '<strong>URL:</strong> ' . get_permalink($page->ID) . '<br>';
            echo '<strong>Template:</strong> ' . get_page_template_slug($page->ID) . '<br>';
            echo '<strong>Status:</strong> ' . $page->post_status;
            echo '</div>';

            // Verifica che il template esista
            $template_file = get_template_directory() . '/page-conferma-email.php';
            if (file_exists($template_file)) {
                echo '<div class="check check-ok"><span class="icon">✓</span>Template file esiste</div>';
            } else {
                echo '<div class="check check-error"><span class="icon">✕</span>Template file NON trovato: ' . $template_file . '</div>';
            }
        } else {
            echo '<div class="check check-error"><span class="icon">✕</span>Pagina "Conferma Email" NON esiste</div>';
            echo '<div class="info-box"><strong>Soluzione:</strong> Esegui lo script <code>setup-pages.php</code> per creare le pagine necessarie</div>';
        }

        // 3. Verifica permalink
        echo '<h2>3. Permalink</h2>';
        $permalink_structure = get_option('permalink_structure');

        if ($permalink_structure) {
            echo '<div class="check check-ok"><span class="icon">✓</span>Permalink configurati correttamente</div>';
            echo '<div class="info-box"><strong>Struttura:</strong> ' . $permalink_structure . '</div>';
        } else {
            echo '<div class="check check-warning"><span class="icon">⚠</span>Permalink non configurati (usando default)</div>';
            echo '<div class="info-box"><strong>Raccomandazione:</strong> Vai su Impostazioni → Permalink e scegli una struttura (es. "Nome articolo")</div>';
        }

        // 4. Verifica configurazione email
        echo '<h2>4. Configurazione Email</h2>';
        $admin_email = get_option('admin_email');
        echo '<div class="check check-ok"><span class="icon">✓</span>Email amministratore: ' . $admin_email . '</div>';

        // Test invio email (opzionale)
        if (isset($_GET['test_email']) && $_GET['test_email'] === '1') {
            $test_result = wp_mail(
                $admin_email,
                'Test Email - Compagni di Viaggi',
                'Questa è una email di test dal sistema di diagnostica.',
                array('Content-Type: text/html; charset=UTF-8')
            );

            if ($test_result) {
                echo '<div class="check check-ok"><span class="icon">✓</span>Test email inviata con successo a ' . $admin_email . '</div>';
            } else {
                echo '<div class="check check-error"><span class="icon">✕</span>Impossibile inviare email di test</div>';
                echo '<div class="info-box">';
                echo '<strong>Possibili cause:</strong><br>';
                echo '- Funzione mail() del server non configurata<br>';
                echo '- Necessario plugin SMTP (es. WP Mail SMTP)<br>';
                echo '- Email bloccata dal provider hosting';
                echo '</div>';
            }
        } else {
            echo '<div class="info-box"><a href="?test_email=1">Clicca qui per inviare una email di test</a></div>';
        }

        // 5. Verifica hook
        echo '<h2>5. WordPress Hooks</h2>';

        if (class_exists('CDV_Email_Verification')) {
            echo '<div class="check check-ok"><span class="icon">✓</span>Classe CDV_Email_Verification caricata</div>';

            // Verifica che gli hook siano registrati
            $hook_registered = has_action('template_redirect', array('CDV_Email_Verification', 'handle_verification'));
            if ($hook_registered !== false) {
                echo '<div class="check check-ok"><span class="icon">✓</span>Hook template_redirect registrato</div>';
            } else {
                echo '<div class="check check-error"><span class="icon">✕</span>Hook template_redirect NON registrato</div>';
            }

            $auth_filter = has_filter('authenticate', array('CDV_Email_Verification', 'block_unverified_login'));
            if ($auth_filter !== false) {
                echo '<div class="check check-ok"><span class="icon">✓</span>Filter authenticate registrato</div>';
            } else {
                echo '<div class="check check-error"><span class="icon">✕</span>Filter authenticate NON registrato</div>';
            }
        } else {
            echo '<div class="check check-error"><span class="icon">✕</span>Classe CDV_Email_Verification NON caricata</div>';
        }

        // 6. Test completo del flusso
        echo '<h2>6. Test Flusso Completo</h2>';
        echo '<div class="info-box">';
        echo '<strong>Per testare il flusso completo:</strong><br>';
        echo '1. Registra un nuovo utente dalla pagina di registrazione<br>';
        echo '2. Controlla che riceva l\'email con il link<br>';
        echo '3. Clicca sul link nell\'email<br>';
        echo '4. Verifica che veda la pagina di successo<br>';
        echo '5. Prova ad accedere con le credenziali<br>';
        echo '6. Controlla i log di errore per eventuali problemi';
        echo '</div>';

        // 7. Utenti non verificati
        echo '<h2>7. Utenti in Attesa di Verifica</h2>';
        $unverified_users = get_users(array(
            'meta_query' => array(
                array(
                    'key' => 'cdv_email_verified',
                    'value' => 'yes',
                    'compare' => '!='
                )
            ),
            'number' => 10
        ));

        if ($unverified_users) {
            echo '<div class="check check-warning"><span class="icon">⚠</span>Trovati ' . count($unverified_users) . ' utenti non verificati</div>';
            echo '<details><summary>Visualizza utenti</summary><pre>';
            foreach ($unverified_users as $user) {
                $email_verified = get_user_meta($user->ID, 'cdv_email_verified', true);
                $user_approved = get_user_meta($user->ID, 'cdv_user_approved', true);
                echo 'ID: ' . $user->ID . ' - ' . $user->user_login . ' (' . $user->user_email . ')' . "\n";
                echo 'Email verificata: ' . ($email_verified === 'yes' ? 'SÌ' : 'NO') . "\n";
                echo 'Utente approvato: ' . ($user_approved === '1' ? 'SÌ' : 'NO') . "\n";
                echo 'Registrato: ' . $user->user_registered . "\n";
                echo "---\n";
            }
            echo '</pre></details>';
        } else {
            echo '<div class="check check-ok"><span class="icon">✓</span>Nessun utente in attesa di verifica</div>';
        }

        // Pulsanti di azione
        echo '<h2>Azioni Rapide</h2>';
        echo '<div class="info-box">';
        echo '<a href="' . admin_url('admin.php?page=cdv-approvals-images') . '" style="background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-right: 10px;">Vai a Pannello Approvazioni</a>';
        echo '<a href="' . admin_url() . '" style="background: #2271b1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Dashboard WordPress</a>';
        echo '</div>';

        ?>

        <hr style="margin: 40px 0;">
        <p style="color: #999; font-size: 14px;">
            <strong>⚠️ IMPORTANTE:</strong> Elimina questo file dopo l'uso per motivi di sicurezza.<br>
            File da eliminare: <code>check-email-verification.php</code>
        </p>
    </div>
</body>
</html>
