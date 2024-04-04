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
        $messages = $this->messageRepository->findAll();

        return $this->render('chat/index.html.twig', [
            'messages' => $messages
        ]);
    }

    #[Route('/chat', name: 'app_chat')]
    public function chat(Request $request)
    {
        $messageFromUser = new Message();
        $messageFromUser->setContent($request->request->get('message'));
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

        return $this->redirectToRoute('app_index', []);
    }

    #[Route('/clear', name: 'app_message_clear')]
    public function clearChat()
    {
        $messages = $this->messageRepository->findAll();

        foreach ($messages as $message) {
            $this->entityManager->remove($message);
        }

        $this->entityManager->flush();

        return $this->redirectToRoute('app_index', []);
    }

    #[Route('/testAjax', name: 'app_ajax_test')]
    public function testAjax()
    {
        $toto = "toto";

        return $this->json([
            'toto' => $toto
        ]);
    }
}
