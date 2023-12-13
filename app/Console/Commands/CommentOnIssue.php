<?php

namespace App\Console\Commands;

use App\Services\OpenAIGateway;
use App\Services\Searcher;
use GitHub;
use Illuminate\Console\Command;

class CommentOnIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comment {issuenum}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Faerie will comment on a given GitHub issue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        dd("test");
        // Grab the issue number from the command line
        $issueNum = $this->argument('issuenum');

        // Grab the issue body and comments from GitHub
        $response = GitHub::issues()->show('OpenAgentsInc', 'openagents', $issueNum);
        $commentsResponse = GitHub::api('issue')->comments()->all('OpenAgentsInc', 'openagents', 1);
        $body = $response['body'];
        $title = $response['title'];

        // Format the issue and comments as messages
        $userAndAssistantMessages = $this->formatIssueAndCommentsAsMessages($body, $commentsResponse);

        // Build the context from a summary of the messages passed as query to a similarity search
        $context = $this->buildContextFrom($userAndAssistantMessages);

        // Build the prompt from the context and the issue title
        $commitPrompt = "You are Faerie, an AI agent specialized in writing & analyzing code.

  You have been summoned to solve an issue titled `" . $title . "`

  You cannot speak English, but you can write code. You respond only with code.

  The issue body is the first message below.

  For additional context, consult the following code snippets:
  ---
  " . $context . "
  ---

  Remember, respond ONLY with a unified code diff. Do not include any other text. Do not use natural language. Only code.";

        $systemMessages = [
            ['role' => 'system', 'content' => $commitPrompt],
        ];
        $messages = array_merge(
            $systemMessages,
            $userAndAssistantMessages,
            [
            ['role' => 'user', 'content' => 'Please respond ONLY with a unified code diff we can submit directly to GitHub. Do not include any other text. Do not use natural language. Only the code diff.'],
        ]
        );

        $gateway = new OpenAIGateway();
        $response = $gateway->makeChatCompletion([
          'model' => 'gpt-4',
          'messages' => $messages,
        ]);
        $commit = $response['choices'][0]['message']['content'];
        $this->info($commit);




        dd();

        // Build the prompt from the context and the issue title
        $commentPrompt = "You are Faerie, an AI agent specialized in writing & analyzing code.

  You have been summoned to OpenAgentsInc/openagents issue #1.

  The issue is titled `" . $title . "`

  The issue body is the first message below.

  For additional context, consult the following code snippets:
  ---
  " . $context . "
  ---

  Please respond with the comment you would like to add to the issue. Write like a senior developer would write; don't introduce yourself or use flowery text or a closing signature.";

        // Build the messages array
        $systemMessages = [
          ['role' => 'system', 'content' => $commentPrompt],
        ];
        $messages = array_merge($systemMessages, $userAndAssistantMessages);

        // Make the chat completion to generate the comment
        $response = $gateway->makeChatCompletion([
          'model' => 'gpt-4',
          'messages' => $messages,
        ]);
        $comment = $response['choices'][0]['message']['content'];
        $this->info($comment);

        // Post the comment to GitHub
        $this->info("POSTING...");
        GitHub::api('issue')->comments()->create('OpenAgentsInc', 'openagents', $issueNum, array('body' => $comment));
        $this->info("DONE!");
    }

    /**
     * Format the issue and comments as messages.
     *
     * @param string $issueBody
     * @param array $commentsResponse
     * @return array
     */
    private function formatIssueAndCommentsAsMessages(string $issueBody, array $commentsResponse): array
    {
        $messages = [];

        $messages[] = ['role' => 'user', 'content' => $issueBody];

        foreach ($commentsResponse as $comment) {
            $role = $comment['user']['login'] === 'FaerieAI' ? 'assistant' : 'user';
            $messages[] = ['role' => $role, 'content' => $comment['body']];
        }

        return $messages;
    }

    private function buildContextFrom(array $messages): string
    {
        $queryInput = '';

        foreach ($messages as $message) {
            $queryInput .= $message['content'] . "\n---\n";
        }

        // Let's do one LLM call to summarize queryInput with an emphasis on the types of files we need to look up
        $gateway = new OpenAIGateway();
        $response = $gateway->makeChatCompletion([
          'model' => 'gpt-4',
          'messages' => [
            ['role' => 'system', 'content' => 'You are a helpful assistant. Speak concisely. Answer the user\'s question based only on the following context: ' . $queryInput],
            ['role' => 'user', 'content' => 'Write 2-3 sentences explaining the types of files we should search for in our codebase to provide the next response in the conversation. Focus only on the next step, not future optimizations. Ignore mentions of video transcriptions or readme/documentation.'],
          ],
        ]);
        $query = $response['choices'][0]['message']['content'];

        $searcher = new Searcher();
        $results = $searcher->queryAllFiles($query);

        // Loop through the results, and for each one, add the file name and the entire content of the file to the context
        $context = '';
        foreach ($results["results"] as $result) {
            $context .= "Content of " . $result['path'] . ": \n```\n";
            $content = $this->getFileContent($result['path']);
            $context .= $content . "\n```\n\n";
        }

        // // Loop through the results, and for each one, add the file name and the first X lines of the file to the context
        // $context = '';
        // foreach ($results["results"] as $result) {
        //     $context .= "Content of " . $result['path'] . ": \n```\n";
        //     $content = $this->getFileContent($result['path']);
        //     $context .= implode("\n", array_slice(explode("\n", $content), 0, 20)) . "\n```\n\n";
        // }

        // dd($context);


        return $context;
    }

    private function getFileContent(string $path): string
    {
        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        fclose($file);

        return $content;
    }
}
