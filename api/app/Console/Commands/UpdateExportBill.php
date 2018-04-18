<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Console\Command;
use Carbon\Carbon as Carbon;
use App\Helpers\Tools;
use App\Helpers\ValidateTools;
use App\Modules\BillOfLanding\Models\BillOfLanding;
use App\Modules\ExportBill\Models\ExportBill;
use App\Modules\UserTransaction\Models\UserTransaction;


class UpdateExportBill extends Command{
    # use DatabaseTransactions;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:update_export_bill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update export bill';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->comment('[+] Begin execute...');
        # Update export value
        $listItem = ExportBill::all();
        foreach ($listItem as $item) {
            $oldTotal = $item->total;
            $item->amount = round($item->bols->sum('total'));
            $item->total = $item->amount + $item->sub_fee;
            $item->save();
            $this->comment('[...] '.$item->uid.': '.$oldTotal.' -> '.$item->total);
        }

        # Update user transaction value
        $listItem = UserTransaction::where('type', 'TH')->get();
        foreach ($listItem as $item) {
            $noteArr = explode(': ', $item->note);
            $exportUid = end($noteArr);
            $exportItem = ExportBill::where('uid', $exportUid)->first();
            if($exportItem){
                $item->amount = $exportItem->total;
                $item->save();
                UserTransaction::recalculate($item);
            }else{
                $item->delete();
            }
        }

        $this->comment('[+] DONE');
    }
}
