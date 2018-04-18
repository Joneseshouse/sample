<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon as Carbon;
use App\Helpers\Tools;
use App\Modules\Order\Models\Order;

class UpdateOrderStatistics extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:update_order_statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update order stattistics';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->comment('[+] Begin execute...');
        Order::updateStatistics();
        $this->comment('[+] DONE');
    }
}
