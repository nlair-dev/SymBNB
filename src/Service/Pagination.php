<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;

class Pagination
{
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getPages()
    {
        $total = count($this->manager->getRepository($this->entityClass)->findAll());

        return ceil($total / $this->limit);
    }

    public function getData()
    {
        $offset = $this->currentPage * $this->limit - $this->limit;
        $repository = $this->manager->getRepository($this->entityClass);

        return $repository->findBy([], [], $this->limit, $offset);
    }

    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }
}