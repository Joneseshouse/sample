<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Helpers\DataGenerator;
use App\Helpers\Tools;
use App\Modules\Role\Models\Role;
use App\Modules\RoleType\Models\RoleType;


class RoleTest extends TestCase{
	use DatabaseTransactions;

    public function setUp(){
        parent::setUp();
        RoleType::create(['uid' => 'admin', 'title' => 'Admin']);
        RoleType::create(['uid' => 'user', 'title' => 'User']);
        $this->adminRoleTypeId = RoleType::where('uid', 'admin')->first()->id;
        $this->userRoleTypeId = RoleType::where('uid', 'user')->first()->id;
    }

    private function removeAllRecords(){
        foreach (Role::all() as $item) {$item->delete();}
    }

    public function test_list(){
        self::removeAllRecords();
        /*
        Role::create([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'Quản trị viên',
            'uid' => 'quan-tri-vien',
            'detail' => 'api/v1/config/list'
        ]);
        */
        Role::create([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 1',
            'uid' => 'title-1',
            'detail' => 'api/v1/config/list'
        ]);
        Role::create([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 2',
            'uid' => 'title-2',
            'detail' => 'api/v1/config/list'
        ]);
        Role::create([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 11',
            'uid' => 'title-11',
            'detail' => 'api/v1/config/list'
        ]);
        # Test pure
        $result = Role::list(['role_type_id' => $this->adminRoleTypeId]);
        $this->assertEquals(count($result['data']['items']), 2);
        $this->assertEquals($result['data']['_meta']['last_page'], 2);

        # Test fail
        $result = Role::list(['role_type_id' => 0]);
        $this->assertEquals($result['success'], false);

        # Test params
        $result = Role::list(['role_type_id' => $this->adminRoleTypeId, 'uid' => 'title-1']);
        $this->assertEquals(count($result['data']['items']), 1);

        # Test keyword
        $result = Role::list(['role_type_id' => $this->adminRoleTypeId], 'title-1');
        $this->assertEquals(count($result['data']['items']), 2);

        # Test order
        $result = Role::list(['role_type_id' => $this->adminRoleTypeId], null, '-uid');
        $this->assertEquals($result['data']['items'][0]['uid'], 'title-2');

        $result = Role::list(['role_type_id' => $this->adminRoleTypeId], null, '-id');
        $this->assertEquals($result['data']['items'][0]['uid'], 'title-11');

        $result = Role::list(['role_type_id' => $this->adminRoleTypeId], null, 'id');
        $this->assertEquals($result['data']['items'][0]['uid'], 'title-1');
    }

    public function test_obj(){
        self::removeAllRecords();

        Role::create([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 1',
            'uid' => 'title-1',
            'detail' => 'api/v1/config/list'
        ]);
        $id = Role::where('uid', 'title-1')->first()->id;

        # Test not found
        $item = Role::obj(['uid' => 'title-2']);
        $this->assertEquals($item['success'], false);

        # Test null input
        $item = Role::obj();
        $this->assertEquals($item['success'], false);

        # Test string input
        $item = Role::obj('hello');
        $this->assertEquals($item['success'], false);

        # Test id
        $item = Role::obj(['id' => $id]);
        $this->assertEquals($item['success'], true);

        # Test id
        $item = Role::obj($id);
        $this->assertEquals($item['success'], true);

        # Test other key
        $item = Role::obj(['uid' => 'title-1']);
        $this->assertEquals($item['success'], true);
    }

    public function test_add(){
        self::removeAllRecords();

        # Test success
        $item = Role::addItem([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 1',
            'uid' => 'title-1',
            'detail' => 'api/v1/config/list'
        ]);
        $this->assertEquals($item['success'], true);
        $this->assertEquals(Role::count(), 1);
        $this->assertEquals($item['data']['default_role'], true);

        # Test duplicate
        $item = Role::addItem([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 1',
            'uid' => 'title-1',
            'detail' => 'api/v1/config/list'
        ]);
        $this->assertEquals($item['success'], false);

        # Test error result
        $input = [
            'success' => false,
            'status_code' => 400
        ];
        $item = Role::addItem($input);
        $this->assertEquals($item, $input);
    }

    public function test_add_default_1_role_type(){
        self::removeAllRecords();

        $item1 = Role::addItem([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 1',
            'uid' => 'title-1',
            'detail' => 'api/v1/config/list'
        ]);
        $this->assertEquals($item1['data']['default_role'], true);

        $item2 = Role::addItem([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 2',
            'uid' => 'title-2',
            'detail' => 'api/v1/config/list',
            'default_role' => true
        ]);
        $item1 = Role::where('uid', 'title-1')->first();
        $item2 = Role::where('uid', 'title-2')->first();
        $this->assertEquals(Role::count(), 2);
        $this->assertEquals($item1->default_role, false);
        $this->assertEquals($item2->default_role, true);
    }

