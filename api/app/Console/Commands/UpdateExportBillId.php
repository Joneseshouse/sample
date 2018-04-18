<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Console\Command;
use Carbon\Carbon as Carbon;
use App\Helpers\Tools;
use App\Helpers\ValidateTools;
use App\Modules\OrderItem\Models\OrderItem;
use App\Modules\ExportBill\Models\ExportBill;
use App\Modules\UserTransaction\Models\UserTransaction;
use App\Modules\BillOfLanding\Models\BillOfLanding;
use App\Modules\VnBillOfLanding\Models\VnBillOfLanding;
use App\Modules\CnBillOfLanding\Models\CnBillOfLanding;
use App\Modules\Order\Models\Order;
use App\Modules\Config\Models\Config;


class UpdateExportBillId extends Command{
    # use DatabaseTransactions;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:update_export_bill_id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update export bill id';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->comment('[+] Begin execute...');
        $listItem = UserTransaction::where('type', 'TH')->get();
        foreach ($listItem as $item) {
            $nodeArr = explode(': ', $item->note);
            $uid = end($nodeArr);
            if($uid){
                $exportBill = ExportBill::where('uid', $uid)->first();
                if($exportBill){
                    $item->export_bill_id = $exportBill->id;
                    $item->save();
                    UserTransaction::recalculate($item);
                }
            }
        }
    }
}
