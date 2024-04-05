<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Service\OpenAiService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ChatController extends AbstractController
{

    public function __construct(
        private MessageRepository $messageRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('chat/index.html.twig', []);
    }

    #[Route('/getmessages', name: 'app_get_messages')]
    public function getmessages(): Response
    {
        $messages = $this->messageRepository->findAll();

        return $this->json([
            'messages' => $messages
        ]);
    }

    #[Route('/chat', name: 'app_chat')]
    public function chat(Request $request)
    {
        $content = json_decode($request->getContent());

        $messageFromUser = new Message();
        $messageFromUser->setContent($content);
        $messageFromUser->setRole('user');
        $this->entityManager->persist($messageFromUser);
        $this->entityManager->flush();

        $messages = $this->messageRepository->findAll();
        $response = OpenAiService::chat($messages);

        $responseFromAi = new Message();
        $responseFromAi->setContent($response);
        $responseFromAi->setRole('assistant');
        $this->entityManager->persist($responseFromAi);
        $this->entityManager->flush();

        return $this->json('message sent');
    }

    #[Route('/clear', name: 'app_message_clear')]
    public function clearChat()
    {
        $messages = $this->messageRepository->findAll();

        foreach ($messages as $message) {
            $this->entityManager->remove($message);
        }

        $this->entityManager->flush();

        return $this->json('message cleared');
    }
}
