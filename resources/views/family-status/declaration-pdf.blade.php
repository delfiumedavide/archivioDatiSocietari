<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 16px;
            color: #1a365d;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header h2 {
            font-size: 14px;
            color: #2d3748;
            margin: 0 0 5px 0;
        }
        .header .subtitle {
            font-size: 11px;
            color: #718096;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #1a365d;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 4px;
            margin: 25px 0 12px 0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table td {
            padding: 5px 8px;
            vertical-align: top;
        }
        .data-table .label {
            font-weight: bold;
            color: #4a5568;
            width: 180px;
            white-space: nowrap;
        }
        .data-table .value {
            color: #1a202c;
        }
        .family-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .family-table th {
            background-color: #edf2f7;
            color: #2d3748;
            font-weight: bold;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #cbd5e0;
            font-size: 11px;
        }
        .family-table td {
            padding: 6px;
            border: 1px solid #cbd5e0;
            font-size: 11px;
        }
        .family-table tr:nth-child(even) {
            background-color: #f7fafc;
        }
        .signature-section {
            margin-top: 60px;
            page-break-inside: avoid;
        }
        .signature-row {
            width: 100%;
        }
        .signature-row td {
            padding: 15px 0;
            vertical-align: bottom;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            width: 250px;
            display: inline-block;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 40px;
            right: 40px;
            text-align: center;
            font-size: 9px;
            color: #a0aec0;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
        .no-members {
            text-align: center;
            color: #718096;
            font-style: italic;
            padding: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gruppo Di Martino</h1>
        <h2>Dichiarazione dello Stato di Famiglia</h2>
        <div class="subtitle">Anno {{ $anno }}</div>
    </div>

    <div class="section-title">Dati Anagrafici</div>
    <table class="data-table">
        <tr>
            <td class="label">Cognome:</td>
            <td class="value">{{ $member->cognome }}</td>
            <td class="label">Nome:</td>
            <td class="value">{{ $member->nome }}</td>
        </tr>
        <tr>
            <td class="label">Codice Fiscale:</td>
            <td class="value" colspan="3">{{ $member->codice_fiscale ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Data di Nascita:</td>
            <td class="value">{{ $member->data_nascita?->format('d/m/Y') ?? '—' }}</td>
            <td class="label">Luogo di Nascita:</td>
            <td class="value">
                {{ $member->luogo_nascita_comune ?? '—' }}
                @if($member->luogo_nascita_provincia)
                    ({{ $member->luogo_nascita_provincia }})
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Nazionalità:</td>
            <td class="value">{{ $member->nazionalita ?? '—' }}</td>
            <td class="label">Sesso:</td>
            <td class="value">{{ $member->sesso ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Stato Civile:</td>
            <td class="value" colspan="3">{{ $statoCivile ?? '—' }}</td>
        </tr>
    </table>

    <div class="section-title">Residenza</div>
    <table class="data-table">
        <tr>
            <td class="label">Indirizzo:</td>
            <td class="value" colspan="3">{{ $member->indirizzo_residenza ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Città:</td>
            <td class="value">{{ $member->citta_residenza ?? '—' }}</td>
            <td class="label">Provincia:</td>
            <td class="value">{{ $member->provincia_residenza ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">CAP:</td>
            <td class="value" colspan="3">{{ $member->cap_residenza ?? '—' }}</td>
        </tr>
    </table>

    <div class="section-title">Composizione Nucleo Familiare</div>
    @if($familyMembers->count() > 0)
        <table class="family-table">
            <thead>
                <tr>
                    <th>Cognome</th>
                    <th>Nome</th>
                    <th>Codice Fiscale</th>
                    <th>Relazione</th>
                    <th>Data di Nascita</th>
                    <th>Luogo di Nascita</th>
                </tr>
            </thead>
            <tbody>
                @foreach($familyMembers as $fm)
                    <tr>
                        <td>{{ $fm->cognome }}</td>
                        <td>{{ $fm->nome }}</td>
                        <td>{{ $fm->codice_fiscale ?? '—' }}</td>
                        <td>{{ $fm->relazione }}</td>
                        <td>{{ $fm->data_nascita?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ $fm->luogo_nascita ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="no-members">Nessun componente del nucleo familiare registrato.</p>
    @endif

    <div class="signature-section">
        <table class="signature-row" style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    Data: _____ / _____ / __________
                </td>
                <td style="width: 50%; text-align: right;">
                    Firma: <span class="signature-line">&nbsp;</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Generato il {{ $generatedAt->format('d/m/Y H:i') }} dal sistema Archivio Societario — Gruppo Di Martino
    </div>
</body>
</html>
