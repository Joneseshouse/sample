<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon as Carbon;
use App\Helpers\Tools;
use App\Helpers\ValidateTools;
use App\Modules\ExportBill\Models\ExportBill;
use App\Modules\ExportBillDaily\Models\ExportBillDaily;

class ExportBillDailyAll extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:export_bill_daily_all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate all export bill daily';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->comment('[+] Begin execute...');
        $firstExport = ExportBill::orderBy('created_at', 'asc')->first();

        $now = Tools::nowDate();
        $lowestDate = Tools::nowDate();
        if($firstExport){
            $lowestDate = ValidateTools::toDate($firstExport->created_at);
        }

        while(true){
            ExportBillDaily::updateByDate($lowestDate);
            if(abs($lowestDate->diffInDays($now)) === 0){
                break;
            }
            $lowestDate = $lowestDate->addDay();
        }

        $this->comment('[+] DONE');
    }
}
