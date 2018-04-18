<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Console\Command;
use Carbon\Carbon as Carbon;
use App\Helpers\Tools;
use App\Helpers\ValidateTools;
use App\Modules\OrderItem\Models\OrderItem;
use App\Modules\OrderItemNote\Models\OrderItemNote;


class UpdateOrderItemNote extends Command{
    # use DatabaseTransactions;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:update_order_item_note';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update order item note';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->comment('[+] Begin execute...');
        $listItem = OrderItem::all();
        foreach ($listItem as $item) {
            if(trim($item->message)){
                OrderItemNote::addItem([
                    'order_item_id' => $item->id,
                    'user_id' => ($item->order_id && $item->order) ? $item->order->user_id : null,
                    'note' => $item->message
                ]);
            }
        }
    }
}
