<?php

namespace App\Http\Controllers;

use App\Mail\DocumentExpiryReminderMail;
use App\Mail\FamilyStatusDeclarationMail;
use App\Models\Document;
use App\Models\FamilyStatusDeclaration;
use App\Models\Member;
use App\Services\AppSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailController extends Controller
{
    public function __construct(private AppSettingsService $settingsService) {}

    public function index(Request $request): View
    {
        $settings = $this->settingsService->get();
        $days = $settings->expiry_reminder_days ?? 30;

        $expiring = Document::with(['company', 'member', 'category'])
            ->expiring($days)
            ->orderBy('expiration_date')
            ->limit(10)
            ->get();

        $expiringCount = Document::expiring($days)->count();
        $expiredCount  = Document::expired()->count();

        // Tab dichiarazioni: carica membri con email e stato dichiarazione per l'anno selezionato
        $anno = (int) ($request->input('anno', date('Y')));

        $members = Member::where('is_active', true)
            ->orderBy('cognome')->orderBy('nome')
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
            'notification_emails'   => 'nullable|string|max:2000',
            'expiry_reminder_days'  => 'required|integer|min:1|max:365',
        ]);

        $this->settingsService->update([
            'notification_emails'  => $validated['notification_emails'] ?? null,
            'expiry_reminder_days' => $validated['expiry_reminder_days'],
            'updated_by'           => auth()->id(),
        ]);

        return redirect()->route('email.index', ['tab' => 'config'])
            ->with('success', 'Configurazione email salvata.');
    }

    public function sendExpiryReminder(): RedirectResponse
    {
        $settings = $this->settingsService->get();
        $rawEmails = trim($settings->notification_emails ?? '');

        if (empty($rawEmails)) {
            return redirect()->route('email.index', ['tab' => 'scadenze'])
                ->with('error', 'Nessun indirizzo email configurato. Aggiungilo nella scheda Configurazione.');
        }

        $emails = collect(preg_split('/[\r\n,]+/', $rawEmails))
            ->map(fn ($e) => trim($e))
            ->filter(fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->values();

        if ($emails->isEmpty()) {
            return redirect()->route('email.index', ['tab' => 'scadenze'])
                ->with('error', 'Nessun indirizzo email valido trovato nella configurazione.');
        }

        $days = $settings->expiry_reminder_days ?? 30;

        $expiring = Document::with(['company', 'member', 'category'])
            ->expiring($days)->orderBy('expiration_date')->get();
        $expired  = Document::with(['company', 'member', 'category'])
            ->expired()->orderBy('expiration_date')->get();

        if ($expiring->isEmpty() && $expired->isEmpty()) {
            return redirect()->route('email.index', ['tab' => 'scadenze'])
                ->with('info', 'Nessun documento in scadenza o scaduto. Nessuna email inviata.');
        }

        $mailable = new DocumentExpiryReminderMail($expiring, $expired, $days);
        $sent = 0;
        $errors = [];

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send($mailable);
                $sent++;
            } catch (\Exception $e) {
                $errors[] = $email;
            }
        }

        $message = "Email promemoria inviata a {$sent} destinatari.";
        if (!empty($errors)) {
            $message .= ' Errori per: ' . implode(', ', $errors);
        }

        return redirect()->route('email.index', ['tab' => 'scadenze'])
            ->with($errors ? 'warning' : 'success', $message);
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

        $sent    = 0;
        $skipped = [];

        foreach ($memberIds as $memberId) {
            $member = Member::find($memberId);

            if (!$member || empty($member->email)) {
                $skipped[] = $member?->full_name . ' (nessuna email)';
                continue;
            }

            $declaration = FamilyStatusDeclaration::where('member_id', $memberId)
                ->where('anno', $anno)
                ->first();

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
