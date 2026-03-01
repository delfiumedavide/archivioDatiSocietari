param()
$ErrorActionPreference = 'Stop'
$desktop = [Environment]::GetFolderPath('Desktop')
$word = New-Object -ComObject Word.Application
$word.Visible = $false

function H1($d, $t)  { $s=$d.Application.Selection; $s.Style=$d.Styles.Item('Titolo 1'); $s.TypeText($t); $s.TypeParagraph() }
function H2($d, $t)  { $s=$d.Application.Selection; $s.Style=$d.Styles.Item('Titolo 2'); $s.TypeText($t); $s.TypeParagraph() }
function PP($d, $t)  { $s=$d.Application.Selection; $s.Style=$d.Styles.Item('Normale'); $s.TypeText($t); $s.TypeParagraph() }
function BB($d, $t)  { $s=$d.Application.Selection; $s.Style=$d.Styles.Item('Punto elenco'); $s.TypeText($t); $s.TypeParagraph() }
function PBR($d)     { $s=$d.Application.Selection; $s.Style=$d.Styles.Item('Normale'); $s.InsertBreak(7) }

# ============================================================
# DOCUMENTO 1: TECNICO
# ============================================================
Write-Host 'Creo documento tecnico...'
$d1 = $word.Documents.Add()
$d1.PageSetup.LeftMargin  = $word.CentimetersToPoints(2.5)
$d1.PageSetup.RightMargin = $word.CentimetersToPoints(2.5)

$sel = $d1.Application.Selection
$sel.Style = $d1.Styles.Item('Titolo')
$sel.TypeText('ARCHIVIO DATI SOCIETARI')
$sel.TypeParagraph()
$sel.Style = $d1.Styles.Item('Sottotitolo')
$sel.TypeText('Documento Tecnico - Architettura, Codice e Database')
$sel.TypeParagraph()
$sel.Style = $d1.Styles.Item('Normale')
$sel.TypeText('Gruppo di Martino  |  Versione 1.0  |  Febbraio 2026  |  Uso interno')
$sel.TypeParagraph()
PBR $d1

H1 $d1 '1. Stack Tecnologico'
PP $d1 'Il gestionale e sviluppato con tecnologie moderne, open source e consolidate, scelte per garantire sicurezza, scalabilita e facilita di manutenzione a lungo termine.'
H2 $d1 '1.1 Backend'
BB $d1 'PHP 8.2+ - linguaggio server-side con tipizzazione strict e funzionalita moderne'
BB $d1 'Laravel 11 - framework MVC full-stack con ORM Eloquent, routing, middleware, scheduler integrato'
BB $d1 'MySQL - database relazionale gestito tramite Migration versionate'
BB $d1 'Redis via Predis 2.2 - cache in memoria per statistiche aggregate della dashboard con TTL 300 secondi'
BB $d1 'DomPDF 3.1 - generazione di PDF server-side per le dichiarazioni stato famiglia'
BB $d1 'minishlink/web-push 8.0 - notifiche push al browser tramite Web Push API con autenticazione VAPID e ECDSA'
BB $d1 'Laravel Sanctum 4.0 - autenticazione API token, pronto per integrazioni future'
H2 $d1 '1.2 Frontend'
BB $d1 'TailwindCSS 3.4 - framework CSS utility-first con plugin Forms e Typography'
BB $d1 'Alpine.js 3.14 - micro-framework JavaScript per interattivita leggera: dropdown, tab, modal, fetch inline'
BB $d1 'Chart.js 4.4 - grafici interattivi nella dashboard: torta, barre, linea temporale'
BB $d1 'Vite 5.0 + laravel-vite-plugin - build tool con Hot Module Replacement in sviluppo'
BB $d1 'Blade - motore di template Laravel con layout, componenti e direttive riutilizzabili'
H2 $d1 '1.3 Infrastruttura'
BB $d1 'Docker - containerizzazione con Nginx come reverse proxy'
BB $d1 'Railway - piattaforma cloud PaaS per deploy automatico via push su GitHub'
BB $d1 'GitHub - versionamento del codice sorgente, branch main'

