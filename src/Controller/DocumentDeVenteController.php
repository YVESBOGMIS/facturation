<?php

namespace App\Controller;

use App\Entity\Encaissement;
use App\Entity\EtatDeVente;
use App\Entity\LigneDeVente;
use App\Entity\TypeDeDocumentDeVente;
use App\Entity\Vente;
use App\Repository\DocumentDeVenteRepository;
use App\Repository\LigneDeVenteRepository;
use App\Repository\VenteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\DocumentDeVente;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/api/ventedoc')]
class DocumentDeVenteController extends AbstractController
{

    #[Route('/upload', name: 'api.upload', methods: ['POST'])]
    public function upload(Request $request, EntityManagerInterface $entityManager,
                           VenteRepository $venteRepository, SluggerInterface $slugger): Response
    {

        //titre: Charger les documents

        // Récupérons le fichier téléversé
        $uploadedFile = $request->files->get('document_vente');

        // Vérifions si un fichier a été téléversé
        if (!$uploadedFile) {
            return $this->json(['message' => 'Aucun fichier téléversé'], Response::HTTP_BAD_REQUEST);
        }

        // Générons un nom de fichier unique avec une extension
        $fileName = md5(uniqid()) . '.' . $uploadedFile->getClientOriginalExtension();

        // Déplacer le fichier téléversé vers le répertoire de destination
        $uploadedFile->move($this->getParameter('upload_directory'), $fileName);

        // Récupérons les données de la requête venant du formulaire
        $venteId = $request->request->get('vente_id');

        // Vérifions si l'ID de la vente est présent
        if (!$venteId) {
            return $this->json(['message' => 'Vente ID manquant'], Response::HTTP_BAD_REQUEST);
        }

        // Récupérons la vente par son ID
        $vente = $venteRepository->findOneBy(['id' => $venteId, 'user' => $this->getUser()]);

        // Vérifions si la vente existe
        if (!$vente) {
            return $this->json(['message' => 'Vente non trouvée'], Response::HTTP_NOT_FOUND);
        }

        // Sélectionner le type de document de vente
        $typeDocumentVenteId = $request->request->get('type_document_vente_id');
        if (!$typeDocumentVenteId) {
            return $this->json(['message' => 'Type de document de vente ID manquant'], Response::HTTP_BAD_REQUEST);
        }
        $typeDocumentVente = $entityManager->getRepository(TypeDeDocumentDeVente::class)->find($typeDocumentVenteId);
        if (!$typeDocumentVente) {
            return $this->json(['message' => 'Type de document de vente non trouvé'], Response::HTTP_NOT_FOUND);
        }


        // Créons une nouvelle instance de DocumentDeVente
        $documentDeVente = new DocumentDeVente();
        $documentDeVente->setDocumentDeVente($fileName);

        // Associer la vente au document de vente
        $documentDeVente->setVente($vente);

        // Associer le type de document de vente
        $documentDeVente->setTypeDocumentVente($typeDocumentVente);

        // Enregistrons le document de vente en base de données
        $entityManager->persist($documentDeVente);
        $entityManager->flush();


        // Récupérer le dernier document téléchargé associé à une vente
        $lastDocument = $entityManager->getRepository(DocumentDeVente::class)
            ->findOneBy(['vente' => $venteId], ['id' => 'DESC']);

        if ($lastDocument) {
            // Obtenir le type de document associé
            $typeDocument = $lastDocument->getTypeDocumentVente();

            // Récupérer le slug du type de document
            $typeDocumentSlug = $typeDocument->getSlug();

            // Récupérer l'état de vente correspondant au slug du type de document
            $etatVente = $entityManager->getRepository(EtatDeVente::class)->findOneBy(['Slug' => $typeDocumentSlug]);

            if ($etatVente) {
                // Mettre à jour l'état de vente de la vente
                $vente->setEtatDeVente($etatVente);

                // Enregistrer les modifications dans la base de données
                $entityManager->persist($vente);
                $entityManager->flush();


            } else {
                // Gérer le cas où l'état de vente correspondant n'a pas été trouvé
                return $this->json(['message' => 'État de vente correspondant non trouvé pour le type de document'], Response::HTTP_NOT_FOUND);
            }
        } else {
            // Gérer le cas où aucun document de vente n'a été trouvé pour la vente
            return $this->json(['message' => 'Aucun document de vente trouvé pour la vente'], Response::HTTP_NOT_FOUND);
        }


        $body = json_decode($request->getContent(), true); // Récupération des données

        $documentDeVente->setVente($vente);

        $documentDeVente->setVente($vente); // Remplacez $venteRequete par $vente

// Vérifier si la vente est trouvée
        if (!$vente) {
            return $this->json(['message' => 'Vente non trouvée'], Response::HTTP_BAD_REQUEST);
        }

        $venteRequete = $vente; // Déclarer et initialiser $venteRequete avec la valeur de $vente

        // Récupérer les données du formulaire
        $body = $request->request->all();

// Vérifier si les données des lignes de vente sont présentes et valides
        if (!isset($body['lignes_de_vente']) || !is_array($body['lignes_de_vente'])) {
            return $this->json(['message' => 'Données de lignes de vente manquantes ou invalides'], Response::HTTP_BAD_REQUEST);
        }

// Parcourir les données des lignes de vente
        foreach ($body['lignes_de_vente'] as $ligneData) {
            // Vérifier si les données de chaque ligne sont complètes
            if (!isset($ligneData['description']) || !isset($ligneData['quantite']) || !isset($ligneData['prix_unitaire'])) {
                return $this->json(['message' => 'Données de ligne de vente incomplètes'], Response::HTTP_BAD_REQUEST);
            }
        }

        $montantTotalDocument = 0;

        // Parcourir le tableau de lignes de vente
        foreach ($body['lignes_de_vente'] as $ligneData) {
            // Récupérons les données de la ligne de vente
            $description = $ligneData['description'] ?? null;
            $quantite = intval($ligneData['quantite'] ?? null);
            $prixUnitaire = intval($ligneData['prix_unitaire'] ?? null);

            // Vérifions si toutes les données sont présentes
            if (!$description || !$quantite || !$prixUnitaire ) {
                return $this->json(['message' => 'Les données de ligne de vente sont incomplètes'], Response::HTTP_BAD_REQUEST);
            }

            // Créons une nouvelle instance de LigneDeVente
            $ligneDeVente = new LigneDeVente();
            $ligneDeVente->setDescription($description);
            $ligneDeVente->setQuantite($quantite);
            $ligneDeVente->setPrixUnitaire($prixUnitaire);
            $ligneDeVente->setDocumentDeVente($documentDeVente);

            // Utiliser une nouvelle variable pour stocker le document de vente de cette ligne
            $documentDeVenteLigne = 0;

            // Mettre le montant total de la ligne de vente
            $montantTotalLigne = $prixUnitaire * $quantite;
            $ligneDeVente->setMontantTotalL($montantTotalLigne);

            // Ajouter le montant total de la ligne à la somme des montants totaux des lignes de vente pour ce document

            $montantTotalDocument += $montantTotalLigne;

            // Récupérer toutes les lignes de vente ayant le même document_de_vente_id que le document de vente en cours de traitement
            $lignesDeVente = $entityManager->getRepository(LigneDeVente::class)->findBy(['documentDeVente' => $documentDeVenteLigne]);



            // Persister les changements dans la base de données
            $entityManager->persist($ligneDeVente);
        }

        // Mettre à jour le montant total de la vente avec le montant total de la ligne de vente
        $venteRequete->setMontantTotal($montantTotalDocument);

        // Persister les changements dans la base de données
        $entityManager->flush();



        return $this->json(['message' => 'Opération document vente effectuée avec succès'],
            Response::HTTP_CREATED);





    }


