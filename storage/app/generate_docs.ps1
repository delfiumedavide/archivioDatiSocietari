$desktop = [Environment]::GetFolderPath('Desktop')
$word = New-Object -ComObject Word.Application
$word.Visible = $false

# ────────────────────────────────────────────────────────────────────────────
# HELPER FUNCTIONS
# ────────────────────────────────────────────────────────────────────────────
function SetTitle($doc, $text, $subtitle) {
    $sel = $doc.Application.Selection
    $sel.ParagraphFormat.SpaceBefore = 60
    $sel.Style = $doc.Styles.Item("Titolo")
    $sel.TypeText($text)
    $sel.TypeParagraph()
    $sel.ParagraphFormat.SpaceBefore = 0
    $sel.Style = $doc.Styles.Item("Sottotitolo")
    $sel.TypeText($subtitle)
    $sel.TypeParagraph()
}

function H1($doc, $text) {
    $sel = $doc.Application.Selection
    $sel.Style = $doc.Styles.Item("Titolo 1")
    $sel.TypeText($text)
    $sel.TypeParagraph()
}

function H2($doc, $text) {
    $sel = $doc.Application.Selection
    $sel.Style = $doc.Styles.Item("Titolo 2")
    $sel.TypeText($text)
    $sel.TypeParagraph()
}

function H3($doc, $text) {
    $sel = $doc.Application.Selection
    $sel.Style = $doc.Styles.Item("Titolo 3")
    $sel.TypeText($text)
    $sel.TypeParagraph()
}

function P($doc, $text) {
    $sel = $doc.Application.Selection
    $sel.Style = $doc.Styles.Item("Normale")
    $sel.TypeText($text)
    $sel.TypeParagraph()
}

function PBold($doc, $text) {
    $sel = $doc.Application.Selection
    $sel.Style = $doc.Styles.Item("Normale")
    $sel.Font.Bold = $true
    $sel.TypeText($text)
    $sel.Font.Bold = $false
    $sel.TypeParagraph()
}

function Bullet($doc, $text) {
    $sel = $doc.Application.Selection
    try {
        $sel.Style = $doc.Styles.Item("Elenco puntato")
    } catch {
        $sel.Style = $doc.Styles.Item("List Bullet")
    }
    $sel.TypeText($text)
    $sel.TypeParagraph()
}

function PageBreak($doc) {
    $sel = $doc.Application.Selection
    $sel.Style = $doc.Styles.Item("Normale")
    $sel.InsertBreak(7)
}

function MetaInfo($doc, $text) {
    $sel = $doc.Application.Selection
    $sel.Style = $doc.Styles.Item("Normale")
    $sel.Font.Color = 0x666666
    $sel.Font.Size = 9
    $sel.TypeText($text)
    $sel.Font.Color = 0x000000
    $sel.Font.Size = 11
    $sel.TypeParagraph()
}

# ════════════════════════════════════════════════════════════════════════════
# DOCUMENTO 1: TECNICO
# ════════════════════════════════════════════════════════════════════════════
$doc1 = $word.Documents.Add()
$doc1.PageSetup.LeftMargin = $word.CentimetersToPoints(2.5)
$doc1.PageSetup.RightMargin = $word.CentimetersToPoints(2.5)
$doc1.PageSetup.TopMargin = $word.CentimetersToPoints(2.5)
$doc1.PageSetup.BottomMargin = $word.CentimetersToPoints(2.5)

SetTitle $doc1 "ARCHIVIO DATI SOCIETARI" "Documento Tecnico — Architettura, Codice e Database"
MetaInfo $doc1 "Gruppo di Martino   |   Versione 1.0   |   Febbraio 2026   |   Uso interno"
$doc1.Application.Selection.TypeParagraph()
PageBreak $doc1

# ─── 1. STACK TECNOLOGICO ───────────────────────────────────────────────────
H1 $doc1 "1. Stack Tecnologico"
P $doc1 "Il gestionale e' sviluppato con tecnologie moderne, open source e consolidate, scelte per garantire sicurezza, scalabilita' e facilita' di manutenzione a lungo termine."

H2 $doc1 "1.1 Backend"
Bullet $doc1 "PHP 8.2+ — linguaggio server-side con tipizzazione strict e funzionalita' moderne (enums, readonly, fibers)"
Bullet $doc1 "Laravel 11 — framework MVC full-stack con ORM Eloquent, routing, middleware, job queue, scheduler integrato"
Bullet $doc1 "MySQL — database relazionale, gestito interamente tramite Migration versionate"
Bullet $doc1 "Redis (Predis 2.2) — cache in memoria per le statistiche aggregate della dashboard (TTL 300 secondi)"
Bullet $doc1 "DomPDF 3.1 — libreria per la generazione di PDF server-side (dichiarazioni stato famiglia)"
Bullet $doc1 "minishlink/web-push 8.0 — notifiche push al browser tramite Web Push API con autenticazione VAPID/ECDSA"
Bullet $doc1 "Laravel Sanctum 4.0 — autenticazione via token API (pronto per future integrazioni mobile/SPA)"

