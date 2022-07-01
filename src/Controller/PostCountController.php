<?php 

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

//calcul le nombre de posts disponibles sur l'api
class PostCountController extends AbstractController // erreur si pas d'abstract controller
{
    public function __construct(private PostRepository $postRepository)
    {
        
    }

    public function __invoke(Request $request): int
    {
        //dd($request->get('online'));

        $onlineQuery  = $request->get('online');
        $conditions = [];
        
        if ($onlineQuery !== null) {
            $conditions = ['online' => $onlineQuery === '1' ? true : false];
        }
        return $this->postRepository->count($conditions);
    }
}