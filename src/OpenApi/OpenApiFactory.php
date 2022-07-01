<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;
use ArrayObject;

class OpenApiFactory implements OpenApiFactoryInterface
{

    public function __construct(private OpenApiFactoryInterface $decorated)
    {
        
    }

    public function __invoke(array $context = []): OpenApi
    {
        
        $openApi = $this->decorated->__invoke($context);
        foreach($openApi->getPaths()->getPaths() as $key => $path){
            //dd($path->getGet()->getSummary());
            if($path->getGet() && $path->getGet()->getSummary() === 'hidden'){
                $openApi->getPaths()->addPath($key, $path->withGet(null));
                //dd($path->whitGet(null));
            }
        }
        //voir config\services.yaml pour configurer l'api
        //dd($openApi);
        //exemple pour créer une nouvelle opération dans l'api
        //$openApi->getPaths()->addPath('/ping', new PathItem(null, 'ping', null, new Operation('pind_id', [], [], 'Réponse')));
        
        //gestion du cookie de connexion a l'api
        $schemas = $openApi->getComponents()->getSecuritySchemes();
        $schemas['cookieAuth'] = new ArrayObject([
            'type' => 'apiKey',
            'in' => 'cookie',
            'name' => 'PHPSESSID',
        ]);

        //definir les routes privées pour lesquelles on veut utiliser le cookie de connexion
        //Le tableau vide [] signifie que toutes les routes sont accessible pour utilisateurs qui sont connectés
        //$openApi = $openApi->withSecurity(['cookieAuth' => []]);

        $schemas = $openApi->getComponents()->getSchemas();
        $schemas['Credentials'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'john@doe.fr',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => '0000',
                ]
            ]
        ]);

        //permet d'effacer la demande de saisir un id sur apiplatform lors de la récuperation des données d'un utilisateur
        $meOperation = $openApi->getPaths()->getPath('/api/me')->getGet()->withParameters([]);
        $mePathItem = $openApi->getPaths()->getPath('/api/me')->withGet($meOperation);
        $openApi->getPaths()->addPath('/api/me', $mePathItem);

        //Ajout de la route pour se connecter à l'api
        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'postApiLogin',
                tags: ['Auth'],
                requestBody: new RequestBody(
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials'
                            ]
                        ]
                    ])
                ),
                responses: [
                    '200' => [
                        'description' => 'Utilisateur connecté',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/User-read.User'
                                ]
                            ]
                        ]
                    ]
                ]
            )
        );
        $openApi->getPaths()->addPath('/api/login', $pathItem);

        //Ajout de la route pour se déconnecter de l'api
        
        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'postApiLogout',
                tags: ['Auth'],
                responses: [
                    '204' => []
                ]
            )
        );
        $openApi->getPaths()->addPath('/logout', $pathItem);

        return $openApi;
    }
}

