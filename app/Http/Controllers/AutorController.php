<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use Illuminate\Http\Request;

class AutorController extends Controller
{
    public function index()
    {
        $autores = Autor::orderBy('nome')->get();

        return view('autores.index', compact('autores'));
    }

    public function create()
    {
        return view('autores.create');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'nacionalidade' => ['required', 'string', 'max:100'],
        ]);

        Autor::create($dados);

        return redirect()->route('autores.index')->with('success', 'Autor cadastrado com sucesso.');
    }

    public function edit(Autor $autor)
    {
        return view('autores.edit', compact('autor'));
    }

    public function update(Request $request, Autor $autor)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'nacionalidade' => ['required', 'string', 'max:100'],
        ]);

        $autor->update($dados);

        return redirect()->route('autores.index')->with('success', 'Autor atualizado com sucesso.');
    }

    public function destroy(Autor $autor)
    {
        if ($autor->livros()->exists()) {
            return redirect()->route('autores.index')->with('error', 'Exclua ou transfira os livros antes de excluir este autor.');
        }

        $autor->delete();

        return redirect()->route('autores.index')->with('success', 'Autor excluído com sucesso.');
    }
}
