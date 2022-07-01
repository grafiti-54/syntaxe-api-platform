<?php

namespace App\DataProvider;


use Ramsey\Uuid\Uuid;
use App\Entity\Dependency;
use App\Repository\DependencyRepository;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;

class DependencyDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface, ItemDataProviderInterface
{

    
    public function __construct(private DependencyRepository $repository ){

    } 


    // LOGIQUE REPETE DANS LE PROVIDER ET LE PERSISTER DEPLACER DANS LE FICHIER DEPENDENCYREPOSITORY.PHP

    // private function getDependencies (): array {

    //     $path = $this->rootPath . '/composer.json';
    //     $json = json_decode(file_get_contents($path),true); //json_decode transforme le json en array 1ere parametre le json 2nd parametre true pour transformer en array
    //     return $json['require'];
    // }

    
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Dependency::class;
    }

    //Methode pour recuperer un item grace a son id et ItemDataProviderInterface
    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = [])
    {
        return $this->repository->find($id);

        // LOGIQUE REPETE DANS LE PROVIDER ET LE PERSISTER DEPLACER DANS LE FICHIER DEPENDENCYREPOSITORY.PHP
        // $dependencies = $this->getDependencies();
        // foreach($dependencies as $name => $version) {
        //     $uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString();
        //     if($uuid === $id) {
        //         return new Dependency($name, $version);
        //     }
        // }
        // return null;
    }

    // LOGIQUE REPETE DANS LE PROVIDER ET LE PERSISTER DEPLACER DANS LE FICHIER DEPENDENCYREPOSITORY.PHP

    // public function getCollection(string $resourceClass, ?string $operationName = null, array $context = [])
    // {
    //     // voir configuration dans le fichier config\services.yaml ligne 30-31
    //     //dd($resourceClass, $operationName, $context);
        
    //     $items = [];
    //     foreach($this->getDependencies() as $name => $version ) {
    //         $items[] = new Dependency($name, $version);
    //     }
    //     //dd($json);
    //     return $items;
    // }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        return $this->repository->findAll();
    }

    //Ne pas oublier de modifier le fichier config\services.yaml ligne 30 pour indiquer que l'on utilise le repository de DependencyRepository
}