PBR $d1
H1 $d1 '2. Architettura del Codice'
PP $d1 'Il progetto segue il pattern MVC di Laravel, arricchito da uno strato Service per la logica di business complessa e riutilizzabile tra controller e comandi Artisan.'
H2 $d1 '2.1 Struttura Cartelle Principali'
BB $d1 'app/Models/            - 20 classi Model Eloquent, una per entita di dominio'
BB $d1 'app/Http/Controllers/  - 17 controller piu 2 Auth, uno per sezione funzionale'
BB $d1 'app/Services/          - 7 servizi per logica di business riutilizzabile'
BB $d1 'app/Console/Commands/  - 2 comandi Artisan per job pianificati via cron'
BB $d1 'app/Http/Requests/     - Form Request per validazione centralizzata degli input'
BB $d1 'database/migrations/   - 34 migration versionate per la struttura del DB'
BB $d1 'resources/views/       - 16 sottocartelle Blade, una per sezione funzionale'
BB $d1 'routes/web.php         - definizione di tutte le route HTTP con middleware'
BB $d1 'config/archivio.php    - configurazioni specifiche: upload, expiration, rate limit'
H2 $d1 '2.2 Layer dei Servizi'
PP $d1 'I servizi separano la logica di business dai controller rendendola testabile e riutilizzabile:'
BB $d1 'DashboardService - aggrega statistiche da piu tabelle; 7 metodi con cache da 300 secondi'
BB $d1 'ExpiryEmailService - invia email di scadenza personalizzate per-utente e a destinatari extra'
BB $d1 'ExpirationCheckService - scansiona documenti a chunk di 500 aggiornando expiration_status'
BB $d1 'DocumentStorageService - upload, download streamed, versionamento file su disco'
BB $d1 'DeclarationService - genera PDF dichiarazioni stato famiglia, gestisce file firmati'
BB $d1 'AppSettingsService - legge e scrive impostazioni globali dalla riga singola in app_settings'
BB $d1 'PushNotificationService - invia notifiche push VAPID tramite Web Push API'
H2 $d1 '2.3 Base Controller e metodo logActivity'
PP $d1 'Il controller base in app/Http/Controllers/Controller.php espone il metodo protetto logActivity che centralizza la scrittura dell audit trail, eliminando circa 28 blocchi ActivityLog::create ripetuti nei controller figli. Accetta: Request, action, description e parametri opzionali modelType, modelId e properties JSON per i diff.'

