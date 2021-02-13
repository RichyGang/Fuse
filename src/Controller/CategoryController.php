<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Ressource;
use App\Form\CategoryType;
use App\Form\RessourceType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{

    /**
     * @var CategoryRepository
     */
    private $repository;

    public function __construct(CategoryRepository $repository)
    {

        $this->repository = $repository;
    }

    /**
     * @Route("/category", name="category")
     */
    public function index(CategoryRepository $repository): Response
    {

//        $category = $this->getDoctrine()->getRepository(Category::class)->findBy([
//            'mother' => null
//        ]);
        $category = $this->repository->findOneBy(['name'=>'Véhicule']);

        dump($category);

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/newcategory", name="category.new")
     */
    public function new(Request $request):Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $category = $form->getData();
            $em = $this->getDoctrine()->getManager();

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('category.new');
        }

        return $this->render('category/new.html.twig',[
            'form'=>$form->createView()
        ]);

    }
}