    #[Route('/upload/ligne/list', name: 'api.list.ligne', methods: ['GET'])]
    public function listLigneDeVente(  EntityManagerInterface      $entityManager,LigneDeVenteRepository $ligneDeVenteRepository): Response
    {
        // Récupérer tous les documents de vente disponibles
        $documentsDeVente =$entityManager->getRepository(DocumentDeVente::class)->findAll();

        // Initialiser le tableau pour stocker les données formatées de toutes les lignes de vente
        $formattedLignesDeVente = [];

        foreach ($documentsDeVente as $documentDeVente) {
            // Récupérer les lignes de vente pour ce document de vente
            $lignesDeVente = $ligneDeVenteRepository->findAll();

            foreach ($lignesDeVente as $ligneDeVente) {
                // Ajouter les données formatées de la ligne de vente au tableau
                $formattedLignesDeVente[] = [
                    'id' => $ligneDeVente->getId(),
                    'description' => $ligneDeVente->getDescription(),
                    'prix_unitaire' => $ligneDeVente->getPrixUnitaire(),
                    'quantite' => $ligneDeVente->getQuantite(),
                    'montant_total' => $ligneDeVente->getMontantTotalL(),
                    'document_de_vente_id' => [
                        'id' => $documentDeVente->getId(),
                        'vente_id' => $documentDeVente->getVente()->getId(),
                        'document_de_vente' =>$documentDeVente->getDocumentDeVente(),
                        'type_document_vente_id' => $documentDeVente->getTypeDocumentVente()->getId()
                    ],
                ];
            }
        }

        // Créer une réponse JSON à partir des données formatées
        return $this->json($formattedLignesDeVente, Response::HTTP_OK);
    }



    #[Route('/upload/etat/list', name: 'api.list.etat', methods: ['GET'])]
    public function listetatdevente(  EntityManagerInterface      $entityManager,): Response
    {
        // Récupérer tous les documents de vente disponibles
        $etatdeventes =$entityManager->getRepository(EtatDeVente::class)->findAll();

        // Initialiser le tableau pour stocker les données formatées de toutes les lignes de vente
        $formattedetat = [];

        foreach ($etatdeventes  as $etatdevente) {

                // Ajouter les données formatées de la ligne de vente au tableau
            $formattedetat[] = [
                    'id' => $etatdevente->getId(),
                    'nom' => $etatdevente->getNom(),
                    'Slug' => $etatdevente->getSlug()
                ];

            }


        // Créer une réponse JSON à partir des données formatées
        return $this->json($formattedetat, Response::HTTP_OK);
    }


    #[Route('/upload/typedoc/list', name: 'api.list.typedoc', methods: ['GET'])]
    public function listtypesdoc(EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les états de vente disponibles
        $typesdocs = $entityManager->getRepository(TypeDeDocumentDeVente::class)->findAll();

        // Initialiser le tableau pour stocker les données formatées de tous les états de vente
        $formattedtypedoc = [];

        foreach ($typesdocs as $typesdoc) {
            // Ajouter les données formatées de l'état de vente au tableau
            $formattedtypedoc[] = [
                'id' => $typesdoc->getId(),
                'nom' => $typesdoc->getNom(),
                'slug' => $typesdoc->getSlug(),
            ];
        }

        // Créer une réponse JSON à partir des données formatées
        return $this->json($formattedtypedoc, Response::HTTP_OK);

}
}