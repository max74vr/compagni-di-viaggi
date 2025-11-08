<?php
/**
 * Script Setup Pagine
 *
 * Crea automaticamente tutte le pagine necessarie per il tema
 *
 * ISTRUZIONI:
 * 1. Carica questo file nella root di WordPress
 * 2. Visita: https://www.compagnidiviaggi.com/setup-pages.php
 * 3. Elimina il file dopo l'uso per sicurezza
 */

// Carica WordPress
require_once('wp-load.php');

// Verifica permessi admin
if (!current_user_can('manage_options')) {
    die('Accesso negato. Devi essere amministratore.');
}

echo '<h1>Setup Pagine - Compagni di Viaggi</h1>';
echo '<p>Creazione automatica delle pagine necessarie...</p>';
echo '<hr>';

// Array pagine da creare
$pages = array(
    array(
        'title' => 'Registrazione',
        'slug' => 'registrazione',
        'template' => 'page-registrazione.php',
        'content' => 'Questa pagina gestisce la registrazione degli utenti.',
    ),
    array(
        'title' => 'Profilo in Attesa',
        'slug' => 'profilo-in-attesa',
        'template' => 'page-profilo-in-attesa.php',
        'content' => 'Il tuo profilo è in attesa di approvazione.',
    ),
    array(
        'title' => 'Dashboard',
        'slug' => 'dashboard',
        'template' => 'page-dashboard.php',
        'content' => 'Gestisci i tuoi viaggi e le richieste.',
    ),
    array(
        'title' => 'Conferma Email',
        'slug' => 'conferma-email',
        'template' => 'page-conferma-email.php',
        'content' => 'Conferma il tuo indirizzo email.',
    ),
    array(
        'title' => 'Privacy Policy',
        'slug' => 'privacy',
        'template' => '',
        'content' => '<h2>Privacy Policy</h2><p>Inserisci qui la tua privacy policy.</p>',
    ),
    array(
        'title' => 'Termini e Condizioni',
        'slug' => 'termini',
        'template' => '',
        'content' => '<h2>Termini e Condizioni</h2><p>Inserisci qui i tuoi termini e condizioni.</p>',
    ),
    array(
        'title' => 'Chi Siamo',
        'slug' => 'chi-siamo',
        'template' => '',
        'content' => '<h2>Chi Siamo</h2><p>Compagni di Viaggi è la piattaforma per trovare compagni di viaggio.</p>',
    ),
    array(
        'title' => 'Blog',
        'slug' => 'blog',
        'template' => '',
        'content' => 'Articoli, consigli e ispirazioni per i tuoi viaggi.',
    ),
);

$created = 0;
$skipped = 0;

foreach ($pages as $page_data) {
    // Controlla se esiste già
    $existing = get_page_by_path($page_data['slug']);

    if ($existing) {
        echo '<p style="color: orange;">⚠️ <strong>' . $page_data['title'] . '</strong> - Già esistente (ID: ' . $existing->ID . ')</p>';
        $skipped++;
        continue;
    }

    // Crea la pagina
    $page_id = wp_insert_post(array(
        'post_title' => $page_data['title'],
        'post_name' => $page_data['slug'],
        'post_content' => $page_data['content'],
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => get_current_user_id(),
    ));

    if ($page_id) {
        // Imposta template se specificato
        if (!empty($page_data['template'])) {
            update_post_meta($page_id, '_wp_page_template', $page_data['template']);
        }

        echo '<p style="color: green;">✅ <strong>' . $page_data['title'] . '</strong> - Creata con successo! (ID: ' . $page_id . ')</p>';
        $created++;
    } else {
        echo '<p style="color: red;">❌ <strong>' . $page_data['title'] . '</strong> - Errore durante la creazione</p>';
    }
}

echo '<hr>';
echo '<h2>Riepilogo</h2>';
echo '<p><strong>Pagine create:</strong> ' . $created . '</p>';
echo '<p><strong>Pagine saltate (già esistenti):</strong> ' . $skipped . '</p>';

// Imposta la pagina Blog come "Posts page"
$blog_page = get_page_by_path('blog');
if ($blog_page) {
    update_option('page_for_posts', $blog_page->ID);
    echo '<p style="color: blue;">ℹ️ La pagina <strong>Blog</strong> è stata impostata come "Pagina articoli" nelle Impostazioni → Lettura</p>';
}

echo '<hr>';
echo '<h2>⚠️ IMPORTANTE - Operazioni Finali</h2>';
echo '<ol>';
echo '<li><strong>Elimina questo file</strong> per sicurezza: <code>setup-pages.php</code></li>';
echo '<li>Vai su <strong>Dashboard → Plugin</strong></li>';
echo '<li><strong>Disattiva</strong> il plugin "Compagni di Viaggi"</li>';
echo '<li><strong>Riattiva</strong> il plugin "Compagni di Viaggi" (questo crea le tabelle del database)</li>';
echo '<li>Vai su <strong>Impostazioni → Permalink</strong> e clicca <strong>Salva modifiche</strong></li>';
echo '</ol>';

echo '<hr>';
echo '<p><a href="' . admin_url() . '" style="background: #2271b1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Vai alla Dashboard</a></p>';
?>
