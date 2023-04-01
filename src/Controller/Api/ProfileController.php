<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use App\Traits\DocumentManagerTrait;
use Throwable;

class ProfileController extends AbstractController
{
    use DocumentManagerTrait;

    public function __construct(private Security $security) {}

    #[Route('/api/profile', name: 'api_profile_delete', methods: ['DELETE'])]
    public function delete(Request $request): JsonResponse
    {
        try {
            $this->dm->remove($this->getUser());
            $this->dm->flush();

            $this->security->logout(false);
            
            $request->getSession()->invalidate(0);

            return $this->json(['message' => "You've successfully deleted your account."]);
        }
        catch (Throwable $e) {
            $message = ($this->getParameter('kernel.environment') === 'dev') ? $e->getMessage() : 'Internal server error.';
            
            return $this->json(['error' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
