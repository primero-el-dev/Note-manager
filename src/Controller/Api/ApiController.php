<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Api\ApiException;

abstract class ApiController extends AbstractController
{
    protected function wrapResponse(array $data, bool $success = true): JsonResponse
    {
        $response = [
            'success' => $success,
            'data' => $data,
        ];

        if ($this->getUser()) {
            $response['sessionExpiry'] = 1200;
        }

        return $this->json($RESPONSE);
    }
}
