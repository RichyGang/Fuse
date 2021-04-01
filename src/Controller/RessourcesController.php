<?php

namespace App\Controller;

use App\Entity\Ressource;
use App\Entity\RessourceAttribute;
use App\Entity\User;
use App\Form\RessourceAttributeType;
use App\Form\RessourceType;
use App\Repository\CategoryAttributeRepository;
use App\Repository\CategoryRepository;
use App\Repository\RessourceRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\Object_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RessourcesController extends AbstractController
{

    /**
     * @var RessourceRepository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $em;

    // 2eme méthode pour récuperer l'entité, l'injection:
    public function __construct(RessourceRepository $repository) // (pas oublier de l'initialiser) L'objectmanager va peremettre te traquer les changements et les efectuer
    {
        $this->repository= $repository; //crée une propriété de l'objet (en haut dans l'annotation) et l'initialise ici
//        $this->em = $em;
    }// on peut aussi faire l'injection de dépendance directement dans la méthode : dans les () de function index, c'est de l'autowiring


    /**
     * @Route("/ressources", name="ressources.index")
     */
    public function index(RessourceRepository $repository): Response
    {
        $ressources = $repository->findAll();

        // Insérer des champs dans la table (persister)
//        $ressource = new Ressource();
//        $ressource ->setName('véhicule')
//            ->setDescription('Système mécanique permettant le déplacement de personnes/marchandises. ');
//         $em = $this->getDoctrine()->getManager();
//        $em->persist($ressource);
//        $em->flush();


        // 1 ere méthode pour récupérer l'entité: (autre que l'injection)
        $repository = $this->getDoctrine()->getRepository(Ressource::class); // il sait automatiquement quel repository puisqu'il a été initialisé dans l'entité Ressource.php
//        dump($repository);

        // (fonctionnent avec la 2eme méthode (injection) puisqu'utilise le 'repository' créé)
        // Méthodes qui permettent de récuperer rapidement un enregistrement: (si la méthode pour récuperer un champ n'hexiste pas, on cree la methode dans le Repository de l'entité, ici : RessourceRepository.php)
  //      $ressource = $this->repository->find(1); //renvoit la premiere ressource avec la valeur de ses champs
        $ressource = $this->repository->findAll(); // renvoit un tableau avec toutes les ressources
  //      dump($ressource);
//        $ressource = $this->repository->findOneBy(['name' => 'moto']); // demande le tableau de tous ceux qui corresondent au critere
//        $ressource[0]->setDescription('nouvelle description');
//        $this->em->flush();

       // $ressource = $this->repository->findAllVisible();
     //   $ressource[0]->setDescription('changement'); // ne propose pas d'autocompletion donc faut typer le retour dans le repository comme tableau
      //  $em = $this->getDoctrine()->getManager();
      //  $this->em->flush();

        return $this->render('ressources/index.html.twig', [
            'current_menu' => 'Ressources',
            'ressources'=> $ressources // ce que j'envoie à la vue
        ]);
    }

    /**
     * @Route("/ressources/edit/{id}", name="ressource.edit")
     */
    public function update(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ressource =$entityManager->getRepository(Ressource::class)->find($id);

        if (!$ressource) {
            throw $this->createNotFoundException(
                'Pas de ressource trouvée pour id '.$id
            );
        }

        //Pour setter le parent de 'moto' id=1 qui est est 'vehicle' id=2
//        $repository = $this->getDoctrine()->getRepository(Ressource::class);
//        $ressource2 = $this->repository->find(2);
//        $ressource->setParent($ressource2);
//        $entityManager->flush();

        return $this->render('ressources/update.html.twig', [
            'current_menu' => 'Ressources',
        ]);

    }

    /**
     * @Route("/ressources/{slug}-{id}", requirements={"slug": "[a-z0-9\-]*"}, name="ressource.show")
     * @param Ressource $ressource
     * @return Response
     */
    public function show(Ressource $ressource, string $slug, RessourceRepository $repository, CategoryAttributeRepository  $attributeRepository): Response //le nom des variables doivent etre ceux des parametres de la route dans l'annotation | mais ici on a mis l'injection de la Ressource et donc ca fait le find tout seul
    {
//        $ressource=$this->repository->find($id); //je récupere la ressource avec le repository

        $ressources = $repository->findAll();
        if($ressource->getSlug() !== $slug)
        {
            return $this->redirectToRoute('ressource.show',[
                'id' => $ressource->getId(),
                'slug' => $ressource->getSlug()
            ], 301);
        }

        return $this->render('ressources/show.html.twig', [
            'ressource' => $ressource, //que je renvoies à ma vue
            'current_menu' => 'Ressources',
            'ressources' => $ressources,
            'category_attributes' => $attributeRepository->findAll()
        ]);
    }

    /**
     * @Route ("/ressources/new", name="ressources.new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request):Response
    {
        $category = null;
        $ressource = new Ressource();
        $form_ress = $this->createForm(RessourceType::class);
        $form_ress->handleRequest($request);

//        $form_attr = $this->createForm(RessourceAttributeType::class);

        if($form_ress->isSubmitted() && $form_ress->isValid()){

            $em = $this->getDoctrine()->getManager();

            // Récupération des retours du form qui sont mappés dans Ressource
            $ressource = $form_ress->getData();

            // Récupération de la category rentrée dans le form
            $category = $form_ress->get('category')->getData();

            if($ressource->getCategory()){

                // Pour chaque attribut de la catégorie on va creer une ressource-attr et lier la valeur de l'input avec l'id categ attr correspondant
                foreach($category->getCategoryAttributes() as $cle => $valeur){
                    $value_attribute = $form_ress->get('ressource_attribute'.$cle)->getData();

                    // Condition pour ne pas executer cette commande sans que les valeurs desattributs ne soient submit
                    if ($value_attribute){

                        $ressource_attribute = new RessourceAttribute();
                        $ressource_attribute->setCategoryAttribute($valeur);
                        $ressource_attribute->setValue($value_attribute);

                        // Par contre on lie la ressource et la ressource attr en l'ajoutant à l'objet Ressource
                        $ressource->addRessourceAttribute($ressource_attribute);

                        $em->persist($ressource_attribute);
                        $em->flush();
                    }
                }
            }

            if ($ressource->getDescription()){

                // AJOUT DE LA PHOTO DE LA RESSOURCE
                /** @var UploadedFile $brochureFile */
                $pictureRessource = $form_ress->get('ressourcePicture')->getData();

                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($pictureRessource) {
                    $originalFilename = pathinfo($pictureRessource->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $newFilename = $originalFilename.'-'.uniqid().'.'.$pictureRessource->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $pictureRessource->move(
                            $this->getParameter('picture_ressource_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        dump($e->getMessage());
                        // ... handle exception if something happens during file upload
                    }

                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents
                    $ressource->setRessourcePicture($newFilename);
                }

                // AJOUT DU USER QUI AJOUTE LA RESSOURCE, OU LA MODIFIE
                /** @var User $user */
                $user = $this->getUser();
                $user->addRessources($ressource);

                $em->persist($ressource);
                $em->flush();

                $this->addFlash('success', 'Ressource bien ajoutée!' );
                return $this->redirectToRoute('ressources.index');
            }
        }

        return $this->render('ressources/new.html.twig',[
            'form_ress'=>$form_ress->createView(),
            'category'=>$category
        ]);
    }

    /**
     * @Route ("/ressources/add", name="ressources.add")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request, CategoryRepository $categoryRepository, CategoryAttributeRepository $attr):Response
    {
        $categoryID = $request->query->get('categoryID');
        $category = $categoryRepository->findOneBy(['id' => $categoryID]);

        $categoryAttr = null;

        if ($categoryID !== null){
            $categoryAttr = $category->getCategoryAttributes();
        }

//        ------------------
//
//        $ressource = new Ressource();
//        $form->handleRequest($request);
//
////        ..... dans le if : && $form->isValid()
//        if($form->isSubmitted()){
//            $ressource = $form->getData();
//            $em = $this->getDoctrine()->getManager();
//
//            $em->persist($ressource);
//            $em->flush();
//            $this->addFlash('success', 'Ressource bien ajoutée!' );
//            return $this->redirectToRoute('ressources.index');
//        }

//        ------------------

        return $this->render('ressources/add.html.twig',[
            'category' => $categoryRepository->findBy([
                'mother' => null
            ]),
            'category_attr' => $categoryAttr,
            'category_ID' => $categoryID
        ]);

    }

}
