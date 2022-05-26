<?php

namespace App\DataFixtures;

use App\Entity\Chambre;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ChambreFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //Informations sur nos chambres
        $chambreArray = [
            [
            "name" => "Chambre vide",
            "description" => "Ceci est une location vide, aenean gravida ante a placerat rhoncus. Mauris odio magna, aliquet id massa ut, convallis porttitor enim. Aenean sit amet leo eu nibh dictum  suscipit erat. ",
            "price" => 20,
            ],
            ["name" => "Chambre meublée", "description" => "Ceci est une location meublée, aliquam rhoncus lacus eget mattis. Cras sed porttitor mauris. Curabitur sit amet laoreet nisi, molestie interdum mi.  justo, convallis nec lacus congue, pretium faucibus mauris", "price" => 35,],

            ["name" => "Appartement vide ", "description" => "Ceci est une location , a tempus diam, aliquam semper odio. Mauris sapien est, lacinia vitae sollicitudin euismod, vehicula feugiat leo. Aliquam nunc  semper, luctus turpis vitae, tristique nunc. ", "price" => 60,],

            ["name" => "Appartement meublé ", "description" => "Ceci est une location de vacance, a tempus diam, aliquam semper odio. Mauris sapien est, lacinia vitae sollicitudin euismod, vehicula feugiat leo. Aliquam nunc  semper, luctus turpis vitae, tristique nunc. ", "price" => 150,],

            ["name" => "Locaux Commercial", "description" => "Ceci est une location commercial, consequat rutrum aliquam. Morbi fringilla feugiat at dolor et, hendrerit pharetra diam. Vivamus nec consectetur nunc. Donec dapibus turpis a fringilla euismod. Nullam blandit tincidunt nulla.", "price" => 200,],


            ["name" => "Locaux pour Bureau", "description" => "Ceci est une location de bureau, vitae felis pretium mi blandit pellentesque vel quis sem. Sed scelerisque mi mauris, quis vehicula augue commodo non. Donec dui justo,  fringilla nulla nulla et magna. Quisque luctus ante orci. ", "price" => 400,],

            ["name" => "Autres Logements ", "description" => "Ceci est une autre location, vehicula feugiat leo. Aliquam nunc enim, feugiat at dolor et, hendrerit pharetra diam. Vivamus nec consectetur nunc. Donec dapibus turpis a semper, luctus turpis vitae, tristique nunc.", "price" => 650,],


        ];

        //On utilise une boucle foreach pour parcourir chaque entrée de notre tableau d'informations sur nos chambres et on utilise chaque entrée pour préparer un nouvel objet chambre à persister
        foreach ($chambreArray as $chambreData) {
            $chambre = new Chambre(); //On crée un nouvel objet chambre avant de le renseigner
            $chambre->setName($chambreData['name']);
            $chambre->setDescription($chambreData['description']);
            $chambre->setPrice($chambreData['price']);

            $manager->persist($chambre);
        }

          //La liste de nos différentes catégories sous la forme d'un tableau associatif, contenant une indication du type de catégorie sous la clef et l'objet Category en valeur. Etant donné que nous allons instanciers les Category plus tard dans une boucle, la valeur actuelle de ces différentes clefs est null
          $categoryArray = [
            'location vide' => null,
            'location meublee' => null,
            'location de vacance' => null,
            'location commercial' => null,
            'location de bureau' => null,
            'autre' => null,
        ];

        //Description générique via faux texte
        $lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed at sapien ut sem convallis euismod. Phasellus eu condimentum augue. Praesent feugiat sem dolor, quis pharetra risus ullamcorper sed. Vivamus  nulla. Nam eget nisi massa. ';

        //Renseignement et implémentation de la liste des Category
        foreach($categoryArray as $key => &$value){ //Nous récupérons le tableau
            //Le & avant $value est un passage en référence, ce qui signifie que nous récupérons la variable en tant que telle plutôt que sa valeur, ce qui nous permet de modifier notre tableau $categoryArray plutôt qu'une copie de value, qui sera supprimée après la boucle
            $value = new Category; //A chaque valeur est attribuée un objet Category
            $value->setName(ucfirst($key)); //Le nom est la clef de l'index
            $value->setDescription($lorem); //La description est un lorem ipsum générique
            $manager->persist($value); //Demande de persistance de notre nouvelle Category
        }


        //On utilise une boucle foreach pour parcourir chaque entrée de notre tableau d'informations sur nos chambres et on utilise chaque entrée pour préparer un nouvel objet chambre à persister
        foreach($chambreArray as $chambreData){
            $chambre = new Chambre(); //On crée un nouvel objet chambre avant de le renseigner
            $chambre->setName($chambreData['name']);
            $chambre->setDescription($chambreData['description']);
            $chambre->setPrice($chambreData['price']);
           
            $manager->persist($chambre);
           
        }

        //On crée une liste de catégories potentielles
        $categories = ['location vide', 'location meublee', 'location de vacance', 'location commercial', 'location de bureau', 'autre'];
        for($i=0;$i<15;$i++){
            //On sélectionne un nom de catégorie au hasard qui servira à nommer la chambre et à déterminer la clef que nous sélectionnons dans $categoryArray
            $selectedCategory = $categories[rand(0, (count($categories) - 1))];
            $chambre = new Chambre(); //On crée un nouveau Product
            $chambre->setName(ucfirst($selectedCategory) . " #" . rand(1000,9999));
            $chambre->setDescription($lorem);
            $chambre->setPrice(rand(1,150) + 0.99);
            
            $manager->persist($chambre); //demande de persistance
        }

        $manager->flush();
        //On appliquera cette méthode load() grâce à la commande:
        //php bin/console doctrine:fixtures:load
    }
}
