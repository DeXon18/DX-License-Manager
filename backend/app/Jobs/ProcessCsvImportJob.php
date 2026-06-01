<?php

namespace App\Jobs;

use App\Models\ImportLog;
use App\Services\Data\CsvImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCsvImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour max

    protected $filePath;
    protected $filename;
    protected $logId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $filePath, string $filename, int $logId)
    {
        $this->filePath = $filePath;
        $this->filename = $filename;
        $this->logId = $logId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CsvImportService $importService)
    {
        try {
            $importService->importAsync($this->filePath, $this->filename, $this->logId);
        } catch (\Exception $e) {
            Log::error("ProcessCsvImportJob failed: " . $e->getMessage());
            
            $importLog = ImportLog::find($this->logId);
            if ($importLog) {
                $importLog->update([
                    'status' => 'failed',
                    'errors' => [$e->getMessage()]
                ]);
            }
        }
    }
}
