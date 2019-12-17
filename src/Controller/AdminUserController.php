<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/users/{page}", name="admin_users_index", requirements={"page" = "\d+"})
     * @param UserRepository $repository
     * @param int $page
     * @return Response
     */
    public function index(UserRepository $repository, int $page = 1) : Response
    {
        $limit = 10;
        $start = $page * $limit - $limit;
        $total = count($repository->findAll());
        $pages = ceil($total / $limit);

        return $this->render('admin/user/index.html.twig', [
            'users' => $repository->findBy([], [], $limit, $start),
            'pages' => $pages,
            'page' => $page
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
