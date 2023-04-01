<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormInterface;
use App\Form\RegistrationFormType;
use App\Entity\User;
use App\Api\ApiException;
use App\Traits\DocumentManagerTrait;
use App\Traits\GetFieldFirstFormErrorMapTrait;
use App\Repository\UserRepository;
use Throwable;

class RegistrationController extends AbstractController
{
    use DocumentManagerTrait;
    use GetFieldFirstFormErrorMapTrait;

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
    ) {}

    #[Route('/api/registration', name: 'api_registration', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try {
            $content = json_decode($request->getContent(), true);
            if (!$content) {
                throw new ApiException('Wrong request body format.', Response::HTTP_BAD_REQUEST);
            }

            $user = new User();
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->submit($content);

            if ($form->isSubmitted() && $form->isValid()) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $form->get('password')->getData());
                $user->setPassword($hashedPassword);

                $this->dm->persist($user);
                $this->dm->flush();

                return $this->json(['message' => "You've registered successfully."]);
            }

            return $this->json(['errors' => $this->getFieldFirstFormErrorMap($form)], Response::HTTP_BAD_REQUEST);
        }
        catch (ApiException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode());
        }
        catch (Throwable $e) {
            $message = ($this->getParameter('kernel.environment') === 'dev') ? $e->getMessage() : 'Internal server error.';
            
            return $this->json(['error' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
