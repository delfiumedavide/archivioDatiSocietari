<?php

namespace App\Http\Controllers;

use App\Services\AppSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        private AppSettingsService $settingsService
    ) {}

    public function index(): View
    {
        $settings = $this->settingsService->get();

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:100'],
            'app_subtitle' => ['required', 'string', 'max:100'],
            'login_title' => ['nullable', 'string', 'max:100'],
            'holding_ragione_sociale' => ['nullable', 'string', 'max:200'],
            'holding_forma_giuridica' => ['nullable', 'string', 'max:50'],
            'holding_codice_fiscale' => ['nullable', 'string', 'max:16'],
            'holding_partita_iva' => ['nullable', 'string', 'max:11'],
            'holding_indirizzo' => ['nullable', 'string', 'max:200'],
            'holding_citta' => ['nullable', 'string', 'max:100'],
            'holding_provincia' => ['nullable', 'string', 'max:2'],
            'holding_cap' => ['nullable', 'string', 'max:5'],
            'holding_telefono' => ['nullable', 'string', 'max:20'],
            'holding_email' => ['nullable', 'string', 'email', 'max:100'],
            'holding_pec' => ['nullable', 'string', 'email', 'max:100'],
            'holding_rea' => ['nullable', 'string', 'max:50'],
            'holding_capitale_sociale' => ['nullable', 'numeric', 'min:0'],
            'declaration_header_title' => ['nullable', 'string', 'max:200'],
            'declaration_header_subtitle' => ['nullable', 'string', 'max:200'],
            'declaration_footer_text' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['updated_by'] = $request->user()->id;

        $this->settingsService->update($validated);

        $this->logActivity($request, 'updated', 'Aggiornate impostazioni generali', \App\Models\AppSetting::class, 1);

        return redirect()->route('settings.index')
            ->with('success', 'Impostazioni aggiornate.');
    }

    public function uploadLogo(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['required', 'file', 'max:2048', 'mimes:svg,png,jpg,jpeg,webp'],
        ]);

        $this->settingsService->updateLogo($request->file('logo'));

        $this->logActivity($request, 'uploaded', 'Aggiornato logo del gestionale', \App\Models\AppSetting::class, 1);

        return redirect()->route('settings.index')
            ->with('success', 'Logo aggiornato.');
    }

    public function uploadFavicon(Request $request): RedirectResponse
    {
        $request->validate([
            'favicon' => ['required', 'file', 'max:1024', 'mimes:svg,png,ico'],
        ]);

        $this->settingsService->updateFavicon($request->file('favicon'));

        $this->logActivity($request, 'uploaded', 'Aggiornata favicon del gestionale', \App\Models\AppSetting::class, 1);

        return redirect()->route('settings.index')
            ->with('success', 'Favicon aggiornata.');
    }

    public function removeLogo(Request $request): RedirectResponse
    {
        $this->settingsService->removeLogo();

        $this->logActivity($request, 'deleted', 'Rimosso logo personalizzato', \App\Models\AppSetting::class, 1);

        return redirect()->route('settings.index')
            ->with('success', 'Logo rimosso. Ripristinato quello predefinito.');
    }
}
