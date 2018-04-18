<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon as Carbon;
use App\Helpers\Tools;
use App\Modules\RateLog\Models\RateLog;

class GenerateRateLog extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:generate_rate_log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate rate log daily';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->comment('[+] Begin execute...');
        $today = Tools::nowDate();
        if(!RateLog::whereDate('created_at', '=' , $today)->count()){
            $defaultRate = intVal(\ConfigDb::get('cny-vnd', 3400));
            $input = [
                'rate' => $defaultRate,
                'order_rate' => $defaultRate,
                # 'buy_rate' => $defaultRate + intVal(\ConfigDb::get('lech-ty-gia-chuyen-khoan')),
                # 'sell_rate' => $defaultRate + intVal(\ConfigDb::get('lech-ty-gia-nho-thanh-toan'))
            ];
            $lastLog = RateLog::orderBy('id', 'desc')->first();
            if($lastLog){
                $input = [
                    'rate' => $lastLog->rate,
                    'order_rate' => $lastLog->order_rate,
                    # 'buy_rate' => $lastLog->buy_rate,
                    # 'sell_rate' => $lastLog->sell_rate
                ];
            }
            $item = RateLog::addItem($input);
        }
        $this->comment('[+] DONE');
    }
}
