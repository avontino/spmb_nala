<?php

namespace App\Console\Commands;

use App\Exports\PenerimaanTemplateExport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class GeneratePenerimaanTemplate extends Command
{
    protected $signature = 'penerimaan:generate-template';

    protected $description = 'Generate template Excel untuk import data penerimaan';

    public function handle()
    {
        $directory = public_path('files');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $path = $directory . '/template-penerimaan.xlsx';

        Excel::store(new PenerimaanTemplateExport, $path);

        $this->info('Template berhasil dibuat di: ' . $path);

        return Command::SUCCESS;
    }
}
