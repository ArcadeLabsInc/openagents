<?php

namespace App\AI;

use App\Models\Agent;
use Illuminate\Database\Eloquent\Collection;
use LaravelIdea\Helper\App\Models\_IH_Agent_C;

class Agents
{
    public const DEMO_AGENTS = [
        '1' => [
            'id' => 1,
            'title' => 'Image Generator',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.',
        ],
        2 => [
            'id' => 2,
            'title' => 'Research Assistant',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.',
        ],
        3 => [
            'id' => 3,
            'title' => 'Brainstorm Bot',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.',
        ],
        4 => [
            'id' => 4,
            'title' => 'Style Suggestions',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.',
        ],
        5 => [
            'id' => 5,
            'title' => 'PDF Reader',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.',
        ],
        6 => [
            'id' => 6,
            'title' => 'Tour Guide',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.',
        ],
    ];

    public static function AGENTS(): _IH_Agent_C|Collection|array
    {
        return Agent::all();
    }
}
