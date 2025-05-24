<?php
namespace App\Imports;

use App\Models\Voter;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VotersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
//        return new Voter([
//            'name'              => //wait exel file
//            'phone'             => //wait exel file
//            'membership_number' => //wait exel file
//        ]);
    }
}
