<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Controller\PostCountController;
use App\Controller\PostPublishController;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\PostRepository; // api platform ou voir et configurer dans config\packages\api_platform.yaml

#[ORM\Entity(repositoryClass:PostRepository::class)]
#[ApiResource(
    // attributes: [
    //     'validation_groups' => [...saisir les groupes]
    // ],
    normalizationContext:['groups' => ['read:collection'],
    'openapi_definition_name' => 'collection', // definir un  nom pour la definition de l'api
    ],
    // seul les champs indiqué dans ['put:Post'] pourront etre modifié
    denormalizationContext:['groups' => ['write:Post']],
    //pagination 
    paginationItemsPerPage: 2,
    //nombre maximum d'item par page lors de la pagination
    paginationMaximumItemsPerPage:2,
    paginationClientItemsPerPage: true,
    
    // dans les operations de collections on pourra seulement récupérer des informations
    collectionOperations: [
        'get',
        'count' => [
            'method' => 'GET',
            'path' => 'posts/count',
            'controller' => PostCountController::class,
            'read' => false,
            'pagination_enabled' => false, // supprimer la pagination
            'filters' => [], // retire les filtres
            'openapi_context' => [
                'summary' => 'Récupére le nombre total d\'articles',
                'parameters' => [
                    [
                        'in' => 'query',
                        'name' => 'online',
                        'schema' => [
                            'type' => 'integer',
                            'maximum' => 1,
                            'minimum' => 0,
                        ],
                        'description' => 'Filtre les articles en ligne',
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Ok',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'integer',
                                    'example' => 3,
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ]

    ],
    //     'post' //=> [
    //         //Contraintes de validations lors de la creation
    //      //'validation_groups' => ['create:Post'] // 1ere facon de faire 
    //      //  'validation_groups' => [Post::class, 'validationGroups'] // function static que l'on créer plus bas 2eme facon de faire
    //     //]
    // ],
    //itemOperations sur un seul élément au contraire de collectionOperations
    itemOperations:[
        'put',
        'delete',
        'get' => [
            'normalization_context' => [
                'groups' => ['read:collection', 'read:item', 'read:Post'],
                'openapi_definition_name' => 'Detail', // definir un  nom pour la definition de l'api
            ],
        ],
        'publish' => [
            'method' => 'POST',
            'path' => '/posts/{id}/publish',
            'controller' => PostPublishController::class,
            'openapi_context' => [
                'summary' => 'Permet de publier un article',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => []
                        ]
                    ]
                ]
            ]
            //'write' => false, // empeche l'ecriture dans la base de données
        ],
    ]
),

ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'title' => 'partial' ])
]

class Post
{
#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column(type:'integer')]
#[Groups(['read:collection'])]
private $id;

#[ORM\Column(type:'string', length:255)]
#[
    Groups(['read:collection' , 'write:Post']),
    Length(min: 5, groups: ['create:Post'])
]
private $title;

#[ORM\Column(type:'string', length:255)]
#[Groups(['read:collection', 'write:Post'])]
private $slug;

#[ORM\Column(type:'text')]
#[Groups(['read:item', 'write:Post'])]
private $content;

#[ORM\Column(type:'datetime')]
#[Groups(['read:item'])]
private $createdAt;

#[ORM\Column(type:'datetime')]
private $updatedAt;
//cascade:['persist'] permet l'jaout d'une nouvelle catégorie lors de l'ajout d'un article
#[ORM\ManyToOne(targetEntity:Category::class, inversedBy:'posts', cascade:['persist'])]
#[
    Groups(['read:item', 'write:Post']),
    Valid()

]
private $category;

#[ORM\Column(type: 'boolean', options: ['default' => "0"])]
#[
    Groups(['read:collection']),
    ApiProperty(openapiContext: ['type' => 'boolean', 'description' => 'En ligne ou pas ?'])
]
private $online = false;


public static function validationGroups(self $post){
    //dd($post);
    return ['create:Post'];
}



public function __construct()
{
    $this->createdAt = new \DateTime();
    $this->updatedAt = new \DateTime();
}

public function getId(): ?int
    {
    return $this->id;
}

function getTitle(): ?string
    {
    return $this->title;
}

function setTitle(string $title): self
    {
    $this->title = $title;

    return $this;
}

function getSlug(): ?string
    {
    return $this->slug;
}

function setSlug(string $slug): self
    {
    $this->slug = $slug;

    return $this;
}

function getContent(): ?string
    {
    return $this->content;
}

function setContent(string $content): self
    {
    $this->content = $content;

    return $this;
}

function getCreatedAt(): ?\DateTime
{
    return $this->createdAt;
}

function setCreatedAt(\DateTime$createdAt): self
    {
    $this->createdAt = $createdAt;

    return $this;
}

function getUpdatedAt(): ?\DateTime
{
    return $this->updatedAt;
}

function setUpdatedAt(\DateTime$updatedAt): self
    {
    $this->updatedAt = $updatedAt;

    return $this;
}

function getCategory(): ?Category
    {
    return $this->category;
}

function setCategory(?Category $category): self
    {
    $this->category = $category;

    return $this;
}

public function isOnline(): ?bool
{
    return $this->online;
}

public function setOnline(bool $online): self
{
    $this->online = $online;

    return $this;
}
}
