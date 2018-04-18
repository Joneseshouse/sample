<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Console\Command;
use Carbon\Carbon as Carbon;
use App\Helpers\Tools;
use App\Helpers\ValidateTools;
use App\Modules\BillOfLanding\Models\BillOfLanding;
use App\Modules\VnBillOfLanding\Models\VnBillOfLanding;
use App\Modules\CnBillOfLanding\Models\CnBillOfLanding;


class CorrectMatchCnBol extends Command{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:correct_math_cn_bol';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Correct match CN bol';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->comment('[+] Begin execute...');
        $listItem = CnBillOfLanding::where('match', true)->get();
        foreach ($listItem as $item) {
            if(BillOfLanding::where('code', $item->code)->whereNotNull('vn_store_date')->count()){
                # Do NOTHING 
            }else{
                $this->comment('[...] ' + $item->code);
                $item->match = false;
                $item->save();
            }
        } 
        $this->comment('[+] DONE...');
    }
}
