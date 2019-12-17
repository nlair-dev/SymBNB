<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings/{page}", name="admin_bookings_index", requirements={"page"="\d+"})
     * @param BookingRepository $repository
     * @param int $page
     * @return Response
     */
    public function index(BookingRepository $repository, int $page = 1) : Response
    {
        $limit = 10;
        $start = $page * $limit - $limit;
        $total = count($repository->findAll());
        $pages = ceil($total / $limit);

        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $repository->findBy([], [], $limit, $start),
            'pages' => $pages,
            'page' => $page
        ]);
    }

    /**
     * edit a booking
     * @Route("/admin/bookings/{id}/edit", name="admin_bookings_edit")
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Booking $booking, Request $request, EntityManagerInterface $manager) : Response
    {
        $form = $this->createForm(AdminBookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setAmount(0);
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash('success',
                "La réservation n°{$booking->getId()} a bien été modifiée");

            return $this->redirectToRoute('admin_bookings_index');
        }

        return $this->render('admin/booking/edit.html.twig', [
            'form' => $form->createView(),
            'booking' => $booking
        ]);
    }

    /**
     * @Route("/admin/bookings/{id}/delete", name="admin_bookings_delete")
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Booking $booking, EntityManagerInterface $manager) : Response
    {
        $manager->remove($booking);
        $manager->flush();
        $this->addFlash('success', "La réservation a bien été supprimée");

        return $this->redirectToRoute('admin_bookings_index');
    }
}
