<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class Stats
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return array
     * @throws NonUniqueResultException
     */
    public function getStats(): array
    {
        $users = $this->getUsersCount();
        $ads = $this->getAdsCount();
        $bookings = $this->getBookingsCount();
        $comments = $this->getCommentsCount();

        return compact('users', 'ads', 'bookings', 'comments');
    }

    /**
     * @return int
     * @throws NonUniqueResultException
     */
    private function getUsersCount(): int
    {
        return $this->manager
            ->createQuery('SELECT Count(u) from App\Entity\User u')
            ->getSingleScalarResult();
    }

    /**
     * @return int
     * @throws NonUniqueResultException
     */
    private function getAdsCount(): int
    {
        return $this->manager
            ->createQuery('SELECT Count(a) from App\Entity\Ad a')
            ->getSingleScalarResult();
    }

    /**
     * @return int
     * @throws NonUniqueResultException
     */
    private function getBookingsCount(): int
    {
        return $this->manager
            ->createQuery('SELECT Count(b) from App\Entity\Booking b')
            ->getSingleScalarResult();
    }

    /**
     * @return int
     * @throws NonUniqueResultException
     */
    private function getCommentsCount(): int
    {
        return $this->manager
            ->createQuery('SELECT Count(c) from App\Entity\Comment c')
            ->getSingleScalarResult();
    }

    public function getAdsStats($direction): array
    {
        return $this->manager
            ->createQuery(
                'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
                FROM App\Entity\Comment c 
                JOIN c.ad a
                JOIN a.author u
                GROUP BY a
                ORDER BY note ' . $direction)
            ->setMaxResults(5)
            ->getResult();
    }
}