<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Console\Command;
use Carbon\Carbon as Carbon;
use App\Helpers\Tools;
use App\Helpers\ValidateTools;
use App\Modules\BillOfLanding\Models\BillOfLanding;


class GetDuplicateBol extends Command{
    # use DatabaseTransactions;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:get_duplicate_bol';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get duplicate bol';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->comment('[+] Begin execute: get_duplicate_bol...');
        $result = [];
        $listItem = BillOfLanding::all();
        foreach ($listItem as $bol) {
            $bol->code = trim($bol->code);
            $bol->save();
        }
        foreach ($listItem as $bol) {
            if(BillOfLanding::where('code', $bol->code)->count() > 1){
                if(!in_array($bol->code, $result)){
                    $result[] = $bol->code;
                    $this->comment($bol->code);
                }
            }
        }
        $this->comment('[+] DONE');
    }
}
