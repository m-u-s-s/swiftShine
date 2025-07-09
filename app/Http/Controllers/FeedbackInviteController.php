<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use Illuminate\Http\Request;

class FeedbackInviteController extends Controller
{
    public function create(RendezVous $rendezVous)
    {
        $this->authorize('view', $rendezVous); // optionnel
        return view('client.feedback-form', compact('rendezVous'));
    }

    public function store(Request $request, RendezVous $rendezVous)
    {
        $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'required|string|min:5',
        ]);

        if ($rendezVous->feedback) {
            return redirect()->route('dashboard')->with('error', 'Feedback déjà donné.');
        }

        $rendezVous->feedback()->create([
            'client_id' => auth()->id(),
            'note' => $request->note,
            'commentaire' => $request->commentaire,
        ]);

        return back()->with('success', 'Merci pour votre retour !');
    }
}
