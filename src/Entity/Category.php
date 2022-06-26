<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;

#[ORM\Entity(repositoryClass:CategoryRepository::class)]
#[ApiResource]
class Category
{
    #[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column(type:'integer')]
#[Groups(['read:Post'])] // #[Groups(['read:Post'])] parametre utilisé dans Post.php pour l'affichage des données lors d'une relation
private $id;

#[ORM\Column(type:'string', length:255)]
#[
    Groups(['read:Post', 'write:Post']),
    Length(min: 3) 
]
private $name;

#[ORM\OneToMany(mappedBy:'category', targetEntity:Post::class)]
private $posts;

public function __construct()
    {
    $this->posts = new ArrayCollection();
}

function getId(): ?int
    {
    return $this->id;
}

function getName(): ?string
    {
    return $this->name;
}

function setName(string $name): self
    {
    $this->name = $name;

    return $this;
}

/**
 * @return Collection<int, Post>
 */
function getPosts(): Collection
    {
    return $this->posts;
}

function addPost(Post $post): self
    {
    if (!$this->posts->contains($post)) {
        $this->posts[] = $post;
        $post->setCategory($this);
    }

    return $this;
}

function removePost(Post $post): self
    {
    if ($this->posts->removeElement($post)) {
        // set the owning side to null (unless already changed)
        if ($post->getCategory() === $this) {
            $post->setCategory(null);
        }
    }

    return $this;
}
}
