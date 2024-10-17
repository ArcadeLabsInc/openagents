<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenAgents</title>
    <link rel="stylesheet" href="{{ asset('css/jbm.css') }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
</head>
<body>
    <main>
        <h1 class="hero">OpenAgents</h1>
        <div class="button-demo">
            <x-button variant="default" size="default">
                Default Button
            </x-button>

            <x-button variant="destructive" size="sm">
                Small Destructive
            </x-button>

            <x-button variant="outline" size="lg">
                Large Outline
            </x-button>

            <x-button variant="secondary" size="default">
                Secondary Button
            </x-button>

            <x-button variant="ghost">
                Ghost Button
            </x-button>

            <x-button variant="link">
                Link Button
            </x-button>
        </div>
    </main>
</body>
</html>