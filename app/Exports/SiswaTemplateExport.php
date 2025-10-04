<?php

namespace App\Exports;

use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiswaTemplateExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Return collection with sample data
        return collect([
            [
                'John Doe',
                'john.doe@student.sch.id',
                '202301001',
                'X-RPL-1',
                '15/08/2005',
                'L',
                'Jl. Merdeka No. 123, Jakarta',
                '081234567890',
                '15/07/2023'
            ],
            [
                'Jane Smith',
                'jane.smith@student.sch.id',
                '202301002',
                'X-TKJ-1',
                '22/03/2005',
                'P',
                'Jl. Sudirman No. 456, Jakarta',
                '081987654321',
                '15/07/2023'
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'NIS',
            'Kelas',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Alamat',
            'No Telepon',
            'Tanggal Masuk'
        ];
    }
}