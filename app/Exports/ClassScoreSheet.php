<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ClassScoreSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    public function __construct(
        private $name,
        private $data,
        private $ranks
    ) {}

    public function headings(): array
    {
        return [
            'Môn',
            'Điểm miệng',
            'Điểm 15 phút',
            'Điểm 1 tiết',
            'Điểm giữa kỹ',
            'Điểm cuối kỳ',
            'Điểm trung bình',
            'Hạng'
        ];
    }

    public function array(): array
    {
        $return = $this->data;
        $r = [];
        foreach ($return as $key => $item) {
            [$subject, $id] = explode("_", array_pop($item));
            array_pop($item);
            $rank = collect($this->ranks[$subject] ?? [])
                ->firstWhere('id', $id);
            $item[] = $rank['score'] ?? '';
            $item[] = $rank['rank'] ?? '';

            $r[$key] = [];
            foreach ($this->headings() as $k => $heading) {
                $r[$key][$heading] = $item[$k] ?? '';
            }
        }
        return $r;
    }

    public function title(): string
    {
        return $this->name;
    }
}
