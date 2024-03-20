<?php

namespace App\Models;

use App\AI\Inferencer;
use App\AI\StabilityAIGateway;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'type', 'config'];

    /**
     * Triggers this node's logic.
     *
     * @param  array  $params  Parameters including 'input', 'streamingFunction', 'agent', 'flow', and 'thread'.
     * @return string The output string from the node.
     *
     * @throws Exception
     */
    public function trigger(array $params): string
    {
        // Extract parameters
        $input = $params['input'];
        $streamingFunction = $params['streamingFunction'];
        $agent = $params['agent'];
        $thread = $params['thread'];

        $inferencer = new Inferencer($streamingFunction);

        // Node-specific logic
        switch ($this->type) {
            case 'inference':
                // Call the Inferencer for LLM inference
                $output = $inferencer->llmInference($agent, $this, $thread, $input, $streamingFunction);
                break;

            case 'finnhub_function_call':
                $output = $inferencer->llmInferenceWithFunctionCalling($agent, $this, $thread, $input, $streamingFunction);
                break;

            case 'stability_text_to_image':
                $gateway = new StabilityAIGateway();
                $output = $gateway->text_to_image($input);
                break;

            case 'plugin':
                $config = json_decode($this->config, true);
                $plugin_id = $config['plugin_id'];
                $plugin = Plugin::find($plugin_id);

                // Extract the zipcode from the input. Could be many forms, so just look for any amount of digits
                $matches = [];
                preg_match('/\d+/', $input, $matches);
                $zipcode = $matches[0] ?? null;

                $params = json_encode([
                    'zipcode' => $zipcode,
                    'apikey' => env('ZIPCODESTACK_API_KEY'),
                ]);

                $output = "```\n".$plugin->call('run', $params)."\n```";

                break;

            default:
                // Default processing logic for nodes
                $output = 'Default node processing for: '.$input;
                break;
        }

        return $output;
    }
}