H2 $doc1 "1.2 Frontend"
Bullet $doc1 "TailwindCSS 3.4 — framework CSS utility-first; plugin Forms e Typography inclusi"
Bullet $doc1 "Alpine.js 3.14 — micro-framework JavaScript per interattivita' leggera: dropdown, tab, modal, fetch inline"
Bullet $doc1 "Chart.js 4.4 — grafici interattivi nella dashboard (grafico a torta, barre, linea temporale)"
Bullet $doc1 "Vite 5.0 + laravel-vite-plugin — build tool moderno con Hot Module Replacement in sviluppo"
Bullet $doc1 "Blade — motore di template Laravel con layout, componenti e direttive riutilizzabili"

H2 $doc1 "1.3 Infrastruttura"
Bullet $doc1 "Docker — containerizzazione dell'applicazione con Nginx come reverse proxy"
Bullet $doc1 "Railway — piattaforma cloud PaaS per il deploy automatico (CI/CD via push su GitHub main)"
Bullet $doc1 "GitHub — versionamento del codice sorgente"

PageBreak $doc1

# ─── 2. ARCHITETTURA ────────────────────────────────────────────────────────
H1 $doc1 "2. Architettura del Codice"
P $doc1 "Il progetto segue il pattern MVC (Model-View-Controller) di Laravel, arricchito da uno strato Service per la logica di business complessa e riutilizzabile."

H2 $doc1 "2.1 Struttura delle Cartelle Principali"
Bullet $doc1 "app/Models/                 — 20 classi Model Eloquent, una per entita' di dominio"
Bullet $doc1 "app/Http/Controllers/       — 17 controller (piu' 2 Auth), uno per sezione funzionale"
Bullet $doc1 "app/Services/               — 7 servizi per logica di business riutilizzabile"
Bullet $doc1 "app/Console/Commands/       — 2 comandi Artisan per job pianificati (cron)"
Bullet $doc1 "app/Http/Requests/          — Form Request per validazione centralizzata degli input"
Bullet $doc1 "app/Http/Middleware/        — middleware personalizzati (permission check, role check)"
Bullet $doc1 "database/migrations/        — 34 migration versionate per la struttura del DB"
Bullet $doc1 "resources/views/            — 16 sottocartelle Blade (una per sezione funzionale)"
Bullet $doc1 "routes/web.php              — definizione di tutte le route HTTP con middleware"
Bullet $doc1 "config/archivio.php         — configurazioni specifiche dell'app (upload, expiration, rate limit)"

H2 $doc1 "2.2 Layer dei Servizi (app/Services/)"
P $doc1 "I servizi separano la logica di business dai controller, rendendola testabile e riutilizzabile sia da controller che da comandi Artisan:"
Bullet $doc1 "DashboardService — aggrega statistiche da piu' tabelle; 7 metodi con Cache::remember(300s)"
Bullet $doc1 "ExpiryEmailService — invia email di scadenza personalizzate per utente + destinatari extra"
Bullet $doc1 "ExpirationCheckService — scansiona tutti i documenti con chunk(500) aggiornando expiration_status"
Bullet $doc1 "DocumentStorageService — upload, download streamed, versionamento file su disco locale"
Bullet $doc1 "DeclarationService — genera PDF dichiarazioni stato famiglia via DomPDF, gestisce file firmati"
Bullet $doc1 "AppSettingsService — legge e scrive le impostazioni globali (riga singola in app_settings)"
Bullet $doc1 "PushNotificationService — invia notifiche push VAPID tramite web-push ai browser registrati"

H2 $doc1 "2.3 Base Controller e logActivity()"
P $doc1 "Il controller base (app/Http/Controllers/Controller.php) espone il metodo protetto logActivity() che centralizza la scrittura dell'audit trail eliminando la ripetizione di ActivityLog::create() in 17 controller figli. Riceve Request, action, description e parametri opzionali modelType, modelId, properties (per i diff JSON)."

PageBreak $doc1

# ─── 3. DATABASE ────────────────────────────────────────────────────────────
H1 $doc1 "3. Database e Modelli Eloquent"
P $doc1 "Il database e' composto da 34 tabelle gestite tramite Migration versionate (ordine garantito da timestamp). Ogni tabella ha una corrispondente classe Model Eloquent con relazioni, scope, cast e accessor dichiarati."

H2 $doc1 "3.1 Dominio Utenti e Accesso"
Bullet $doc1 "users — anagrafica (name, email, password, is_active, last_login_at, last_login_ip)"
Bullet $doc1 "roles — ruoli: admin, manager, operatore"
Bullet $doc1 "permissions — permessi atomici (documents.download, membri.delete, ecc.)"
Bullet $doc1 "role_user — pivot molti-a-molti utenti/ruoli"
Bullet $doc1 "permission_user — permessi aggiuntivi assegnati direttamente all'utente"
Bullet $doc1 "company_user — pivot multi-tenancy: definisce quali aziende vede ogni utente non-admin"
Bullet $doc1 "sessions — sessioni utente persistenti su DB"
Bullet $doc1 "push_subscriptions — endpoint e chiavi per le notifiche push browser"

