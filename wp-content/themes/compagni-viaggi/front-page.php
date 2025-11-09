<?php
/**
 * Homepage Template
 */

get_header();
?>

<main class="site-main">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>Trova il Tuo Compagno di Viaggio</h1>
                <p>Unisciti alla community di viaggiatori. Scopri nuove destinazioni, trova compagni di viaggio e crea ricordi indimenticabili insieme.</p>

                <!-- Search Box -->
                <div class="search-box">
                    <form class="search-form" action="<?php echo esc_url(home_url('/viaggi')); ?>" method="get">
                        <div class="form-group">
                            <label for="destination">Destinazione</label>
                            <input type="text" id="destination" name="s" placeholder="Dove vuoi andare?">
                        </div>

                        <div class="form-group">
                            <label for="travel_type">Tipo di Viaggio</label>
                            <select id="travel_type" name="tipo_viaggio">
                                <option value="">Tutti i tipi</option>
                                <?php
                                $types = get_terms(array(
                                    'taxonomy' => 'tipo_viaggio',
                                    'hide_empty' => false,
                                ));
                                foreach ($types as $type) {
                                    echo '<option value="' . esc_attr($type->slug) . '">' . esc_html($type->name) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" class="btn-search">Cerca Viaggi</button>
                    </form>

                    <!-- CTA Button -->
                    <div class="hero-cta" style="text-align: center; margin-top: calc(var(--spacing-unit) * 4);">
                        <a href="<?php echo esc_url(home_url('/crea-viaggio')); ?>" class="btn-primary btn-large" style="font-size: 1.1rem; padding: calc(var(--spacing-unit) * 2) calc(var(--spacing-unit) * 4); display: inline-flex; align-items: center; gap: calc(var(--spacing-unit) * 1); box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
                            ✨ Inserisci il Tuo Annuncio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Travels -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>Proposte di Viaggi</h2>
                <p>Scopri i viaggi più popolari della community</p>
            </div>

            <div class="grid">
                <?php
                $featured_travels = new WP_Query(array(
                    'post_type' => 'viaggio',
                    'posts_per_page' => 6,
                    'meta_query' => array(
                        array(
                            'key' => 'cdv_travel_status',
                            'value' => 'open',
                            'compare' => '=',
                        ),
                        array(
                            'key' => 'cdv_end_date',
                            'value' => date('Y-m-d'),
                            'compare' => '>=',
                            'type' => 'DATE',
                        ),
                    ),
                ));

                if ($featured_travels->have_posts()) :
                    while ($featured_travels->have_posts()) : $featured_travels->the_post();
                        get_template_part('template-parts/content', 'travel-card');
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <div class="no-travels">
                        <p>Nessun viaggio disponibile al momento. <?php if (is_user_logged_in()) : ?><a href="<?php echo esc_url(home_url('/crea-viaggio')); ?>">Crea il primo annuncio!</a><?php endif; ?></p>
                    </div>
                    <?php
                endif;
                ?>
            </div>

            <div class="text-center mt-3">
                <a href="<?php echo esc_url(get_post_type_archive_link('viaggio')); ?>" class="btn-primary">
                    Vedi Tutti i Viaggi →
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="section" style="background-color: white;">
        <div class="container">
            <div class="section-title">
                <h2>Come Funziona</h2>
                <p>In pochi semplici passi puoi trovare i tuoi compagni di viaggio</p>
            </div>

            <div class="grid">
                <div class="feature-card">
                    <div class="feature-icon">👤</div>
                    <h3>1. Crea il Tuo Profilo</h3>
                    <p>Registrati e completa il tuo profilo con interessi, lingue parlate e stili di viaggio preferiti.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🔍</div>
                    <h3>2. Cerca o Crea un Viaggio</h3>
                    <p>Cerca tra i viaggi disponibili o crea il tuo e aspetta che altri viaggiatori si uniscano.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">💬</div>
                    <h3>3. Connettiti e Organizza</h3>
                    <p>Usa la chat di gruppo per conoscere i compagni di viaggio e organizzare i dettagli insieme.</p>
                </div>
            </div>

            <style>
                .feature-card {
                    text-align: center;
                    padding: calc(var(--spacing-unit) * 4);
                }
                .feature-icon {
                    font-size: 4rem;
                    margin-bottom: calc(var(--spacing-unit) * 2);
                }
                .feature-card h3 {
                    color: var(--primary-color);
                    margin-bottom: calc(var(--spacing-unit) * 2);
                }
            </style>
        </div>
    </section>

    <!-- Travel Stories Section -->
    <section class="section" style="background-color: white;">
        <div class="container">
            <div class="section-title">
                <h2>📖 Racconti di Viaggio</h2>
                <p>Lasciati ispirare dalle esperienze dei nostri viaggiatori</p>
            </div>

            <div class="stories-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: calc(var(--spacing-unit) * 4);">
                <?php
                $recent_stories = new WP_Query(array(
                    'post_type' => 'racconto',
                    'posts_per_page' => 3,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC',
                ));

                if ($recent_stories->have_posts()) :
                    while ($recent_stories->have_posts()) : $recent_stories->the_post();
                        get_template_part('template-parts/content', 'story-card');
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <div class="no-stories" style="grid-column: 1 / -1; text-align: center; padding: calc(var(--spacing-unit) * 4) 0;">
                        <p style="color: var(--text-medium);">Nessun racconto disponibile al momento.</p>
                    </div>
                    <?php
                endif;
                ?>
            </div>

            <?php if ($recent_stories->found_posts > 0) : ?>
                <div class="text-center mt-3">
                    <a href="<?php echo esc_url(home_url('/racconti')); ?>" class="btn-primary">
                        Vedi Tutti i Racconti →
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="section">
        <div class="container">
            <div class="stats-grid">
                <?php
                global $wpdb;
                $total_travels = wp_count_posts('viaggio')->publish;
                $total_users = count_users()['total_users'];
                $total_participants = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cdv_travel_participants WHERE status = 'accepted'");
                ?>

                <div class="stat-item">
                    <div class="stat-number"><?php echo $total_travels; ?></div>
                    <div class="stat-label">Viaggi Pubblicati</div>
                </div>

                <div class="stat-item">
                    <div class="stat-number"><?php echo $total_users; ?></div>
                    <div class="stat-label">Viaggiatori</div>
                </div>

                <div class="stat-item">
                    <div class="stat-number"><?php echo $total_participants; ?></div>
                    <div class="stat-label">Partecipazioni</div>
                </div>

                <div class="stat-item">
                    <div class="stat-number">4.8</div>
                    <div class="stat-label">Rating Medio</div>
                </div>
            </div>

            <style>
                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: calc(var(--spacing-unit) * 4);
                    text-align: center;
                }
                .stat-number {
                    font-size: 3rem;
                    font-weight: 700;
                    color: var(--primary-color);
                    margin-bottom: calc(var(--spacing-unit) * 1);
                }
                .stat-label {
                    font-size: 1.1rem;
                    color: var(--text-medium);
                }
            </style>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section cta-section" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
        <div class="container text-center">
            <h2 style="color: white;">Pronto per la Tua Prossima Avventura?</h2>
            <p style="font-size: 1.2rem; margin-bottom: calc(var(--spacing-unit) * 4); opacity: 0.95;">
                Unisciti a migliaia di viaggiatori che hanno già trovato i loro compagni di viaggio perfetti.
            </p>
            <?php if (is_user_logged_in()) : ?>
                <a href="<?php echo esc_url(home_url('/crea-viaggio')); ?>" class="btn-primary">
                    Crea il Tuo Annuncio
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/registrazione')); ?>" class="btn-primary">
                    Registrati Gratis
                </a>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
get_footer();
