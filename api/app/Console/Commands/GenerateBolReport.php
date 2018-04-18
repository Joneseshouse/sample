<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon as Carbon;
use App\Helpers\Tools;
use App\Modules\BolReport\Models\BolReport;

class GenerateBolReport extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:generate_bol_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Bill Of Landing daily report';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->comment('[+] Begin execute...');
        BolReport::report();
        $this->comment('[+] DONE');
    }
}
