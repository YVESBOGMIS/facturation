<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Entity\Encaissement;
use App\Repository\ClientsRepository;
use App\Repository\VenteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EncaissementRepository;




    #[Route('/api')]
    class StatisticController extends AbstractController
{
    #[Route('/stat/nbvente', name: 'nbre.vente', methods: ['GET'])]
    public function nbrVentesSurPeriode(Request $request, EntityManagerInterface $entityManager, VenteRepository $venteRepository): Response
            {
                // Récupérer les paramètres de la période depuis la requête JSON
                $req = json_decode($request->getContent(), true);

                // Récupérer l'utilisateur actuellement authentifié
                $user = $this->getUser();


                // Récupérer le nombre de ventes dans la période spécifiée
                $nbVentes = $venteRepository->countVentesSurPeriode($user, $req['date_debut'] ?? null, $req['date_fin'] ?? null);

                // Retourner le nombre de ventes sous forme de réponse JSON
                return $this->json(['nombre_de_ventes' => $nbVentes], Response::HTTP_OK);
    }

        #[Route('/stat/encaissements', name: 'stat.encaissements_vente', methods: ['GET'])]
        public function encaisementglobalesurpreriodparuser(Request $request, VenteRepository $venteRepository,
                                                     EntityManagerInterface $entityManager,
                                                     EncaissementRepository $encaissementRepository): Response
        {
            // Récupérer les paramètres de la période depuis la requête JSON
            $req = json_decode($request->getContent(), true);

            // Récupérer l'utilisateur actuellement authentifié
            $user = $this->getUser();

            // Créer un QueryBuilder pour Encaissement
            $queryBuilder = $encaissementRepository->createQueryBuilder('e');

            // Utiliser le repository EncaissementRepository pour obtenir le montant total des encaissements dans la période spécifiée
            $montantTotal = $encaissementRepository->findEncaisementGlobalSurPeriode($queryBuilder, $user, $req['date_debut'] ?? null, $req['date_fin'] ?? null);

            // Retourner le montant total des encaissements de la vente sous forme de réponse JSON
            return $this->json(['total_encaissement' => intval($montantTotal)], Response::HTTP_OK);
        }




        #[Route('/stat/montantencaiss/vente/{id}  ', name: 'stat.encaissements.global', methods: ['GET'])]
        public function encaisementparvente(int $id,Request $request, EntityManagerInterface $entityManager, EncaissementRepository $encaissementRepository): Response
        {
            // Récupérer les paramètres de la période depuis la requête JSON
            $req = json_decode($request->getContent(), true);

            // Récupérer l'utilisateur actuellement authentifié
            $user = $this->getUser();

            // Vérifier si l'utilisateur est authentifié
            if (!$user) {
                return $this->json(['message' => 'Utilisateur non authentifié'], Response::HTTP_UNAUTHORIZED);
            }

            // Récupérer l'ID de l'utilisateur authentifié
            $userId = $user->getId();

            // Vérifier si l'ID de l'utilisateur est présent
            if (!$userId) {
                return $this->json(['message' => 'ID utilisateur manquant'], Response::HTTP_BAD_REQUEST);
            }

            // Requête pour obtenir les montants des encaissements de la vente spécifiée dans la période spécifiée
            $query = $encaissementRepository->createQueryBuilder('e')
                ->select('SUM(e.Montant) as montant_total')
                ->join('e.vente', 'v')
                ->where('v.id = :id')
                ->setParameter('id', $id)

                ->getQuery();

            // Exécuter la requête pour obtenir le montant total des encaissements de la vente spécifiée dans la période spécifiée
            $montantTotal = $query->getSingleScalarResult();

            // Retourner le montant total des encaissements de la vente sous forme de réponse JSON
            return $this->json(['montant total des encaissements pour cette vente' => $montantTotal], Response::HTTP_OK);

        }




        #[Route('/stat/meilleur/client', name: 'stat.meilleur.user', methods: ['GET'])]
        public function meilleurClientsparuser(Request $request,
                                        EntityManagerInterface $entityManager): Response
        {

            // Récupérer les paramètres de la période depuis la requête JSON
            $req = json_decode($request->getContent(), true);

            // Récupérer l'utilisateur actuellement authentifié
            $user = $this->getUser();



            // Exécuter la requête pour obtenir les meilleurs clients
            $queryBuilder = $entityManager->createQueryBuilder();
            $results = $queryBuilder
                ->select('c.id', 'c.Nom','c.Prenom', 'SUM(e.Montant) AS montant_total_verser', 'COUNT(v.client) AS nombre_vente_Encaisser')
                ->from('App\Entity\Encaissement', 'e')
                ->join('e.vente', 'v')
                ->join('v.client', 'c')
                ->where('c.user = :userId')
                ->andWhere('v.etat_de_vente = :etatDeVenteId')
                ->groupBy('c.id', 'c.Nom')
                ->orderBy('montant_total_verser', 'DESC')
                ->setParameter('userId', $user)
                ->setParameter('etatDeVenteId', 7)
                ->getQuery()
                ->getResult();

            return $this->json($results);


        }


        #[Route('/stat/meilleur/clients/user', name: 'stat.meilleur', methods: ['GET'])]
        public function meilleurClients(Request $request, EncaissementRepository $encaissementRepository): Response
        {
            $user = $this->getUser(); // Supposons que l'entité User est retournée ici

            // Récupérer les paramètres de la période depuis la requête JSON
            $req = json_decode($request->getContent(), true);

            // Appeler la méthode du repository avec le QueryBuilder créé
            $topClients = $encaissementRepository
                ->findByTopClientsByUser($user, $req['date_debut'] ?? null, $req['date_fin'] ?? null);

            return $this->json($topClients);
        }


        #[Route('/stat/taux/vente/aboutie', name: 'stat.taux', methods: ['POST'])]
        public function tauxVentesAbouties(VenteRepository $venteRepository): Response
        {
            $user = $this->getUser();
            // Récupérer le nombre total de ventes
            $totalVentes = $venteRepository->getTotalVentes($user);

            // Récupérer le nombre de ventes abouties
            $ventesAbouties = $venteRepository->getVentesAbouties($user);

            // Calculer le taux de ventes abouties
            $tauxVentesAbouties = $totalVentes > 0 ? ($ventesAbouties / $totalVentes) * 100 : 0;

            return $this->json(['taux_ventes_abouties' => $tauxVentesAbouties]);
        }




    }
