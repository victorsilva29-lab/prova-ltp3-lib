<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Autor;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class LivroController extends Controller
{
    public function index()
    {
        $livros = Livro::with('autor')->orderBy('titulo')->get();

        return view('livros.index', compact('livros'));
    }

    public function create()
    {
        $autores = Autor::orderBy('nome')->get();

        return view('livros.create', compact('autores'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'ano_publicacao' => ['required', 'integer', 'digits:4'],
            'isbn' => ['required', 'string', 'max:20', 'unique:livros,isbn'],
            'autor_id' => ['required', 'integer', 'exists:autores,id'],
        ]);

        Livro::create($dados);

        return redirect()->route('livros.index')->with('success', 'Livro cadastrado com sucesso.');
    }

    public function edit(Livro $livro)
    {
        $autores = Autor::orderBy('nome')->get();

        return view('livros.edit', compact('livro', 'autores'));
    }

    public function update(Request $request, Livro $livro)
    {
        $dados = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'ano_publicacao' => ['required', 'integer', 'digits:4'],
            'isbn' => ['required', 'string', 'max:20', Rule::unique('livros', 'isbn')->ignore($livro)],
            'autor_id' => ['required', 'integer', 'exists:autores,id'],
        ]);

        $livro->update($dados);

        return redirect()->route('livros.index')->with('success', 'Livro atualizado com sucesso.');
    }

    public function destroy(Livro $livro)
    {
        $livro->delete();

        return redirect()->route('livros.index')->with('success', 'Livro excluído com sucesso.');
    }
}