PBR $d1
H1 $d1 '3. Database e Modelli Eloquent'
PP $d1 'Il database e composto da 34 tabelle gestite tramite Migration versionate. Ogni tabella ha una classe Model Eloquent con relazioni, scope, cast e accessor dichiarati.'
H2 $d1 '3.1 Dominio Utenti e Accesso'
BB $d1 'users - anagrafica: name, email, password, is_active, last_login_at, last_login_ip'
BB $d1 'roles - ruoli disponibili: admin, manager, operatore'
BB $d1 'permissions - permessi atomici: documents.download, membri.delete e altri'
BB $d1 'role_user - pivot molti-a-molti utenti e ruoli'
BB $d1 'permission_user - permessi aggiuntivi assegnati direttamente all utente'
BB $d1 'company_user - pivot multi-tenancy: quali aziende vede ogni utente non-admin'
BB $d1 'sessions - sessioni utente persistenti su DB'
BB $d1 'push_subscriptions - endpoint e chiavi per notifiche push browser'
H2 $d1 '3.2 Dominio Aziende'
BB $d1 'companies - denominazione, CF, PI, PEC, forma giuridica, sede legale, capitale sociale, logo'
BB $d1 'company_officers - member_id, ruolo, data_nomina, data_scadenza, data_cessazione, compenso'
BB $d1 'shareholders - tipo, nome, CF, quota_percentuale, quota_valore, diritti_voto'
BB $d1 'company_relationships - relazioni inter-societarie: parent/child, tipo, quota di partecipazione'
H2 $d1 '3.3 Dominio Documenti'
BB $d1 'documents - company_id o member_id, category_id, title, file_path, expiration_date, expiration_status'
BB $d1 'document_versions - version_number, file_path, change_notes, uploaded_by, timestamps'
BB $d1 'document_categories - nome, scope company/member/both, sort_order'
H2 $d1 '3.4 Dominio Soci e Famiglia'
BB $d1 'members - dati personali, residenza, domicilio, contatti, white_list, white_list_scadenza, stato_civile'
BB $d1 'family_members - componenti nucleo: nome, relazione, data_nascita, data_inizio, data_fine'
BB $d1 'family_status_changes - stato_civile, data_variazione, note, registered_by'
BB $d1 'family_status_declarations - anno, pdf_path, signed_path, is_generated, is_signed'
H2 $d1 '3.5 Dominio Libri Sociali'
BB $d1 'riunioni - tipo CDA/Assemblea/Collegio; status programmata/convocata/svolta/annullata; convocazione e verbale PDF'
BB $d1 'delibere - numero progressivo per riunione, oggetto, esito approvata/respinta/sospesa, note'
BB $d1 'riunione_partecipanti - member_id, presenza presente/assente/delegato, note'
H2 $d1 '3.6 Dominio Sistema'
BB $d1 'activity_logs - user_id, action, model_type, model_id, description, properties JSON, ip_address, user_agent'
BB $d1 'app_settings - riga singola: SMTP cifrato, email destinatari, schedule reminders, branding, holding info'
BB $d1 'notifications, cache, jobs - tabelle standard Laravel'

PBR $d1
H1 $d1 '4. Autenticazione e Autorizzazione RBAC'
PP $d1 'Il sistema implementa un controllo accessi a due livelli: ruoli macro e permessi granulari. Sopra si applica il company scoping per il multi-tenancy.'
H2 $d1 '4.1 Ruoli'
BB $d1 'admin - accesso completo a tutte le sezioni e tutte le aziende; vede Libri Sociali, Utenti, Impostazioni'
BB $d1 'manager - accesso alle sezioni operative: aziende, documenti, soci, stati familiari'
BB $d1 'operatore - accesso limitato ai permessi assegnati individualmente'
H2 $d1 '4.2 Permessi Granulari'
BB $d1 'companies, members, stati_famiglia, documents - abilitano le rispettive sezioni del menu'
BB $d1 'documents.download - permesso specifico per scaricare file (separato dall accesso alla sezione)'
BB $d1 'documents.delete, membri.delete - eliminazione separata dal permesso di accesso'
H2 $d1 '4.3 Company Scoping'
PP $d1 'Ogni utente non-admin e assegnato a una o piu aziende tramite la pivot company_user. I tre Model principali implementano scopeForUser per filtrare automaticamente le query. L admin riceve null da accessibleCompanyIds e bypassa tutti i filtri.'
BB $d1 'Company::forUser - whereIn id companyIds'
BB $d1 'Member::forUser - whereHas officers whereIn company_id companyIds'
BB $d1 'Document::forUser - documenti delle company assegnate inclusi documenti dei loro soci'

