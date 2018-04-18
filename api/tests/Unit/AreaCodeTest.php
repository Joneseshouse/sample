<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Helpers\DataGenerator;
use App\Helpers\Tools;
use App\Modules\AreaCode\Models\AreaCode;


class AreaCodeTest extends TestCase{
	use DatabaseTransactions;

    public function setUp(){
        parent::setUp();
    }

    private function removeAllRecords(){
        foreach (AreaCode::all() as $item) {$item->delete();}
    }

    public function test_list(){
        self::removeAllRecords();

        AreaCode::create(['title' => 'title-1', 'code' => 'c-1', 'delivery_fee_unit' => 50000]);
        AreaCode::create(['title' => 'title-2', 'code' => 'c-2', 'delivery_fee_unit' => 50000]);
        AreaCode::create(['title' => 'title-11', 'code' => 'c-11', 'delivery_fee_unit' => 50000]);

        # Test pure -> no pagination
        $result = AreaCode::list();
        $this->assertEquals(count($result['data']['items']), 3);
        $this->assertEquals($result['data']['_meta']['last_page'], 1);

        # Test params
        $result = AreaCode::list(['title' => 'title-1']);
        $this->assertEquals(count($result['data']['items']), 1);

        # Test keyword
        $result = AreaCode::list([], 'title-1');
        $this->assertEquals(count($result['data']['items']), 2);

        # Test order
        $result = AreaCode::list([], null, '-code');
        $this->assertEquals($result['data']['items'][0]['code'], 'c-2');

        $result = AreaCode::list([], null, '-id');
        $this->assertEquals($result['data']['items'][0]['code'], 'c-11');

        $result = AreaCode::list([], null, 'id');
        $this->assertEquals($result['data']['items'][0]['code'], 'c-1');
    }

    public function test_obj(){
        self::removeAllRecords();

        AreaCode::create(['title' => 'title-1', 'code' => 'c-1', 'delivery_fee_unit' => 50000]);
        $id = AreaCode::where('code', 'c-1')->first()->id;

        # Test not found
        $item = AreaCode::obj(['code' => 'c-2']);
        $this->assertEquals($item['success'], false);

        # Test null input
        $item = AreaCode::obj();
        $this->assertEquals($item['success'], false);

        # Test string input
        $item = AreaCode::obj('hello');
        $this->assertEquals($item['success'], false);

        # Test id
        $item = AreaCode::obj(['id' => $id]);
        $this->assertEquals($item['success'], true);

        # Test id
        $item = AreaCode::obj($id);
        $this->assertEquals($item['success'], true);

        # Test other key
        $item = AreaCode::obj(['code' => 'c-1']);
        $this->assertEquals($item['success'], true);
    }

    public function test_add(){
        self::removeAllRecords();

        # Test success
        $item = AreaCode::addItem(['title' => 'title-1', 'code' => 'c-1', 'delivery_fee_unit' => 50000]);
        $this->assertEquals($item['success'], true);
        $this->assertEquals(AreaCode::count(), 1);


        # Test duplicate
        $item = AreaCode::addItem(['title' => 'title-1', 'code' => 'c-1', 'delivery_fee_unit' => 50000]);
        $this->assertEquals($item['success'], false);

        # Test error result
        $input = [
            'success' => false,
            'status_code' => 400
        ];
        $item = AreaCode::addItem($input);
        $this->assertEquals($item, $input);
    }

    public function test_edit(){
        self::removeAllRecords();

        AreaCode::create(['title' => 'title-1', 'code' => 'c-1', 'delivery_fee_unit' => 50000]);
        AreaCode::create(['title' => 'title-2', 'code' => 'c-2', 'delivery_fee_unit' => 50000]);

        $id = AreaCode::where('code', 'c-1')->first()->id;

        # Test not found
        $item = AreaCode::editItem($id -1, ['title' => 'title 2']);
        $this->assertEquals($item['success'], false);

        # Test duplicate
        $item = AreaCode::editItem($id, ['code' => 'c-2']);
        $this->assertEquals($item['success'], false);

        # Test error result
        $input = [
            'success' => false,
            'status_code' => 400
        ];
        $item = AreaCode::editItem($id, $input);
        $this->assertEquals($item, $input);

        # Test success
        $item = AreaCode::editItem($id, ['code' => 'c-3']);
        $this->assertEquals($item['success'], true);
        $item = AreaCode::find($id);
        $this->assertEquals($item->code, 'c-3');
    }

    public function test_remove(){
        self::removeAllRecords();

        AreaCode::create(['title' => 'title-1', 'code' => 'c-1', 'delivery_fee_unit' => 50000]);
        AreaCode::create(['title' => 'title-2', 'code' => 'c-2', 'delivery_fee_unit' => 50000]);
        AreaCode::create(['title' => 'title-11', 'code' => 'c-11', 'delivery_fee_unit' => 50000]);

        $id1 = AreaCode::where('code', 'c-1')->first()->id;
        $id2 = AreaCode::where('code', 'c-2')->first()->id;
        $id3 = AreaCode::where('code', 'c-11')->first()->id;

        # Test not found
        $item = AreaCode::removeItem($id1 - 1);
        $this->assertEquals($item['success'], false);

        # Test remove single success
        $item = AreaCode::removeItem($id1);
        $this->assertEquals($item['success'], true);
        $this->assertEquals(AreaCode::count(), 2);

        # Test remove multiple success
        $item = AreaCode::removeItem(implode(',', [$id2, $id3]));
        $this->assertEquals($item['success'], true);
        $this->assertEquals(AreaCode::count(), 0);
    }
}
