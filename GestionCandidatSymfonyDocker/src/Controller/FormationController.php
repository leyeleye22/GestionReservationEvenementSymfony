<?php

namespace App\Controller;
use App\Entity\Formation;
use App\Entity\Candidature;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;
/**
 * @OA\Post(
 *     path="/api/formation",
 *     summary="Ajouter une nouvelle formation",
 *     description="Permet à un administrateur d'ajouter une nouvelle formation",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nomFormation", "description", "dateDebut", "durerFormation", "niveau", "placeDisponible", "domaineFormation"},
 *             @OA\Property(property="nomFormation", type="string", example="Nom de la Formation"),
 *             @OA\Property(property="description", type="string", example="Description de la Formation"),
 *             @OA\Property(property="dateDebut", type="datetime", format="date", example="2024-01-01"),
 *             @OA\Property(property="durerFormation", type="string", example="Débutant"),
 *             @OA\Property(property="niveau", type="string", example="Débutant"),
 *             @OA\Property(property="placeDisponible", type="integer", example=20),
 *             @OA\Property(property="domaineFormation", type="string", example="Informatique")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Formation créée avec succès"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Données invalides"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès refusé"
 *     ),
 * )
 */
#[Route('/api', name: 'api_')]
class FormationController extends AbstractController
{
    // #[IsGranted('ROLE_ADMIN')]
    #[Route('/formation', name: 'app_api_formation', methods:['POST'])]
   
    public function add(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager): Response
    {
        // if (!$this->isGranted('ROLE_ADMIN')) {
        //     return $this->json(['message' => 'Accès refusé'], Response::HTTP_FORBIDDEN);
        // }
    
        $data = $request->getContent();
        $formation = $serializer->deserialize($data, Formation::class, 'json');
    
        $errors = $validator->validate($formation);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $violation) {
                $errorMessages[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }
    
        $entityManager->persist($formation);
        $entityManager->flush();
    
        return $this->json($formation, Response::HTTP_CREATED);
    }
    

 /**
     * @OA\Delete(
     *     path="/api/formation/{id}",
     *     summary="Supprimer une formation",
     *     description="Permet à un administrateur de supprimer une formation existante",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Formation supprimée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Formation non trouvée"
     *     ),
     *     
     * )
     */

  
#[Route("/formation/{id}", name:"delete_formation", methods:["DELETE"])]
#[IsGranted("ROLE_ADMIN")]
public function delete(EntityManagerInterface $entityManager, $id): Response
{
    $formation = $entityManager->getRepository(Formation::class)->find($id);

    if (!$formation) {
        return $this->json(['message' => 'Formation non trouvée'], Response::HTTP_NOT_FOUND);
    }

    $entityManager->remove($formation);
    $entityManager->flush();

    return $this->json(['message' => 'Formation supprimée avec succès'], Response::HTTP_OK);
}
  /**
     * @OA\Put(
     *     path="/api/formation/{id}",
     *     summary="Mettre à jour une formation",
     *     description="Permet à un administrateur de mettre à jour les informations d'une formation",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     * @OA\JsonContent(
     *             required={"nomFormation"},
     *             @OA\Property(property="nomFormation", type="string", example="Nom de la Formation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Formation mise à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Formation non trouvée"
     *     ),
     *     
     * )
     */


#[Route("/formation/{id}", name:"edit_formation", methods:["PUT"])]
#[IsGranted("ROLE_ADMIN")]
public function edit(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, $id): Response
{
    $formation = $entityManager->getRepository(Formation::class)->find($id);

    if (!$formation) {
        return $this->json(['message' => 'Formation non trouvée'], Response::HTTP_NOT_FOUND);
    }

    $data = $request->getContent();
    $serializer->deserialize($data, Formation::class, 'json', ['object_to_populate' => $formation]);

    $errors = $validator->validate($formation);
    if (count($errors) > 0) {
        $errorMessages = [];
        foreach ($errors as $violation) {
            $errorMessages[$violation->getPropertyPath()] = $violation->getMessage();
        }
        return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
    }

    $entityManager->flush();

    return $this->json($formation, Response::HTTP_OK);
}


/**
     * @OA\Put(
     *     path="/candidature/{id}/accept",
     *     summary="Accepter une candidature",
     *     description="Permet à un administrateur d'accepter une candidature",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la candidature",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Candidature acceptée avec succès"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé"
     *     ),
     *    
     * )
     */



