<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Repository\AchatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AchatController extends AbstractController
{
    #[Route('/achat/list', name: 'auth.achat.list', methods: ['GET'])]
    public function list(SerializerInterface $serializer , achatRepository $achatRepository) : Response
    {
        //récupérer toutes les instances de l'entité achat stockées en bd
        $achat = $achatRepository->findAll();

        // Sérialiser la liste des achats en format JSON
        $body = $serializer->serialize($achat, 'json');

        // Créer une réponse JSON à partir des données sérialisées
        return $this->json($body, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }


    #[Route('/achat/create', name: 'crud.achat.create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les données du corps de la requête
        $body = json_decode($request->getContent(), true);// Récupération des données

        // Créer un nouvel achat avec les données reçues
        $achat = new Achat();
        $achat->setUser_id ($body['user_id ']);
        $achat->setEtat_achat_id($body['etat_achat_id']);
        $achat->setFournisseur_id($body['fournisseur_id']);
        $achat->setDate_achat($body['date_achat']);
        $achat->setMontantt_total($body['montantt_total']);

        // Persister le nouvel achat en base de données
        $entityManager->persist($achat);
        $entityManager->flush();

        // Retourner une réponse indiquant que l'achat a été ajouté avec succès
        return $this->json(['message' =>'Le achat a été ajouté avec succès'], Response::HTTP_CREATED);
    }


    #[Route('/achat/delete/{id}', name: 'crud.achat.delete', methods: ['DELETE'])]
    public function delete(int $id, AchatRepository $achatRepository,EntityManagerInterface $entityManager): Response
    {
        // Rechercher l'achat en fonction de son identifiant
        $achat = $achatRepository->find($id);
        // Vérifier si le client existe
        if (!$achat) {
            // Retourner une réponse indiquant que l'achat n'a pas été trouvé
            return $this->json(['message' => 'achat non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Supprimer l'achat de la base de données
        $entityManager->remove($achat);
        $entityManager->flush();

        // Retourner une réponse indiquant que l'achat a été supprimé avec succès
        return $this->json(['message' => 'Le achat a été supprimé avec succès'], Response::HTTP_OK);
    }



    #[Route('/achat/update/{id}', name: 'crud.achat.update', methods: ['PUT'])]
    public function update(Request $request, Achat $achat, EntityManagerInterface $entityManager): Response
    {
        // Rechercher l'achat en fonction de son identifiant


// Vérifier si l'achat existe
        if (!$achat) {
            return $this->json('achat non trouvé', Response::HTTP_NOT_FOUND);
        }

        // Récupérer les nouvelles informations de l'achat depuis la requête
        $body = json_decode($request->getContent(), true);

        // Mettre à jour les informations du client avec les nouvelles données
        $achat->setUser_id ($body['user_id ']);
        $achat->setEtat_achat_id($body['etat_achat_id']);
        $achat->setFournisseur_id($body['fournisseur_id']);
        $achat->setDate_achat($body['date_achat']);
        $achat->setMontantt_total($body['montantt_total']);

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        return $this->json(['message'=>'Les informations du achat ont été mises à jour avec succès'], Response::HTTP_OK);
    }

    private function getDoctrine()
    {
    }
}
