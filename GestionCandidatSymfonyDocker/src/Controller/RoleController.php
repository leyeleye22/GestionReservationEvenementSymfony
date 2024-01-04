<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Role;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
//creationRole
class RoleController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/role/ajouter', name: 'ajouter_role', methods: ['POST'])]
    public function ajouterRole(Request $request, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        $jsonData = $request->getContent();

        try {

            $role = $serializer->deserialize($jsonData, Role::class, 'json');
        } catch (\Exception $e) {
            return $this->json(['message' => 'Invalid JSON: ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        // Valider l'objet Role
        $errors = $validator->validate($role);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        // Enregistrer le nouveau rôle
        $this->entityManager->persist($role);
        $this->entityManager->flush();

        // Réponse en cas de succès
        return $this->json(['message' => 'Rôle ajouté avec succès', 'role' => $role], Response::HTTP_CREATED);
    }
}
