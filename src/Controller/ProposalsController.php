<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Proposal;
use App\Entity\Ressource;
use App\Entity\User;
use App\Form\ProposalType;
use App\Form\RessourceType;
use App\Repository\CategoryRepository;
use App\Repository\ProposalRepository;
use App\Repository\RessourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProposalsController extends AbstractController
{

    /**
     * @var ProposalRepository
     */
    private $proposalRepository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(ProposalRepository $proposalRepository, EntityManagerInterface $em)
    {
        $this->proposalRepository = $proposalRepository;
        $this->em = $em;
    }

    /**
     * @Route("/proposals", name="proposals")
     * @param Request $request
     * @return Response
     */
    public function index(ProposalRepository $proposalRepository, CategoryRepository $categoryRepository, Request $request): Response
    {

        /** @var User $user */
        $user = $this->getUser();

        if ($user) {

            // On recupere le nom de la categorie puis l'entité (surement un moyen de recup direct l'entité)
            $category_name = $request->query->get('category');
            $category = $categoryRepository->findOneBy(['name'=>$category_name]);

            $proposals = $proposalRepository->findAllExcept($this->getUser());

            return $this->render('proposals/index.html.twig', [
                'category' => $category,
                'proposals' => $proposals,
                'categories' => $categoryRepository->findAll()
            ]);
        }
        else{
        $category_name = $request->query->get('category');
        $category = $categoryRepository->findOneBy(['name'=>$category_name]);

        return $this->render('proposals/index.html.twig', [
            'category' => $category,
            'proposals' => $proposalRepository->findAll(),
            'categories' => $categoryRepository->findAll()
        ]);
        }

    }

//
//    /**
//     * @Route("/proposals/new", name="proposals.new")
//     * @param Request $request
//     * @return Response
//     */
//    public function new(Request $request, RessourceRepository $ressourceRepository): Response
//    {
//        $proposal = new Proposal();
//        $form = $this->createForm(ProposalType::class);
//
//        $form->handleRequest($request);
//
//        $category = $form->get('category')->getData();
//        $ressources = $ressourceRepository->findBy(['category'=>$category]);
//
//
////        if (isset($_POST['chose_ressource_button'])) {
////            dump('hello');
////            if ($_POST['ressource_chosen'] != null) {
////                dump('ca marche');
////            }
////        }
////            if
////            if ($category != null && $form_cat_attr != null) {
////                $category_attribute = new CategoryAttribute();
////                $category_attribute->setName($form_cat_attr->getData()->getName());
////                $category_attribute->setUnity($form_cat_attr->getData()->getUnity());
////                $category_attribute->addCategory($category);
////
////                $em->persist($category_attribute);
////                $em->flush();
////            }
//
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $proposal = $form->getData();
//            /** @var User $user */
//            $user = $this->getUser();
//            $user->addProposal($proposal);
//
//            $this->em->persist($proposal);
//            $this->em->persist($user);
//            $this->em->flush();
//
//            $this->addFlash('success', 'Proposition bien ajoutée!' );
//
//            return $this->redirectToRoute('proposals');
//        }
//
//
//        return $this->render('proposals/new.html.twig', [
//            'form' => $form->createView(),
//            'ressources' => $ressources,
//            'category' => $category,
//        ]);
//
//    }

    /**
     * @Route("/proposals/new", name="proposal.new")
     * @param Request $request
     * @return Response
     */
    public function new(CategoryRepository $categoryRepository, RessourceRepository $ressourceRepository, Request $request): Response //le nom des variables doivent etre ceux des parametres de la route dans l'annotation | mais ici on a mis l'injection de la Ressource et donc ca fait le find tout seul
    {
//        $form = $this->createForm(ProposalType::class, $proposal);
//        $form->handleRequest($request);
//
//        $category = $form->getData()->getCategory();
//        $ressource_attribute = $form->getData()->getRessourceAttribute();

        $user = $this->getUser();
        $proposal = new Proposal();
        $em = $this->getDoctrine()->getManager();

        // Pour proposer toutes les categories pour en choisir une
        $categories = $categoryRepository->findAll();

        // On recupere le nom de la categorie puis l'entité (surement un moyen de recup direct l'entité)
        $category_name = $request->query->get('category');
        $category = $categoryRepository->findOneBy(['name'=>$category_name]);

        $ressources = null;

        if ($category != null){
            $ressources = $ressourceRepository->findBy(['category'=>$category]);
        }

//        on récupère l'id de la ressource sélectionné
        $ressource_id = $request->query->get('ressource_id');
//        on récupère l'entité ressource en question
        $ressource = $ressourceRepository->findOneBy(['id'=>$ressource_id]);

//        $form = $this->createForm(ProposalType::class);
        $form = $this->createForm(ProposalType::class);
        $form->handleRequest($request);

        if ($ressource != null){

            // AJOUT DE LA PHOTO DE LA RESSOURCE
            /** @var UploadedFile $brochureFile */
            $proposalPicture = $form->get('proposalPicture')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($proposalPicture) {
                $originalFilename = pathinfo($proposalPicture->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = $originalFilename.'-'.uniqid().'.'.$proposalPicture->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $proposalPicture->move(
                        $this->getParameter('picture_proposal_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    dump($e->getMessage());
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $proposal->setRessourcePicture($newFilename);
            }

            $quantity = $form->get('quantity')->getData();
            $location = $form->get('location')->getData();
            $need_or_ask = $form->get('need_or_ask')->getData();

            if($quantity and $location){
                dump('ok');
                $user->addProposal($proposal);
                $proposal->setNeedOrAsk($need_or_ask);
                $proposal->setRessource($ressource);
                $proposal->setQuantity($quantity);
                $proposal->setLocation($location);

                $this->em->persist($proposal);
                $this->em->persist($user);

                $this->em->flush();

                $this->addFlash('success', 'Félicitation, votre proposition est ajoutée');

                return $this->redirectToRoute('proposals');
            }


//            if ($form->isSubmitted() && $form->isValid()) {
//
//                $proposal = $form->getData();
//
//
//                $this->em->persist($proposal);
//                $this->em->persist($user);
//                $this->em->flush();
//
//                $this->addFlash('success', 'Proposition bien ajoutée!' );
//
//                return $this->redirectToRoute('proposals');
//            }


        }

        return $this->render('proposals/new.html.twig', [
            'categories' => $categories,
            'ressources' => $ressources,
            'category' => $category,
            'ressource' => $ressource,
            'form'=> $form->createView()
        ]);
    }

    /**
     * @Route ("/proposal/{id}/add", name="proposal.add", methods="POST|GET")
     * @param Proposal $proposal
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function add(Proposal $proposal, Request $request):Response // injection pour récuperer la ressource qui nous interesse
    {
        $form = $this->createForm(ProposalType::class, $proposal);
        $form->handleRequest($request);

//        dump($form->getData());

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
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $ressource = $form->getData();
//            dump($ressource);
//            $category = $ressource->getCategory();
//
//            $em = $this->getDoctrine()->getManager();
//
//            $em->persist($ressource);
//            $em->flush();
//            return $this->redirectToRoute('ressources.index');
//        }

        return $this->render('proposals/add.html.twig', [
            'proposal' => $proposal,
            'form' => $form->createView()
        ]);
    }



//    /**
//     * @Route("/proposals/add/{id}", name="proposal.add")
//     * @param Proposal $proposal
//     * @return Response
//     */
//    public function add(Proposal $proposal, Request $request): Response //le nom des variables doivent etre ceux des parametres de la route dans l'annotation | mais ici on a mis l'injection de la Ressource et donc ca fait le find tout seul
//    {
//        $form = $this->createForm(ProposalType::class, $proposal);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $proposal = $form->getData();
//            $em = $this->getDoctrine()->getManager();
//
//            $em->persist($proposal);
//            $em->flush();
//            $this->addFlash('success', 'Proposition bien ajoutée!' );
//            return $this->redirectToRoute('ressources.index');
//        }
//
//
//        return $this->render('proposals/add.html.twig', [
//            'proposal' => $proposal,
//            'form' => $form->createView()
//        ]);
//
//    }


}