PBR $d1
H1 $d1 '5. Pattern di Codice e Ottimizzazioni'
H2 $d1 '5.1 Eloquent Scopes Riutilizzabili'
BB $d1 'scopeActive - is_active = true su Company, Member, User'
BB $d1 'scopeForUser - filtra per accesso utente su Company, Member, Document'
BB $d1 'scopeSearch - ricerca LIKE multi-campo su denominazione, CF, PI'
BB $d1 'scopeExpiring / scopeExpired / scopeValid - stati scadenza documenti'
BB $d1 'scopeWithDetails - eager load company + member + category su Document, usato in 12 punti'
BB $d1 'scopeOrderByName - orderBy cognome poi nome su Member, usato in 6 controller'
H2 $d1 '5.2 Ottimizzazioni Performance Implementate'
BB $d1 'Eliminazione N+1: bulk load con whereIn e keyBy prima dei loop, es. sendDeclarations'
BB $d1 'Chunk di 500: ExpirationCheckService elabora documenti a blocchi per risparmiare RAM'
BB $d1 'Cache::remember 300s: 7 metodi DashboardService con chiave md5 di companyIds'
BB $d1 'Eager loading esplicito con with su tutti i controller che caricano relazioni'
BB $d1 'getCurrentStatoCivileAttribute: controlla relationLoaded prima di eseguire una nuova query'
BB $d1 'Download streamed: StreamedResponse evita di caricare il file in memoria'
H2 $d1 '5.3 Comandi Artisan Pianificati'
BB $d1 'CheckDocumentExpirations - aggiorna expiration_status, invia notifiche push; pianificato alle 08:00'
BB $d1 'SendDocumentExpiryEmail - invia email memo via ExpiryEmailService; schedulabile con cron'
H2 $d1 '5.4 Gestione File'
BB $d1 'Percorso su disco: companies/{id}/{categoria}/ oppure members/{id}/{categoria}/'
BB $d1 'Versionamento: ogni nuovo upload crea un DocumentVersion, il documento punta all ultima versione'
BB $d1 'Tipi consentiti: pdf, doc, docx, xls, xlsx, jpg, jpeg, png, zip, p7m'
BB $d1 'Dimensione massima: 50 MB configurabile via env UPLOAD_MAX_SIZE_MB'

PBR $d1
H1 $d1 '6. Email, Notifiche e Sicurezza'
H2 $d1 '6.1 SMTP Configurabile da Interfaccia'
PP $d1 'I parametri SMTP sono salvati in app_settings con la password cifrata tramite il cast encrypted di Laravel (AES-256). AppServiceProvider::boot legge le impostazioni e sovrascrive la configurazione mail di Laravel a runtime. Una guard Schema::hasTable protegge da errori durante le migration.'
H2 $d1 '6.2 Flusso Email Scadenze - ExpiryEmailService'
BB $d1 'Fase 1: per ogni utente attivo carica documenti con forUser e invia email personalizzata solo se ci sono scadenze'
BB $d1 'Fase 2: per destinatari manuali in app_settings.notification_emails invia email globale con tutte le scadenze'
H2 $d1 '6.3 Notifiche Push'
BB $d1 'Standard W3C Web Push con autenticazione VAPID basata su ECDSA P-256'
BB $d1 'Endpoint e chiavi salvati in push_subscriptions per ogni browser/dispositivo'
BB $d1 'Invio automatico da CheckDocumentExpirations al rilevamento di nuove scadenze'
H2 $d1 '6.4 Sicurezza'
BB $d1 'CSRF protection su tutti i form POST/PUT/DELETE via token Blade'
BB $d1 'Rate limiting login: max 5 tentativi, poi blocco con messaggio sui secondi rimanenti'
BB $d1 'Audit trail completo: ogni azione registrata in activity_logs con IP e user agent'
BB $d1 'SoftDelete su Company, Member, Document: record mai eliminati fisicamente dal DB'
BB $d1 'Verifica MIME type reale degli upload, non solo estensione del file'
BB $d1 'abort_unless 403 su ogni operazione che richiede accesso a risorse specifiche'

$p1 = "$desktop\Documentazione Tecnica - Archivio Societario.docx"
$d1.SaveAs2($p1, 16)
$d1.Close()
Write-Host "Documento tecnico salvato: $p1"

