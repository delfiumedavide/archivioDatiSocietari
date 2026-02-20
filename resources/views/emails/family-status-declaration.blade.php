<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0; background: #f4f4f4; }
        .wrapper { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #1a365d; color: #fff; padding: 24px 32px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 4px 0 0; font-size: 12px; color: #a0b8d8; }
        .content { padding: 28px 32px; line-height: 1.7; }
        .attachment-note { background: #f0fff4; border-left: 4px solid #38a169; padding: 12px 16px; border-radius: 4px; margin-top: 24px; font-size: 13px; color: #276749; }
        .attachment-note strong { display: block; margin-bottom: 2px; }
        .footer { background: #f7fafc; border-top: 1px solid #e2e8f0; padding: 16px 32px; text-align: center; font-size: 11px; color: #a0aec0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Dichiarazione Stato di Famiglia</h1>
            <p>Anno {{ $anno }} — {{ $memberName }}</p>
        </div>

        <div class="content">
            {!! nl2br(e($body)) !!}

            <div class="attachment-note">
                <strong>Documento allegato:</strong>
                Dichiarazione Stato di Famiglia — Anno {{ $anno }}<br>
                Si prega di stampare il documento, firmarlo e restituirne una copia.
            </div>
        </div>

        <div class="footer">
            Messaggio inviato dal sistema {{ config('app.name') }} il {{ now()->format('d/m/Y \a\l\l\e H:i') }}.
        </div>
    </div>
</body>
</html>
