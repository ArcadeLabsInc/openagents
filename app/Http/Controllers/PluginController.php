<?php

namespace App\Http\Controllers;

use App\Models\Plugin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mauricius\LaravelHtmx\Http\HtmxResponse;

class PluginController extends Controller
{
    public function index()
    {
        return view('plugins', [
            'plugins' => Plugin::all(),
        ]);
    }

    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'fee' => 'required|numeric',
            'description' => 'required',
            'wasm_url' => 'required|url|active_url',
        ]);

        if ($validator->fails()) {
            return view('plugin-upload-failed', [
                'errors' => $validator->errors(),
            ]);
        }

        $plugin = Plugin::create([
            'name' => request('name'),
            'fee' => request('fee'),
            'description' => request('description'),
            'wasm_url' => request('wasm_url'),
        ]);

        // Get the updated list of plugins
        $plugins = Plugin::all();

        // Return the updated plugin grid as an HTMX response
        return with(new HtmxResponse())
            // ->renderFragment('components.plugin-grid', 'plugin-grid', compact('plugins'))
            // ->addFragment('components.plugin-grid', 'plugin-grid', compact('plugins'))
            // ->addFragment('components.plugin-upload-form', 'plugin-upload-form', ['success' => 'Plugin uploaded successfully.']);
            // ->addFragment('components.plugin-grid', 'plugin-grid', compact('plugins'));
            ->addFragment('components.plugin-grid', 'plugin-grid', compact('plugins'));
    }
}
