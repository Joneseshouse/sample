<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Console\Command;
use Carbon\Carbon as Carbon;
use App\Helpers\Tools;
use App\Helpers\ValidateTools;
use App\Modules\UserTransaction\Models\UserTransaction;
use App\Modules\ExportBill\Models\ExportBill;
use App\Modules\BillOfLanding\Models\BillOfLanding;
use App\Modules\Order\Models\Order;
use App\Modules\Purchase\Models\Purchase;
use App\Modules\OrderItem\Models\OrderItem;


class RefreshExport extends Command{
    use DatabaseTransactions;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:refresh_export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh export';

    private static function removeOrderOrPurchaseWithoutItem () {
        $listItem = Purchase::all();
        foreach($listItem as $item) {
            if (!OrderItem::where('purchase_id', $item->id)->count()) {
                if ($item->order) {
                    $item->order->delete();
                }
            }
        }
    }

    private static function removeTdTransaction () {
        UserTransaction::where('type', 'TD')->delete();
    }

    private static function changeThtoXhTransaction() {
        UserTransaction::where('type', 'TH')->update(['type' => 'XH']);
    }

    private static function updateExportBill() {
        $listExport = ExportBill::get();
        foreach($listExport as $exportItem) {
            $bolArr = [];
            $bols = $exportItem->bols;
            foreach($bols as $bol) {
                if ($bol && !in_array($bol->id, $bolArr)){
                    array_push($bolArr, $bol->id);
                }
            }
            $data = [
                "oldItem" => $exportItem,
                "sub_fee" => 0,
                "list_id" => implode(',', $bolArr),
                "admin_id" => $exportItem->admin_id
            ];
            if (!count($bolArr)) {
                // Remove export bill without any bols
                // Remove corresponding transaction
                UserTransaction::where('export_bill_id', $exportItem->id)->delete();
                $exportItem->delete();
            } else {
                ExportBill::addItem($data, null);
            }
        }
    }

    private static function checkVcBill() {
        // Find all bols that didn't have purchase_id
        // Don't care how it export
        $listItem = BillOfLanding::
            whereNull('purchase_id')->
            whereNotNull('user_id')->
            where('mass', '>', 0)->
            get();
        foreach($listItem as $item) {
            $transaction = UserTransaction::
                where('bol_id', $item->id)->
                where('type', 'VC')->
                first();
            $transactionData = [
                'user_id' => $item->user_id,
                'type' => 'VC',
                'amount' => floor(abs($item->delivery_fee)),
                'money_type' => '-',
                'note' => 'Tiền đơn VC: '.$item->code,
                'bol_id' => $item->id
            ];
            if (!$transaction) {
                UserTransaction::addItem($transactionData, 0);
            } else {
                UserTransaction::editItem($transaction->id, $transactionData, 0);
            }
        }
    }

    private static function checkFulfillPurchases() {
        // To create TH also
        $query = Order::whereIn('status', ['confirm', 'purchasing', 'purchased', 'done']);
        $listOrder = $query->get();
        foreach($listOrder as $order) {
            $listPurchase = $order->purchases;
            foreach($listPurchase as $purchase) {
                $bols = $purchase->billsOfLanding()->count();
                $exported = $purchase->billsOfLanding()->whereNotNull('export_bill_id')->count();
                if ($bols && $bols === $exported) {
                    // find corresponding export bill from purchase_id
                    $listBols = $purchase->billsOfLanding;
                    $listExportBill = [];
                    foreach($listBols as $bol) {
                        array_push($listExportBill, $bol->export_bill_id);
                        rsort($listExportBill);
                    }
                    $exportBill = ExportBill::find($listExportBill[0]);
                    $transactionData = [
                        'user_id' => $purchase->user_id,
                        'type' => 'TH',
                        'amount' => floor(abs($purchase->total - $purchase->delivery_fee)),
                        'money_type' => '-',
                        'note' => 'Tiền hàng mã GD: '.$purchase->code,
                        'export_bill_id' => $listExportBill[0],
                        'purchase_id' => $purchase->id
                    ];
                    UserTransaction::addItem($transactionData, $exportBill->admin);
                }
            }
        }
    }

    private static function updateGdTransactionEqualOrderTotal() {
        $listGd = UserTransaction::where('type', 'GD')->get();
        foreach($listGd as $gd) {
            $order = Order::find($gd->order_id);
            if (!$order) {
                $gd->delete();
            } else {
                $gd->amount = round($order->total);
                $gd->save();
            }
        }
    }

    private static function recalculateTransaction() {
        $transactions = UserTransaction::whereIn('type', ['GD', 'TH', 'XH'])->get();
        foreach($transactions as $transaction) {
            UserTransaction::recalculate($transaction);
        }
    }

    public function handle(){
        $this->comment('[+] Begin execute...');
        self::removeOrderOrPurchaseWithoutItem();
        self::removeTdTransaction();
        self::changeThtoXhTransaction();
        self::checkVcBill();
        self::updateExportBill();
        self::checkFulfillPurchases();
        self::updateGdTransactionEqualOrderTotal();
        self::recalculateTransaction();
    }
}
