<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Entity\Category;
use App\Form\ChambreType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(ManagerRegistry $doctrine): Response
    {
        //On fait appel à l'Entity Manager et au Repository de chambre pour récupérer la liste de tous nos chambres
        $entityManager = $doctrine->getManager();
        $chambreRepository = $entityManager->getRepository(Chambre::class);

        //On récupère la liste de nos chambres
        $chambres = $chambreRepository->findAll(); //On récupère tout

        //On transmet nos chambres à notre Twig
        return $this->render('index/index.html.twig', [
            'chambres' => $chambres,
        ]);
    }

    #[Route('category/{categoryName}', name: 'index_category')]
    public function indexCategory(string $categoryName, ManagerRegistry $doctrine): Response
    {
        // Cette methode nous rend les chambres dont la categorie correspond à la valeur entree dans notre barre d'adresse en tant que "categoryName"

        // Afin de pouvoir communiquer avec notre base de donnees, nous avons besoin de l'entity Manager ainsi que du Repository de chambre
        $entityManager = $doctrine->getManager();
        $chambreRepository = $entityManager->getRepository(Chambre::class);
        //Liste des Categories
        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();
        // Nous verifions si la categorie mentionnee dans notre barre d'adresse existe parmi nos chambres, si non, nous retournons à l'index
        // Nous recuperons les chambres dont la categorie correspond:
        $chambres = $chambreRepository->findBy(['category' => $categoryName], ['id' => 'DESC']);
        if (empty($chambres)) {
            return $this->redirectToRoute('app_index');
        }
        // Nous transmettons, s'il existent, les chambres reçus sur index.html.twig
        return $this->render('index/index.html.twig', [
            'chambres' => $chambres,
            'categories' => $categories,
        ]);
    }
    #[Route('/chambre/create', name: 'chambre_create')]
    public function createChambre(Request $request, ManagerRegistry $doctrine): Response
    {
        // Cette methode à pour objectif de creer un nouveau chambre dont les differentes informations sont passees de l'utilisateur à l'application par l'intermediaire d'un formulaire

        //On fait appel à l'Entity Manager et au Repository de chambre pour récupérer la liste de tous nos chambres
        $entityManager = $doctrine->getManager();
        $chambreRepository = $entityManager->getRepository(Chambre::class);

        //On creer une nouvelle chambre
        $chambre = new Chambre();

        $chambreForm = $this->createForm(ChambreType::class, $chambre);
        // 
        $chambreForm->handleRequest($request);
        // 
        if ($chambreForm->isSubmitted() && $chambreForm->isValid()) {

            $entityManager->persist($chambre);
            $entityManager->flush();
            // nous retournons à l'accueil
            return $this->redirectToRoute('app_index');
        }
        //nous transettons notre formulaire de chambre à Twig 
        return $this->render('index/dataForm.html.twig', [

            'formName' => 'création de chambre',
            'dataForm' => $chambreForm->createView(), // createView() prepare l'affichage du form
        ]);
    }

    #[Route('/chambre/update/{chambreId}', name: 'chambre_update')]
    public function updateChambre(int $chambreId, Request $request, ManagerRegistry $doctrine): Response
    {
        // cette methode nous permet de modifier grace à un formulaire le contenu d'une chambre identifié via son ID transmis via notre URL

        // Afin de recuperer la chambre désiré, nous avons besoin de l'Entity Manager et du Repository de la chambre
        $entityManager = $doctrine->getManager();
        $chambreRepository = $entityManager->getRepository(Chambre::class);

        $chambre = $chambreRepository->find($chambreId);
        // si la chambre n'existe pas, nous retournons à l'index
        if (!$chambre) {
            return $this->redirectToRoute('app_index');
        }

        $chambreForm = $this->createForm(ChambreType::class, $chambre);
        // Nous appliquons les valeurs de notre objet request à notre Chambre
        $chambreForm->handleRequest($request);
        // si notre formulaire est rempli et valide
        if ($chambreForm->isSubmitted() && $chambreForm->isValid()) {

            $entityManager->persist($chambre);
            $entityManager->flush();
            // 
            return $this->redirectToRoute('app_index');
        }

        // nous transettons notre formulaire de bulletin à twig
        return $this->render('index/dataForm.html.twig', [
            'formName' => 'création de la Chambre',
            'dataForm' => $chambreForm->createView(),
        ]);
    }

    #[Route('/chambre/delete/{chambreId}', name: 'chambre_delete')]
    public function deleteChambre(ManagerRegistry $doctrine, int $chambreId): Response
    {
        // 
        $entityManager = $doctrine->getManager();
        $chambreRepository = $entityManager->getRepository(Chambre::class);
        // 
        $chambre = $chambreRepository->find($chambreId);
        // 
        if (!$chambre) {
            return $this->redirectToRoute('app_index');
        }
        // 
        $entityManager->remove($chambre);
        $entityManager->flush();
        return $this->redirectToRoute('app_index', [
            'chambres' => $chambre
        ]);
    }

    #[Route('/chambre/display/{chambreId}', name: 'chambre_display')]
    public function displayChambre(int $chambreId, ManagerRegistry $doctrine): Response
    {
        //Cette methode affiche une chambre dont l'ID est spécifié dans la barre d'adresse

        // Afin de mener une recherche dans notre BDD, nous avons besoin de l'Entity Manager ainsi que du Repoitory de la chambre
        $entityManager = $doctrine->getManager();
        $chambreRepository = $entityManager->getRepository(Chambre::class);

        // Nous recherchons la chambre via son ID en utilisant la methode find() du Repository.
        $chambre = $chambreRepository->find($chambreId);
        // si la recherche ne mene à rien, $chambre vaut null, et nous retournons à l'index 
        if (!$chambre) {
            return $this->redirectToRoute('app_index');
        }
        // si nous avons notre Chambre, nous le transmetton à index.html.twig
        return $this->render('index/chambre_display.html.twig', [
            'chambre' => $chambre,
        ]);
    }

    #[Route('/template', name: 'index_template')]
    public function cheatsheet(): Response
    {
        return $this->render('index/template.html.twig', [
            'template_var' => true,
        ]);
    }
}
