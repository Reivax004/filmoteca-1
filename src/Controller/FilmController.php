<?php

declare(strict_types=1);

namespace App\Controller;
use Twig\Environment;
use App\Core\TemplateRenderer;
use App\Entity\Film;
use App\Repository\FilmRepository;
use App\Service\EntityMapper;
use Twig\Loader\FilesystemLoader;

class FilmController
{
    private TemplateRenderer $renderer;
    private EntityMapper $entityMapper;
    private Environment $twig;
    private FilmRepository $filmRepository;

    public function __construct()
{
    $this->renderer = new TemplateRenderer();
    $this->entityMapper = new EntityMapper();
    $this->filmRepository = new FilmRepository();

    
    $loader = new FilesystemLoader(__DIR__ . '/../views');
    $this->twig = new Environment($loader, ['cache' => false,]);
}

    public function list(array $queryParams)
    {
        $films = $this->filmRepository->findAll();

        /* $filmEntities = [];
        foreach ($films as $film) {
            $filmEntity = new Film();
            $filmEntity->setId($film['id']);
            $filmEntity->setTitle($film['title']);
            $filmEntity->setYear($film['year']);
            $filmEntity->setType($film['type']);
            $filmEntity->setSynopsis($film['synopsis']);
            $filmEntity->setDirector($film['director']);
            $filmEntity->setCreatedAt(new \DateTime($film['created_at']));
            $filmEntity->setUpdatedAt(new \DateTime($film['updated_at']));

            $filmEntities[] = $filmEntity;
        } */

        //dd($films);

        echo $this->renderer->render('film/list.html.twig', [
            'films' => $films,
        ]);

        // header('Content-Type: application/json');
        // echo json_encode($films);
    }

    public function create(): void
    {

        if (isset($_POST["ajout"])) {
            $film=$this->entityMapper->mapToEntity($_POST,Film::class);
            $this->filmRepository->create($film);

            header('Location: /film/list');
            exit();
        }

        echo $this->renderer->render('film/add.html.twig');
    }

    public function update(array $queryParams): void
    {
        $film = $this->filmRepository->find((int) $queryParams['id']);
        
        if (isset($_POST['modif'])) {
            
            $film = $this->entityMapper->mapToEntity($_POST, Film::class);
            
            $this->filmRepository->update((int) $queryParams['id'], $film);
            header('Location: /film/list');
            exit();
        }
        echo $this->renderer->render('film/update.html.twig', ['film' => $film]);
    }



    public function read(array $queryParams)
    {
        $film = $this->filmRepository->find((int) $queryParams['id']);
        echo $this->twig->render('film/details.html.twig', ['film' => $film,]);
        
    }

    public function delete(array $queryParams):void{

        $this->filmRepository->delete((int) $queryParams['id']);
        header('Location: /film/list');
        exit();
    }
}