# ============================================================
# DOCUMENTO 2: MANUALE UTENTE
# ============================================================
Write-Host 'Creo manuale utente...'
$d2 = $word.Documents.Add()
$d2.PageSetup.LeftMargin  = $word.CentimetersToPoints(2.5)
$d2.PageSetup.RightMargin = $word.CentimetersToPoints(2.5)

$sel = $d2.Application.Selection
$sel.Style = $d2.Styles.Item('Titolo')
$sel.TypeText('ARCHIVIO DATI SOCIETARI')
$sel.TypeParagraph()
$sel.Style = $d2.Styles.Item('Sottotitolo')
$sel.TypeText('Manuale Utente - Cosa fa il gestionale e come funziona')
$sel.TypeParagraph()
$sel.Style = $d2.Styles.Item('Normale')
$sel.TypeText('Gruppo di Martino  |  Versione 1.0  |  Febbraio 2026')
$sel.TypeParagraph()
PBR $d2

H1 $d2 'Introduzione'
PP $d2 "L'Archivio Dati Societari e il gestionale interno del Gruppo di Martino. E uno strumento web accessibile da qualsiasi browser senza bisogno di installare nulla sul computer. Serve a raccogliere, organizzare e monitorare tutte le informazioni relative alle societa del gruppo: documenti, cariche, soci, verbali e dichiarazioni."
PP $d2 'Questo documento spiega cosa fa il gestionale sezione per sezione, in modo che chiunque possa capire le funzionalita disponibili e come usarle.'
H2 $d2 'Come si accede'
BB $d2 "Si apre il browser e si va all'indirizzo del gestionale (fornito dall'amministratore)"
BB $d2 "Si inserisce email e password assegnate dall'admin"
BB $d2 'Dopo il login si arriva alla Dashboard con le statistiche principali'
BB $d2 "In caso di troppi tentativi errati, il sistema blocca temporaneamente l'accesso per sicurezza"
H2 $d2 'La barra laterale (menu)'
PP $d2 "A sinistra dello schermo c'e sempre il menu di navigazione. Le voci visibili dipendono dal proprio ruolo: un operatore potrebbe non vedere le sezioni riservate agli admin."

PBR $d2
H1 $d2 '1. Dashboard - La pagina principale'
PP $d2 "La dashboard e la prima cosa che appare dopo il login. Mostra una panoramica rapida dello stato del gruppo, personalizzata in base alle aziende a cui l'utente ha accesso."
H2 $d2 'Cosa si vede nella dashboard'
BB $d2 'Numero totale di societa, soci e documenti archiviati'
BB $d2 'Documenti in scadenza entro 30 giorni e gia scaduti, evidenziati in giallo e rosso'
BB $d2 'Grafico a torta: distribuzione documenti per stato (validi / in scadenza / scaduti)'
BB $d2 'Grafico a barre: documenti per categoria (visure, contratti, certificati, ecc.)'
BB $d2 'Grafico a barre: documenti per azienda'
BB $d2 "Grafico a linee: attivita di caricamento negli ultimi 12 mesi"
BB $d2 "Elenco ultime azioni registrate nel sistema"
BB $d2 'Solo admin: prossime riunioni, verbali mancanti, statistiche dichiarazioni'
PP $d2 'I dati della dashboard si aggiornano automaticamente ogni 5 minuti grazie alla cache, per garantire velocita anche con molti documenti.'

