<?php

namespace App\Controller;

use App\Entity\Vente;
use App\Repository\ClientsRepository;
use App\Repository\EtatDeVenteRepository;
use App\Repository\VenteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
  class ReportingController extends AbstractController
  {
 #[Route('/export/vente/period', name: 'app_stat_reporting', methods: ['GET'])]
        public function listerVentesSurPeriode(Request $request, EntityManagerInterface $entityManager,
        VenteRepository $venteRepository): Response
        {
            // Récupérer les paramètres de la période depuis la requête JSON
            $req = json_decode($request->getContent(), true);

            // Vérifier la présence des clés "date_debut" et "date_fin"
            if (!isset($req['date_debut']) || !isset($req['date_fin'])) {
                return $this->json(['message' => 'Les clés "date_debut" et "date_fin" sont requises dans la requête.'], Response::HTTP_BAD_REQUEST);
            }

            // Convertir les dates de début et de fin au format DateTime
            $dateDebut = new \DateTime($req['date_debut']);
            $dateFin = new \DateTime($req['date_fin']);

            // Vérifier la validité de la période
            if ($dateDebut > $dateFin) {
                return $this->json(['message' => 'La date de début doit être antérieure à la date de fin'], Response::HTTP_BAD_REQUEST);
            }

            // Récupérer les ventes dans la période spécifiée à partir du repository
            $ventes = $venteRepository->createQueryBuilder('v')
                ->where('v.date_de_vente >= :dateDebut')
                ->andWhere('v.date_de_vente <= :dateFin')
                ->setParameter('dateDebut', $dateDebut)
                ->setParameter('dateFin', $dateFin)
                ->getQuery()
                ->getResult();

            // Construire un tableau associatif contenant les données pertinentes pour chaque vente
            $formattedVentes = [];
            foreach ($ventes as $vente) {
                $formattedVentes[] = [
                    'id' => $vente->getId(),
                    'etat_de_vente_id' => $vente->getEtatDeVente()->getId(),
                    'user_id' => $vente->getUser()->getId(),
                    'client_id' => $vente->getClient()->getId(),
                    'date_de_vente' => $vente->getDateDeVente()->format('Y-m-d H:i:s'), // Formatage de la date
                    'montant_total' => $vente->getMontantTotal(),
                ];
            }

            // Retourner les ventes sous forme de réponse JSON
            return $this->json($formattedVentes, Response::HTTP_OK);
  }

    #[Route('/export/clients', name: 'client.list', methods: ['GET'])]
    public function list(ClientsRepository $clientsRepository) : Response
    {
        //récupérer toutes les instances de l'entité Client stockées en bd
        $clients = $clientsRepository->findBy(['user'=>$this->getUser()]);


        // Construire un tableau associatif contenant les données pertinentes pour chaque clients
        $formattedclients = [];
        foreach ( $clients as $client) {


            $formattedclients[] = [
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
        return $this->json($formattedclients, Response::HTTP_OK);

    }

    #[Route('/filtre/vente/client/{Id}', name: 'ventes.client', methods: ['GET'])]
    public function ventesParClient(int $Id, VenteRepository $venteRepository): JsonResponse
    {
        // Récupérer les ventes correspondant à l'ID de l'état de vente spécifié
        $ventes = $venteRepository->findByClient($Id);

        // Construire un tableau associatif contenant les données pertinentes pour chaque vente
        $formattedVentes = [];
        foreach ($ventes as $vente) {
            $formattedVentes[] = [
                'id' => $vente->getId(),
                'etat_de_vente_id' => $vente->getEtatDeVente()->getId(),
                'user_id' => $vente->getUser()->getId(),
                'client_id' => $vente->getClient()->getId(),
                'date_de_vente' => $vente->getDateDeVente()->format('Y-m-d H:i:s'), // Formatage de la date
                'montant_total' => $vente->getMontantTotal(),
            ];
        }

        // Retourner les ventes sous forme de réponse JSON
        return $this->json($formattedVentes, Response::HTTP_OK);
    }

    #[Route('/filtre/vente/statut/{id}', name: 'ventes.statuts', methods: ['GET'])]
    public function ventesParstatut(int $id,EntityManagerInterface $entityManager, VenteRepository $venteRepository, EtatDeVenteRepository $etatDeVenteRepository): JsonResponse
    {
        // Récupérer les ventes de l'état  spécifié à partir du repository des ventes
        $etatVente = $etatDeVenteRepository->find($id);
;
        if (!$etatVente) throw new NotFoundHttpException("L'etat de vente n'existe pas");

        $ventes =$entityManager->getRepository(Vente::class)->findBy(['etat_de_vente' => $etatVente]);

        //$ventes=$venteRepository->findByEtatDeVenteId($id); pour deuxième façons de faire avec une methode dans repository
        // Construire un tableau associatif contenant les données pertinentes pour chaque vente
        $formattedVentes = [];
        foreach ($ventes as $vente) {
            $formattedVentes[] = [
                'id' => $vente->getId(),
                'etat_de_vente_id' => $vente->getEtatDeVente()->getId(),
                'user_id' => $vente->getUser()->getId(),
                'client_id' => $vente->getClient()->getId(),
                'date_de_vente' => $vente->getDateDeVente()->format('Y-m-d H:i:s'), // Formatage de la date
                'montant_total' => $vente->getMontantTotal(),
            ];
        }

        // Retourner les ventes sous forme de réponse JSON
        return $this->json($formattedVentes, Response::HTTP_OK);
    }


}
