<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0; background: #f4f4f4; }
        .wrapper { max-width: 680px; margin: 20px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #1a365d; color: #fff; padding: 24px 32px; }
        .header h1 { margin: 0; font-size: 20px; letter-spacing: 0.5px; }
        .header p { margin: 4px 0 0; font-size: 13px; color: #a0b8d8; }
        .content { padding: 28px 32px; }
        .section-title { font-size: 15px; font-weight: bold; color: #1a365d; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin: 24px 0 14px; }
        .section-title:first-child { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th { background: #edf2f7; color: #2d3748; font-size: 12px; font-weight: bold; padding: 8px 10px; text-align: left; border: 1px solid #cbd5e0; }
        td { padding: 8px 10px; border: 1px solid #e2e8f0; font-size: 13px; vertical-align: top; }
        tr:nth-child(even) td { background: #f7fafc; }
        .badge-warning { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; background: #fef3c7; color: #92400e; }
        .badge-danger { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; background: #fee2e2; color: #991b1b; }
        .empty-note { color: #718096; font-style: italic; font-size: 13px; padding: 10px 0; }
        .footer { background: #f7fafc; border-top: 1px solid #e2e8f0; padding: 16px 32px; text-align: center; font-size: 11px; color: #a0aec0; }
        .summary-box { background: #ebf8ff; border-left: 4px solid #3182ce; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; }
        .summary-box p { margin: 0; font-size: 13px; color: #2b6cb0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>{{ $appSettings->app_name ?? 'Archivio Societario' }}</h1>
            <p>Promemoria Scadenze Documenti</p>
        </div>

        <div class="content">
            <div class="summary-box">
                <p>
                    <strong>Riepilogo al {{ now()->format('d/m/Y') }}:</strong>
                    {{ $expiring->count() }} document{{ $expiring->count() === 1 ? 'o in scadenza' : 'i in scadenza' }}
                    nei prossimi {{ $days }} giorni,
                    {{ $expired->count() }} document{{ $expired->count() === 1 ? 'o scaduto' : 'i scaduti' }}.
                </p>
            </div>

            {{-- Documenti in scadenza --}}
            <div class="section-title">Documenti in Scadenza (entro {{ $days }} giorni)</div>
            @if($expiring->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Titolo</th>
                            <th>Intestatario</th>
                            <th>Scadenza</th>
                            <th>Giorni Rimanenti</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiring as $doc)
                            <tr>
                                <td>{{ $doc->title }}</td>
                                <td>{{ $doc->owner_name }}</td>
                                <td>{{ $doc->expiration_date?->format('d/m/Y') ?? '—' }}</td>
                                <td>
                                    @if($doc->days_until_expiration !== null)
                                        <span class="badge-warning">{{ $doc->days_until_expiration }} gg</span>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="empty-note">Nessun documento in scadenza nei prossimi {{ $days }} giorni.</p>
            @endif

            {{-- Documenti scaduti --}}
            <div class="section-title">Documenti Già Scaduti</div>
            @if($expired->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Titolo</th>
                            <th>Intestatario</th>
                            <th>Scaduto il</th>
                            <th>Stato</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expired as $doc)
                            <tr>
                                <td>{{ $doc->title }}</td>
                                <td>{{ $doc->owner_name }}</td>
                                <td>{{ $doc->expiration_date?->format('d/m/Y') ?? '—' }}</td>
                                <td><span class="badge-danger">Scaduto</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="empty-note">Nessun documento scaduto.</p>
            @endif
        </div>

        <div class="footer">
            Generato automaticamente il {{ now()->format('d/m/Y \a\l\l\e H:i') }} dal sistema {{ $appSettings->app_name ?? 'Archivio Societario' }}.
            Questo messaggio è stato inviato agli indirizzi configurati nelle impostazioni.
        </div>
    </div>
</body>
</html>
