<?php

namespace App\Http\Controllers;

use App\Models\TermsOfUse;
use Illuminate\Http\Request;

class TermsOfUseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['show']);
    }

    public function index()
    {
        $terms = TermsOfUse::all();
        return view('terms.index', compact('terms'));
    }

    public function show($id)
    {
        $term = TermsOfUse::findOrFail($id);

        return view('terms.show', compact('term'));
    }

    public function create()
    {
        return view('terms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        TermsOfUse::create([
            'title' => $request->title,
            'content' => preg_replace("/\n+/", "\n", $request->content),
        ]);

        return redirect()->route('terms.index')->with('success', 'Termo criado com sucesso!');
    }

    public function edit($id)
    {
        $term = TermsOfUse::findOrFail($id);
        return view('terms.edit', compact('term'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $term = TermsOfUse::findOrFail($id);
        $term->update([
            'title' => $request->title,
            'content' => preg_replace("/\n+/", "\n", $request->content),
        ]);

        return redirect()->route('terms.index')->with('success', 'Termo atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $term = TermsOfUse::findOrFail($id);
        $term->delete();

        return redirect()->route('terms.index')->with('success', 'Termo excluído com sucesso!');
    }
}
