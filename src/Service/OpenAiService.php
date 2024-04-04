<?php

namespace App\Service;

use OpenAI;

class OpenAiService
{
    public static function chat(string $message)
    {
        $client = OpenAI::client("key");

        $result = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => "parle moi comme un marseillais qui adore le pastis et qui utilise beaucoup d'argot"],
                ['role' => 'user', 'content' => $message],
            ],
        ]);

        $response = $result->choices[0]->message->content;
        return $response;
    }
}
