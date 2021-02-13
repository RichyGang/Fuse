<?php

namespace App\Controller\Admin;

use App\Entity\Ressource;
use App\Form\RessourceType;
use App\Repository\RessourceRepository;
use Doctrine\Common\Annotations\Annotation\Required;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdminRessourcesController extends AbstractController {

    /**
     * @var RessourceRepository
     */
    private $repository;

    public function __construct(RessourceRepository $repository) //on injecte le repository concerné
    {
        $this->repository = $repository;
    }

    /**
     * @Route ("/admin/ressources", name ="admin.ressources")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index() //va permettre de récupérer l'ensemble des biens dont il va falloir le repository
    {
        $ressources = $this->repository->findAll();
        return $this->render('admin/ressources/index.html.twig', compact('ressources'));
    }

    /**
     * @Route ("/admin/{id}/edit", name="admin.ressources.edit", methods="POST|GET")
     * @IsGranted("ROLE_ADMIN")
     * @param Ressource $ressource
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Ressource $ressource, Request $request) // injection pour récuperer la ressource qui nous interesse
    {
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ressource = $form->getData();
            $em = $this->getDoctrine()->getManager();

            $em->persist($ressource);
            $em->flush();
            return $this->redirectToRoute('ressources.index');
        }

        return $this->render('admin/ressources/edit.html.twig', [
            'ressource' => $ressource,
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route ("/admin/{id}/delete", name="admin.ressources.delete")
     * @param Ressource $ressource
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Ressource $ressource):Response
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($ressource);
        $em->flush();

        return $this->redirectToRoute('admin.ressources');


    }

}