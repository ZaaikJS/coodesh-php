<?php

namespace App\Console\Commands;

use App\Models\Word;
use Illuminate\Console\Command;

class ImportWords extends Command
{
    protected $signature = 'import:words {filename}';

    protected $description = 'Importar palavras para a DB';

    public function handle()
    {
        $filename = $this->argument('filename');

        $filePath = base_path($filename);

        if (! file_exists($filePath)) {
            $this->error('Arquivo não encontrado');

            return 1;
        }

        $wordsData = json_decode(file_get_contents($filePath), true);

        foreach ($wordsData as $word => $value) {
            Word::updateOrCreate(['word' => $word]);
        }

        $this->info('Todas as palavras foram importadas');

        return 0;
    }
}