    public function test_add_default_2_role_type(){
        self::removeAllRecords();

        # Role type 1
        Role::addItem([
            'role_type_id' => $this->userRoleTypeId,
            'title' => 'title 1',
            'uid' => 'title-1',
            'detail' => 'api/v1/config/list'
        ]);

        # Role type 2
        Role::addItem([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 2',
            'uid' => 'title-2',
            'detail' => 'api/v1/config/list',
        ]);

        $item1 = Role::where('uid', 'title-1')->first();
        $item2 = Role::where('uid', 'title-2')->first();
        # Both role are 1st role of each role type => both default
        $this->assertEquals($item1->default_role, true);
        $this->assertEquals($item2->default_role, true);


        # Role type 2
        Role::addItem([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 3',
            'uid' => 'title-3',
            'detail' => 'api/v1/config/list',
            'default_role' => true # Set default this role
        ]);

        $item1 = Role::where('uid', 'title-1')->first();
        $item2 = Role::where('uid', 'title-2')->first();
        $item3 = Role::where('uid', 'title-3')->first();

        # 1st role type not affected
        # 2rd not default any more
        # 3nd default by now
        $this->assertEquals($item1->default_role, true);
        $this->assertEquals($item2->default_role, false);
        $this->assertEquals($item3->default_role, true);


        # Role type 2
        Role::addItem([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 4',
            'uid' => 'title-4',
            'detail' => 'api/v1/config/list',
        ]);

        $item1 = Role::where('uid', 'title-1')->first();
        $item2 = Role::where('uid', 'title-2')->first();
        $item3 = Role::where('uid', 'title-3')->first();
        $item4 = Role::where('uid', 'title-4')->first();

        # 1st role type not affected
        # 2rd not default any more
        # 3nd default by now
        # 4th not affected
        $this->assertEquals($item1->default_role, true);
        $this->assertEquals($item2->default_role, false);
        $this->assertEquals($item3->default_role, true);
        $this->assertEquals($item4->default_role, false);
    }

    public function test_edit(){
        self::removeAllRecords();

        Role::addItem([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 1',
            'uid' => 'title-1',
            'detail' => 'api/v1/config/list'
        ]);
        Role::addItem([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 2',
            'uid' => 'title-2',
            'detail' => 'api/v1/config/list'
        ]);

        $id = Role::where('uid', 'title-1')->first()->id;

        # Test not found
        $item = Role::editItem($id -1, ['title' => 'value 2']);
        $this->assertEquals($item['success'], false);

        # Test duplicate
        $item = Role::editItem($id, ['title' => 'title 2']);
        $this->assertEquals($item['success'], false);

        # Test error result
        $input = [
            'success' => false,
            'status_code' => 400
        ];
        $item = Role::editItem($id, $input);
        $this->assertEquals($item, $input);

        # Test success
        $item = Role::editItem($id, ['title' => 'title 3']);
        $this->assertEquals($item['success'], true);
        $item = Role::find($id);
        $this->assertEquals($item->uid, 'title-3');
    }

    public function test_edit_default_1_role_type(){
        self::removeAllRecords();

        $item1 = Role::addItem([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 1',
            'uid' => 'title-1',
            'detail' => 'api/v1/config/list',
            'default_role' => true
        ]);
        $item2 = Role::addItem([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 2',
            'uid' => 'title-2',
            'detail' => 'api/v1/config/list'
        ]);

        $item1 = Role::where('uid', 'title-1')->first();
        $item2 = Role::where('uid', 'title-2')->first();
        $this->assertEquals($item1->default_role, true);
        $this->assertEquals($item2->default_role, false);

        $id = Role::where('uid', 'title-2')->first()->id;
        Role::editItem($id, ['title' => 'title 3', 'default_role' => true]);

        $item1 = Role::where('uid', 'title-1')->first();
        $item2 = Role::where('uid', 'title-3')->first();

        $this->assertEquals($item1->default_role, false);
        $this->assertEquals($item2->default_role, true);
    }

    public function test_edit_default_2_role_type(){
        self::removeAllRecords();

        # Role type 1
        Role::addItem([
            'role_type_id' => $this->userRoleTypeId,
            'title' => 'title 1',
            'uid' => 'title-1',
            'detail' => 'api/v1/config/list'
        ]);

        # Role type 2
        Role::addItem([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 2',
            'uid' => 'title-2',
            'detail' => 'api/v1/config/list',
        ]);

        Role::addItem([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 3',
            'uid' => 'title-3',
            'detail' => 'api/v1/config/list',
        ]);

        $item1 = Role::where('uid', 'title-1')->first();
        $item2 = Role::where('uid', 'title-2')->first();
        $item3 = Role::where('uid', 'title-3')->first();

        # 1st role type deafult
        # 2rd role type default
        # 3nd role type not default
        $this->assertEquals($item1->default_role, true);
        $this->assertEquals($item2->default_role, true);
        $this->assertEquals($item3->default_role, false);


        $id = Role::where('uid', 'title-3')->first()->id;
        Role::editItem($id, ['title' => 'title 4', 'default_role' => true]);


        $item1 = Role::where('uid', 'title-1')->first();
        $item2 = Role::where('uid', 'title-2')->first();
        $item3 = Role::where('uid', 'title-4')->first();

        # 1st role type not affected
        # 2rd not default any more
        # 3nd default by now
        $this->assertEquals($item1->default_role, true);
        $this->assertEquals($item2->default_role, false);
        $this->assertEquals($item3->default_role, true);
    }

    public function test_remove(){
        self::removeAllRecords();

        Role::addItem([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 1',
            'uid' => 'title-1',
            'detail' => 'api/v1/config/list'
        ]);
        Role::addItem([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 2',
            'uid' => 'title-2',
            'detail' => 'api/v1/config/list'
        ]);
        Role::addItem([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'title 11',
            'uid' => 'title-11',
            'detail' => 'api/v1/config/list'
        ]);

        $id1 = Role::where('uid', 'title-1')->first()->id;
        $id2 = Role::where('uid', 'title-2')->first()->id;
        $id3 = Role::where('uid', 'title-11')->first()->id;

        # Test not found
        $item = Role::removeItem($id1 - 1);
        $this->assertEquals($item['success'], false);

        # Test remove single success
        $item = Role::removeItem($id1);
        $this->assertEquals($item['success'], true);
        $this->assertEquals(Role::count(), 2);

        # Test remove multiple success
        $item = Role::removeItem(implode(',', [$id2, $id3]));
        $this->assertEquals($item['success'], true);
        $this->assertEquals(Role::count(), 0);
    }
}
