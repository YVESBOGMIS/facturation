<?php

namespace App\Controller;

use App\Entity\EtatDeVente;
use App\Entity\User;
use App\Entity\Vente;
use App\Repository\ClientsRepository;
use App\Repository\EtatDeVenteRepository;
use App\Repository\UserRepository;
use App\Repository\VenteRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\PseudoTypes\StringValue;
use phpDocumentor\Reflection\Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/vente')]
class VenteController extends AbstractController
{

    #[Route('/list', name: 'vente.list', methods: ['GET'])]
    public function list(Request $request,venteRepository $venteRepository): Response
    {
        // Récupérer toutes les instances de l'entité Vente stockées en base de données
        $ventes = $venteRepository->findBy(['user' => $this->getUser()]);

// Construire un tableau associatif contenant les données pertinentes pour chaque vente
        $formattedVentes = [];
        foreach ($ventes as $vente) {


            $formattedVentes[] = [
                'id' => $vente->getId(),
                'etat_de_vente_id' => $vente->getEtatDeVente(),
                'user_id' => $vente->getUser()->getId(),
                'client_id' => [
                    'id' => $vente->getClient()->getId(),
                    'nom' => $vente->getClient()->getNom(),
                    'prenom' => $vente->getClient()->getPrenom(),
                    'Email' => $vente->getClient()->getEmail(),
                    'Telephone' => $vente->getClient()->getTelephone(),
                    'Addresse' => $vente->getClient()->getAddresse(),
                ],
                'date_de_vente' => $vente->getDateDeVente(),
                'montant_total' => $vente->getMontantTotal(),
            ];
        }

// Créer une réponse JSON à partir des données formatées
        return $this->json($formattedVentes, Response::HTTP_OK);

    }


    #[Route('/create', name: 'create.vente', methods: ['POST'])]
    public function create(Request $request, ClientsRepository $clientsRepository, EtatDeVenteRepository $etatDeVenteRepository,
                           EntityManagerInterface $entityManager
    ): Response
    {
        $body = json_decode($request->getContent(), true);// Récupération des données

        // Récupérer les données du corps de la requête et Vérifier si 'client_id' est présent et non nul

        if (isset($body['client_id']) ) {
            // Récupérer le client à partir de 'client_id'
            $client = $clientsRepository->findOneBy(['id' => $body['client_id'], 'user' => $this->getUser()]);

            $Date_de_vente = $body['date_de_vente'];
            $Montant_Total = $body['montant_Total'];


            // Vérifier si le client existe

            if ($client ) {


                // Créer une nouvelle vente avec le client récupéré
                $vente = new vente();
                $vente->setClient($client);
                $vente->setMontantTotal($Montant_Total);
                $vente->setUser($this->getUser());
                $Date_de_vente = new DateTime($Date_de_vente);
                $vente->setDateDeVente($Date_de_vente);


                // Associer l'état "initialisé" à la vente
                $etatInitialise = $entityManager->getRepository(EtatDeVente::class)->findOneBy(['Slug' => 'IN']);
                $vente->setEtatDeVente($etatInitialise);

                // Associer la vente au client
                $client->addVente($vente);


            } else {
                // Répondre avec une erreur car le client n'existe pas

                return $this->json(['message' => 'Le client spécifié ou l\'état de vente  n\'existe pas']);


            }
        } else {
            // Répondre avec une erreur car 'client_id' est manquant ou nul
            return $this->json(['message' => 'L\'identifiant du client est manquant ou invalide']);
        }

        // Persister la nouvelle vente en base de données
        $entityManager->persist($vente);
        $entityManager->flush();

        // Répondre avec une confirmation de la création de la vente
        return $this->json(['message' => 'La vente a été créée avec succès'], Response::HTTP_CREATED);
    }


