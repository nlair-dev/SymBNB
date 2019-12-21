<?php

namespace App\Controller;

use App\Service\Stats;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     * @param Stats $stats
     * @return Response
     * @throws NonUniqueResultException
     */
    public function index(Stats $stats): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats->getStats(),
            'bestAds' => $stats->getAdsStats('DESC'),
            'worstAds' => $stats->getAdsStats('ASC'),
        ]);
    }
}
