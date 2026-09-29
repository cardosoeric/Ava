<?php

require __DIR__ . '/../vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

/*
|--------------------------------------------------------------------------
| Configurações
|--------------------------------------------------------------------------
*/

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$app->add(function (Request $request, $handler) {

    $response = $handler->handle($request);

    return $response->withHeader(
        'Content-Type',
        'application/json; charset=utf-8'
    );
});

/*
|--------------------------------------------------------------------------
| MISSÕES INICIAIS
|--------------------------------------------------------------------------
*/

$missoes = [
    [
        'id' => 1,
        'nome' => 'Apollo 11',
        'ano' => 1969,
        'agencia' => 'NASA',
        'status' => 'Concluída'
    ],
    [
        'id' => 2,
        'nome' => 'Voyager 1',
        'ano' => 1977,
        'agencia' => 'NASA',
        'status' => 'Em operação'
    ],
    [
        'id' => 3,
        'nome' => 'Artemis II',
        'ano' => 2026,
        'agencia' => 'NASA',
        'status' => 'Planejada'
    ],
    [
        'id' => 4,
        'nome' => 'Mars 2020',
        'ano' => 2020,
        'agencia' => 'NASA',
        'status' => 'Concluída'
    ],
    [
        'id' => 5,
        'nome' => 'Juno',
        'ano' => 2011,
        'agencia' => 'NASA',
        'status' => 'Em operação'
    ]
];

/*
|--------------------------------------------------------------------------
| GET /status
|--------------------------------------------------------------------------
*/

$app->get('/status', function (
    Request $request,
    Response $response
) {

    $dados = [
        'status' => 'ok'
    ];

    $response->getBody()->write(
        json_encode(
            $dados,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        )
    );

    return $response->withStatus(200);
});

/*
|--------------------------------------------------------------------------
| GET /missoes
|--------------------------------------------------------------------------
*/

$app->get('/missoes', function (
    Request $request,
    Response $response
) use (&$missoes) {

    $response->getBody()->write(
        json_encode(
            $missoes,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        )
    );

    return $response->withStatus(200);
});

/*
|--------------------------------------------------------------------------
| GET /missoes/{id}
|--------------------------------------------------------------------------
*/

$app->get('/missoes/{id}', function (
    Request $request,
    Response $response,
    array $args
) use (&$missoes) {

    $id = (int) $args['id'];

    foreach ($missoes as $missao) {

        if ($missao['id'] === $id) {

            $response->getBody()->write(
                json_encode(
                    $missao,
                    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                )
            );

            return $response->withStatus(200);
        }
    }

    $erro = [
        'erro' => 'Missão não encontrada'
    ];

    $response->getBody()->write(
        json_encode(
            $erro,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        )
    );

    return $response->withStatus(404);
});

/*
|--------------------------------------------------------------------------
| POST /missoes
|--------------------------------------------------------------------------
*/

$app->post('/missoes', function (
    Request $request,
    Response $response
) use (&$missoes) {

    $dados = $request->getParsedBody();

    if (
        !isset($dados['nome']) ||
        !isset($dados['ano']) ||
        !isset($dados['agencia']) ||
        !isset($dados['status'])
    ) {

        $erro = [
            'erro' => 'Todos os campos são obrigatórios'
        ];

        $response->getBody()->write(
            json_encode(
                $erro,
                JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
            )
        );

        return $response->withStatus(400);
    }

    $novoId = count($missoes) > 0
        ? max(array_column($missoes, 'id')) + 1
        : 1;

    $novaMissao = [
        'id' => $novoId,
        'nome' => $dados['nome'],
        'ano' => (int) $dados['ano'],
        'agencia' => $dados['agencia'],
        'status' => $dados['status']
    ];

    $missoes[] = $novaMissao;

    $response->getBody()->write(
        json_encode(
            $novaMissao,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        )
    );

    return $response->withStatus(201);
});

/*
|--------------------------------------------------------------------------
| PUT /missoes/{id}
|--------------------------------------------------------------------------
*/

$app->put('/missoes/{id}', function (
    Request $request,
    Response $response,
    array $args
) use (&$missoes) {

    $id = (int) $args['id'];

    $dados = $request->getParsedBody();

    if (!$dados) {

        $erro = [
            'erro' => 'Dados não enviados'
        ];

        $response->getBody()->write(
            json_encode(
                $erro,
                JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
            )
        );

        return $response->withStatus(400);
    }

    foreach ($missoes as $indice => $missao) {

        if ($missao['id'] === $id) {

            if (
                !isset($dados['nome']) ||
                !isset($dados['ano']) ||
                !isset($dados['agencia']) ||
                !isset($dados['status'])
            ) {

                $erro = [
                    'erro' => 'Todos os campos são obrigatórios'
                ];

                $response->getBody()->write(
                    json_encode(
                        $erro,
                        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                    )
                );

                return $response->withStatus(400);
            }

            $missoes[$indice] = [
                'id' => $id,
                'nome' => $dados['nome'],
                'ano' => (int) $dados['ano'],
                'agencia' => $dados['agencia'],
                'status' => $dados['status']
            ];

            $response->getBody()->write(
                json_encode(
                    $missoes[$indice],
                    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                )
            );

            return $response->withStatus(200);
        }
    }

    $erro = [
        'erro' => 'Missão não encontrada'
    ];

    $response->getBody()->write(
        json_encode(
            $erro,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        )
    );

    return $response->withStatus(404);
});

/*
|--------------------------------------------------------------------------
| DELETE /missoes/{id}
|--------------------------------------------------------------------------
*/

$app->delete('/missoes/{id}', function (
    Request $request,
    Response $response,
    array $args
) use (&$missoes) {

    $id = (int) $args['id'];

    foreach ($missoes as $indice => $missao) {

        if ($missao['id'] === $id) {

            unset($missoes[$indice]);

            $missoes = array_values($missoes);

            return $response->withStatus(204);
        }
    }

    $erro = [
        'erro' => 'Missão não encontrada'
    ];

    $response->getBody()->write(
        json_encode(
            $erro,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        )
    );

    return $response->withStatus(404);
});

/*
|--------------------------------------------------------------------------
| INICIAR API
|--------------------------------------------------------------------------
*/

$app->run();