<?php

namespace App\Imports;

use App\Models\Voter;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class VotersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Voter([
            'name'              => $row['name'],                         // الاسم
            'phone'             => $row['phone'],                        // رقم الهاتف
            'membership_number' => $row['membership_number'],           // رقم العضوية
            'has_voted'         => $row['has_voted'] ?? false,          // هل صوت (افتراضي false)
            'is_admin'          => $row['is_admin'] ?? false,           // هل هو مشرف (افتراضي false)
            'verification_code' => $row['verification_code'] ?? null,   // كود التحقق (رقمي أو null)
            'code_expires_at'   => isset($row['code_expires_at']) && $row['code_expires_at']
                ? Carbon::parse($row['code_expires_at'])
                : null,                              // تاريخ انتهاء كود التحقق
        ]);
    }
}
