<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon as Carbon;
use App\Helpers\Tools;
use App\Helpers\ValidateTools;
use App\Modules\ExportBill\Models\ExportBill;
use App\Modules\ExportBillDaily\Models\ExportBillDaily;

class ExportBillDailySingle extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:export_bill_daily_single';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate export bill daily';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->comment('[+] Begin execute...');

        ExportBillDaily::updateByDate();

        $this->comment('[+] DONE');
    }
}
