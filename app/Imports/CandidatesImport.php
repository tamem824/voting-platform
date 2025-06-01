<?php

namespace App\Imports;

use App\Models\Candidate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CandidatesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Candidate([
            'name' => $row['name'],
            'type' => $row['type'], // يجب أن تكون القيمة إما 'president' أو 'member'
            'bio'  => $row['bio'] ?? null,
            'photo' => $row['photo'] ?? null, // مسار أو اسم الصورة (يمكن تركه فارغًا)
        ]);
    }
}
