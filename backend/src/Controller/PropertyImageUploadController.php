<?php

namespace App\Controller;

use App\Entity\Property;
use App\Entity\PropertyImage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PropertyImageUploadController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SluggerInterface $slugger,
    ) {
    }

    #[Route('/api/properties/{id}/images', name: 'api_property_images_upload', methods: ['POST'])]
    public function upload(string $id, Request $request): JsonResponse
    {
        // Get the property
        $property = $this->entityManager->getRepository(Property::class)->find($id);
        
        if (!$property) {
            return $this->json(['error' => 'Property not found'], Response::HTTP_NOT_FOUND);
        }

        // Check if user owns this property
        if ($property->getOwner() !== $this->getUser()) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        // Get uploaded files
        $files = $request->files->get('images', []);
        
        if (empty($files)) {
            return $this->json(['error' => 'No images provided'], Response::HTTP_BAD_REQUEST);
        }

        // Ensure files is an array
        if (!is_array($files)) {
            $files = [$files];
        }

        $uploadedImages = [];
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/properties';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($files as $index => $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            // Validate file
            $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                continue;
            }

            // Generate unique filename
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

            try {
                $file->move($uploadDir, $newFilename);
                
                // Create PropertyImage entity
                $propertyImage = new PropertyImage();
                $propertyImage->setProperty($property);
                $propertyImage->setUrl('/uploads/properties/' . $newFilename);
                $propertyImage->setDisplayOrder($index);
                
                $this->entityManager->persist($propertyImage);
                
                $uploadedImages[] = [
                    'id' => $propertyImage->getId(),
                    'url' => '/uploads/properties/' . $newFilename,
                    'displayOrder' => $index,
                ];
            } catch (FileException $e) {
                // Handle error
                continue;
            }
        }

        $this->entityManager->flush();

        return $this->json([
            'message' => 'Images uploaded successfully',
            'images' => $uploadedImages,
        ], Response::HTTP_CREATED);
    }
}
