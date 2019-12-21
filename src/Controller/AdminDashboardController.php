<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function index(EntityManagerInterface $manager): Response
    {
        $users = $manager->createQuery('SELECT Count(u) from App\Entity\User u')->getSingleScalarResult();
        $ads = $manager->createQuery('SELECT Count(a) from App\Entity\Ad a')->getSingleScalarResult();
        $bookings = $manager->createQuery('SELECT Count(b) from App\Entity\Booking b')->getSingleScalarResult();
        $comments = $manager->createQuery('SELECT Count(c) from App\Entity\Comment c')->getSingleScalarResult();

        $bestAds = $manager
            ->createQuery(
                'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
                FROM App\Entity\Comment c 
                JOIN c.ad a
                JOIN a.author u
                GROUP BY a
                ORDER BY note DESC')
            ->setMaxResults(5)
            ->getResult();

        $worstAds = $manager
            ->createQuery(
                'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
                FROM App\Entity\Comment c 
                JOIN c.ad a
                JOIN a.author u
                GROUP BY a
                ORDER BY note ASC')
            ->setMaxResults(5)
            ->getResult();

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => compact('users', 'ads', 'bookings', 'comments'),
            'bestAds' => $bestAds,
            'worstAds' => $worstAds,
        ]);
    }
}
