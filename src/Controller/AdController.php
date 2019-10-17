<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    private $repository;

    public function __construct(AdRepository $repository)
    {
        $this->repository = $repository;    
    }

    /**
     * @Route("/ads", name="ads_index")
     */
    public function index()
    {
        $ads = $this->repository->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    /**
     * Permet de créer une annonce
     * @Route("/ads/new", name="ads_create")
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function create(Request $request, ObjectManager $manager)
    {
        $ad = new Ad();
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);
            $manager->flush();
        }

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Allow show one ad
     * @Route("/ads/{slug}", name="ads_show")
     * @param Ad $ad
     * @return Response
     */
    public function show(Ad $ad)
    {
        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
        ]);
    }


}
