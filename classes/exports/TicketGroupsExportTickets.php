<?php namespace Waka\Support\Classes\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
//
use Waka\Support\Models\TicketGroup;

class TicketGroupsExportTickets implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnWidths
{
    public $parentId;

    public function __construct($initOptions)
    {
        $this->parentId = $initOptions['modelId'];
    }

    //startKeep/

    public function headings(): array
    {
        return [
            'id',
            'code',
            'ticket_type',
            'temps',
            'state',
            'user',
            'next',
            'url',
            'ticket_group',
            'awake_at',
            'messages'
        ];
    }

    public function collection()
    {
        $parent = TicketGroup::find($this->parentId);
        $request = $parent->tickets()->with('ticket_group', 'ticket_type', 'user', 'next')->get();
        $request->transform(function ($item) {
            $returnedItem = [];
            $messages = $item->getMessagesAsTxt();
            $returnedItem['id'] = $item->id;
            $returnedItem['code'] = $item->code;
            $returnedItem['ticket_type'] = $item->ticket_type->name ?? null; 
            $returnedItem['temps'] = $item->temps;
            $returnedItem['state'] = $item->state;
            $returnedItem['user'] = $item->user->fullName ?? false; 
            $returnedItem['next'] = $item->next->fullName ?? null;
            $returnedItem['url'] = $item->url ?? null; 
            $returnedItem['ticket_group'] = $item->ticket_group->name ?? null; 
            
            if($item->state != 'sleep') {
                $item->awake_at = null;
            }
            $returnedItem['awake_at'] = $item->awake_at;
            $returnedItem['messages'] = $messages;
            
            return $returnedItem;
        });;
        return $request;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A'    => ['font' => ['bold' => true]],
            1 => ['font' => ['bold' => true]],
            'A1:A50' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFFFF00'],
                ],
            ],
            'L' => [
                'font' => ['size' => 8],
                'alignment' => ['wrapText' => true],
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'L' => 75,            
        ];
    }

    //endKeep/
}