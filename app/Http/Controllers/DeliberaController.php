<?php

namespace App\Http\Controllers;

use App\Models\Delibera;
use App\Models\Riunione;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeliberaController extends Controller
{
    public function store(Request $request, Riunione $riunione): RedirectResponse
    {
        $validated = $request->validate([
            'oggetto' => 'required|string|max:500',
            'esito'   => 'required|in:approvata,respinta,sospesa',
            'note'    => 'nullable|string|max:2000',
        ]);

        $numero = $riunione->delibere()->max('numero') + 1;

        $riunione->delibere()->create([
            'numero'  => $numero,
            'oggetto' => $validated['oggetto'],
            'esito'   => $validated['esito'],
            'note'    => $validated['note'] ?? null,
        ]);

        return redirect()->route('libri-sociali.show', $riunione)
            ->with('success', "Delibera n. {$numero} aggiunta.");
    }

    public function update(Request $request, Delibera $delibera): RedirectResponse
    {
        $validated = $request->validate([
            'oggetto' => 'required|string|max:500',
            'esito'   => 'required|in:approvata,respinta,sospesa',
            'note'    => 'nullable|string|max:2000',
        ]);

        $delibera->update($validated);

        return redirect()->route('libri-sociali.show', $delibera->riunione_id)
            ->with('success', 'Delibera aggiornata.');
    }

    public function destroy(Delibera $delibera): RedirectResponse
    {
        $riunioneId = $delibera->riunione_id;
        $delibera->delete();

        return redirect()->route('libri-sociali.show', $riunioneId)
            ->with('success', 'Delibera eliminata.');
    }
}