PBR $d2
H1 $d2 '2. Societa - Gestione delle aziende del gruppo'
PP $d2 "In questa sezione si trovano tutte le societa del gruppo. E il cuore del gestionale: documenti, cariche e riunioni sono tutti collegati a una specifica societa."
H2 $d2 'Scheda di una societa'
BB $d2 'Dati anagrafici: denominazione, forma giuridica (Srl, SpA ecc.), codice fiscale, partita IVA, PEC'
BB $d2 'Sede legale: indirizzo, citta, provincia, CAP'
BB $d2 'Dati economici: capitale sociale, quota versata, data di costituzione, numero REA, codice ATECO'
BB $d2 'Contatti: telefono, email, sito web'
BB $d2 'Note interne libere e logo aziendale caricabile'
H2 $d2 'Cariche societarie'
PP $d2 'Per ogni societa si possono registrare tutte le cariche: amministratori, sindaci, revisori, ecc.'
BB $d2 'Si specifica: nome del socio, ruolo, data di nomina, eventuale data di scadenza, compenso'
BB $d2 "Quando una carica finisce si registra la data di cessazione; rimane nell'archivio come storico"
BB $d2 'Le cariche in scadenza compaiono nello Scadenzario nella scheda Cariche'
H2 $d2 'Azionisti e Soci'
BB $d2 'Tipo: persona fisica o persona giuridica (altra societa)'
BB $d2 'Quote: percentuale, valore in euro, diritti di voto'
BB $d2 'Date di ingresso e uscita dalla compagine societaria'
H2 $d2 'Relazioni tra societa'
PP $d2 'Si possono mappare le relazioni di controllo o partecipazione tra le aziende del gruppo (controllante, controllata, quota di partecipazione), creando un registro della struttura societaria.'

PBR $d2
H1 $d2 '3. Soci - Anagrafica dei componenti'
PP $d2 "La sezione Soci contiene l'anagrafica di tutte le persone fisiche coinvolte nelle societa del gruppo. E separata dalla sezione Societa perche la stessa persona puo avere ruoli in piu aziende."
H2 $d2 'Dati del socio'
BB $d2 'Dati personali: nome, cognome, codice fiscale, data e luogo di nascita, nazionalita, sesso'
BB $d2 'Residenza e domicilio (se diversi)'
BB $d2 'Contatti: telefono, cellulare, email, PEC'
BB $d2 'Stato civile attuale (aggiornato dalla sezione Stati Familiari)'
BB $d2 'Note interne libere'
H2 $d2 'White List antiriciclaggio'
PP $d2 "Ogni socio puo essere marcato come iscritto alla White List (registro antiriciclaggio). Si registra la data di scadenza dell'iscrizione. Le scadenze imminenti compaiono nello Scadenzario."
H2 $d2 'Documenti personali del socio'
PP $d2 "Dalla scheda del socio si possono vedere e caricare documenti che lo riguardano direttamente: carta d'identita, tessera sanitaria, certificato di residenza, ecc."

PBR $d2
H1 $d2 '4. Documenti - Archivio digitale con scadenzario'
PP $d2 "E la sezione piu usata quotidianamente. Permette di caricare, organizzare e tenere traccia di tutti i documenti del gruppo, con controllo automatico delle scadenze."
H2 $d2 'Caricare un documento'
BB $d2 "A chi appartiene: una societa specifica oppure un socio specifico"
BB $d2 'Categoria: visura camerale, contratto, certificato, verbale, ecc. (categorie predefinite)'
BB $d2 'Titolo e descrizione libera'
BB $d2 'File da caricare: PDF, Word, Excel, immagini, ZIP, p7m (max 50 MB)'
BB $d2 'Data di scadenza facoltativa: se inserita il sistema monitora il documento automaticamente'
H2 $d2 'Versionamento dei documenti'
PP $d2 "Se un documento viene aggiornato (es. contratto rinnovato), si carica una nuova versione senza perdere la precedente. Il sistema mantiene uno storico completo con: numero versione, data upload, chi ha caricato, note sul cambiamento."
H2 $d2 'Filtri e ricerca'
BB $d2 'Per societa o socio associato al documento'
BB $d2 'Per categoria del documento'
BB $d2 'Per stato: valido, in scadenza, scaduto'
BB $d2 'Per parola chiave nel titolo'
H2 $d2 'Scadenzario unificato'
PP $d2 'La pagina Scadenzario (nel menu Documenti) mostra in un unico posto tutte le scadenze, organizzate in tre schede:'
BB $d2 'Scheda Documenti: documenti in scadenza entro 90 giorni e gia scaduti, con link diretto al documento'
BB $d2 'Scheda Cariche: cariche societarie con data di scadenza imminente o gia passata'
BB $d2 'Scheda White List: iscrizioni white list dei soci in scadenza o scadute'
PP $d2 'Ogni voce mostra i giorni rimanenti o di ritardo con colori semaforo: verde = ok, giallo = attenzione, rosso = azione richiesta.'

