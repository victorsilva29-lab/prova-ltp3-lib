@extends('layouts.app')

@section('title', 'Cadastrar livro')

@section('content')
    <h1 class="mb-6 text-2xl font-bold">Cadastrar livro</h1>
    <form method="POST" action="{{ route('livros.store') }}" class="max-w-xl space-y-4 rounded bg-white p-6 shadow">
        @csrf
        <div>
            <label for="titulo" class="block mb-1 font-medium">Título</label>
            <input id="titulo" name="titulo" type="text" maxlength="255" required value="{{ old('titulo') }}" class="w-full rounded border px-3 py-2">
            @error('titulo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="ano_publicacao" class="block mb-1 font-medium">Ano de publicação</label>
            <input id="ano_publicacao" name="ano_publicacao" type="number" min="1000" max="9999" step="1" required value="{{ old('ano_publicacao') }}" class="w-full rounded border px-3 py-2">
            @error('ano_publicacao') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="isbn" class="block mb-1 font-medium">ISBN</label>
            <input id="isbn" name="isbn" type="text" maxlength="20" required value="{{ old('isbn') }}" class="w-full rounded border px-3 py-2">
            @error('isbn') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="autor_id" class="block mb-1 font-medium">Autor</label>
            <select id="autor_id" name="autor_id" required class="w-full rounded border px-3 py-2">
                <option value="">Selecione um autor</option>
                @foreach ($autores as $autor)
                    <option value="{{ $autor->id }}" @selected((string) old('autor_id') === (string) $autor->id)>{{ $autor->nome }}</option>
                @endforeach
            </select>
            @error('autor_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            @if ($autores->isEmpty())
                <p class="mt-2">Cadastre um <a class="text-indigo-600 underline" href="{{ route('autores.create') }}">autor</a> antes de salvar um livro.</p>
            @endif
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Salvar</button>
            <a href="{{ route('livros.index') }}" class="text-indigo-600">Voltar à listagem</a>
        </div>
    </form>
@endsection
