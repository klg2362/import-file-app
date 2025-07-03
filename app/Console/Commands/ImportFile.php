<?php

namespace App\Console\Commands;

use App\Services\FileImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class ImportFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:file {path_to_file} {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a fixed-width file into the database';

    /**
     * Execute the console command.
     */
    public function handle(FileImportService $importService)
    {
        $validator = Validator::make([
            'path_to_file' => $this->argument('path_to_file'),
            'id' => $this->argument('id'),
        ], [
            'path_to_file' => 'required|string',
            'id' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return Command::FAILURE;
        }

        $filePath = $this->argument('path_to_file');
        $fileId = $this->argument('id');

        if (!file_exists($filePath)) {
            $this->error("File not found at: {$filePath}");
            return Command::FAILURE;
        }

        $this->info("Starting import file: {$filePath} with ID: {$fileId}");

        try {
            $fileImport = $importService->importFile($filePath, $fileId);

            if ($fileImport->wasRecentlyCreated) {
                $this->info("Import completed successfully!");
                $this->line("Duration: " . $fileImport->getAttribute('created_at')->diffForHumans
                    ($fileImport->getAttribute('completed_at'), true));
            } else {
                $this->warn("File with ID {$fileId} was already imported on {$fileImport->getAttribute('completed_at')}.");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
