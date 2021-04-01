<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\CategoryAttribute;
use App\Entity\Ressource;
use App\Form\CategoryAttributeType;
use App\Form\CategoryType;
use App\Form\RessourceType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class AdminCategoryController extends AbstractController
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
     * @Route("admin/category", name="admin.category")
     */
    public function index(CategoryRepository $repository): Response
    {
        $categories = $repository->findAll();

        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories
        ]);
    }


    /**
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     * @Route("admin/category/new", name="admin.category.new")
     */
    public function new(Request $request):Response
    {

        $form_cat = $this->createForm(CategoryType::class);
        $form_cat->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if($form_cat->isSubmitted() && $form_cat->isValid()){
            $category = new Category();
            $category->setName($form_cat->getData()->getName());
            $category->setMother($form_cat->getData()->getMother());

            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Catégorie bien ajoutée, ajoutez-y des attributs !' );
            return $this->redirectToRoute('admin.category.new.attribute',[
                'id' => $category->getId(),
            ], 301);
        }

//        if (isset($_POST['add_attribute_button'])) {
//            if ($category != null && $form_cat_attr != null) {
//                $category_attribute = new CategoryAttribute();
//                $category_attribute->setName($form_cat_attr->getData()->getName());
//                $category_attribute->setUnity($form_cat_attr->getData()->getUnity());
//                $category_attribute->addCategory($category);
//
//                $em->persist($category_attribute);
//                $em->flush();
//            }
//        }
//        } else if (isset($_POST['add_attribute_button'])) {
////                $category = $form_cat->getData();
////                $em = $this->getDoctrine()->getManager();
////
////                $em->persist($category);
////                $em->flush();
////
////                return $this->redirectToRoute('category');
////
//        } else {
//            //no button pressed
//        }

        return $this->render('admin/category/new.html.twig',[
            'form_cat'=>$form_cat->createView(),
        ]);

    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     * @Route("admin/category/new/attribute/{id}", name="admin.category.new.attribute")
     */
    public function newAttribute(int $id, Request $request):Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $category = $entityManager->getRepository(Category::class)->find($id);
        $attributes = $category->getCategoryAttributes();

        // Formulaire ajout attribut à cette catégorie

        $form_cat_attr = $this->createForm(CategoryAttributeType::class);
        $form_cat_attr->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if($form_cat_attr->isSubmitted() && $form_cat_attr->isValid()) {
            $category_attribute = new CategoryAttribute();
            $category_attribute->setName($form_cat_attr->getData()->getName());
            $category_attribute->setUnity($form_cat_attr->getData()->getUnity());
            $category_attribute->addCategory($category);

            $em->persist($category_attribute);
            $em->flush();
        }

        if (!$category) {
            throw $this->createNotFoundException(
                'Pas de catégorie trouvée pour id '.$id
            );
        }
        return $this->render('admin/category/newAttribute.html.twig',[
            'category' => $category,
            'attributes' => $attributes,
            'form_cat_attr'=>$form_cat_attr->createView(),
        ]);
    }

    /**
     * @Route ("/admin/category/{id}/edit", name="admin.category.edit", methods="POST|GET")
     * @IsGranted("ROLE_ADMIN")
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Category $category, Request $request) // injection pour récuperer la category qui nous interesse
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

//        $category = $form->getData()->getCategory();
//        $ressource_attribute = $form->getData()->getRessourceAttribute();


//        if($category){
////            dump($category->getCategoryAttributes());
//
//            // Pour chaque attribut de la catégorie on va creer une ressource-attr et lier la valeur de l'input avec l'id categ attr correspondant
//            foreach($ressource_attribute as $cle => $valeur){
////                $value_attribute = $form->get('ressource_attribute'.$cle)->getData();
//
//                // Condition pour ne pas executer cette commande sans que les valeurs desattributs ne soient submit
//                if ($ressource_attribute){
//
////                        $ressource_attribute->setCategoryAttribute($valeur);
////                        $ressource_attribute->setValue($value_attribute);
////
////                        // Par contre on lie la ressource et la ressource attr en l'ajoutant à l'objet Ressource
////                        $ressource->addRessourceAttribute($ressource_attribute);
//
////                    $em->persist($ressource_attribute);
////                    $em->flush();
//                }
//            }
//        }

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            dump($category);
//            $category = $ressource->getCategory();

            $em = $this->getDoctrine()->getManager();

            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('admin.category');
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route ("/admin/category/{id}/delete", name="admin.category.delete")
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Category $category):Response
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('admin.category');

    }


}
