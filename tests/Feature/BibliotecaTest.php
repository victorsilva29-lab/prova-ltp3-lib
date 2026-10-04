<?php

namespace Tests\Feature;

use App\Models\Autor;
use App\Models\Livro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BibliotecaTest extends TestCase
{
    use RefreshDatabase;

    private function autor()
    {
        return Autor::create(['nome' => 'Machado de Assis', 'nacionalidade' => 'Brasileira']);
    }

    private function dadosLivro($autor, $isbn = '9788535914849')
    {
        return ['titulo' => 'Dom Casmurro', 'ano_publicacao' => 1899, 'isbn' => $isbn, 'autor_id' => $autor->id];
    }

    public function test_crud_de_autores()
    {
        $this->get('/autores')->assertOk()->assertSee('Nenhum autor cadastrado.');
        $this->get('/autores/create')->assertOk();
        $this->post('/autores', ['nome' => 'Machado', 'nacionalidade' => 'Brasileira'])
            ->assertRedirect('/autores')->assertSessionHas('success');
        $autor = Autor::firstOrFail();
        $this->get("/autores/{$autor->id}/edit")->assertOk()->assertSee('Machado');
        $this->put("/autores/{$autor->id}", ['nome' => 'Machado de Assis', 'nacionalidade' => 'Brasileira'])
            ->assertRedirect('/autores');
        $this->assertDatabaseHas('autores', ['id' => $autor->id, 'nome' => 'Machado de Assis']);
        $this->delete("/autores/{$autor->id}")->assertRedirect('/autores')->assertSessionHas('success');
        $this->assertDatabaseMissing('autores', ['id' => $autor->id]);
    }

    public function test_crud_de_livros_e_relacionamentos()
    {
        $autor = $this->autor();
        $outro = Autor::create(['nome' => 'Outro autor', 'nacionalidade' => 'Brasileira']);
        $this->get('/livros')->assertOk()->assertSee('Nenhum livro cadastrado.');
        $this->get('/livros/create')->assertOk()->assertSee($autor->nome);
        $this->post('/livros', $this->dadosLivro($autor))->assertRedirect('/livros')->assertSessionHas('success');
        $livro = Livro::firstOrFail();
        $this->assertTrue($livro->autor->is($autor));
        $this->assertTrue($autor->livros->first()->is($livro));
        $this->get('/livros')->assertOk()->assertSee('Dom Casmurro')->assertSee($autor->nome);
        $this->get("/livros/{$livro->id}/edit")->assertOk()->assertSee('Dom Casmurro');
        $this->put("/livros/{$livro->id}", array_merge($this->dadosLivro($outro), ['titulo' => 'Título revisado']))
            ->assertRedirect('/livros')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('livros', ['id' => $livro->id, 'titulo' => 'Título revisado', 'autor_id' => $outro->id]);
        $this->delete("/livros/{$livro->id}")->assertRedirect('/livros')->assertSessionHas('success');
        $this->assertDatabaseMissing('livros', ['id' => $livro->id]);
    }

    public function test_validacao_de_autor_em_cadastro_e_edicao()
    {
        $this->from('/autores/create')->post('/autores', [])->assertRedirect('/autores/create')
            ->assertSessionHasErrors(['nome', 'nacionalidade']);
        $autor = $this->autor();
        $this->put("/autores/{$autor->id}", ['nome' => str_repeat('a', 256), 'nacionalidade' => str_repeat('b', 101)])
            ->assertSessionHasErrors(['nome', 'nacionalidade']);
        $this->assertSame('Machado de Assis', $autor->fresh()->nome);
    }

    public function test_validacao_de_livro_em_cadastro_e_edicao()
    {
        $autor = $this->autor();
        $livro = Livro::create($this->dadosLivro($autor));
        $this->post('/livros', [])->assertSessionHasErrors(['titulo', 'ano_publicacao', 'isbn', 'autor_id']);
        $this->post('/livros', $this->dadosLivro($autor))->assertSessionHasErrors('isbn');
        $invalido = ['titulo' => 'Título preservado', 'ano_publicacao' => 123, 'isbn' => str_repeat('a', 21), 'autor_id' => 9999];
        $this->from('/livros/create')->post('/livros', $invalido)
            ->assertRedirect('/livros/create')->assertSessionHasErrors(['ano_publicacao', 'isbn', 'autor_id'])
            ->assertSessionHasInput('titulo', 'Título preservado');
        $this->get('/livros/create')->assertSee('Título preservado');
        $outro = Livro::create($this->dadosLivro($autor, 'outro-isbn'));
        $this->put("/livros/{$outro->id}", $this->dadosLivro($autor))->assertSessionHasErrors('isbn');
        $this->put("/livros/{$livro->id}", $invalido)->assertSessionHasErrors(['ano_publicacao', 'isbn', 'autor_id']);
        $this->assertSame('Dom Casmurro', $livro->fresh()->titulo);
    }

    public function test_autor_com_livros_nao_pode_ser_excluido()
    {
        $autor = $this->autor();
        $livro = Livro::create($this->dadosLivro($autor));
        $this->delete("/autores/{$autor->id}")->assertRedirect('/autores')->assertSessionHas('error');
        $this->assertDatabaseHas('autores', ['id' => $autor->id]);
        $this->assertDatabaseHas('livros', ['id' => $livro->id]);
    }

    public function test_binding_retorna_404_para_registros_inexistentes()
    {
        foreach (['autores', 'livros'] as $recurso) {
            $this->get("/{$recurso}/9999/edit")->assertNotFound();
            $this->put("/{$recurso}/9999", [])->assertNotFound();
            $this->delete("/{$recurso}/9999")->assertNotFound();
        }
    }
}
