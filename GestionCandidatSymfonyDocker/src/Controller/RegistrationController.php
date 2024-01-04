<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/api/inscriptioncandidat",
 *     summary="Inscrire un nouveau candidat",
 *     description="Permet d'inscrire un nouveau candidat",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nom", "prenom", "email", "password", "adresse", "niveauEtude", "dateNaissance"},
 *             @OA\Property(property="nom", type="string", example="Dupont"),
 *             @OA\Property(property="prenom", type="string", example="Jean"),
 *             @OA\Property(property="email", type="string", format="email", example="jean.dupont@example.com"),
 *             @OA\Property(property="password", type="string", example="yourpassword"),
 *             @OA\Property(property="adresse", type="string", example="123 rue de la Paix"),
 *             @OA\Property(property="niveauEtude", type="string", example="Master en informatique"),
 *             @OA\Property(property="dateNaissance", type="string", format="date", example="1990-01-01")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Candidat inscrit avec succès"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Données invalides"
 *     )
 * )
 */
#[Route('/api', name: 'api_')]
class RegistrationController extends AbstractController
{
    #[Route('/inscriptioncandidat', name: 'inscrire_candidat', methods: ['POST'])]

    public function register(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $constraints = new Assert\Collection([
            'nom' => [new Assert\NotBlank(), new Assert\Length(['min' => 4])],
            'prenom' => [new Assert\NotBlank(), new Assert\Length(['min' => 4])],
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'password' => [new Assert\NotBlank()],
            'adresse' => [new Assert\NotBlank()],
            'niveauEtude' => [new Assert\NotBlank()],
            'dateNaissance' => [new Assert\NotBlank()],

        ]);

        $violations = $validator->validate($data, $constraints);
        if (count($violations) > 0) {
            return $this->json(['errors' => (string) $violations], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);
        $user->setEmail($data['email']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setAdresse($data['adresse']);
        $user->setNiveauEtude($data['niveauEtude']);
        $dateNaissance = new \DateTime($data['dateNaissance']);
        $user->setDateNaissance($dateNaissance);
        $user->setRoles(['ROLE_CANDIDAT']);


        $roleCandidat = $em->getRepository(Role::class)->findOneBy(['nomRole' => 'candidat']);
        if (!$roleCandidat) {

            return $this->json(['error' => 'Role candidat not found'], JsonResponse::HTTP_BAD_REQUEST);
        }


        $user->addRole($roleCandidat);

        $em->persist($user);
        $em->flush();

        return $this->json(['message' => 'Utilisateur ajouté avec succès'], JsonResponse::HTTP_CREATED);
    }



    /**
     * @OA\Post(
     *     path="/api/inscrireadmin",
     *     summary="Inscrire un nouvel administrateur",
     *     description="Permet d'inscrire un nouvel administrateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nom", "prenom", "email", "password", "adresse"},
     *             @OA\Property(property="nom", type="string", example="Leroy"),
     *             @OA\Property(property="prenom", type="string", example="Alice"),
     *             @OA\Property(property="email", type="string", format="email", example="alice.leroy@example.com"),
     *             @OA\Property(property="password", type="string", example="adminpassword"),
     *             @OA\Property(property="adresse", type="string", example="456 avenue Liberté")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Administrateur inscrit avec succès"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     )
     * )
     */


    //ladmin
    #[Route('/inscrireadmin', name: 'inscrire_admin', methods: ['POST'])]
    public function ajouterUtilisateurAdmin(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $constraints = new Assert\Collection([
            'nom' => [new Assert\NotBlank(), new Assert\Length(['min' => 2]), new Assert\Regex('/^[a-zA-Z]+$/')],
            'prenom' => [new Assert\NotBlank(), new Assert\Length(['min' => 4]), new Assert\Regex('/^[a-zA-Z]+$/')],
            'email' => [new Assert\NotBlank()],
            'adresse' => [new Assert\NotBlank()],
            'password' => [new Assert\NotBlank()],

        ]);

        $violations = $validator->validate($data, $constraints);
        if (count($violations) > 0) {
            return $this->json(['errors' => (string) $violations], JsonResponse::HTTP_BAD_REQUEST);
        }

        $roleAdmin = $em->getRepository(Role::class)->findOneBy(['nomRole' => 'admin']);
        if (!$roleAdmin) {
            return $this->json(['error' => 'Role admin non trouve'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);
        $user->setEmail($data['email']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setAdresse($data['adresse']);
        $user->setRoles(['ROLE_ADMIN']);

        $user->addRole($roleAdmin);

        $em->persist($user);
        $em->flush();

        return $this->json(['message' => 'Admin ajouté avec succès'], JsonResponse::HTTP_CREATED);
    }
}
