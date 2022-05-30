<?php

namespace App\Controller;

use App\Entity\Tag;
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
        //On récupère la liste des Categories
        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();
        //On récupère la liste de nos chambres
        $chambres = $chambreRepository->findAll(); //On récupère tout
        $selectedCategory = ['name'=>'Annonce immobilier', 'description'=>''];
        //On transmet nos chambres à notre Twig
        return $this->render('index/index.html.twig', [
            'selectedCategory'=> $selectedCategory,
            'categories' => $categories,
            'chambres' => $chambres,

        ]);
    }

    #[Route('/category/{categoryName}', name: 'index_category')]
    public function indexCategory(string $categoryName, ManagerRegistry $doctrine): Response
    {
        //Cette méthode présente tous les Products liés à une Category dont le nom est indiqué dans l'URL

        //Nous récupérons l'Entity Manager ainsi que le Repository pertinent (categoryRepository)
        $entityManager = $doctrine->getManager();
        $categoryRepository = $entityManager->getRepository(Category::class);
        //Liste des Categories
        $categories = $categoryRepository->findAll();
        //Nous récupérons la Category dont le nom est indiqué. Si celle-ci n'est pas trouvée, nous retournons à l'index
        $category = $categoryRepository->findOneBy(['name' => $categoryName]);
        if (!$category) {
            return $this->redirectToRoute('app_index');
        }
        //On récupère la liste des chambres liés à la Category que nous transmettons à Twig
        $chambres = $category->getChambres();
        //Rendu Twig
        return $this->render('index/index.html.twig', [
            'selectedCategory' => $category,
            'categories' => $categories,
            'chambres' => $chambres,
        ]);
    }

    #[Route('/tag/{tagName}', name: 'index_tag')]
    public function indexTag(string $tagName, ManagerRegistry $doctrine): Response
    {
        //Cette méthode renvoie la liste de tous les chambres liés au Tag dont le nom est affiché au sein de notre URL

        //Afin de pouvoir communiquer avec notre base de données et récupérer les chambres lié à notre Tag, nous avons besoin de l'Entity Manager ainsi que du Repository de Tag
        $entityManager = $doctrine->getManager();
        $tagRepository = $entityManager->getRepository(Tag::class);
        //Nous récupérons nos catégories
        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();
        //Nous recherchons le Tag dont le nom a été renseigné dans notre URL, s'il n'est pas trouvé, nous retournons à l'index
        $tag = $tagRepository->findOneBy(['name' => $tagName]);
        if(!$tag){
            return $this->redirectToRoute('app_index');
        }
        //On récupère les chambres de notre tag
        $chambres = $tag->getChambres();
        //Nous transmettons la liste des chambres et des Categories à notre index.html.twig
        return $this->render('index/index.html.twig', [
            'categories' => $categories,
            'chambres' => $chambres
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
        //Liste des Categories
        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();
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
            'categories' => $categories,
            'formName' => 'modification de la Chambre',
            'dataForm' => $chambreForm->createView(),
        ]);
    }

    #[Route('/chambre/delete/{chambreId}', name: 'chambre_delete')]
    public function deleteChambre(ManagerRegistry $doctrine, int $chambreId): Response
    {
        // 
        $entityManager = $doctrine->getManager();
        $chambreRepository = $entityManager->getRepository(Chambre::class);
        //Liste des Categories
        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();
        $chambre = $chambreRepository->find($chambreId);
        // 
        if (!$chambre) {
            return $this->redirectToRoute('app_index');
        }
        // 
        $entityManager->remove($chambre);
        $entityManager->flush();
        return $this->redirectToRoute('app_index', [
            'categories' => $categories,
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
        //Liste des Categories
        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();
        // Nous recherchons la chambre via son ID en utilisant la methode find() du Repository.
        $chambre = $chambreRepository->find($chambreId);
        // si la recherche ne mene à rien, $chambre vaut null, et nous retournons à l'index 
        if (!$chambre) {
            return $this->redirectToRoute('app_index');
        }
        // si nous avons notre Chambre, nous le transmetton à index.html.twig
        return $this->render('index/chambre_display.html.twig', [
            'categories' => $categories,
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