PBR $d2
H1 $d2 '5. Stati Familiari - Dichiarazioni e nucleo familiare'
PP $d2 "Questa sezione gestisce le informazioni sullo stato civile e il nucleo familiare di ogni socio, con generazione automatica delle dichiarazioni annuali in PDF."
H2 $d2 'Variazioni stato civile'
BB $d2 'Si registra ogni cambiamento: matrimonio, divorzio, vedovanza, ecc.'
BB $d2 'Per ogni variazione: nuovo stato civile, data, note, chi ha registrato la variazione'
BB $d2 'Il sistema mantiene lo storico completo di tutte le variazioni nel tempo'
H2 $d2 'Nucleo familiare'
BB $d2 "Si registrano i componenti del nucleo: nome, codice fiscale, relazione, data di nascita"
BB $d2 "Si traccia l'entrata e l'uscita di ogni componente dal nucleo (es. figlio che si emancipa)"
H2 $d2 'Dichiarazioni annuali PDF'
BB $d2 "1. Si seleziona l'anno e si clicca Genera per un socio, oppure Genera Tutte per produrle in massa"
BB $d2 '2. Il PDF generato viene scaricato, stampato e fatto firmare al socio'
BB $d2 '3. Il documento firmato (o firmato digitalmente) viene caricato nel sistema'
BB $d2 "4. E possibile scaricare tutte le dichiarazioni di un anno in un unico file ZIP"

PBR $d2
H1 $d2 '6. Libri Sociali - Riunioni, verbali e delibere'
PP $d2 'Sezione riservata agli amministratori. Gestisce il registro delle riunioni societarie: CDA, assemblee, collegio sindacale.'
H2 $d2 'Tipi di riunione gestiti'
BB $d2 'Consiglio di Amministrazione (CDA)'
BB $d2 'Assemblea Ordinaria dei Soci'
BB $d2 'Assemblea Straordinaria dei Soci'
BB $d2 'Collegio Sindacale'
H2 $d2 'Ciclo di vita di una riunione'
BB $d2 'Programmata: data fissata ma non ancora convocata ufficialmente'
BB $d2 'Convocata: convocazione inviata, si allega il PDF della convocazione'
BB $d2 'Svolta: si registrano presenze e delibere, si allega il verbale PDF'
BB $d2 'Annullata: riunione cancellata'
H2 $d2 'Cosa si registra per una riunione svolta'
BB $d2 'Luogo, data e ora della riunione, ordine del giorno'
BB $d2 'Presenze: per ogni membro si indica se era presente, assente o in delega'
BB $d2 'Delibere: numero progressivo, oggetto, esito (approvata / respinta / sospesa) e note'
BB $d2 'Verbale PDF allegato dopo la riunione'
H2 $d2 'Dashboard Libri Sociali'
BB $d2 'Prossime riunioni nei 30 giorni successivi'
BB $d2 'Verbali da caricare: riunioni svolte senza verbale allegato (evidenziate in rosso)'
BB $d2 "Statistiche: riunioni nell'anno corrente, verbali mancanti"