H2 $doc1 "3.2 Dominio Aziende"
Bullet $doc1 "companies — anagrafica societa' (denominazione, CF, PI, PEC, forma giuridica, sede, capitale sociale, logo)"
Bullet $doc1 "company_officers — cariche societarie (member_id, ruolo, data_nomina, data_scadenza, data_cessazione, compenso)"
Bullet $doc1 "shareholders — azionisti (tipo persona fisica/giuridica, nome, CF, quota_percentuale, quota_valore, diritti_voto)"
Bullet $doc1 "company_relationships — relazioni inter-societarie (parent/child, tipo relazione, quota)"

H2 $doc1 "3.3 Dominio Documenti"
Bullet $doc1 "documents — documento (company_id o member_id, category_id, title, file_path, expiration_date, expiration_status)"
Bullet $doc1 "document_versions — versioni storiche (version_number, file_path, change_notes, uploaded_by, timestamps)"
Bullet $doc1 "document_categories — categorie (nome, label, scope: company/member/both, sort_order)"

H2 $doc1 "3.4 Dominio Soci e Famiglia"
Bullet $doc1 "members — anagrafica socio (nome, cognome, CF, dati anagrafici, residenza, domicilio, contatti, white_list, white_list_scadenza, stato_civile)"
Bullet $doc1 "family_members — componenti del nucleo familiare (nome, relazione, data_nascita, data_inizio, data_fine)"
Bullet $doc1 "family_status_changes — variazioni stato civile (stato_civile, data_variazione, note, registered_by)"
Bullet $doc1 "family_status_declarations — dichiarazioni PDF annuali generate (anno, pdf_path, signed_path, is_generated, is_signed)"

H2 $doc1 "3.5 Dominio Libri Sociali"
Bullet $doc1 "riunioni — riunioni (tipo: CDA/Assemblea Ordinaria/Straordinaria/Collegio Sindacale, data_ora, luogo, status: programmata/convocata/svolta/annullata, convocazione_path, verbale_path)"
Bullet $doc1 "delibere — deliberazioni per riunione (numero progressivo, oggetto, esito: approvata/respinta/sospesa, note)"
Bullet $doc1 "riunione_partecipanti — presenze (member_id, presenza: presente/assente/delegato, note)"

H2 $doc1 "3.6 Dominio Sistema"
Bullet $doc1 "activity_logs — audit trail (user_id, action, model_type, model_id, description, properties JSON, ip_address, user_agent)"
Bullet $doc1 "app_settings — riga singola di configurazione globale (SMTP cifrato, email destinatari, schedule reminders, branding, holding info)"
Bullet $doc1 "notifications — notifiche Laravel (tabella standard, tipo polymorphic)"
Bullet $doc1 "cache — cache su DB (se Redis non disponibile)"
Bullet $doc1 "jobs — queue di job Laravel"

PageBreak $doc1

# ─── 4. AUTENTICAZIONE E RBAC ───────────────────────────────────────────────
H1 $doc1 "4. Autenticazione e Autorizzazione (RBAC)"
P $doc1 "Il sistema implementa un controllo di accesso a due livelli: ruoli (macro-aree di accesso) e permessi granulari (operazioni specifiche). Sopra questo strato si applica il company scoping per il multi-tenancy."

H2 $doc1 "4.1 Ruoli"
Bullet $doc1 "admin — accesso completo a tutte le sezioni e tutte le aziende; vede Libri Sociali, gestione Utenti, Impostazioni"
Bullet $doc1 "manager — accesso alle sezioni di business (aziende, documenti, soci, stati familiari)"
Bullet $doc1 "operatore — accesso limitato ai permessi assegnati individualmente"

H2 $doc1 "4.2 Permessi Granulari (esempi)"
Bullet $doc1 "companies, members, stati_famiglia, documents — abilitano le rispettive sezioni"
Bullet $doc1 "documents.download — permesso specifico per scaricare file"
Bullet $doc1 "documents.delete, membri.delete — cancellazione (separata dal permesso di accesso)"

H2 $doc1 "4.3 Company Scoping"
P $doc1 "Ogni utente non-admin e' assegnato a una o piu' aziende tramite la tabella pivot company_user. I tre Model principali implementano scopeForUser() per filtrare automaticamente:"
Bullet $doc1 "Company::forUser($user) — where('id', IN, companyIds)"
Bullet $doc1 "Member::forUser($user) — whereHas('officers', whereIn('company_id', companyIds))"
Bullet $doc1 "Document::forUser($user) — documenti delle company assegnate + documenti dei membri di quelle company"
Bullet $doc1 "L'admin riceve null da accessibleCompanyIds() e bypassa tutti i filtri"

PageBreak $doc1

# ─── 5. PATTERN E OTTIMIZZAZIONI ────────────────────────────────────────────
H1 $doc1 "5. Pattern di Codice e Ottimizzazioni"

H2 $doc1 "5.1 Eloquent Scopes Riutilizzabili"
Bullet $doc1 "scopeActive() — filtra per is_active = true su Company, Member, User"
Bullet $doc1 "scopeForUser(User) — filtra per accesso utente su Company, Member, Document"
Bullet $doc1 "scopeSearch(?string) — ricerca LIKE multi-campo su denominazione, CF, PI"
Bullet $doc1 "scopeExpiring($days=30) / scopeExpired() / scopeValid() — stati scadenza documenti"
Bullet $doc1 "scopeWithDetails() — eager load company + member + category su Document (usato in 12 punti)"
Bullet $doc1 "scopeOrderByName() — orderBy cognome + nome su Member (usato in 6 controller)"

