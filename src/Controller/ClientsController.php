<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Entity\User;
use App\Repository\ClientsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/client')]
class ClientsController extends AbstractController
{
    #[Route('/list', name: 'client.list1', methods: ['GET'])]
    public function list(ClientsRepository $clientsRepository) : Response
    {
        //récupérer toutes les instances de l'entité Client stockées en bd
        $clients = $clientsRepository->findBy(['user'=>$this->getUser()]);


        // Construire un tableau associatif contenant les données pertinentes pour chaque clients
        $formatclients = [];
        foreach ( $clients as $client) {


            $formatclients[] = [
                'id' => $client->getId(),
                'Nom' => $client->getNom(),
                'Prenom' => $client->getPrenom(),
                'Email' => $client->getEmail(),
                'Telephone' => $client->getTelephone(),
                'Addresse' => $client->getAddresse(),
                'User_id' => $client->getUser()->getId(),

            ];
        }

        // Créer une réponse JSON à partir des données formatées
        return $this->json( $formatclients, Response::HTTP_OK);

    }


    #[Route('/create', name: 'client.create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les données du corps de la requête
        $body = json_decode($request->getContent(), true);// Récupération des données

        // Créer un nouveau client avec les données reçues
        $client = new Clients();
        $client->setNom($body['Nom']);
        $client->setPrenom($body['Prenom']);
        $client->setEmail($body['Email']);
        $client->setTelephone ($body['Telephone']);
        $client->setAddresse($body['Addresse']);
        $client->setUser($this->getUser());

        // Persister le nouveau client en base de données
        $entityManager->persist($client);
        $entityManager->flush();

        // Retourner une réponse indiquant que le client a été ajouté avec succès
        return $this->json(['message' =>'Le client a été ajouté avec succès'], Response::HTTP_CREATED);
    }


    #[Route('/delete/{id}', name: 'client.delete', methods: ['DELETE'])]
    public function delete(int $id, ClientsRepository $clientsRepository,EntityManagerInterface $entityManager): Response
    {
        // Rechercher le client en fonction de son identifiant
        $client = $clientsRepository->find($id);
        // Vérifier si le client existe
        if (!$client) {
            // Retourner une réponse indiquant que le client n'a pas été trouvé
            return $this->json(['message' => 'Client non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier si le client appartient à l'utilisateur actuel
        if ($client->getUser() !== $this->getUser()) {// $client->getUser() proprietaire
            // de la bd à la bd (client appartenant à la bd); $this->getUser() propritaire de la requette

            return $this->json(['message'=>'Ce client n\'appartient pas à cet utilisateur']);
        }

        // Supprimer le client de la base de données
        $entityManager->remove($client);
        $entityManager->flush();

        // Retourner une réponse indiquant que le client a été supprimé avec succès
        return $this->json(['message' => 'Le client a été supprimé avec succès'], Response::HTTP_OK);
    }


    #[Route('/update/{id}', name: 'client.update', methods: ['PATCH'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager,
                           ClientsRepository $clientsRepository): Response
    {
        // Rechercher le client en fonction de son identifiant
        $client = $clientsRepository->find($id);

        // Vérifier si le client existe
        if (!$client) {
            return $this->json(['message'=>'Client non trouvé'], Response::HTTP_NOT_FOUND);
        }
        // Vérifier si le client appartient à l'utilisateur actuel
        if ($client->getUser() !== $this->getUser()) {// $client->getUser() proprietaire
            // de la bd à la bd (client appartenant à la bd); $this->getUser() propritaire de la requette

            return $this->json(['message'=>'Ce client n\'appartient pas à cet utilisateur']);
        }
        // Récupérer les nouvelles informations du client depuis la requête
        $body = json_decode($request->getContent(), true);

        // Mettre à jour les informations du client avec les nouvelles données
        $client->setNom($body['Nom']);
        $client->setPrenom($body['Prenom']);
        $client->setEmail($body['Email']);
        $client->setTelephone ($body['telephone']);
        $client->setAddresse($body['Addresse']);


        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        return $this->json(['message'=>'Les informations du client ont été mises à jour avec succès'], Response::HTTP_OK);
    }
}