PBR $d2
H1 $d2 '7. Email e Notifiche automatiche'
PP $d2 'Il gestionale invia automaticamente promemoria quando si avvicinano le scadenze di documenti, cariche o iscrizioni white list.'
H2 $d2 'Come funzionano le email di scadenza'
BB $d2 "Ogni utente riceve un'email personalizzata con le scadenze delle societa che gestisce"
BB $d2 'Gli amministratori vedono le scadenze di tutte le societa'
BB $d2 "Si puo aggiungere una lista di indirizzi extra per un'email riepilogativa globale"
BB $d2 "L'email puo essere inviata manualmente dalla sezione Email o impostata per partire automaticamente"
H2 $d2 'Dichiarazioni via email'
PP $d2 "Dalla sezione Email si puo inviare un promemoria ai soci per compilare la dichiarazione dello stato famiglia dell'anno in corso."
H2 $d2 'Notifiche nel browser'
PP $d2 "Se si autorizza il browser a ricevere notifiche, il gestionale invia avvisi automatici quando nuovi documenti vanno in scadenza, anche a pagina chiusa."
H2 $d2 'Configurazione SMTP - solo Admin'
PP $d2 "L'amministratore puo configurare il server email direttamente dal gestionale: indirizzo server, porta, cifratura, credenziali. Il pulsante Testa Connessione verifica che tutto funzioni prima di salvare."

PBR $d2
H1 $d2 '8. Utenti e controllo degli accessi - solo Admin'
PP $d2 "Gli amministratori gestiscono gli account degli altri utenti, definendo cosa puo vedere e fare ognuno."
H2 $d2 'Ruoli disponibili'
BB $d2 'Amministratore: accesso completo a tutto il sistema, vede tutte le societa'
BB $d2 'Manager: accesso alle sezioni operative (societa, documenti, soci, stati familiari)'
BB $d2 'Operatore: accesso personalizzato in base ai permessi assegnati'
H2 $d2 'Permessi specifici'
BB $d2 'Permesso di scaricare file: documents.download'
BB $d2 'Permesso di eliminare documenti: documents.delete'
BB $d2 'Permesso di eliminare soci: membri.delete'
H2 $d2 'Societa assegnate'
PP $d2 "Per gli utenti non-admin si specificano le societa a cui hanno accesso. Garantisce che ogni operatore veda solo i dati rilevanti per il proprio lavoro."

PBR $d2
H1 $d2 '9. Impostazioni generali - solo Admin'
BB $d2 'Nome e sottotitolo del gestionale (mostrati in cima e nel browser)'
BB $d2 'Dati della holding capogruppo: ragione sociale, CF, PI, indirizzo, contatti'
BB $d2 'Testo e titolo del modulo di login'
BB $d2 'Logo personalizzato e favicon (le icone del browser)'
BB $d2 'Intestazione e footer per le dichiarazioni stato famiglia generate in PDF'

PBR $d2
H1 $d2 '10. Log Attivita - Tracciamento completo'
PP $d2 "Il gestionale registra automaticamente ogni azione degli utenti: chi ha fatto cosa, quando e da quale indirizzo IP. Visibile nella sezione Log Attivita (admin e manager)."
H2 $d2 'Cosa viene tracciato'
BB $d2 'Login e logout di ogni utente'
BB $d2 'Creazione, modifica ed eliminazione di societa, soci, documenti, cariche'
BB $d2 'Download di file e generazione di PDF'
BB $d2 'Modifiche alle impostazioni e al logo'
BB $d2 'Invio di email e tentativi di login falliti'
H2 $d2 'A cosa serve'
BB $d2 'Verificare chi ha modificato un dato e quando'
BB $d2 'Rilevare eventuali accessi non autorizzati'
BB $d2 'Rispettare i requisiti normativi di tracciabilita (GDPR, compliance aziendale)'

$p2 = "$desktop\Manuale Utente - Archivio Societario.docx"
$d2.SaveAs2($p2, 16)
$d2.Close()
Write-Host "Manuale utente salvato: $p2"

$word.Quit()
Write-Host 'COMPLETATO'
