<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Helpers\DataGenerator;
use App\Helpers\Tools;
use App\Modules\RoleType\Models\RoleType;


class RoleTypeTest extends TestCase{
	use DatabaseTransactions;

    public function setUp(){
        parent::setUp();
    }

    private function removeAllRecords(){
        foreach (RoleType::all() as $item) {$item->delete();}
    }

    public function test_list(){
        self::removeAllRecords();

        RoleType::create(['uid' => 'uid-1', 'title' => 'title 1']);
        RoleType::create(['uid' => 'uid-2', 'title' => 'title 2']);
        RoleType::create(['uid' => 'uid-11', 'title' => 'title 11']);

        # Test pure
        $result = RoleType::list();
        $this->assertEquals(count($result['data']['items']), 2);
        $this->assertEquals($result['data']['_meta']['last_page'], 2);

        # Test params
        $result = RoleType::list(['uid' => 'uid-1']);
        $this->assertEquals(count($result['data']['items']), 1);

        # Test keyword
        $result = RoleType::list([], 'uid-1');
        $this->assertEquals(count($result['data']['items']), 2);

        # Test order
        $result = RoleType::list([], null, '-uid');
        $this->assertEquals($result['data']['items'][0]['uid'], 'uid-2');

        $result = RoleType::list([], null, '-id');
        $this->assertEquals($result['data']['items'][0]['uid'], 'uid-11');

        $result = RoleType::list([], null, 'id');
        $this->assertEquals($result['data']['items'][0]['uid'], 'uid-1');
    }

    public function test_obj(){
        self::removeAllRecords();

        RoleType::create(['uid' => 'uid-1', 'title' => 'title 1']);
        $id = RoleType::where('uid', 'uid-1')->first()->id;

        # Test not found
        $item = RoleType::obj(['uid' => 'uid-2']);
        $this->assertEquals($item['success'], false);

        # Test null input
        $item = RoleType::obj();
        $this->assertEquals($item['success'], false);

        # Test string input
        $item = RoleType::obj('hello');
        $this->assertEquals($item['success'], false);

        # Test id
        $item = RoleType::obj(['id' => $id]);
        $this->assertEquals($item['success'], true);

        # Test id
        $item = RoleType::obj($id);
        $this->assertEquals($item['success'], true);

        # Test other key
        $item = RoleType::obj(['uid' => 'uid-1']);
        $this->assertEquals($item['success'], true);
    }

    public function test_add(){
        self::removeAllRecords();

        # Test success
        $item = RoleType::addItem(['title' => 'title 1']);
        $this->assertEquals($item['success'], true);
        $this->assertEquals(RoleType::count(), 1);
        $this->assertEquals($item['data']['uid'], 'title-1');


        # Test duplicate
        $item = RoleType::addItem(['title' => 'title 1']);
        $this->assertEquals($item['success'], false);

        # Test error result
        $input = [
            'success' => false,
            'status_code' => 400
        ];
        $item = RoleType::addItem($input);
        $this->assertEquals($item, $input);
    }

    public function test_edit(){
        self::removeAllRecords();

        RoleType::create(['uid' => 'uid-1', 'title' => 'title 1']);
        RoleType::create(['uid' => 'uid-2', 'title' => 'title 11']);

        $id = RoleType::where('uid', 'uid-1')->first()->id;

        # Test not found
        $item = RoleType::editItem($id -1, ['title' => 'title 2']);
        $this->assertEquals($item['success'], false);

        # Test duplicate
        $item = RoleType::editItem($id, ['uid' => 'uid-2']);
        $this->assertEquals($item['success'], false);

        # Test error result
        $input = [
            'success' => false,
            'status_code' => 400
        ];
        $item = RoleType::editItem($id, $input);
        $this->assertEquals($item, $input);

        # Test success
        $item = RoleType::editItem($id, ['title' => 'title 3']);
        $this->assertEquals($item['success'], true);
        $item = RoleType::find($id);
        $this->assertEquals($item->uid, 'title-3');
    }

    public function test_remove(){
        self::removeAllRecords();

        RoleType::create(['uid' => 'uid-1', 'title' => 'title 1']);
        RoleType::create(['uid' => 'uid-2', 'title' => 'title 2']);
        RoleType::create(['uid' => 'uid-11', 'title' => 'title 11']);

        $id1 = RoleType::where('uid', 'uid-1')->first()->id;
        $id2 = RoleType::where('uid', 'uid-2')->first()->id;
        $id3 = RoleType::where('uid', 'uid-11')->first()->id;

        # Test not found
        $item = RoleType::removeItem($id1 - 1);
        $this->assertEquals($item['success'], false);

        # Test remove single success
        $item = RoleType::removeItem($id1);
        $this->assertEquals($item['success'], true);
        $this->assertEquals(RoleType::count(), 2);

        # Test remove multiple success
        $item = RoleType::removeItem(implode(',', [$id2, $id3]));
        $this->assertEquals($item['success'], true);
        $this->assertEquals(RoleType::count(), 0);
    }
}
