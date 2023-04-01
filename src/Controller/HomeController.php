<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\NoteFormType;
use App\Entity\Note;
use App\Entity\AdditionalParameter;

class HomeController extends AbstractController
{
    #[Route('/{first?}', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'sessionExpiry' => ($this->getUser()) ? (time() + 1200) : '',
        ]);
    }
}
