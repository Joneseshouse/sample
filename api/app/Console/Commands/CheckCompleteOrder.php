<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon as Carbon;
use App\Helpers\Tools;
use App\Helpers\ValidateTools;
use App\Modules\Order\Models\Order;
use App\Modules\BillOfLanding\Models\BillOfLanding;

class CheckCompleteOrder extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:check_complete_order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check complete order';

    /*
    'new',
    'confirm',
    'purchasing',
    'purchased',
    'complain',
    'done'
    */

    function checkBeforeDone ($listItem) {
        foreach($listItem as $item) {
            $bols = $item->billsOfLanding()->count();
            $purchases = $item->purchases()->count();
            $purchased = $item->purchases()->
                                whereNotNull('code')->
                                where('code', '!=', '')->
                                count();
            $exported = $item->billsOfLanding()->
                                whereNotNull('export_store_date')->
                                count();
            # print_r([$bols, $purchases, $purchased, $exported]);die;
            // Đã duyệt mua: chưa có vận đơn && chưa có mã giao dịch 
            # if(!$bols && !$purchased) {
            if($purchased < $purchases) {
                $item->status = 'confirm';
                $item->save();
            }

            // Đang giao dịch: Chưa đủ vận đơn && có đủ mã giao dịch 
            if($bols < $purchases && $purchased === $purchases) {
                $item->status = 'purchasing';
                $item->save();
            }

            // Giao dịch xong: Đủ mã vận đơn && đủ mã giao dịch && chưa xuất hết
            if($bols >= $purchases && $purchased === $purchases && $exported <= $bols) {
                $item->status = 'purchased';
                $item->save();
            }
        }
    }

    function checkDone () {
        $listItem = Order::where('status', 'purchased')->get();
        foreach ($listItem as $item) {
            # Tất cả các bol chưa xuất của order này
            $notExportedBol = BillOfLanding::
                where('order_id', $item->id)->
                whereNull('export_store_date')->count();
            # Nếu tất cả đều đã xuất
            if(!$notExportedBol){
                # Bol xuất cuối cùng
                $lastBol = BillOfLanding::
                    where('order_id', $item->id)->
                    orderBy('export_store_date', 'desc')->first();
                # Order have at least 1 bol
                if($lastBol){
                    $now = Tools::nowDate();
                    $lastDate = ValidateTools::toDate($lastBol->export_store_date);
                    # Nếu ngày xuất của bol cuối cùng lớn hơn hoặc bằng ngày khiếu nại
                    if(floatVal($lastDate->diffInDays($now, false)) >= floatVal($item->user->complain_day)){
                        $this->comment('[...] '.$item->uid);
                        $item->status = 'done';
                        $item->save();
                    }
                }
            }
        }
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->comment('[+] Begin execute...');
        $listItem = Order::
            whereIn('status', ['confirm', 'purchasing'])->
            # where('id', 2842)->
            get();
        self::checkBeforeDone($listItem);
        self::checkDone();
        $this->comment('[+] DONE');
    }
}
