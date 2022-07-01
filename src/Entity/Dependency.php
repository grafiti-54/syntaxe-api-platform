<?php 

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

//nouvelles recources extérieur en entrée de l'api
#[ApiResource(
    itemOperations: [
        'get',
        'delete', 
        'put' =>[
            'denormalization_context' => [
                'groups' => ['put:Dependency']
            ]
        ]
    ],
    collectionOperations: ['get', 'post'],
    paginationEnabled: false,
)]
class Dependency
{
    //No identifier defined in "App\Entity\Dependency". You should add #
    #[ApiProperty(
        identifier:true,
    )]
    private string $uuid;

    #[ApiProperty(
        description:'Nom de la dépendance',
    ),
    Length(min: 2),
    NotBlank()
    ]
    private string $name;

    #[ApiProperty(
        description:'Version de la dépendance',
        openapiContext: [
            'exemple' => '5.2.*',
        ]
        ), Length(min: 2),
           NotBlank(),
           Groups(['put:Dependency'])
    ]
    private string $version;




    public function __construct(
        //string $uuid, pas besoin de definir l'id lors de la creation d'un nouvelle dependance ( gestion automatique dans DataProvider.php)
        string $name,
        string $version,
    ) {
        $this->uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString();
        $this->name = $name;
        $this->version = $version;
    }
    

    /**
     * Get the value of uuid
     */ 
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of version
     */ 
    public function getVersion()
    {
        return $this->version;
    }

    //En cas de modification (put) rajouter les setters pour les champs à modifier

    /**
     * Set the value of version
     *
     * @return  self
     */ 
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }
}