H2 $doc1 "5.2 Ottimizzazioni Performance Implementate"
Bullet $doc1 "Eliminazione N+1: bulk load con whereIn + keyBy prima dei loop (es. sendDeclarations)"
Bullet $doc1 "chunk(500): ExpirationCheckService elabora documenti a blocchi salvando memoria RAM"
Bullet $doc1 "Cache::remember(300s): 7 metodi DashboardService con chiave basata su md5(json(companyIds))"
Bullet $doc1 "Eager loading esplicito con with() su tutti i controller che caricano relazioni"
Bullet $doc1 "Lazy load evitato: getCurrentStatoCivileAttribute() controlla relationLoaded() prima di query"
Bullet $doc1 "Download streamed: StreamedResponse evita di caricare file in memoria per il download"

H2 $doc1 "5.3 Comandi Artisan Pianificati"
Bullet $doc1 "CheckDocumentExpirations — aggiorna expiration_status, invia notifiche push; pianificato alle 08:00"
Bullet $doc1 "SendDocumentExpiryEmail — invia email memo via ExpiryEmailService; schedulabile con cron"

H2 $doc1 "5.4 Gestione File"
Bullet $doc1 "Percorso su disco: companies/{id}/{categoria}/ oppure members/{id}/{categoria}/"
Bullet $doc1 "Versionamento: ogni nuovo upload crea un DocumentVersion; il documento principale punta all'ultima versione"
Bullet $doc1 "Tipi consentiti: pdf, doc, docx, xls, xlsx, jpg, jpeg, png, zip, p7m"
Bullet $doc1 "Dimensione massima: 50MB (configurabile via env UPLOAD_MAX_SIZE_MB)"

PageBreak $doc1

# ─── 6. EMAIL, NOTIFICHE E SICUREZZA ────────────────────────────────────────
H1 $doc1 "6. Email, Notifiche e Sicurezza"

H2 $doc1 "6.1 SMTP Configurabile da Interfaccia"
P $doc1 "I parametri SMTP (host, porta, cifratura, username, password, mittente) sono salvati in app_settings con la password cifrata tramite il cast encrypted di Laravel (AES-256). All'avvio, AppServiceProvider::boot() legge le impostazioni e sovrascrive la config mail di Laravel a runtime. Una guard Schema::hasTable() protegge da errori durante le migration."

H2 $doc1 "6.2 Flusso Email Scadenze"
P $doc1 "ExpiryEmailService::sendAll(days, extraEmails) esegue due fasi:"
Bullet $doc1 "Fase 1 — per ogni utente attivo: carica documenti con forUser(), invia email personalizzata solo se ci sono scadenze"
Bullet $doc1 "Fase 2 — per i destinatari manuali (app_settings.notification_emails): email globale con tutte le scadenze"

H2 $doc1 "6.3 Notifiche Push"
Bullet $doc1 "Standard W3C Web Push con autenticazione VAPID (ECDSA P-256)"
Bullet $doc1 "Endpoint e chiavi salvati in push_subscriptions per ogni browser/dispositivo"
Bullet $doc1 "Invio automatico tramite CheckDocumentExpirations al rilevamento di nuove scadenze"

H2 $doc1 "6.4 Sicurezza"
Bullet $doc1 "CSRF protection su tutti i form POST/PUT/DELETE via token Blade"
Bullet $doc1 "Rate limiting login: max 5 tentativi, poi blocco con messaggio sui secondi rimanenti"
Bullet $doc1 "Audit trail completo: ogni azione registrata in activity_logs con IP e user agent"
Bullet $doc1 "SoftDelete su Company, Member, Document: i record non vengono mai eliminati fisicamente dal DB"
Bullet $doc1 "Verifica MIME type reale degli upload (non solo estensione del file)"
Bullet $doc1 "abort_unless(403) su ogni operazione che richiede accesso a risorse specifiche"

# Salva DOC 1
$savePath1 = $desktop + "\Documentazione Tecnica - Archivio Societario.docx"
$doc1.SaveAs([ref]$savePath1, 16)
$doc1.Close()
Write-Host "DOC1_SAVED:$savePath1"

# ════════════════════════════════════════════════════════════════════════════
# DOCUMENTO 2: FUNZIONALE (UTENTE)
# ════════════════════════════════════════════════════════════════════════════
$doc2 = $word.Documents.Add()
$doc2.PageSetup.LeftMargin = $word.CentimetersToPoints(2.5)
$doc2.PageSetup.RightMargin = $word.CentimetersToPoints(2.5)
$doc2.PageSetup.TopMargin = $word.CentimetersToPoints(2.5)
$doc2.PageSetup.BottomMargin = $word.CentimetersToPoints(2.5)

SetTitle $doc2 "ARCHIVIO DATI SOCIETARI" "Manuale Utente — Cosa fa il gestionale e come funziona"
MetaInfo $doc2 "Gruppo di Martino   |   Versione 1.0   |   Febbraio 2026"
$doc2.Application.Selection.TypeParagraph()
PageBreak $doc2

