<?php namespace Waka\Support\Classes\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
//
use Waka\Support\Models\Ticket;
use Waka\Support\Models\TicketMessage;

class TicketsExportMessages implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnWidths
{
    public $listIds;

    public function __construct($initOptions = [])
    {
        //trace_log($initOptions);
        $this->listIds =  $initOptions['listIds'] ?? null;
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
        $request = null;
        //trace_log($this->listIds);
        if ($this->listIds) {
            $request = Ticket::whereIn('id', $this->listIds)->with('ticket_group', 'ticket_type', 'user', 'next')->get();
        } else {
            $request = Ticket::with('ticket_group', 'ticket_type')->get(); 
        }
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

}
