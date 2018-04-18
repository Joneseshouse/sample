<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Modules\Admin\Models\Admin;
use App\Modules\Config\Models\Config;
use App\Modules\RoleType\Models\RoleType;
use App\Modules\Role\Models\Role;


class ConfigApiTest extends TestCase{
    use DatabaseTransactions;

    public function setUp(){
        parent::setUp();
        $this->fingerprint = 'test';
        $this->password = 'Qwerty!@#456';
        $this->password_md5 = 'b4e14b357c18dc9cd74b3c0f648c07c0';
        $this->password_hash = '$2y$10$/zo2NiM6ZzE1V7OUFkA7veYGbFnYxv.be/49XlrGLpGYfEVPTsmz.';
        # Add role type
        RoleType::addItem(['uid' => 'admin', 'title' => 'Admin']);
        $this->adminRoleTypeId = RoleType::where('uid', 'admin')->first()->id;

        # Add role
        Role::addItem([
            'role_type_id' => $this->adminRoleTypeId,
            'title' => 'Quản trị viên',
            'uid' => 'quan-tri-vien',
            'detail' => 'api/v1/config/list'
        ]);
        $this->roleId = Role::where('uid', 'quan-tri-vien')->first()->id;

        # Add test admin
        Admin::addItem([
            'email' => 'admin@gmail.com',
            'first_name' => 'first name',
            'last_name' => 'last name',
            'password' => $this->password_md5,
            'role_id' => $this->roleId,
        ]);

        # Get token
        $result = $this->json('POST',route('Admin.authenticate'),
            [
                'email' => 'admin@gmail.com',
                'password' => $this->password_md5
            ],[
                'Fingerprint' => $this->fingerprint
            ]
        );
        $this->token = $result->original['data']['token'];

        # Set headers
        $this->headers = [
            'HTTP_Authorization' => 'Bearer ' . $this->token,
            'Fingerprint' => $this->fingerprint
        ];
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
        $result = $this->json('GET', route('Config.list'),[
            'page' => 1
        ],$this->headers)->original;
        $this->assertEquals(count($result['data']['items']), 2);
        $this->assertEquals($result['data']['_meta']['last_page'], 2);

        # Test params
        $result = $this->json('GET', route('Config.list'), [
            'page' => 1,
            'uid' => 'uid-1'
        ], $this->headers)->original;
        $this->assertEquals(count($result['data']['items']), 1);

        # Test keyword
        $result = $this->json('GET', route('Config.list'),[
            'page' => 1,
            'keyword' => 'uid-1'
        ], $this->headers)->original;
        $this->assertEquals(count($result['data']['items']), 2);

        # Test order
        $result = $this->json('GET', route('Config.list'), [
            'page' => 1,
            'orderBy' => '-uid'
        ], $this->headers)->original;
        $this->assertEquals($result['data']['items'][0]['uid'], 'uid-2');

        $result = $this->json('GET', route('Config.list'), [
            'page' => 1,
            'orderBy' => '-id'
        ], $this->headers)->original;
        $this->assertEquals($result['data']['items'][0]['uid'], 'uid-11');

        $result = $this->json('GET', route('Config.list'), [
            'page' => 1,
            'orderBy' => 'id'
        ], $this->headers)->original;
        $this->assertEquals($result['data']['items'][0]['uid'], 'uid-1');
    }

    public function test_obj(){
        self::removeAllRecords();

        Config::create(['uid' => 'uid-1', 'value' => 'value 1']);
        $id = Config::where('uid', 'uid-1')->first()->id;

        # Test false
        $result = $this->json('GET', route('Config.obj'), [
            'id' => $id - 1,
        ], $this->headers)->original;
        $this->assertEquals($result['success'], false);

        # Test success
        $result = $this->json('GET', route('Config.obj'), [
            'id' => $id,
        ], $this->headers)->original;
        $this->assertEquals($result['success'], true);
    }

    public function test_add(){
        self::removeAllRecords();

        # Test success
        $result = $this->json('POST', route('Config.addItem'),[
            'uid' => 'uid-1',
            'value' => 'value 1'
        ], $this->headers)->original;
        $this->assertEquals($result['success'], true);
        $this->assertEquals(Config::count(), 1);

        # Test duplicate
        $result = $this->json('POST', route('Config.addItem'), [
            'uid' => 'uid-1',
            'value' => 'value 1'
        ], $this->headers)->original;
        $this->assertEquals($result['success'], false);
    }

    public function test_edit(){
        self::removeAllRecords();

        Config::create(['uid' => 'uid-1', 'value' => 'value 1']);
        Config::create(['uid' => 'uid-2', 'value' => 'value 2']);

        $id = Config::where('uid', 'uid-1')->first()->id;

        # Test not found
        $result = $this->json('POST', route('Config.editItem'), [
            'id' => $id - 1,
            'value' => 'value 2'
        ], $this->headers)->original;
        $this->assertEquals($result['success'], false);

        # Test duplicate
        $result = $this->json('POST', route('Config.editItem'), [
            'id' => $id,
            'uid' => 'uid-2'
        ], $this->headers)->original;
        $this->assertEquals($result['success'], false);

        # Test success
        $result = $this->json('POST', route('Config.editItem'), [
            'id' => $id,
            'uid' => 'uid-3',
            'value' => 'value 3'
        ], $this->headers)->original;
        $this->assertEquals($result['success'], true);
        $item = Config::find($id);
        $this->assertEquals($item->uid, 'uid-3');
        $this->assertEquals($item->value, 'value 3');
    }

    public function test_remove(){
        self::removeAllRecords();

        Config::create(['uid' => 'uid-1', 'value' => 'value 1']);
        Config::create(['uid' => 'uid-2', 'value' => 'value 2']);
        Config::create(['uid' => 'uid-11', 'value' => 'value 3']);

        $id1 = Config::where('uid', 'uid-1')->first()->id;
        $id2 = Config::where('uid', 'uid-2')->first()->id;
        $id3 = Config::where('uid', 'uid-11')->first()->id;

        # Test not found
        $result = $this->json('POST', route('Config.removeItem'), [
            'id' => $id1 - 1,
        ], $this->headers)->original;
        $this->assertEquals($result['success'], false);

        # Test remove single success
        $result = $this->json('POST', route('Config.removeItem'), [
            'id' => $id1,
        ], $this->headers)->original;
        $this->assertEquals($result['success'], true);
        $this->assertEquals(Config::count(), 2);

        # Test remove single success
        $result = $this->json('POST', route('Config.removeItem'), [
            'id' => implode(',', [$id2, $id3]),
        ], $this->headers)->original;
        $this->assertEquals($result['success'], true);
        $this->assertEquals(Config::count(), 0);
    }

    public function test_rate(){
        self::removeAllRecords();

        Config::create(['uid' => 'cny-vnd', 'value' => '3400']);

        # Test false
        $result = $this->json('GET', route('Config.rate'), [], $this->headers)->original;
        $this->assertEquals($result['success'], true);
        $this->assertEquals($result['data']['rate'], 3400);
    }
}
