<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/users/{page}", name="admin_users_index", requirements={"page" = "\d+"})
     * @param Pagination $pagination
     * @param int $page
     * @return Response
     */
    public function index(Pagination $pagination, int $page = 1) : Response
    {
        $pagination
            ->setEntityClass(User::class)
            ->setCurrentPage($page);

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/users/{id}/delete", name="admin_users_delete")
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(User $user, EntityManagerInterface $manager) : Response
    {
        if (count($user->getBookings()) === 0) {
            $manager->remove($user);
            $manager->flush();
            $this->addFlash('success', 'L\'utilisateur a bien été supprimé');
        } else {
            $this->addFlash('warning', 'Vous ne pouvez supprimer un utilisateur avec des réservations');
        }

        return $this->redirectToRoute('admin_users_index');
    }
}
