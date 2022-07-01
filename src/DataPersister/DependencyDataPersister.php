<?php

namespace App\DataPersister;

use App\Entity\Dependency;
use App\Repository\DependencyRepository;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

//Classe pour persister les données de Dependency de l'api dans la base de données
class DependencyDataPersister implements ContextAwareDataPersisterInterface
{

    public function __construct(private DependencyRepository $repository)
    {
        // $this->dependencyRepository = $dependencyRepository;
    }
    

    // verifie si la data depend de l'entité dependency
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Dependency;
    }

    public function persist($data, array $context = [])
    {
        $this->repository->persist($data);
    }

    // supprime la data de l'entité dependency
    public function remove($data, array $context = [])
    {
        $this->repository->remove($data);
    }
}