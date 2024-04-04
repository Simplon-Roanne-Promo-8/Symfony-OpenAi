<?php

namespace App\Service;

use OpenAI;

class OpenAiService
{
    public static function chat(array $messages)
    {

        $client = OpenAI::client("key");

        $messagesToOpenAi = [];
        foreach ($messages as $message) {
            $messagesToOpenAi[] = ['role' => $message->getRole(), 'content' => $message->getContent()];
        }

        $result = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messagesToOpenAi,
        ]);

        $response = $result->choices[0]->message->content;
        return $response;
    }
}