# ─── INTRO ──────────────────────────────────────────────────────────────────
H1 $doc2 "Introduzione"
P $doc2 "L'Archivio Dati Societari e' il gestionale interno del Gruppo di Martino. E' uno strumento web accessibile da qualsiasi browser (Chrome, Firefox, Edge, Safari) senza bisogno di installare nulla sul computer. Serve a raccogliere, organizzare e monitorare tutte le informazioni relative alle societa' del gruppo: documenti, cariche, soci, verbali, dichiarazioni e molto altro."
P $doc2 "Questo documento spiega cosa fa il gestionale sezione per sezione, in modo che chiunque — anche senza esperienza tecnica — possa capire le funzionalita' disponibili e come usarle."

H2 $doc2 "Come si accede"
Bullet $doc2 "Si apre il browser e si va all'indirizzo del gestionale (fornito dall'amministratore)"
Bullet $doc2 "Si inserisce email e password assegnate dall'admin"
Bullet $doc2 "Dopo il login si arriva alla Dashboard (la pagina principale con le statistiche)"
Bullet $doc2 "In caso di troppi tentativi errati, il sistema blocca temporaneamente l'accesso per sicurezza"

H2 $doc2 "La barra laterale (menu)"
P $doc2 "A sinistra dello schermo c'e' sempre il menu di navigazione con tutte le sezioni. Le voci che si vedono dipendono dal proprio ruolo: un operatore potrebbe non vedere certe sezioni riservate agli admin."

PageBreak $doc2

# ─── DASHBOARD ──────────────────────────────────────────────────────────────
H1 $doc2 "1. Dashboard — La pagina principale"
P $doc2 "La dashboard e' la prima cosa che appare dopo il login. Mostra una panoramica rapida dello stato del gruppo, personalizzata in base alle aziende a cui l'utente ha accesso."

H2 $doc2 "Cosa si vede nella dashboard"
Bullet $doc2 "Numero totale di societa', soci e documenti archiviati"
Bullet $doc2 "Documenti in scadenza (entro 30 giorni) e gia' scaduti — evidenziati in giallo e rosso"
Bullet $doc2 "Grafico a torta: distribuzione documenti per stato (validi / in scadenza / scaduti)"
Bullet $doc2 "Grafico a barre: documenti suddivisi per categoria (es. visure, contratti, certificati)"
Bullet $doc2 "Grafico a barre: documenti per azienda"
Bullet $doc2 "Grafico lineare: attivita' di caricamento documenti negli ultimi 12 mesi"
Bullet $doc2 "Elenco delle ultime azioni registrate nel sistema (log attivita')"
Bullet $doc2 "Sezione Admin: prossime riunioni, verbali mancanti, statistiche dichiarazioni (solo per admin)"

P $doc2 "Nota: i dati della dashboard si aggiornano automaticamente ogni 5 minuti grazie a un sistema di cache, per garantire velocita' anche con molti documenti."

PageBreak $doc2

# ─── SOCIETA' ───────────────────────────────────────────────────────────────
H1 $doc2 "2. Societa' — Gestione delle aziende del gruppo"
P $doc2 "In questa sezione si trovano tutte le societa' del gruppo. E' il cuore del gestionale: quasi tutto il resto (documenti, cariche, riunioni) e' collegato a una specifica societa'."

H2 $doc2 "Scheda di una societa'"
P $doc2 "Ogni societa' ha una scheda con:"
Bullet $doc2 "Dati anagrafici: denominazione, forma giuridica (Srl, SpA, ecc.), codice fiscale, partita IVA, PEC"
Bullet $doc2 "Sede legale: indirizzo, citta', provincia, CAP"
Bullet $doc2 "Dati economici: capitale sociale, quota versata, data di costituzione, numero REA, codice ATECO"
Bullet $doc2 "Contatti: telefono, email, sito web"
Bullet $doc2 "Note interne libere"
Bullet $doc2 "Logo aziendale (caricabile)"

H2 $doc2 "Cariche societarie (chi fa cosa nell'azienda)"
P $doc2 "Per ogni societa' si possono registrare tutte le cariche: amministratori, sindaci, revisori, ecc."
Bullet $doc2 "Si specifica: nome del socio, ruolo, data di nomina, data di scadenza (se prevista), compenso"
Bullet $doc2 "Quando una carica finisce, si registra la data di cessazione — la carica diventa storica ma rimane nell'archivio"
Bullet $doc2 "Le cariche in scadenza compaiono nello scadenzario (sezione Documenti > Scadenzario)"

H2 $doc2 "Azionisti / Soci"
P $doc2 "Si possono registrare tutti i soci o azionisti di ogni azienda:"
Bullet $doc2 "Tipo: persona fisica o persona giuridica (altra societa')"
Bullet $doc2 "Quote: percentuale, valore in euro, diritti di voto"
Bullet $doc2 "Date di ingresso e uscita dalla compagine"

H2 $doc2 "Relazioni tra societa'"
P $doc2 "Il gestionale permette di mappare le relazioni di controllo o partecipazione tra le aziende del gruppo (chi e' controllante, chi e' controllata, quota di partecipazione). Questo crea un registro della struttura del gruppo."

PageBreak $doc2

# ─── SOCI ───────────────────────────────────────────────────────────────────
H1 $doc2 "3. Soci — Anagrafica dei componenti"
P $doc2 "La sezione Soci contiene l'anagrafica completa di tutte le persone fisiche coinvolte nelle societa' del gruppo (amministratori, sindaci, soci, ecc.). E' separata dalla sezione Societa' perche' una stessa persona puo' avere ruoli in piu' aziende."

