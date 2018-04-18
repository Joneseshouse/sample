<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Helpers\DataGenerator;
use App\Helpers\Tools;
use App\Modules\Config\Models\Config;


class ConfigTest extends TestCase{
	use DatabaseTransactions;

    public function setUp(){
        parent::setUp();
    }

    private function removeAllRecords(){
        foreach (Config::all() as $item) {$item->delete();}
    }

    public function test_list(){
        self::removeAllRecords();

        Config::create(['uid' => 'uid-1', 'value' => 'value 1']);
        Config::create(['uid' => 'uid-2', 'value' => 'value 2']);
        Config::create(['uid' => 'uid-11', 'value' => 'value 11']);

        # Test pure
        $result = Config::list();
        $this->assertEquals(count($result['data']['items']), 2);
        $this->assertEquals($result['data']['_meta']['last_page'], 2);

        # Test params
        $result = Config::list(['uid' => 'uid-1']);
        $this->assertEquals(count($result['data']['items']), 1);

        # Test keyword
        $result = Config::list([], 'uid-1');
        $this->assertEquals(count($result['data']['items']), 2);

        # Test order
        $result = Config::list([], null, '-uid');
        $this->assertEquals($result['data']['items'][0]['uid'], 'uid-2');

        $result = Config::list([], null, '-id');
        $this->assertEquals($result['data']['items'][0]['uid'], 'uid-11');

        $result = Config::list([], null, 'id');
        $this->assertEquals($result['data']['items'][0]['uid'], 'uid-1');
    }

    public function test_get(){
        self::removeAllRecords();

        Config::create(['uid' => 'uid-1', 'value' => 'value 1']);

        # Test fail
        $item = Config::get('uid-11', 'hello');
        $this->assertEquals($item, 'hello');

        # Test success
        $item = Config::get('uid-1');
        $this->assertEquals($item, 'value 1');
    }

    public function test_obj(){
        self::removeAllRecords();

        Config::create(['uid' => 'uid-1', 'value' => 'value 1']);
        $id = Config::where('uid', 'uid-1')->first()->id;

        # Test not found
        $item = Config::obj(['uid' => 'uid-2']);
        $this->assertEquals($item['success'], false);

        # Test null input
        $item = Config::obj();
        $this->assertEquals($item['success'], false);

        # Test string input
        $item = Config::obj('hello');
        $this->assertEquals($item['success'], false);

        # Test id
        $item = Config::obj(['id' => $id]);
        $this->assertEquals($item['success'], true);

        # Test id
        $item = Config::obj($id);
        $this->assertEquals($item['success'], true);

        # Test other key
        $item = Config::obj(['uid' => 'uid-1']);
        $this->assertEquals($item['success'], true);
    }

    public function test_add(){
        self::removeAllRecords();

        # Test success
        $item = Config::addItem(['uid' => 'uid-1', 'value' => 'value 1']);
        $this->assertEquals($item['success'], true);
        $this->assertEquals(Config::count(), 1);


        # Test duplicate
        $item = Config::addItem(['uid' => 'uid-1', 'value' => 'value 1']);
        $this->assertEquals($item['success'], false);

        # Test error result
        $input = [
            'success' => false,
            'status_code' => 400
        ];
        $item = Config::addItem($input);
        $this->assertEquals($item, $input);
    }

    public function test_edit(){
        self::removeAllRecords();

        Config::create(['uid' => 'uid-1', 'value' => 'value 1']);
        Config::create(['uid' => 'uid-2', 'value' => 'value 11']);

        $id = Config::where('uid', 'uid-1')->first()->id;

        # Test not found
        $item = Config::editItem($id -1, ['value' => 'value 2']);
        $this->assertEquals($item['success'], false);

        # Test duplicate
        $item = Config::editItem($id, ['uid' => 'uid-2']);
        $this->assertEquals($item['success'], false);

        # Test error result
        $input = [
            'success' => false,
            'status_code' => 400
        ];
        $item = Config::editItem($id, $input);
        $this->assertEquals($item, $input);

        # Test success
        $item = Config::editItem($id, ['uid' => 'uid-3']);
        $this->assertEquals($item['success'], true);
        $item = Config::find($id);
        $this->assertEquals($item->uid, 'uid-3');
    }

    public function test_remove(){
        self::removeAllRecords();

        Config::create(['uid' => 'uid-1', 'value' => 'value 1']);
        Config::create(['uid' => 'uid-2', 'value' => 'value 2']);
        Config::create(['uid' => 'uid-11', 'value' => 'value 11']);

        $id1 = Config::where('uid', 'uid-1')->first()->id;
        $id2 = Config::where('uid', 'uid-2')->first()->id;
        $id3 = Config::where('uid', 'uid-11')->first()->id;

        # Test not found
        $item = Config::removeItem($id1 - 1);
        $this->assertEquals($item['success'], false);

        # Test remove single success
        $item = Config::removeItem($id1);
        $this->assertEquals($item['success'], true);
        $this->assertEquals(Config::count(), 2);

        # Test remove multiple success
        $item = Config::removeItem(implode(',', [$id2, $id3]));
        $this->assertEquals($item['success'], true);
        $this->assertEquals(Config::count(), 0);
    }
}
