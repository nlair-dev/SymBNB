<?php

namespace App\Controller;

use App\Repository\AdRepository;
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
     * Allow show one ad
     * @Route("/ad/{slug}", name="ads_show")
     * @param string $slug
     * @return Response
     */
    public function show(string $slug)
    {
        $ad = $this->repository->findOneBySlug($slug);

        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
        ]);
    }
}