H2 $doc2 "Dati del socio"
Bullet $doc2 "Dati personali: nome, cognome, codice fiscale, data e luogo di nascita, nazionalita', sesso"
Bullet $doc2 "Residenza e domicilio (se diversi)"
Bullet $doc2 "Contatti: telefono, cellulare, email, PEC"
Bullet $doc2 "Stato civile attuale (aggiornato automaticamente dalla sezione Stati Familiari)"
Bullet $doc2 "Note interne libere"

H2 $doc2 "White List antiriciclaggio"
P $doc2 "Ogni socio puo' essere marcato come iscritto alla White List (il registro antiriciclaggio). In questo caso si puo' registrare la data di scadenza dell'iscrizione. Le scadenze imminenti compaiono nello scadenzario."

H2 $doc2 "Documenti del socio"
P $doc2 "Dalla scheda del socio si puo' vedere e caricare i documenti che lo riguardano direttamente (es. carta d'identita', tessera sanitaria, certificato di residenza). Questi sono separati dai documenti aziendali."

PageBreak $doc2

# ─── DOCUMENTI ──────────────────────────────────────────────────────────────
H1 $doc2 "4. Documenti — Archivio digitale con scadenzario"
P $doc2 "E' la sezione piu' usata quotidianamente. Permette di caricare, organizzare e tenere traccia di tutti i documenti del gruppo (aziendali e personali dei soci), con controllo automatico delle scadenze."

H2 $doc2 "Caricare un documento"
P $doc2 "Quando si carica un documento si specifica:"
Bullet $doc2 "A chi appartiene: una societa' specifica oppure un socio specifico"
Bullet $doc2 "Categoria: visura camerale, contratto, certificato, verbale, ecc. (le categorie sono predefinite)"
Bullet $doc2 "Titolo e descrizione libera"
Bullet $doc2 "Il file (PDF, Word, Excel, immagini, ZIP, file firmati digitalmente .p7m — max 50 MB)"
Bullet $doc2 "Data di scadenza (facoltativa): se inserita, il sistema monitora automaticamente il documento"

H2 $doc2 "Versionamento dei documenti"
P $doc2 "Se un documento viene aggiornato (es. il contratto viene rinnovato), si puo' caricare una nuova versione senza perdere la precedente. Il sistema mantiene uno storico completo con:"
Bullet $doc2 "Numero di versione progressivo"
Bullet $doc2 "Data di upload di ogni versione"
Bullet $doc2 "Chi ha caricato ogni versione"
Bullet $doc2 "Note sul cambiamento"

H2 $doc2 "Filtri e ricerca"
P $doc2 "Nell'elenco documenti si puo' filtrare per:"
Bullet $doc2 "Societa' o Socio a cui e' associato il documento"
Bullet $doc2 "Categoria del documento"
Bullet $doc2 "Stato: valido, in scadenza, scaduto"
Bullet $doc2 "Parola chiave nel titolo"

H2 $doc2 "Scadenzario unificato"
P $doc2 "La pagina 'Scadenzario' (accessibile dal menu Documenti) mostra in un unico posto tutte le scadenze monitorate, organizzate in tre schede:"
Bullet $doc2 "Scheda Documenti: documenti in scadenza (entro 90 giorni) e gia' scaduti, con link diretto al documento"
Bullet $doc2 "Scheda Cariche: cariche societarie che stanno per scadere o gia' scadute"
Bullet $doc2 "Scheda White List: iscrizioni white list dei soci in scadenza o gia' scadute"
P $doc2 "Ogni voce mostra i giorni rimanenti (o i giorni di ritardo) con colori semaforo: verde = ok, giallo = attenzione, rosso = azione richiesta."

PageBreak $doc2

# ─── STATI FAMILIARI ────────────────────────────────────────────────────────
H1 $doc2 "5. Stati Familiari — Dichiarazioni e nucleo familiare"
P $doc2 "Questa sezione gestisce le informazioni sullo stato civile e il nucleo familiare di ogni socio, con la possibilita' di generare in automatico le dichiarazioni annuali in formato PDF."

H2 $doc2 "Variazioni stato civile"
P $doc2 "Ogni cambiamento dello stato civile di un socio (matrimonio, divorzio, vedovanza, ecc.) viene registrato con:"
Bullet $doc2 "Il nuovo stato civile"
Bullet $doc2 "La data della variazione"
Bullet $doc2 "Note aggiuntive"
Bullet $doc2 "Chi ha registrato la variazione"
P $doc2 "Il sistema mantiene uno storico completo di tutte le variazioni nel tempo."

H2 $doc2 "Nucleo familiare"
P $doc2 "Per ogni socio si possono registrare i componenti del suo nucleo familiare:"
Bullet $doc2 "Nome, cognome, codice fiscale del familiare"
Bullet $doc2 "Relazione (coniuge, figlio, genitore, ecc.)"
Bullet $doc2 "Data di nascita e luogo di nascita"
Bullet $doc2 "Date di entrata e uscita dal nucleo (es. figlio che si emancipa)"

