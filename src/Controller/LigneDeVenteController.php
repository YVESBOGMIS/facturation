<?php

namespace App\Controller;

use App\Entity\Encaissement;
use App\Entity\LigneDeVente;
use App\Entity\TypeDeDocumentDeVente;
use App\Entity\Vente;
use App\Repository\DocumentDeVenteRepository;
use App\Repository\VenteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\DocumentDeVente;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/api')]
class LigneDeVenteController extends AbstractController
        {
        #[Route('/lignedevente', name: 'app_ligne_de_vente', methods: ['POST'])]
        public function lignedevente(
        Request $request,
        EntityManagerInterface $entityManager,
        VenteRepository $venteRepository
        ): Response {
        $body = json_decode($request->getContent(), true); // Récupération des données

        // Récupérer les données du corps de la requête et Vérifier si 'document_vente_id' est présent et non nul
        if (isset($body['document_vente_id'])) {

        // Récupérer le document de vente à partir de 'document_vente_id'
        $documentDeVenteRequete = $entityManager->getRepository(DocumentDeVente::class)->findOneBy(['id' => $body['document_vente_id']]);
        // Récupérer la vente à partir de 'vente_id'
        $venteRequete = $venteRepository->findOneBy(['id' => $body['vente_id']]);
        } else {
        // Si 'document_vente_id' ou 'vente_id' est manquant, renvoyer une erreur
        return $this->json(['message' => 'Identifiant(s) de document de vente ou de vente manquant(s)'], Response::HTTP_BAD_REQUEST);
        }


        // Vérifions si le tableau de lignes de vente est présent dans le corps de la requête
        if (!isset($body['lignes_de_vente']) || !is_array($body['lignes_de_vente'])) {
        return $this->json(['message' => 'Aucune ligne de vente trouvée dans le corps de la requête'], Response::HTTP_BAD_REQUEST);
        }

        // Parcourir le tableau de lignes de vente
        foreach ($body['lignes_de_vente'] as $ligneData) {
        // Récupérons les données de la ligne de vente
        $description = $ligneData['description'] ?? null;
        $quantite = $ligneData['quantite'] ?? null;
        $prixUnitaire = $ligneData['prix_unitaire'] ?? null;

        // Vérifions si toutes les données sont présentes
        if (!$description || !$quantite || !$prixUnitaire || !$documentDeVenteRequete) {
        return $this->json(['message' => 'Les données de ligne de vente sont incomplètes'], Response::HTTP_BAD_REQUEST);
        }

        // Créons une nouvelle instance de LigneDeVente
        $ligneDeVente = new LigneDeVente();
        $ligneDeVente->setDescription($description);
        $ligneDeVente->setQuantite($quantite);
        $ligneDeVente->setPrixUnitaire($prixUnitaire);

        // Utiliser une nouvelle variable pour stocker le document de vente de cette ligne
        $documentDeVenteLigne = $documentDeVenteRequete;

        // Mettre le montant total de la ligne de vente
        $montantTotalLigne = $prixUnitaire * $quantite;
        $ligneDeVente->setMontantTotalL($montantTotalLigne);

        // Ajouter le montant total de la ligne à la somme des montants totaux des lignes de vente pour ce document
        $montantTotalDocument = 0;
        $montantTotalDocument += $montantTotalLigne;

        // Récupérer toutes les lignes de vente ayant le même document_de_vente_id que le document de vente en cours de traitement
        $lignesDeVente = $entityManager->getRepository(LigneDeVente::class)->findBy(['documentDeVente' => $documentDeVenteLigne]);

        // Parcourir les lignes de vente pour calculer la somme des montants totaux des lignes de vente ayant le même document vente id avant la nouvelle ligne de vente ajoutée
        foreach ($lignesDeVente as $ligne) {
        if ($ligne !== $ligneDeVente) {
        $montantTotalDocument += $ligne->getMontantTotalL();
        }
        }

        // Mettre à jour le montant total de la vente avec le montant total de la ligne de vente
        $venteRequete->setMontantTotal($montantTotalDocument);

        // Persister les changements dans la base de données
        $entityManager->persist($venteRequete);
        $entityManager->persist($ligneDeVente);
        }

        // Persister les changements dans la base de données
        $entityManager->flush();

        return $this->json(['message' => 'Lignes de vente ajoutées avec succès'], Response::HTTP_CREATED);

        }
}
