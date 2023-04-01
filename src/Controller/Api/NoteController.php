<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Form\NoteFormType;
use App\Entity\AdditionalParameter;
use App\Repository\NoteRepository;
use App\Repository\AdditionalParameterRepository;
use App\Entity\Note;
use App\Traits\DocumentManagerTrait;
use App\Traits\GetFieldFirstFormErrorMapTrait;
use App\Api\ApiException;
use Throwable;

class NoteController extends AbstractController
{
    use DocumentManagerTrait;
    use GetFieldFirstFormErrorMapTrait;

    public function __construct(
        private NoteRepository $noteRepository,
        private AdditionalParameterRepository $additionalParameterRepository,
    ) {}

    #[Route('/api/note', name: 'api_note_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json(['notes' => $this->noteRepository->findBy(['user' => $this->getUser()])]);
    }

    #[Route('/api/note', name: 'api_note_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return $this->wrapInTryCatchAndGetJsonResponse(function () use ($request) {
            $content = $this->getJsonContentOrThrowApiException($request);

            $note = new Note();
            $form = $this->createForm(NoteFormType::class, $note);
            $form->submit($content);

            if ($form->isSubmitted() && $form->isValid()) {
                $note->setUser($this->getUser());

                $this->dm->persist($note);
                $this->dm->flush();

                return [['message' => "You've added new note successfully.", 'note' => $note], Response::HTTP_OK];
            }

            return [['errors' => $this->getFieldFirstFormErrorMap($form)], Response::HTTP_BAD_REQUEST];
        });
    }

    #[Route('/api/note/{id}', name: 'api_note_update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        return $this->wrapInTryCatchAndGetJsonResponse(function () use ($id, $request) {
            $note = $this->getNoteIfChangePermittedOrThrowApiException($id);
            $content = $this->getJsonContentOrThrowApiException($request);

            $form = $this->createForm(NoteFormType::class, $note);
            $form->submit($content);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->dm->persist($note);
                $this->dm->flush();

                // https://github.com/doctrine/mongodb-odm/issues/698
                // Orphan removal on elements detached from collection
                $this->additionalParameterRepository->removeOrphaned();

                return [['message' => "You've updated note successfully.", 'note' => $note], Response::HTTP_OK];
            }

            return [['errors' => $this->getFieldFirstFormErrorMap($form)], Response::HTTP_BAD_REQUEST];
        });
    }

    #[Route('/api/note/{id}', name: 'api_note_delete', methods: ['DELETE'])]
    public function delete(string $id, Request $request): JsonResponse
    {
        return $this->wrapInTryCatchAndGetJsonResponse(function () use ($id, $request) {
            $note = $this->getNoteIfChangePermittedOrThrowApiException($id);

            $this->dm->remove($note);
            $this->dm->flush();

            return [['message' => "You've deleted note successfully."], Response::HTTP_OK];
        });
    }

    /**
     * @param callable - returns $this->json's args or throws ApiException / Throwable
     */
    private function wrapInTryCatchAndGetJsonResponse(callable $action): JsonResponse
    {
        try {
            return $this->json(...$action());
        }
        catch (ApiException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode());
        }
        catch (Throwable $e) {
            $message = ($this->getParameter('kernel.environment') === 'dev') ? $e->getMessage() : 'Internal server error.';
            
            return $this->json(['error' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @throws ApiException
     */
    private function getNoteIfChangePermittedOrThrowApiException(string $id): Note
    {
        $note = $this->noteRepository->find($id);
        if (!$note) {
            throw new ApiException('Not found.', Response::HTTP_NOT_FOUND);
        }

        if ($note->getUser()->getId() !== $this->getUser()->getId()) {
            throw new ApiException('Not found.', Response::HTTP_NOT_FOUND);
        }

        return $note;
    }

    /**
     * @throws ApiException
     */
    private function getJsonContentOrThrowApiException(Request $request): array
    {
        $content = json_decode($request->getContent(), true);
        if ($content) {
            return $content;
        }
        
        throw new ApiException('Wrong JSON format.', Response::HTTP_BAD_REQUEST);
    }
}