H2 $doc2 "Dichiarazioni annuali"
P $doc2 "Il gestionale genera automaticamente le dichiarazioni di stato famiglia in PDF per ogni socio. Il flusso di lavoro e':"
Bullet $doc2 "1. Si va nella pagina Dichiarazioni e si seleziona l'anno"
Bullet $doc2 "2. Si clicca 'Genera' per un singolo socio, oppure 'Genera Tutte' per produrle in massa"
Bullet $doc2 "3. Il PDF generato viene scaricato, stampato, fatto firmare al socio"
Bullet $doc2 "4. Il documento firmato (o firmato digitalmente) viene caricato di nuovo nel sistema"
Bullet $doc2 "5. La dichiarazione risulta completata: si vedono sia la versione generata che quella firmata"
P $doc2 "E' possibile scaricare tutte le dichiarazioni di un anno in un unico file ZIP."

PageBreak $doc2

# ─── LIBRI SOCIALI ──────────────────────────────────────────────────────────
H1 $doc2 "6. Libri Sociali — Riunioni, verbali e delibere"
P $doc2 "La sezione Libri Sociali e' riservata agli amministratori e permette di gestire il registro delle riunioni societarie: CDA, assemblee ordinarie e straordinarie, collegio sindacale."

H2 $doc2 "Tipi di riunione gestiti"
Bullet $doc2 "Consiglio di Amministrazione (CDA)"
Bullet $doc2 "Assemblea Ordinaria dei Soci"
Bullet $doc2 "Assemblea Straordinaria dei Soci"
Bullet $doc2 "Collegio Sindacale"

H2 $doc2 "Ciclo di vita di una riunione"
P $doc2 "Ogni riunione passa attraverso questi stati:"
Bullet $doc2 "Programmata — la data e' fissata ma non ancora convocata ufficialmente"
Bullet $doc2 "Convocata — la convocazione e' stata inviata (si puo' allegare il PDF della convocazione)"
Bullet $doc2 "Svolta — la riunione si e' tenuta; si possono registrare le presenze e le delibere"
Bullet $doc2 "Annullata — la riunione e' stata cancellata"

H2 $doc2 "Cosa si registra per una riunione svolta"
Bullet $doc2 "Luogo e data/ora della riunione"
Bullet $doc2 "Ordine del giorno"
Bullet $doc2 "Chi era presente: per ogni membro si indica se era presente, assente o in delega"
Bullet $doc2 "Delibere adottate: per ogni delibera si registra numero progressivo, oggetto e esito (approvata / respinta / sospesa)"
Bullet $doc2 "PDF del verbale (si carica dopo la riunione)"

H2 $doc2 "Dashboard Libri Sociali"
P $doc2 "La pagina principale dei Libri Sociali mostra:"
Bullet $doc2 "Prossime riunioni in programma nei 30 giorni successivi"
Bullet $doc2 "Verbali ancora da caricare (riunioni gia' svolte senza verbale allegato)"
Bullet $doc2 "Statistiche: riunioni quest'anno, verbali mancanti"

PageBreak $doc2

# ─── EMAIL E NOTIFICHE ──────────────────────────────────────────────────────
H1 $doc2 "7. Email e Notifiche automatiche"
P $doc2 "Il gestionale invia automaticamente email di promemoria quando si avvicinano le scadenze di documenti, cariche o iscrizioni white list."

H2 $doc2 "Come funzionano le email di scadenza"
Bullet $doc2 "Ogni utente del gestionale riceve un'email personalizzata con le scadenze delle societa' che gestisce"
Bullet $doc2 "Gli amministratori vedono le scadenze di tutte le societa'"
Bullet $doc2 "L'email puo' essere inviata manualmente dalla sezione Email, oppure impostata per partire automaticamente"
Bullet $doc2 "Si puo' aggiungere una lista di indirizzi extra che riceveranno una email riepilogativa globale"
Bullet $doc2 "La finestra di preavviso e' configurabile (di default: documenti in scadenza entro 30 giorni)"

H2 $doc2 "Dichiarazioni stato famiglia via email"
P $doc2 "Dalla sezione Email si puo' anche inviare un promemoria ai soci chiedendo di compilare e restituire la dichiarazione dello stato famiglia per l'anno in corso."

H2 $doc2 "Notifiche nel browser"
P $doc2 "Se si autorizza il browser a ricevere notifiche, il gestionale inviera' avvisi automatici quando nuovi documenti vanno in scadenza — anche quando la pagina e' chiusa."

H2 $doc2 "Configurazione SMTP (solo Admin)"
P $doc2 "L'amministratore puo' configurare il server email direttamente dal gestionale (senza accedere al server): indirizzo del server SMTP, porta, tipo di cifratura, credenziali. C'e' anche un pulsante 'Testa Connessione' per verificare che tutto funzioni prima di salvare."

PageBreak $doc2

# ─── UTENTI E ACCESSI ───────────────────────────────────────────────────────
H1 $doc2 "8. Utenti e controllo degli accessi (solo Admin)"
P $doc2 "Gli amministratori possono creare e gestire gli account degli altri utenti del gestionale, definendo cosa puo' vedere e fare ognuno."

