<?php
namespace App\Imports;

use App\Models\Candidate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CandidatesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
//        return new Candidate([
//            'name' => //wait exel file
//            'type' => //wait exel file
//            'bio'  => //wait exel file
//            'photo'=>
//        ]);
    }
}
