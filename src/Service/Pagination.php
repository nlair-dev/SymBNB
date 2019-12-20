<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Classe de pagination qui extrait toute notion de calcul et de récupération de données de nos controllers
 * Elle nécessite après instanciation qu'on lui passe l'entité sur laquelle on souhaite paginer
 * @package App\Service
 */
class Pagination
{
    /**
     * Le nom de l'entité sur laquelle on veut effectuer une pagination
     * @var string
     */
    private $entityClass;

    /**
     * Le nombre d'enregistrement a récupérer
     * @var int
     */
    private $limit = 10;

    /**
     * La page sur laquelle on se trouve actuellement
     * @var int
     */
    private $currentPage = 1;

    /**
     * Le manager de doctrice qui nous permet notament de trouver le repository dont on a besoin
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * Le moteur de template twig qui va permettre de générer le rendu de la pagination
     * @var Twig\Environment
     */
    private $twig;

    /**
     * Le nom de la route que l'on veut utiliser pour les boutons de navigation
     * @var string
     */
    private $route;

    /**
     * Le chemin vers le template qui contient la pagination
     * @var string
     */
    private $templatePath;

    /**
     * Constructeur du service de pagination qui sera appelé par Symfony
     *
     * Ne pas oublier de configurer le fichier services.yml afin que Symfony sache quelle valeur utiliser pour $templatePath
     * @param EntityManagerInterface $manager
     * @param Environment $twig
     * @param RequestStack $request
     * @param $templatePath
     */
    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, $templatePath)
    {
        // On récupère le nom de la route à utiliser à partir des attributs de la requête actuelle
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->manager = $manager;
        $this->twig = $twig;
        $this->templatePath = $templatePath;
    }

    /**
     * Permet d'afficher le rendu de la navigation au sein d'un template twig
     *
     * On se sert ici de notre moteur de rendu afin de compiler le template qui se trouve au chemin
     * de notre propriété $templatePath, en lui passant les variables :
     * - page => La page actuelle sur laquelle on se trouve
     * - pages => le nombre total de pages qui existent
     * - route => le nom de la route à utiliser pour les liens de navigation
     *
     * Attention : cette fonction ne retourne rien, elle affiche directement le rendu
     *
     * @return void
     * @throws RuntimeError
     * @throws SyntaxError*@throws \Exception
     * @throws LoaderError
     */
    public function display(): void
    {
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route,
        ]);
    }

    /**
     * Permet de récupérer le nombre de pages qui existent sur une entité particulière
     *
     * Elle se sert de Doctrine pour récupérer le repository qui correspond à l'entité que l'on souhaite
     * paginer (voir la propriété $entityClass) puis elle trouve le nombre total d'enregistrements grâce
     * à la fonction findAll() du repository
     *
     * @return int
     * @throws Exception si la proprité $entityClass n'est pas configurée
     */
    public function getPages(): int
    {
        if (empty($this->entityClass)) {
            throw new Exception("Vous n'avez pas spécifier l'enité sur laquelle nous devons paginer !");
        }

        $total = count($this->manager->getRepository($this->entityClass)->findAll());

        return ceil($total / $this->limit);
    }

    /**
     * Permet de récupérer les données paginées pour une entité spécifique
     *
     * Elle se sert de Doctrine afin de récupérer le repository pour l'entité spécifiée
     * puis grâce au repository et à sa fonction findBy on récupère les données dans une
     * certaine limite et en partant d'un offset
     * @return array
     * @throws Exception
     */
    public function getData(): array
    {
        if (empty($this->entityClass)) {
            throw new Exception("Vous n'avez pas spécifier l'enité sur laquelle nous devons paginer !");
        }

        $offset = $this->currentPage * $this->limit - $this->limit;
        $repository = $this->manager->getRepository($this->entityClass);

        return $repository->findBy([], [], $this->limit, $offset);
    }


    /**
     * @param string $route
     * @return $this
     */
    public function setRoute(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @param string $templatePath
     * @return $this
     */
    public function setTemplatePath(string $templatePath): self
    {
        $this->templatePath = $templatePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    /**
     * @param int $currentPage
     * @return $this
     */
    public function setCurrentPage(int $currentPage): self
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param string $entityClass
     * @return $this
     */
    public function setEntityClass(string $entityClass): self
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}