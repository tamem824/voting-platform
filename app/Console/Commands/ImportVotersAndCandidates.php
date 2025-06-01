<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\VotersImport;
use App\Imports\CandidatesImport;

class ImportVotersAndCandidates extends Command
{
    protected $signature = 'import:data';
    protected $description = 'Import voters and candidates from Excel files';

    public function handle()
    {
        $this->info('🚀 Starting import...');

        $votersPath = storage_path('app/imports/voters.xlsx');
        $candidatesPath = storage_path('app/imports/candidates.xlsx');

        if (!file_exists($votersPath) || !file_exists($candidatesPath)) {
            $this->error('❌ One or both Excel files not found!');
            return;
        }

        Excel::import(new VotersImport, $votersPath);
        $this->info('✅ Voters imported successfully.');

        Excel::import(new CandidatesImport, $candidatesPath);
        $this->info('✅ Candidates imported successfully.');

        $this->info('🎉 All data imported successfully.');
    }
}
