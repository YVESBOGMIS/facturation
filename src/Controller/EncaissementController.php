<?php

namespace App\Controller;

use App\Entity\Encaissement;
use App\Entity\EtatDeVente;
use App\Entity\LigneDeVente;
use App\Entity\TypeDeDocumentDeVente;
use App\Entity\Vente;
use App\Repository\EncaissementRepository;
use App\Repository\VenteRepository;
use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\DocumentDeVente;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/api')]
class EncaissementController extends AbstractController
{
    #[Route('/encaissement', name: 'encaissement.regist', methods: ['POST'])]
    public function encaissement(Request $request, EntityManagerInterface $entityManager, VenteRepository $venteRepository): Response
    {

// Récupérer les données du formulaire
        $body = json_decode($request->getContent(), true);

// Vérifier si les données nécessaires sont présentes dans le formulaire
        if (!isset($body['vente_id'], $body['Mode_de_paiement'], $body['date'], $body['Montant'])) {
            return $this->json(['message' => 'Certaines données sont manquantes dans le formulaire'], Response::HTTP_BAD_REQUEST);
        }

// Récupérer la vente associée à l'ID fourni dans le formulaire
        $vente = $venteRepository->findOneBy(['id' => $body['vente_id'], 'user' => $this->getUser()]);

// Vérifier si la vente existe
        if (!$vente) {
            return $this->json(['message' => 'La vente associée à cet identifiant est introuvable'], Response::HTTP_NOT_FOUND);
        }

// Créer une nouvelle instance d'Encaissement
        $encaissement = new Encaissement();
        $encaissement->setModeDePaiement($body['Mode_de_paiement']);
        $encaissement->setMontant($body['Montant']);
        $encaissement->setDate(new \DateTime($body['date']));
        $encaissement->setVente($vente);

// Enregistrer l'encaissement dans la base de données

        $entityManager->persist($encaissement);
        $entityManager->flush();

// Définir l'état de vente "En cours d'encaissement"
            $etatDeVenteEncoursDEncaissement = $entityManager->getRepository(EtatDeVente::class)->findOneBy(['Slug' => 'ENDEN']);

// Définir l'état de vente "Encaissée"
            $etatDeVenteEncaissee = $entityManager->getRepository(EtatDeVente::class)->findOneBy(['Slug' => 'EN']);

// Vérifier si les états de vente existent
            if (!$etatDeVenteEncoursDEncaissement || !$etatDeVenteEncaissee) {
                // Gérer l'erreur, par exemple en lançant une exception

                return $this->json(['message' => '"Les états de vente nécessaires ne sont pas trouvés."']);
            }

// Calculer la somme des montants des encaissements pour cette vente
            $montantTotalEncaissements = 0;

// Parcourir les encaissements de la vente
            foreach ($vente->getEncaissements() as $encaissement) {
                // Ajouter le montant de chaque encaissement à la somme totale
                $montantTotalEncaissements += $encaissement->getMontant();
            }

// Mettre à jour l'état de la vente en fonction de la somme des encaissements
            if ($montantTotalEncaissements < $vente->getMontantTotal()) {
                // Si la somme des encaissements est inférieure au montant total de la vente, mettre l'état à "En cours d'encaissement"
                $vente->setEtatDeVente($etatDeVenteEncoursDEncaissement);
            } elseif ($montantTotalEncaissements >= $vente->getMontantTotal() && $montantTotalEncaissements > 0) {
                // Si la somme des encaissements est égale au montant total de la vente et supérieure à zéro, mettre l'état à "Encaissée"
                $vente->setEtatDeVente($etatDeVenteEncaissee);
            }


// Persister les changements dans la base de données
            $entityManager->persist($vente);
            $entityManager->flush();





        // Vous pouvez appeler la méthode flush() une seule fois après la boucle foreach pour améliorer les performances
        $entityManager->flush();
        return $this->json(['message' => 'Encaissements ajoutés avec succès'], Response::HTTP_CREATED);
    }

    #[Route('/encaissement/list', name: 'encaissement.list', methods: ['POST'])]
    public function encaissementlist(Request $request, VenteRepository $venteRepository, EncaissementRepository $encaissementRepository): Response
    {
        // récupéré les ventes pour cet utilisateur
        $ventes = $venteRepository->findBy(['user' => $this->getUser()]);

        // Formatter les données des encaissements
        $formattedEncaissements = [];
        foreach ($ventes as $vente) {

            // Récupérer les encaissements pour cette vente
            $encaissements = $encaissementRepository->findBy(['vente' => $vente]);
            foreach ($encaissements as $encaissement) {
                $formattedEncaissements[]= [
                    'id' => $encaissement->getId(), // Ajouter l'ID de l'encaissement
                    'vente' => [
                        'id' => $vente->getId(), // ID de la vente
                        'etat_de_vente_id' => $vente->getEtatDeVente(),
                        'user_id' => $vente->getUser()->getId(),
                        'client' => [
                            'id' => $vente->getClient()->getId(),
                            'nom' => $vente->getClient()->getNom(),
                            'prenom' => $vente->getClient()->getPrenom(),
                            'email' => $vente->getClient()->getEmail(),
                            'telephone' => $vente->getClient()->getTelephone(),
                            'addresse' => $vente->getClient()->getAddresse(),
                        ],
                        'date_de_vente' => $vente->getDateDeVente(),
                        'montant_total' => $vente->getMontantTotal(),
                    ],
                    'mode_de_paiement' => $encaissement->getModeDePaiement(),
                    'date' => $encaissement->getDate()->format('Y-m-d'),
                    'montant' => $encaissement->getMontant(),
                ];
            }
        }

        // Créer une réponse JSON à partir des données formatées
        return $this->json($formattedEncaissements, Response::HTTP_OK);
    }



}
