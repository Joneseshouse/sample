<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Console\Command;
use Carbon\Carbon as Carbon;
use App\Helpers\Tools;
use App\Helpers\ValidateTools;
use App\Modules\OrderItem\Models\OrderItem;
use App\Modules\UserTransaction\Models\UserTransaction;
use App\Modules\BillOfLanding\Models\BillOfLanding;
use App\Modules\ExportBill\Models\ExportBill;
use App\Modules\Order\Models\Order;
use App\Modules\Purchase\Models\Purchase;
use App\Modules\Config\Models\Config;


class TestFunction extends Command{
    use DatabaseTransactions;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:test_function';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test function';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->comment('[+] Begin execute...');
        // Dang GD = tong don - tong xuat - tong giao dich thanh cong bo di phi van chuyen
        $query = Order::whereIn('status', ['confirm', 'purchasing', 'purchased', 'done']);
        $tongDon = round($query->sum('total'));
        $tongXuat = ExportBill::sum('amount');

        // $tongDon = 0;
        $totalPurchase = 0;
        $numberPurchase = 0;
        $listOrder = $query->get();
        foreach($listOrder as $order) {
            $listPurchase = $order->purchases;
            foreach($listPurchase as $purchase) {
                // Xuat het tat ca cac bol
                // $tongDon += $purchase->total;
                $bols = $purchase->billsOfLanding()->count();
                $exported = $purchase->billsOfLanding()->whereNotNull('export_bill_id')->count();
                if ($bols && $bols === $exported) {
                    $totalPurchase += ($purchase->total - $purchase->delivery_fee);
                    $numberPurchase++;
                }
                if ($bols < $exported) {
                    $this->comment('WTF???');
                }
            }
        }

        $purchasing = UserTransaction::whereIn('type', ['GD', 'TH', 'XH'])->sum('purchasing');
        $totalXh = UserTransaction::where('type', 'XH')->sum('purchasing');
        $totalTh = UserTransaction::where('type', 'TH')->sum('purchasing');
        $numberTh = UserTransaction::where('type', 'TH')->count();

        $this->comment('---');
        $this->comment('Total order');
        $this->comment(number_format($tongDon));

        $this->comment('---');
        $this->comment('Total exported (delivery fee)');
        $this->comment(number_format($tongXuat));

        $this->comment('---');
        $this->comment('Hypothesis purchasing');
        $this->comment(number_format($tongDon - $tongXuat - $totalPurchase));

        $this->comment('---');
        $this->comment('Total GD, TH, XH');
        $this->comment(number_format($purchasing));

        $this->comment('---');
        $this->comment('Total purchase');
        $this->comment(number_format($totalPurchase));

        $this->comment('---');
        $this->comment('Total TH');
        $this->comment(number_format($totalTh));

        $this->comment('---');
        $this->comment('Number of completed purchase & TH');
        $this->comment(number_format($numberPurchase));
        $this->comment(number_format($numberTh));
        // Bill xuat nhung ko nam trong don xuat nao
        /*
        $listItem = Purchase::all();
        foreach($listItem as $item) {
            if (!OrderItem::where('purchase_id', $item->id)->count()) {
                $this->comment($item->id);
                if ($item->order) {
                    $item->order->delete();
                }
            }
        }
         */
        // Bill nam trong 2 don xuat
    }
}
