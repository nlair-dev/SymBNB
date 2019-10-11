<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * Home function
     * @Route("/", name="homepage")
     * @return Response
     */
    public function home(): Response
    {
        $prenoms = ["Lior" => 31, "Joseph" => 12, "Anne" => 55];
        
        return $this->render('home.html.twig', [
                'title' => "Bonjour à tous",
                'age' => 8,
                'tableau' => $prenoms
            ]
        );
    }

    /**
     * Show hello page with a name for parameter
     * @Route("/hello/{prenom}/age/{age}", name="hello")
     * @Route("/hello", name="hello_base")
     * @Route("/hello/{prenom}", name="hello_prenom")
     * @return Response
     */
    public function hello(string $prenom = 'anonyme', $age = 0): Response 
    {
        return $this->render('hello.html.twig', [
            'prenom' => $prenom,
            'age' => $age,
        ]);
    }
}