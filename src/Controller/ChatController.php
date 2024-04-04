<?php

namespace App\Controller;

use App\Service\OpenAiService;
use OpenAI;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ChatController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(Request $request): Response
    {

        $response = $request->query->get('response');

        return $this->render('chat/index.html.twig', [
            'response' => $response,
        ]);
    }

    #[Route('/chat', name: 'app_chat')]
    public function chat(Request $request)
    {
        $response = OpenAiService::chat($request->request->get('message'));

        return $this->redirectToRoute('app_index', ['response' => $response]);
    }
}
