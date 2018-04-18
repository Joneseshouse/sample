<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon as Carbon;
use App\Helpers\Tools;
use App\Modules\OrderItem\Models\OrderItem;

class UpdateLocalAvatar extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:update_local_avatar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update local avatar';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->comment('[+] Begin execute...');
        // $url = 'https://cbu01.alicdn.com/img/ibank/2016/738/023/3342320837_27799352.jpg';
        $listItem = OrderItem::
            whereNull('local_avatar')->
            where('local_avatar_try', '<=' , 5)->
            get();
        $total = $listItem->count();
        $counter = 1;
        foreach ($listItem as $item) {
            $mediaPrefix = 'order_item/';
            if(strpos($item->avatar, $mediaPrefix) !== 0){
                try{
                    $fileName = str_random(20).'.jpg';
                    if(strpos($item->avatar, '//') === 0){
                        $item->avatar = config('app.protocol').':'.$item->avatar;
                    }
                    if(strpos($item->avatar, '://') === 0){
                        $item->avatar = config('app.protocol').$item->avatar;
                    }
                    copy($item->avatar, config('app.media_root').'order_item/'.$fileName);
                    $this->comment('[+] '.floor($counter/$total*100).'%');
                    $item->local_avatar = $mediaPrefix.$fileName;
                }catch(\Exception $e){
                    $this->comment('[-] fail');
                    $item->local_avatar_try+=1;
                }catch(\Error $e){
                    $this->comment('[-] fail');
                    $item->local_avatar_try+=1;
                }
                $item->save();
            }else{
                # Check the downloaded avatar.
                if(file_exists(config('app.media_root').$item->avatar)){
                    $this->comment('[+] '.floor($counter/$total*100).'%');
                    $item->local_avatar = $item->avatar;
                    $item->save();
                }
            }
            $counter++;
        }
        $this->comment('[+] DONE');
    }
}
