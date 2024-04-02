<?php

namespace App\AI;

use App\Models\Thread;

class SimpleInferencer
{
    public static function inference(string $prompt, string $model, Thread $thread, callable $streamFunction): string
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a helpful assistant.',
                // 'content' => 'You are a helpful assistant on OpenAgents.com. Answer the inquiry from the user.',
            ],
            ...get_previous_messages($thread),
            [
                'role' => 'user',
                'content' => $prompt,
            ],
        ];

        // switch model
        switch ($model) {
            case 'mistral-large-latest':
                $client = new MistralAIGateway();
                break;
            case 'mixtral-8x7b-32768':
                $client = new GroqAIGateway();
                break;
        }

        return $client->chat()->createStreamed([
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => 9024,
            'stream_function' => $streamFunction,
        ]);
    }
}

function get_previous_messages(Thread $thread)
{
    return $thread->messages()
        ->orderBy('created_at', 'asc')
        ->get()
        ->map(function ($message) {
            // If agent_id is not null, this is agent. Otherwise user
            if ($message->agent_id) {
                $role = 'assistant';
            } else {
                $role = 'user';
            }

            // Check if message content starts with "data:image/" -- but use actual method
            if (strtolower(substr($message->body, 0, 11)) === 'data:image/') {
                $content = '<image>';
            } else {
                $content = $message->body;
            }

            return [
                'role' => $role,
                'content' => $content,
            ];
        })
        ->toArray();
}