H2 $doc2 "Ruoli disponibili"
Bullet $doc2 "Amministratore — accesso completo a tutto il sistema; vede tutte le societa'"
Bullet $doc2 "Manager — accesso alle sezioni operative (societa', documenti, soci, stati familiari)"
Bullet $doc2 "Operatore — accesso personalizzato in base ai permessi assegnati"

H2 $doc2 "Permessi specifici"
P $doc2 "Oltre al ruolo generale, si possono assegnare permessi atomici per operazioni sensibili:"
Bullet $doc2 "Permesso di scaricare file (documents.download)"
Bullet $doc2 "Permesso di eliminare documenti (documents.delete)"
Bullet $doc2 "Permesso di eliminare soci (membri.delete)"

H2 $doc2 "Societa' assegnate"
P $doc2 "Per gli utenti non-admin si specificano le societa' a cui hanno accesso. Questo garantisce che ogni operatore veda solo i dati rilevanti per il proprio lavoro, senza poter accedere per errore a dati di altre aziende."

PageBreak $doc2

# ─── IMPOSTAZIONI ───────────────────────────────────────────────────────────
H1 $doc2 "9. Impostazioni generali (solo Admin)"
P $doc2 "La sezione Impostazioni permette di personalizzare il gestionale:"

Bullet $doc2 "Nome e sottotitolo del gestionale (mostrati in cima e nel browser)"
Bullet $doc2 "Dati della holding capogruppo (ragione sociale, CF, PI, indirizzo, contatti)"
Bullet $doc2 "Titolo e testo del modulo di login"
Bullet $doc2 "Logo personalizzato e favicon (le icone del browser)"
Bullet $doc2 "Intestazione e footer per le dichiarazioni stato famiglia generate in PDF"

PageBreak $doc2

# ─── LOG ATTIVITA' ──────────────────────────────────────────────────────────
H1 $doc2 "10. Log Attivita' — Tracciamento completo"
P $doc2 "Il gestionale registra automaticamente ogni azione svolta dagli utenti: chi ha fatto cosa, quando, da quale indirizzo IP. Questo e' visibile nella sezione 'Log Attivita'' (accessibile ad admin e manager)."

H2 $doc2 "Cosa viene tracciato"
Bullet $doc2 "Login e logout di ogni utente"
Bullet $doc2 "Creazione, modifica ed eliminazione di societa', soci, documenti, cariche"
Bullet $doc2 "Download di file"
Bullet $doc2 "Generazione e upload di dichiarazioni"
Bullet $doc2 "Modifiche alle impostazioni e al logo"
Bullet $doc2 "Invio di email"
Bullet $doc2 "Tentativi di login falliti"

H2 $doc2 "A cosa serve"
P $doc2 "Il log attivita' serve per:"
Bullet $doc2 "Verificare chi ha modificato un certo dato e quando"
Bullet $doc2 "Rilevare eventuali accessi non autorizzati"
Bullet $doc2 "Rispettare i requisiti normativi di tracciabilita' (GDPR, compliance aziendale)"

PageBreak $doc2

# ─── RIEPILOGO SEZIONI ──────────────────────────────────────────────────────
H1 $doc2 "Riepilogo delle sezioni e visibilita'"
P $doc2 "La seguente tabella riassume chi puo' accedere a cosa:"

# Crea tabella
$table = $doc2.Tables.Add($doc2.Application.Selection.Range, 8, 3)
$table.Style = "Griglia tabella 5 scuro - Enfasi 1"
$table.AllowAutoFit = $true

# Header
$table.Cell(1,1).Range.Text = "Sezione"
$table.Cell(1,2).Range.Text = "Visibile a"
$table.Cell(1,3).Range.Text = "Note"

# Righe
$rows = @(
    @("Dashboard", "Tutti gli utenti", "Dati filtrati per le proprie aziende"),
    @("Societa'", "Admin, Manager, Operatori con permesso", "Solo le aziende assegnate per non-admin"),
    @("Soci", "Admin, Manager, Operatori con permesso", "Solo i soci delle proprie aziende"),
    @("Documenti e Scadenzario", "Admin, Manager, Operatori con permesso", "Download richied permesso specifico"),
    @("Stati Familiari", "Admin, Manager, Operatori con permesso", "Dichiarazioni PDF generate in automatico"),
    @("Libri Sociali", "Solo Admin", "Riunioni, verbali, delibere, presenti"),
    @("Email e Notifiche", "Solo Admin", "Configurazione SMTP, invio memo")
)

for ($i = 0; $i -lt $rows.Count; $i++) {
    $table.Cell($i+2, 1).Range.Text = $rows[$i][0]
    $table.Cell($i+2, 2).Range.Text = $rows[$i][1]
    $table.Cell($i+2, 3).Range.Text = $rows[$i][2]
}

$doc2.Application.Selection.MoveDown(5, 2)
$doc2.Application.Selection.TypeParagraph()

P $doc2 "Per qualsiasi dubbio o richiesta di accesso a sezioni aggiuntive, contattare l'amministratore del sistema."

# Salva DOC 2
$savePath2 = $desktop + "\Manuale Utente - Archivio Societario.docx"
$doc2.SaveAs([ref]$savePath2, 16)
$doc2.Close()

$word.Quit()
Write-Host "DOC2_SAVED:$savePath2"
Write-Host "DONE"
