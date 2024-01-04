<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Candidature;
use OpenApi\Annotations as OA;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Annotation\Groups;
 /**
     * @OA\Post(
     *     path="/api/postuler",
     *     summary="Postuler à une formation",
     *     description="Permet à un utilisateur de postuler à une formation",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nomFormation"},
     *             @OA\Property(property="nomFormation", type="string", example="Nom de la Formation"),
     *             required={"formation_id"},
     *             @OA\Property(property="formation_id", type="integer", example="Id de la Formation"),
     *             required={"user_id"},
     *             @OA\Property(property="user_id", type="integer", example="Id de la user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Candidature enregistrée"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Candidature déjà existante ou données invalides"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Formation non trouvée"
     *     ),
     *     security={{ "bearerAuth":{} }}
     * )
     * 
     */
#[Route("/api",name:"api_")]
class CandidatureController extends AbstractController
{
    
   

    #[Route("/postuler", name:"postuler", methods:["POST"])]
   
    public function postuler(Request $request, EntityManagerInterface $entityManager): Response
    {
      
        // $this->denyAccessUnlessGranted('ROLE_CANDIDAT');
    
        $candidat = $this->getUser();
        $data = json_decode($request->getContent(), true);
    
        // $user = $this->getUser(); 
        $formationId = $data['formation_id'];
    

        $candidatureExistante = $entityManager->getRepository(Candidature::class)->findOneBy([
            // 'user' => $user,
            'formation' => $formationId
        ]);
    
        if ($candidatureExistante) {
            return $this->json(['message' => 'Vous avez déjà postulé à cette formation'], Response::HTTP_BAD_REQUEST);
        }


        $formation = $entityManager->getRepository(Formation::class)->findOneBy(['nomFormation' => $data['nomFormation']]);

        if (!$formation) {
            return $this->json(['message' => 'Formation non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $candidature = new Candidature();
        $candidature->setUser($candidat);
        $candidature->setFormation($formation);
        $entityManager->persist($candidature);
        $entityManager->flush();

        return $this->json(['message' => 'Candidature enregistrée'], Response::HTTP_OK);
    }
}


