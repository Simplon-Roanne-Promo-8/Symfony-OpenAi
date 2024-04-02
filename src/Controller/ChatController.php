<?php

namespace App\Controller;

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

        $client = OpenAI::client('key');

        $message = $request->request->get('message');

        $result = $client->chat()->create([
            'model' => 'gpt-3.5-turbo-0125',
            'messages' => [
                ['role' => 'system', 'content' => 'Répond en français et comme un marseillais'],
                ['role' => 'user', 'content' => $message],
            ],
        ]);

        $response = $result->choices[0]->message->content;
        return $this->redirectToRoute('app_index', ['response' => $response]);
    }
}
