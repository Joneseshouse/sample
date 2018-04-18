<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class ResetDb extends Command{
    use DatabaseTransactions;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:resetdb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset db all';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->comment('[+] Begin execute...');
        $this->comment(\DB::connection()->getDatabaseName());
        /*
        admin_transactions
        bills_of_landing
        bol_reports
        bols_daily
        cart_items
        chat_lost
        check_bills
        check_items
        cn_bill_of_landing_fails
        cn_bills_of_landing
        collect_bols
        export_bill_daily
        export_bills
        login_failed_logs
        lost
        order_item-notes
        order_items
        orders
        purchases
        rate_logs
        receipts
        vn_bills_of_landing
        */
        // Admin related
        \DB::table('admin_transactions')->truncate();

        // User related
        \DB::table('login_failed_logs')->truncate();
        \DB::table('user_order_logs')->truncate();
        \DB::table('user_transactions')->truncate();
        \DB::table('user_accounting')->truncate();

        // Order related
        // \DB::table('rate_logs')->truncate();
        \DB::table('chat_lost')->truncate();
        \DB::table('lost')->truncate();
        \DB::table('receipts')->truncate();
        \DB::table('export_bill_daily')->truncate();
        \DB::table('export_bills')->truncate();
        \DB::table('collect_bols')->truncate();
        \DB::table('bols_daily')->truncate();
        \DB::table('bol_reports')->truncate();
        \DB::table('check_bills')->truncate();
        \DB::table('check_items')->truncate();
        \DB::table('cart_items')->truncate();
        \DB::table('order_item_notes')->truncate();
        \DB::table('order_items')->truncate();
        \DB::table('vn_bills_of_landing')->truncate();
        \DB::table('cn_bill_of_landing_fails')->truncate();
        \DB::table('cn_bills_of_landing')->truncate();
        \DB::table('bills_of_landing')->truncate();
        \DB::table('purchases')->truncate();
        \DB::table('orders')->truncate();

        $this->comment('[+] Execute DONE!');
    }
}
