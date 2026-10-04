@extends('layouts.app')

@section('title', 'Cadastrar autor')

@section('content')
    <h1 class="mb-6 text-2xl font-bold">Cadastrar autor</h1>
    <form method="POST" action="{{ route('autores.store') }}" class="max-w-xl space-y-4 rounded bg-white p-6 shadow">
        @csrf
        <div>
            <label for="nome" class="block mb-1 font-medium">Nome</label>
            <input id="nome" name="nome" type="text" maxlength="255" required value="{{ old('nome') }}" class="w-full rounded border px-3 py-2">
            @error('nome') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="nacionalidade" class="block mb-1 font-medium">Nacionalidade</label>
            <input id="nacionalidade" name="nacionalidade" type="text" maxlength="100" required value="{{ old('nacionalidade') }}" class="w-full rounded border px-3 py-2">
            @error('nacionalidade') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Salvar</button>
            <a href="{{ route('autores.index') }}" class="text-indigo-600">Voltar à listagem</a>
        </div>
    </form>
@endsection
