<?php

use Illuminate\Support\Facades\Artisan;

it('registers custom fields migration tag correctly', function () {
    // Verifica se o arquivo existe
    $migrationPath = __DIR__ . '/../database/migrations/add_custom_fields_to_notifications_table.php.stub';
    expect(file_exists($migrationPath))->toBeTrue();
    
    // Tenta executar o comando vendor:publish com a tag específica
    Artisan::call('vendor:publish', [
        '--tag' => 'laravel-notifications-custom-fields-migration',
        '--force' => true,
    ]);
    
    $output = Artisan::output();
    
    // Se não encontrar a tag, o output conterá "No publishable resources"
    expect($output)->not->toContain('No publishable resources for tag');
});
