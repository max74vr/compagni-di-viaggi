# Schema Dati Viaggi - Compagni di Viaggi

## Campi Viaggio WordPress

### Post Standard
- **Titolo** (post_title): Nome accattivante del viaggio
- **Descrizione** (post_content): Descrizione completa del viaggio (min 200 caratteri)
- **Immagine Copertina** (featured_image): Foto della destinazione

### Meta Fields Custom
- **cdv_start_date**: Data inizio (YYYY-MM-DD)
- **cdv_end_date**: Data fine (YYYY-MM-DD)
- **cdv_destination**: Città/Località principale
- **cdv_country**: Paese
- **cdv_budget**: Budget stimato in Euro (per persona)
- **cdv_max_participants**: Numero massimo partecipanti (2-20)
- **cdv_travel_status**: Stato (open/full/in_progress/completed/cancelled)

### Tassonomie
- **tipo_viaggio**: Avventura, Mare, Montagna, Città d'Arte, Cultura, Relax, Food & Wine, Sport, Zaino in Spalla
- **destinazione**: Europa, Asia, Africa, America, Oceania (aggiungere specifiche)

---

## Prompt per ChatGPT: Generazione 10 Viaggi Fittizi

```
Genera 10 annunci di viaggi realistici per una piattaforma italiana di ricerca compagni di viaggio.

Per ogni viaggio fornisci questi dati in formato JSON:

{
  "title": "Titolo accattivante del viaggio (max 60 caratteri)",
  "content": "Descrizione completa e coinvolgente del viaggio. Includi: cosa vedremo, stile del viaggio, cosa è incluso/escluso, tipo di alloggio, livello di attività fisica richiesto, perché è un'esperienza unica. Min 300 parole, scrivi in prima persona plurale ('Esploreremo...', 'Visiteremo...'), tono amichevole e invitante.",
  "start_date": "2025-MM-DD (date realistiche tra marzo 2025 e dicembre 2025)",
  "end_date": "2025-MM-DD",
  "destination": "Città o località principale",
  "country": "Paese",
  "budget": numero_intero (budget realistico per persona per l'intera durata, range 300-3000€),
  "max_participants": numero (tra 4 e 12),
  "tipo_viaggio": ["scegli 1-2 tra: Avventura, Mare, Montagna, Città d'Arte, Cultura, Relax, Food & Wine, Sport, Zaino in Spalla"],
  "image_search": "Termine ricerca per trovare immagine su Unsplash (es: 'santorini sunset greece')"
}

Requisiti:
- Varia le destinazioni: Europa (5), Asia (2), America (2), Africa (1)
- Varia i tipi: mix di avventura, relax, cultura, sport
- Varia le durate: weekend (2-3 giorni), settimana (5-7 giorni), lungo (10-15 giorni)
- Budget proporzionato alla destinazione e durata
- Descrizioni autentiche e dettagliate
- Evita clichè turistici, scrivi in modo personale

Esempi di titoli efficaci:
- "Weekend a Barcellona: Gaudì, Tapas e Movida"
- "Trekking in Marocco: 7 giorni nel deserto del Sahara"
- "Giappone in Fiore: Tokyo, Kyoto e Monte Fuji in primavera"
- "Road Trip Islanda: Aurora Boreale e Terme Naturali"

Genera i 10 viaggi in un array JSON valido.
```

---

## Esempio Output Atteso

```json
[
  {
    "title": "Weekend a Lisbona: Tram, Pastéis e Fado",
    "content": "Esploreremo insieme la magica Lisbona in un weekend intenso ma rilassato...",
    "start_date": "2025-04-12",
    "end_date": "2025-04-14",
    "destination": "Lisbona",
    "country": "Portogallo",
    "budget": 450,
    "max_participants": 8,
    "tipo_viaggio": ["Città d'Arte", "Food & Wine"],
    "image_search": "lisbon tram alfama"
  },
  // ... altri 9 viaggi
]
```

---

## Import Viaggi in WordPress

Dopo aver generato i dati, useremo questo script per importarli:

```php
// Da eseguire in WordPress Tools → PHP Snippet o Functions.php temporaneo

$travels = [/* JSON generato */];

foreach ($travels as $travel_data) {
    // Crea post
    $post_id = wp_insert_post([
        'post_title' => $travel_data['title'],
        'post_content' => $travel_data['content'],
        'post_status' => 'publish', // o 'pending' se vuoi moderazione
        'post_type' => 'viaggio',
        'post_author' => 1, // ID admin
    ]);

    if ($post_id) {
        // Meta fields
        update_post_meta($post_id, 'cdv_start_date', $travel_data['start_date']);
        update_post_meta($post_id, 'cdv_end_date', $travel_data['end_date']);
        update_post_meta($post_id, 'cdv_destination', $travel_data['destination']);
        update_post_meta($post_id, 'cdv_country', $travel_data['country']);
        update_post_meta($post_id, 'cdv_budget', $travel_data['budget']);
        update_post_meta($post_id, 'cdv_max_participants', $travel_data['max_participants']);
        update_post_meta($post_id, 'cdv_travel_status', 'open');

        // Tassonomie
        wp_set_post_terms($post_id, $travel_data['tipo_viaggio'], 'tipo_viaggio');

        // Scarica immagine da Unsplash (opzionale)
        // require_once(ABSPATH . 'wp-admin/includes/media.php');
        // require_once(ABSPATH . 'wp-admin/includes/file.php');
        // require_once(ABSPATH . 'wp-admin/includes/image.php');
        // $image_url = "https://source.unsplash.com/1200x600/?" . urlencode($travel_data['image_search']);
        // media_sideload_image($image_url, $post_id, '', 'id');
    }
}
```

---

## Alternative: Generatore Manuale

Se preferisci, posso creare direttamente 10 viaggi fittizi completi da copiare-incollare.
Vuoi che proceda con questo?
