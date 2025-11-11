<?php
/**
 * Archive template for Viaggi
 */

get_header();
?>

<main class="site-main">
    <div class="page-header">
        <div class="container">
            <h1>Tutti i Viaggi</h1>
            <p>Esplora tutti i viaggi disponibili e trova la tua prossima avventura</p>
        </div>
    </div>

    <div class="container">
        <div class="archive-layout">
            <!-- Filters Sidebar -->
            <aside class="filters-sidebar">
                <h3>Filtra Viaggi</h3>

                <form method="get" action="<?php echo esc_url(get_post_type_archive_link('viaggio')); ?>" class="filters-form">
                    <div class="filter-group">
                        <label for="search">Cerca</label>
                        <input type="text" id="search" name="s" value="<?php echo get_search_query(); ?>" placeholder="Destinazione...">
                    </div>

                    <div class="filter-group">
                        <label for="tipo_viaggio">Tipo di Viaggio</label>
                        <select id="tipo_viaggio" name="tipo_viaggio">
                            <option value="">Tutti</option>
                            <?php
                            $types = get_terms(array(
                                'taxonomy' => 'tipo_viaggio',
                                'hide_empty' => false,
                            ));
                            foreach ($types as $type) {
                                $selected = isset($_GET['tipo_viaggio']) && $_GET['tipo_viaggio'] === $type->slug ? 'selected' : '';
                                echo '<option value="' . esc_attr($type->slug) . '" ' . $selected . '>' . esc_html($type->name) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="destinazione">Destinazione</label>
                        <select id="destinazione" name="destinazione">
                            <option value="">Tutte</option>
                            <?php
                            $destinations = get_terms(array(
                                'taxonomy' => 'destinazione',
                                'hide_empty' => false,
                            ));
                            foreach ($destinations as $dest) {
                                $selected = isset($_GET['destinazione']) && $_GET['destinazione'] === $dest->slug ? 'selected' : '';
                                echo '<option value="' . esc_attr($dest->slug) . '" ' . $selected . '>' . esc_html($dest->name) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Periodo di Partenza</label>
                        <input type="month" name="date_from" value="<?php echo isset($_GET['date_from']) ? esc_attr($_GET['date_from']) : ''; ?>" placeholder="Da">
                        <input type="month" name="date_to" value="<?php echo isset($_GET['date_to']) ? esc_attr($_GET['date_to']) : ''; ?>" placeholder="A" style="margin-top: 8px;">
                    </div>

                    <div class="filter-group">
                        <label>Budget per Persona (€)</label>
                        <div style="display: flex; gap: 8px;">
                            <input type="number" name="budget_min" value="<?php echo isset($_GET['budget_min']) ? esc_attr($_GET['budget_min']) : ''; ?>" placeholder="Min" min="0">
                            <input type="number" name="budget_max" value="<?php echo isset($_GET['budget_max']) ? esc_attr($_GET['budget_max']) : ''; ?>" placeholder="Max" min="0">
                        </div>
                    </div>

                    <div class="filter-group">
                        <label for="max_participants">Numero Partecipanti</label>
                        <select id="max_participants" name="max_participants">
                            <option value="">Tutti</option>
                            <option value="2-5" <?php selected(isset($_GET['max_participants']) && $_GET['max_participants'] === '2-5'); ?>>2-5 persone</option>
                            <option value="6-10" <?php selected(isset($_GET['max_participants']) && $_GET['max_participants'] === '6-10'); ?>>6-10 persone</option>
                            <option value="11-20" <?php selected(isset($_GET['max_participants']) && $_GET['max_participants'] === '11-20'); ?>>11-20 persone</option>
                            <option value="20+" <?php selected(isset($_GET['max_participants']) && $_GET['max_participants'] === '20+'); ?>>Più di 20</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="travel_status">Stato Viaggio</label>
                        <select id="travel_status" name="travel_status">
                            <option value="">Tutti</option>
                            <option value="open" <?php selected(isset($_GET['travel_status']) && $_GET['travel_status'] === 'open'); ?>>Aperto</option>
                            <option value="full" <?php selected(isset($_GET['travel_status']) && $_GET['travel_status'] === 'full'); ?>>Completo</option>
                            <option value="closed" <?php selected(isset($_GET['travel_status']) && $_GET['travel_status'] === 'closed'); ?>>Chiuso</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="orderby">Ordina per</label>
                        <select id="orderby" name="orderby">
                            <option value="date" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'date'); ?>>Più Recenti</option>
                            <option value="start_date" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'start_date'); ?>>Data Partenza</option>
                            <option value="budget_asc" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'budget_asc'); ?>>Budget: Basso → Alto</option>
                            <option value="budget_desc" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'budget_desc'); ?>>Budget: Alto → Basso</option>
                            <option value="participants" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'participants'); ?>>Posti Disponibili</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%;">Applica Filtri</button>

                    <?php if (!empty($_GET['s']) || !empty($_GET['tipo_viaggio']) || !empty($_GET['destinazione']) ||
                              !empty($_GET['date_from']) || !empty($_GET['date_to']) || !empty($_GET['budget_min']) ||
                              !empty($_GET['budget_max']) || !empty($_GET['max_participants']) || !empty($_GET['travel_status']) ||
                              (isset($_GET['orderby']) && $_GET['orderby'] !== 'date')) : ?>
                        <a href="<?php echo esc_url(get_post_type_archive_link('viaggio')); ?>" class="btn-secondary" style="width: 100%; text-align: center; margin-top: 10px;">
                            Reset Filtri
                        </a>
                    <?php endif; ?>
                </form>
            </aside>

            <!-- Travels Grid -->
            <div class="travels-content">
                <?php
                // Separate active and expired travels
                $active_travels = array();
                $expired_travels = array();
                $today = date('Y-m-d');

                if (have_posts()) :
                    while (have_posts()) : the_post();
                        $end_date = get_post_meta(get_the_ID(), 'cdv_end_date', true);
                        if ($end_date && $end_date < $today) {
                            $expired_travels[] = $post;
                        } else {
                            $active_travels[] = $post;
                        }
                    endwhile;
                    wp_reset_postdata();

                    // Get total count from query (not just current page)
                    global $wp_query;
                    $total_travels = $wp_query->found_posts;
                    ?>
                    <div class="results-header">
                        <p>
                            <?php echo $total_travels . ' ' . ($total_travels === 1 ? 'viaggio trovato' : 'viaggi trovati'); ?>
                        </p>
                    </div>

                    <div class="grid">
                        <?php
                        // Show active travels first
                        foreach ($active_travels as $post) :
                            setup_postdata($post);
                            get_template_part('template-parts/content', 'travel-card');
                        endforeach;

                        // Show expired travels with badge
                        foreach ($expired_travels as $post) :
                            setup_postdata($post);
                            set_query_var('is_expired', true);
                            get_template_part('template-parts/content', 'travel-card');
                            set_query_var('is_expired', false);
                        endforeach;
                        wp_reset_postdata();
                        ?>
                    </div>

                    <?php cdv_pagination(); ?>

                <?php else : ?>
                    <div class="no-results">
                        <h2>Nessun viaggio trovato</h2>
                        <p>Prova a modificare i filtri di ricerca o <a href="<?php echo esc_url(get_post_type_archive_link('viaggio')); ?>">visualizza tutti i viaggi</a>.</p>
                        <?php if (is_user_logged_in()) : ?>
                            <a href="<?php echo esc_url(admin_url('post-new.php?post_type=viaggio')); ?>" class="btn-primary">
                                Crea il Primo Viaggio
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<style>
.page-header {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: calc(var(--spacing-unit) * 6) 0;
    text-align: center;
    margin-bottom: calc(var(--spacing-unit) * 6);
}

