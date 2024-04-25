<?php

namespace App\Livewire;

use App\AI\Models;
use Livewire\Component;

class ModelSelector extends Component
{
    public $selectedModel = '';

    public $models;

    public $thread;

    public function mount()
    {
        $this->models = Models::MODELS;

        $this->selectedModel = Models::getModelForThread($this->thread);

        if (session()->has('selectedModel')) {
            session()->forget('selectedModel');
        }
    }

    public function render()
    {
        return view('livewire.model-selector');
    }
}
