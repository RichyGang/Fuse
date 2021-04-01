<?php

namespace App\Controller;

use App\Repository\RessourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AcceuilController extends AbstractController
{
    /**
     * @Route("/", name="acceuil")
     * @param RessourceRepository $repository
     * @return Response
     */
    public function index(RessourceRepository $repository): Response
    {
        $ressources = $repository->findAll();



//        $ressources_id = $repository->find()->getParent()->getName();
//        dump($ressources);
//        dump($ressources_id);
//        $ressources_parent =;
//        $ressource_parents = $this->repository->findOneBy(['parent' => $ressources ])
//        dump($ressources_parent);
        return $this->render('acceuil/index.html.twig', [
            'ressources' => $ressources,
        ]);
    }

}