.page-header h1 {
    color: white;
    margin-bottom: calc(var(--spacing-unit) * 2);
}

.page-header p {
    font-size: 1.1rem;
    opacity: 0.95;
}

.archive-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: calc(var(--spacing-unit) * 4);
    margin-bottom: calc(var(--spacing-unit) * 6);
}

.filters-sidebar {
    background: white;
    padding: calc(var(--spacing-unit) * 3);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    height: fit-content;
    position: sticky;
    top: calc(var(--spacing-unit) * 10);
}

.filters-sidebar h3 {
    margin-bottom: calc(var(--spacing-unit) * 3);
    padding-bottom: calc(var(--spacing-unit) * 2);
    border-bottom: 2px solid var(--primary-color);
}

.filters-form {
    display: flex;
    flex-direction: column;
    gap: calc(var(--spacing-unit) * 2);
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: calc(var(--spacing-unit) * 1);
}

.filter-group label {
    font-weight: 500;
    color: var(--text-medium);
    font-size: 0.9rem;
}

.results-header {
    margin-bottom: calc(var(--spacing-unit) * 3);
    padding-bottom: calc(var(--spacing-unit) * 2);
    border-bottom: 1px solid var(--border-color);
}

.results-header p {
    color: var(--text-medium);
    font-weight: 500;
}

.no-results {
    text-align: center;
    padding: calc(var(--spacing-unit) * 8) calc(var(--spacing-unit) * 3);
    background: white;
    border-radius: var(--border-radius);
}

@media (max-width: 768px) {
    .archive-layout {
        grid-template-columns: 1fr;
    }

    .filters-sidebar {
        position: static;
    }
}
</style>

<?php
get_footer();
