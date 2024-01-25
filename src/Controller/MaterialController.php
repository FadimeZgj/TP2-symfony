<?php

namespace App\Controller;

use App\Entity\Material;
use OpenApi\Attributes as OA;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class MaterialController extends AbstractController
{
    #[Route('/materials', name: 'app_materials', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne tous les Matériaux',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['pen:read']))
        )
    )]
    #[OA\Tag(name: 'Matériaux')]
    #[Security(name: 'Bearer')]
    public function index(MaterialRepository $materialRepository): JsonResponse
    {
        $materials = $materialRepository->findAll();

        return $this->json([
            'materials' => $materials,
        ], context: [
            'groups' => ['pen:read']
        ]);
    }

    #[Route('/material/{id}', name: 'app_material_get', methods: ['GET'])]
    #[OA\Tag(name: 'Matériaux')]
    public function get(Material $material): JsonResponse
    {
        return $this->json($material, context: [
            'groups' => ['pen:read'],
        ]);
    }

    #[Route('/materials', name: 'app_material_add', methods: ['POST'])]
    #[OA\Tag(name: 'Matériaux')]
    public function add(
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $material = new Material();
            $material->setName($data['name']);


            $em->persist($material);
            $em->flush();

            return $this->json($material, context: [
                'groups' => ['pen:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/material/{id}', name: 'app_material_update', methods: ['PUT', 'PATCH'])]
    #[OA\Tag(name: 'Matériaux')]
    public function update(
        Material $material,
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $material->setName($data['name']);

            $em->persist($material);
            $em->flush();

            return $this->json($material, context: [
                'groups' => ['pen:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/material/{id}', name: 'app_material_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Matériaux')]
    public function delete(Material $material, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($material);
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Matériel supprimé'
        ]);
    }
}