    #[Route('/delete/{id}', name: 'vente.delete', methods: ['DELETE'])]
    public function delete(int $id, venteRepository $venteRepository, EntityManagerInterface $entityManager): Response
    {
        // Rechercher la vente en fonction de son identifiant
        $vente = $venteRepository->find($id);
        // Vérifier si la vente existe
        if (!$vente) {
            // Retourner une réponse indiquant que la vente n'a pas été trouvé
            return $this->json(['message' => 'vente non trouvé'], Response::HTTP_NOT_FOUND);
        }
        // Vérifier si la vente appartient à l'utilisateur actuel
        if ($vente->getUser() !== $this->getUser()) {// $vente->getUser() proprietaire
            // de la bd à la bd (vente appartenant à la bd); $this->getUser() propritaire de la requette

            return $this->json(['message' => 'Cette vente n\'appartient pas à cet utilisateur']);
        }

        // Supprimer la vente de la base de données
        $entityManager->remove($vente);
        $entityManager->flush();

        // Retourner une réponse indiquant que le client a été supprimé avec succès
        return new Response('La vente a été supprimé avec succès', Response::HTTP_OK);
    }


    #[Route('/update/{id}', name: 'vente.update', methods: ['PATCH'])]
    public function update(int                    $id, Request $request, ClientsRepository $clientsRepository,
                           UserRepository         $userRepository, EtatDeVenteRepository $etatDeVenteRepository,
                           EntityManagerInterface $entityManager,
                           VenteRepository        $venteRepository): Response
    {

        // Rechercher la vente en fonction de son identifiant
        $vente = $venteRepository->find($id);

        // Vérifier si la vente existe
        if (!$vente) {
            return $this->json(['message' => 'vente non trouvé'], Response::HTTP_NOT_FOUND);
        }
        // Récupérer l'utilisateur actuellement authentifié
        $user = $this->getUser();
        // Vérifier si la vente appartient à l'utilisateur actuel
        if ($vente->getUser() !== $user) {// $vente->getUser() proprietaire
            //de la bd à la bd (vente appartenant à la bd); $this->getUser() propritaire de la requette

            return $this->json(['message' => 'Cet vente n\'appartient pas à cet utilisateur']);
        }

        // Récupérer les données du corps de la requête
        $body = json_decode($request->getContent(), true);

        // Récupérer les données du corps de la requête

        $body = json_decode($request->getContent(), true);// Récupération des données
        $client = $clientsRepository->findOneBy(['id' => $body['client_id'], 'user' => $this->getUser()]);
        $etat_de_vente = $etatDeVenteRepository->findOneBy(['id' => $body['etat_de_vente_id']]);
        $Date_de_vente = $body['date_de_vente'];
        $Montant_Total = $body['montant_Total'];
        // Créer une nouvelle vente avec les données reçues

        $vente = new vente();
        $vente->setClient($client);
        $vente->setEtatDeVente($etat_de_vente);
       $vente->setUser($this->getUser());
        $vente->setMontantTotal($Montant_Total);
        $Date_de_vente = new DateTime($Date_de_vente);
        $vente->setDateDeVente($Date_de_vente);


        // Enregistrer les modifications dans la base de données
        $entityManager->persist($vente);
        $entityManager->flush();

        return $this->json(['message' => 'Les informations du vente ont été mises à jour avec succès'], Response::HTTP_OK);


    }

    #[Route('/annuler/{id}', name: 'annuler.vente', methods: ['PATCH'])]
    public function annuler($id,Request $request,
                           EntityManagerInterface $entityManager
    ): Response{
        // Récupérer l'ID de la vente depuis la route
        $vente = $entityManager->getRepository(Vente::class)->find($id);

        // Valider si la vente existe
        if (!$vente) {
            return $this->json(['message' => 'Vente non trouvée'], Response::HTTP_NOT_FOUND);
        }

// Récupérer l'état "Annulé" depuis la base de données
        $etatAnnule = $entityManager->getRepository(EtatDeVente::class)->findOneBy(['Slug' => 'AN']);

// Assigner l'état "Annulé" à la vente
        $vente->setEtatDeVente($etatAnnule);

// Persister les changements dans la base de données
        $entityManager->flush();

        return $this->json(['message' => 'État de la vente marqué comme annulé avec succès'], Response::HTTP_OK);

    }









}