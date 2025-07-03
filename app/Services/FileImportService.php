<?php

namespace App\Services;

use App\Models\FileImport;
use App\Models\Record;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class FileImportService
{
    protected const RECORD_TYPE_POSITION = 17;
    protected const RECORD_TYPE_LENGTH = 2;
    protected array $specs = [];

    public function __construct()
    {
        $this->loadSpecs();
    }

    protected function loadSpecs(): void
    {
        $specsPath = database_path('schemas/import_file_specs.csv');

        if (!file_exists($specsPath)) {
            throw new \RuntimeException("Specs file not found at: {$specsPath}");
        }

        $file = fopen($specsPath, 'rb');
        fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            $this->specs[$row[5]][] = [
                'id' => (int)$row[0],
                'start_range' => (int)$row[1],
                'end_range' => (int)$row[2],
                'length' => (int)$row[3],
                'description' => $row[4],
                'record_type' => $row[5],
            ];
        }

        fclose($file);
    }

    public function importFile(string $filePath, string $fileId): FileImport
    {
        return DB::transaction(function () use ($filePath, $fileId) {
            $fileImport = FileImport::firstOrCreate(
                ['file_id' => $fileId],
                [
                    'file_path' => $filePath,
                    'started_at' => now(),
                ]
            );

            if ($fileImport->getAttribute('completed_at') !== null) {
                return $fileImport;
            }

            $validLines = 0;

            LazyCollection::make(function () use ($filePath) {
                $handle = fopen($filePath, 'rb');

                while (($line = fgets($handle)) !== false) {
                    yield $line;
                }

                fclose($handle);
            })
            ->filter(function ($line) {
                 return trim($line) !== '';
            })
            ->each(function ($line) use (&$validLines, $fileImport) {
                $this->processLine($fileImport, $line, ++$validLines);
            });

            $fileImport->setAttribute('completed_at', now())->save();

            return $fileImport;
        });
    }

    protected function processLine(FileImport $fileImport, string $line, int $lineNumber): void
    {
        $recordType = substr($line, self::RECORD_TYPE_POSITION, self::RECORD_TYPE_LENGTH);

        if (!isset($this->specs[$recordType])) {
            Log::warning("Skipped unknown record type: {$recordType}");
            return;
        }

        $data = [];
        foreach ($this->specs[$recordType] as $spec) {
            $value = trim(substr($line, $spec['start_range'] - 1, $spec['length']));
            $data[$spec['description']] = $value !== '' ? $value : null;
        }

        Record::updateOrCreate(
            [
                'file_import_id' => $fileImport->getKey(),
                'record_type' => $recordType,
                'line_number' => $lineNumber,
            ],
            ['data' => $data]
        );
    }
}
