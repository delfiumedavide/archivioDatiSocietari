<?php

namespace App\Http\Controllers;

use App\Mail\FamilyStatusDeclarationMail;
use App\Models\Document;
use App\Models\FamilyStatusDeclaration;
use App\Models\Member;
use App\Services\AppSettingsService;
use App\Services\ExpiryEmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailController extends Controller
{
    public function __construct(
        private AppSettingsService $settingsService,
        private ExpiryEmailService $expiryEmailService,
    ) {}

    public function index(Request $request): View
    {
        $settings = $this->settingsService->get();
        $days = $settings->expiry_reminder_days ?? 30;

        $expiring = Document::withDetails()
            ->expiring($days)
            ->orderBy('expiration_date')
            ->limit(10)
            ->get();

        $expiringCount = Document::expiring($days)->count();
        $expiredCount  = Document::expired()->count();

        // Tab dichiarazioni: carica membri con email e stato dichiarazione per l'anno selezionato
        $anno = (int) ($request->input('anno', date('Y')));

        $members = Member::where('is_active', true)
            ->orderByName()
            ->get();

        $declarations = FamilyStatusDeclaration::forYear($anno)
            ->whereIn('member_id', $members->pluck('id'))
            ->get()
            ->keyBy('member_id');

        return view('email.index', compact(
            'settings',
            'days',
            'expiring',
            'expiringCount',
            'expiredCount',
            'anno',
            'members',
            'declarations',
        ));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'notification_emails'     => 'nullable|string|max:2000',
            'expiry_reminder_days'    => 'required|integer|min:1|max:365',
            'expiry_reminder_enabled' => 'nullable|boolean',
            'expiry_reminder_time'    => 'nullable|regex:/^\d{2}:\d{2}$/',
        ]);

        $this->settingsService->update([
            'notification_emails'     => $validated['notification_emails'] ?? null,
            'expiry_reminder_days'    => $validated['expiry_reminder_days'],
            'expiry_reminder_enabled' => isset($validated['expiry_reminder_enabled']) ? (bool) $validated['expiry_reminder_enabled'] : false,
            'expiry_reminder_time'    => $validated['expiry_reminder_time'] ?? '08:00',
            'updated_by'              => auth()->id(),
        ]);

        return redirect()->route('email.index', ['tab' => 'config'])
            ->with('success', 'Configurazione email salvata.');
    }

    public function sendExpiryReminder(): RedirectResponse
    {
        $settings = $this->settingsService->get();
        $days     = $settings->expiry_reminder_days ?? 30;

        ['sent' => $sent, 'errors' => $errors] = $this->expiryEmailService->sendAll(
            $days,
            $settings->notification_emails ?? ''
        );

        if ($sent === 0 && empty($errors)) {
            return redirect()->route('email.index', ['tab' => 'scadenze'])
                ->with('info', 'Nessun documento in scadenza o scaduto. Nessuna email inviata.');
        }

        $message = "Email promemoria inviata a {$sent} destinatari.";
        if (!empty($errors)) {
            $message .= ' Errori per: ' . implode(', ', $errors);
        }

        return redirect()->route('email.index', ['tab' => 'scadenze'])
            ->with($errors ? 'warning' : 'success', $message);
    }

    public function updateSmtpSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'smtp_host'         => 'nullable|string|max:255',
            'smtp_port'         => 'nullable|integer|min:1|max:65535',
            'smtp_encryption'   => 'nullable|in:tls,ssl,starttls',
            'smtp_username'     => 'nullable|string|max:255',
            'smtp_password'     => 'nullable|string|max:500',
            'smtp_from_address' => 'nullable|email|max:255',
            'smtp_from_name'    => 'nullable|string|max:255',
        ]);

        $data = [
            'smtp_host'         => $validated['smtp_host'] ?? null,
            'smtp_port'         => $validated['smtp_port'] ?? null,
            'smtp_encryption'   => $validated['smtp_encryption'] ?? null,
            'smtp_username'     => $validated['smtp_username'] ?? null,
            'smtp_from_address' => $validated['smtp_from_address'] ?? null,
            'smtp_from_name'    => $validated['smtp_from_name'] ?? null,
            'updated_by'        => auth()->id(),
        ];

        // Only update password if a new one was provided
        if (!empty($validated['smtp_password'])) {
            $data['smtp_password'] = $validated['smtp_password'];
        }

        $this->settingsService->update($data);

        return redirect()->route('email.index', ['tab' => 'config'])
            ->with('success', 'Configurazione SMTP salvata.');
    }

    public function testSmtpConnection(Request $request): JsonResponse
    {
        $host = trim($request->input('smtp_host', ''));
        $port = (int) $request->input('smtp_port', 587);

        if (empty($host)) {
            return response()->json(['ok' => false, 'message' => 'Inserisci un host SMTP prima di testare la connessione.']);
        }

        if ($port < 1 || $port > 65535) {
            return response()->json(['ok' => false, 'message' => 'Porta non valida.']);
        }

        $errno  = 0;
        $errstr = '';

        try {
            $socket = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, 5);

            if ($socket) {
                fclose($socket);

                return response()->json([
                    'ok'      => true,
                    'message' => "Connessione a {$host}:{$port} riuscita.",
                ]);
            }

            return response()->json([
                'ok'      => false,
                'message' => "Impossibile connettersi a {$host}:{$port}" . ($errstr ? ": {$errstr}" : '.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => 'Errore: ' . $e->getMessage()]);
        }
    }

    public function sendDeclarations(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_ids'   => 'required|array|min:1',
            'member_ids.*' => 'integer|exists:members,id',
            'subject'      => 'required|string|max:255',
            'body'         => 'required|string|max:5000',
            'anno'         => 'required|integer|min:2000|max:2100',
        ]);

        $anno      = $validated['anno'];
        $subject   = $validated['subject'];
        $body      = $validated['body'];
        $memberIds = $validated['member_ids'];

        // Bulk load — evita N+1 (2 query totali invece di 2×N)
        $members      = Member::whereIn('id', $memberIds)->get()->keyBy('id');
        $declarations = FamilyStatusDeclaration::whereIn('member_id', $memberIds)
            ->where('anno', $anno)
            ->get()
            ->keyBy('member_id');

        $sent    = 0;
        $skipped = [];

        foreach ($memberIds as $memberId) {
            $member = $members->get($memberId);

            if (!$member || empty($member->email)) {
                $skipped[] = ($member?->full_name ?? "ID {$memberId}") . ' (nessuna email)';
                continue;
            }

            $declaration = $declarations->get($memberId);

            if (!$declaration || !$declaration->generated_path) {
                $skipped[] = $member->full_name . ' (PDF non generato)';
                continue;
            }

            try {
                Mail::to($member->email)->send(
                    new FamilyStatusDeclarationMail($member, $declaration, $subject, $body)
                );
                $sent++;
            } catch (\Exception $e) {
                $skipped[] = $member->full_name . ' (errore invio)';
            }
        }

        $message = "Email inviate: {$sent}.";
        if (!empty($skipped)) {
            $message .= ' Saltati: ' . implode(', ', $skipped) . '.';
        }

        $flashType = $sent > 0 ? ($skipped ? 'warning' : 'success') : 'error';

        return redirect()->route('email.index', ['tab' => 'dichiarazioni', 'anno' => $anno])
            ->with($flashType, $message);
    }
}
