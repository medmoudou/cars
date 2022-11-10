<?php

namespace App\Controller;

use App\Repository\CarRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeController extends AbstractController
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/', name: 'app_home')]
    public function index(CarRepository $carRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $q = $request->query->get('q');
        $c = $request->query->get('c');
        $query = $carRepository->findByQuery($q, $c);
        $cars = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            9 // nb of cars per page
        );

        $res = $this->client->request(
            'GET',
            'https://api.open-meteo.com/v1/forecast?latitude=48.8567&longitude=2.3510&hourly=temperature_2m' //Paris weather ^_^
        );

        $content = $res->toArray();
        $id = array_search(date("Y-m-d") . 'T' . date("H:00"), $content['hourly']['time']);
        $temperature = $content['hourly']['temperature_2m'][$id];

        return $this->render('home/index.html.twig', [
            'temperature' => $temperature,
            'cars' => $cars,
            'categories' =>  $categoryRepository->findAll(),
            'controller_name' => 'HomeController',
        ]);
    }
}
