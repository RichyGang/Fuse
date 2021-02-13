<?php

namespace App\Controller;

use App\Entity\Proposal;
use App\Entity\Ressource;
use App\Entity\User;
use App\Form\ProposalType;
use App\Form\RessourceType;
use App\Repository\ProposalRepository;
use App\Repository\RessourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     */
    public function index(ProposalRepository $repository): Response
    {

        /** @var User $user */
        $user = $this->getUser();

        if ($user) {
            return $this->render('proposals/index.html.twig', [
                'proposals' => $repository->findAllExcept($this->getUser())
            ]);
        }

        return $this->render('proposals/index.html.twig', [
            'proposals' => $repository->findAll()
        ]);



    }

    /**
     * @Route("/proposals/new", name="proposals.new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $proposal = new Proposal();
        $form = $this->createForm(ProposalType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump($form);
            $proposal = $form->getData();
            /** @var User $user */
            $user = $this->getUser();
            $user->addProposal($proposal);

            $this->em->persist($proposal);
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Proposition bien ajoutée!' );

            return $this->redirectToRoute('proposals');
        }


        return $this->render('proposals/new.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/proposals/add/{id}", name="proposal.add")
     * @param Proposal $proposal
     * @return Response
     */
    public function add(Proposal $proposal, Request $request): Response //le nom des variables doivent etre ceux des parametres de la route dans l'annotation | mais ici on a mis l'injection de la Ressource et donc ca fait le find tout seul
    {

        $form = $this->createForm(ProposalType::class, $proposal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $proposal = $form->getData();
            $em = $this->getDoctrine()->getManager();

            $em->persist($proposal);
            $em->flush();
            $this->addFlash('success', 'Proposition bien ajoutée!' );
            return $this->redirectToRoute('ressources.index');
        }


        return $this->render('proposals/add.html.twig', [
            'proposal' => $proposal,
            'form' => $form->createView()
        ]);



    }
}