 /**
     * @Route("/candidature/{id}/accept", name="api_accept_candidature", methods={"PUT"})
     */
    public function acceptCandidature(Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        // Assurez-vous que l'utilisateur est connecté et est un administrateur
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Mettez à jour le statut de la candidature pour l'accepter
        $candidature->setStatus('accepte');

        $entityManager->flush();

        return $this->json(['message' => 'Candidature acceptée avec succès'], Response::HTTP_OK);
    }



     /**
     * @OA\Put(
     *     path="/candidature/{id}/reject",
     *     summary="Rejeter une candidature",
     *     description="Permet à un administrateur de rejeter une candidature",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la candidature",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Candidature rejetée avec succès"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé"
     *     ),
     *   
     * )
     */
    #[Route("/candidature/{id}/reject", name:"api_reject_candidature", methods:["PUT"])]
    public function rejectCandidature(Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        
        $candidature->setStatus('refuser');

        $entityManager->flush();

        return $this->json(['message' => 'Candidature refusée avec succès'], Response::HTTP_OK);
    }


    /**
     * @OA\Get(
     *     path="/candidatures",
     *     summary="Lister toutes les candidatures",
     *     description="Affiche une liste de toutes les candidatures",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des candidatures récupérée avec succès"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé"
     *     ),
     *   
     * )
     */

    #[Route('/candidatures', name: 'app_api_candidatures', methods: ['GET'])]
public function getCandidatures(EntityManagerInterface $entityManager): JsonResponse
{
    // Assurez-vous que l'utilisateur est connecté et est un administrateur
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    // Récupérez toutes les candidatures depuis la base de données
    $candidatures = $entityManager->getRepository(Candidature::class)->findAll();

    // Transformez les objets Candidature en tableau pour la réponse JSON
    $formattedCandidatures = [];
    foreach ($candidatures as $candidature) {
        $formattedCandidatures[] = [
            'id' => $candidature->getId(),
            'status' => $candidature->getStatus(),
            'formation' => [
                'id' => $candidature->getFormation()->getId(),
                'nomFormation' => $candidature->getFormation()->getNomFormation(),
                // Ajoutez d'autres propriétés de la formation si nécessaire
            ],
            'user' => [
                'id' => $candidature->getUser()->getId(),
                'nom' => $candidature->getUser()->getNom(),
                'prenom' => $candidature->getUser()->getPrenom(),
                // Ajoutez d'autres propriétés de l'utilisateur si nécessaire
            ],
        ];
    }

    return $this->json($formattedCandidatures);
}





private function formatCandidatures(array $candidatures): array
{
    $formattedCandidatures = [];
    foreach ($candidatures as $candidature) {
        $formattedCandidatures[] = [
            'id' => $candidature->getId(),
            'status' => $candidature->getStatus(),
           
        ];
    }
    return $formattedCandidatures;
}
/**
     * @OA\Get(
     *     path="/candidatures/acceptees",
     *     summary="Lister les candidatures acceptées",
     *     description="Affiche une liste des candidatures acceptées",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des candidatures acceptées récupérée avec succès"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé"
     *     ),
     *     
     * )
     */

#[Route('/candidatures/acceptees', name: 'app_api_candidatures_acceptees', methods: ['GET'])]
public function getCandidaturesAcceptees(EntityManagerInterface $entityManager): JsonResponse
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $candidaturesAcceptees = $entityManager->getRepository(Candidature::class)->findBy(['status' => 'accepter']);

    $formattedCandidatures = $this->formatCandidatures($candidaturesAcceptees);

    return $this->json($formattedCandidatures);
}


   /**
     * @OA\Get(
     *     path="/candidatures/refusees",
     *     summary="Lister les candidatures refusées",
     *     description="Affiche une liste des candidatures refusées",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des candidatures refusées récupérée avec succès"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé"
     *     ),
     *     
     * )
     */

#[Route('/candidatures/refusees', name: 'app_api_candidatures_refusees', methods: ['GET'])]
public function getCandidaturesRefusees(EntityManagerInterface $entityManager): JsonResponse
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $candidaturesRefusees = $entityManager->getRepository(Candidature::class)->findBy(['status' => 'refuser']);

    
    $formattedCandidatures = $this->formatCandidatures($candidaturesRefusees);

    return $this->json($formattedCandidatures);
}
}
