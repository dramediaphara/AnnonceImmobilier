<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Category;
use App\Entity\Reservation;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'order_display')]
    public function displayOrders(ManagerRegistry $doctrine): Response
    {
        //Cette méthode présente la commande active et les commandes validées sous forme de tableaux, les uns à la suite des autres, et la commande active en premier.

        //Afin de pouvoir récupérer les commandes qui nous intéressent de notre base de données, nous avons besoin de l'Entity Manager ainsi que du Repository de Order
        $entityManager = $doctrine->getManager();
        $orderRepository = $entityManager->getRepository(Order::class);
        //On récupère l'Utilisateur en cours
        $user = $this->getUser();
        //On récupère la liste des Catégories
        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();
        //Maintenant que nous possédons le Repository de Order, nous récupérons tout d'abord la commande en mode panier, puis, les commandes validées
        $activeOrder = $orderRepository->findOneBy(['status' => 'panier', 'user' => $user,]); //findOneBy car on veut UNE commande
        $archivedOrders = $orderRepository->findBy(['status' => 'validee', 'user' => $user,]); //findBy, on récupère un tableau contenant toutes les commandes en mode panier, d'où la variable $archivedOrderS

        //On transmet les commandes à notre page Twig
        return $this->render('order/order_display.html.twig', [
            'categories' => $categories,
            'activeOrder' => $activeOrder,
            'archivedOrders' => $archivedOrders,
        ]);
    }

    #[Route('/reservation/delete/{reservationId}', name: 'reservation_delete')]
    public function deleteReservation(int $reservationId, ManagerRegistry $doctrine): Response
    {
        //Cette méthode permet la suppression d'une Reservation d'une commande en cours, laquelle est identifiée par son ID fourni dans notre URL

        //Tout d'abord, nous avons de l'Entity Manager ainsi que du Repository de Reservation pour récupérer la réservation à supprimer
        $entityManager = $doctrine->getManager();
        $reservationRepository = $entityManager->getRepository(Reservation::class);
        //Nous recherchons la Reservation en question. Si elle n'existe pas ou que la commande liée n'est pas en mode panier, nous retournons au tableau de bord des commandes
        $reservation = $reservationRepository->find($reservationId);
        if(!$reservation || !$reservation->getOrder() || $reservation->getOrder()->getStatus() != "panier" || $reservation->getOrder()->getUser() != $this->getUser()){
            //La seconde condition est présente pour éviter de faire à un appel à la méthode getStatus() sur NULL
            return $this->redirectToRoute('order_display');
        }
        //Une fois que nous avons notre Reservation, nous nous chargeons de restituer la quantity récupérée au stock du Product lié
        if($reservation->getChambres()){
            $chambre = $reservation->getChambres();
            $chambre->setStock($reservation->getQuantity() + $chambre->getStock());
            $entityManager->persist($chambre);
        }
        //On peut placer la quantity de notre reservation à zéro (symbolique)
        $reservation->setQuantity(0);
        //On vérifie si la commande liée à la Reservation est vide après retrait de cette dernière. Si oui, nous supprimons également la commande
        $order = $reservation->getOrder();
        $order->removeReservation($reservation);
        //Si le tableau de Reservations de notre commande est vide
        if(!$order->getReservations()->toArray()){
            $entityManager->remove($order);
        }
        //On procède à la suppression de notre Reservation avant de retourner sur le tableau de bord des Commandes
        $entityManager->remove($reservation);
        $entityManager->flush();
        return $this->redirectToRoute('order_display');
    }

    #[Route('/delete/{orderId}', name: 'order_delete')]
    public function deleteOrder(int $orderId, ManagerRegistry $doctrine): Response
    {
        //Cette méthode permet la suppression d'une commande et de toutes les Réservations qui lui sont liées

        //Afin de pouvoir récupérer la commande de notre base de données, nous avons besoin de l'Entity Manager ainsi que du Repository de Order
        $entityManager = $doctrine->getManager();
        $orderRepository = $entityManager->getRepository(Order::class);
        //On récupère la commande à supprimer, si celle-ci n'existe pas ou n'est pas en mode panier, nous retournons au tableau de bord
        $order = $orderRepository->find($orderId);
        if(!$order || ($order->getStatus() != 'panier') || $order()->getUser() != $this->getUser()){
            return $this->redirectToRoute('order_display');
        }
        //Avant de supprimer notre commande, nous devons supprimer chaque Reservation contenu en cette dernière
        foreach($order->getReservations() as $reservation){
            //Une fois que nous avons notre Reservation, nous nous chargeons de restituer la quantity récupérée au stock du Product lié
            if($reservation->getChambres()){
                $chambre = $reservation->getChambres();
                $chambre->setStock($reservation->getQuantity() + $chambre->getStock());
                $entityManager->persist($chambre);
            }
            $entityManager->remove($reservation);
        }
        //Une fois que toutes les Reservations ont reçu leur requête de suppression, nous passons notre requête de suppression de la commande:
        $entityManager->remove($order);
        $entityManager->flush();
        //On repart sur le tableau de bord commande
        return $this->redirectToRoute('order_display');
    }

    #[Route('/validate', name: 'order_validate')]
    public function validateOrder(ManagerRegistry $doctrine): Response
    {
        //Cette méthode récupère la commande (Order) en cours et change son statut de "panier" à "validée"

        //Afin de pouvoir récupérer notre commande, nous avons de l'Entity Manager ainsi que du Repository de Order
        $entityManager = $doctrine->getManager();
        $orderRepository = $entityManager->getRepository(Order::class);
        //Nous vérifions s'il existe une commande (Order) en cours. Si nous ne trouvons pas de commandee en mode panier, nous retournons au tableau de bord des commandes:
        $order = $orderRepository->findOneBy(['status' => 'panier']);
        //Si la commande n'existe pas OU que son statut est différent de panier -> redirection
        if(!$order || ($order->getStatus() != 'panier') || $order()->getUser() != $this->getUser()){
            return $this->redirectToRoute('order_display');
        }
        //Une fois que nous avons récupéré notre entity Order, nous devons modifier la valeur de son attribut $status
        $order->setStatus("validée");
        //Une fois que notre commande est modifiée, il suffit de la persister avant de revenir à notre tableau de bord de commandes
        $entityManager->persist($order);
        $entityManager->flush();
        return $this->redirectToRoute('order_display');
    }


}
