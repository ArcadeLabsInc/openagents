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
            case 'claude-3-sonnet-20240229':
            case 'claude-3-haiku-20240307':
            case 'claude-3-opus-20240229':
                $client = new AnthropicAIGateway();
                $inference = $client->createStreamed([
                    'model' => $model,
                    'messages' => $messages,
                    'max_tokens' => 4096,
                    'stream_function' => $streamFunction,
                ]);
                break;
            case 'mistral-tiny':
            case 'mistral-small-latest':
            case 'mistral-medium-latest':
            case 'mistral-large-latest':
            case 'open-mixtral-8x7b':
            case 'open-mistral-7b':
                $client = new MistralAIGateway();

                $inference = $client->chat()->createStreamed([
                    'model' => $model,
                    'messages' => $messages,
                    'max_tokens' => 9024,
                    'stream_function' => $streamFunction,
                ]);
                break;
            case 'mixtral-8x7b-32768':
                $client = new GroqAIGateway();
                break;
            case 'gpt-4':
                $client = new OpenAIGateway();
                $inference = $client->stream([
                    'model' => $model,
                    'messages' => $messages,
                    'max_tokens' => 5000,
                    'stream_function' => $streamFunction,
                ]);
                break;
            case 'gpt-4-turbo-preview':
                $client = new OpenAIGateway();
                $inference = $client->stream([
                    'model' => $model,
                    'messages' => $messages,
                    'max_tokens' => 3500,
                    'stream_function' => $streamFunction,
                ]);
                break;
            case 'gpt-3.5-turbo-16k':
                $client = new OpenAIGateway();
                $inference = $client->stream([
                    'model' => $model,
                    'messages' => $messages,
                    'max_tokens' => 15024,
                    'stream_function' => $streamFunction,
                ]);
                break;
        }

        return $inference;
    }
}

function get_previous_messages(Thread $thread)
{
    return $thread->messages()
        ->orderBy('created_at', 'asc')
        ->get()
        ->map(function ($message) {
            // If model is not null, this is agent. Otherwise user
            if ($message->model !== null) {
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
