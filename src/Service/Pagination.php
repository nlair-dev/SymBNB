<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Pagination
{
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;

    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request)
    {
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->manager = $manager;
        $this->twig = $twig;
    }

    public function display()
    {
        $this->twig->display('admin/partials/pagination.html.twig', [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route,
        ]);
    }

    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    public function getRoute()
    {
        return $this->route;
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