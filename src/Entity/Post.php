<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\PostRepository; // api platform ou voir et configurer dans config\packages\api_platform.yaml
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass:PostRepository::class)]
#[ApiResource(
    // attributes: [
    //     'validation_groups' => [...saisir les groupes]
    // ],
    normalizationContext:['groups' => ['read:collection']],
    // seul les champs indiqué dans ['put:Post'] pourront etre modifié
    denormalizationContext:['groups' => ['write:Post']],
    
    // dans les operations de collections on pourra seulement récupérer des informations
    collectionOperations: [
        'get',
        'post' //=> [
            //Contraintes de validations lors de la creation
         //'validation_groups' => ['create:Post'] // 1ere facon de faire 
         //  'validation_groups' => [Post::class, 'validationGroups'] // function static que l'on créer plus bas 2eme facon de faire
        //]
    ],
    //itemOperations sur un seul élément au contraire de collectionOperations
    itemOperations:[
        'put',
        'delete',
        'get' => [
            'normalization_context' => ['groups' => ['read:collection', 'read:item', 'read:Post']],
        ],
    ]
)]

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
}
