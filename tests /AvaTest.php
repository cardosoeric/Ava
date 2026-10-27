<?php
// tests/ProdutosTest.php
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
 
class AvaTest extends TestCase
{
    public function testPostCriaProduto(): void
    {
    require __DIR__ . '/../src/app.php'; // retorna $app sem chamar ->run()
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/ava')
            ->withHeader('Content-Type', 'application/json');
 
        $request->getBody()->write(json_encode(['nome' => 'Voyager 1']));
        $request->getBody()->rewind();
        $response = $app->handle($request);
 
        $this->assertEquals(201, $response->getStatusCode());
 
        $corpo = json_decode((string) $response->getBody(), true);
 
        $this->assertEquals('Voyager 1', $corpo['nome']);
 
    }
    public function testPostCriaProdutoSemNomeRetornaErro(): void
    {
        require __DIR__ . '/../src/app.php'; // retorna $app sem chamar ->run()
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/ava')
            ->withHeader('Content-Type', 'application/json');
 
        $request->getBody()->write(json_encode([]));
        $request->getBody()->rewind();
        $response = $app->handle($request);
 
        $this->assertEquals(400, $response->getStatusCode());
 
        $corpo = json_decode((string) $response->getBody(), true);
 
        $this->assertEquals('O campo nome é obrigatório', $corpo['error']);
 
    }
}