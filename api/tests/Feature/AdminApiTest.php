<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use App\Modules\Admin\Models\Admin;
use App\Modules\RoleType\Models\RoleType;
use App\Modules\Role\Models\Role;


class AdminApiTest extends TestCase{
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
        $listItem = Admin::whereNotIn('email', ['admin@gmail.com'])->get();
        foreach ($listItem as $item) {$item->delete();}
    }

    public function test_list(){
        self::removeAllRecords();

        Admin::create([
            'email' => 'email1@gmail.com',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_hash,
            'role_id' => $this->roleId,
        ]);
        Admin::create([
            'email' => 'email2@gmail.com',
            'first_name' => 'first name 2',
            'last_name' => 'last name 2',
            'password' => $this->password_hash,
            'role_id' => $this->roleId,
        ]);
        Admin::create([
            'email' => 'email11@gmail.com',
            'first_name' => 'first name 11',
            'last_name' => 'last name 11',
            'password' => $this->password_hash,
            'role_id' => $this->roleId,
        ]);

        # Test pure
        $result = $this->json('GET', route('Admin.list'),[
            'page' => 1
        ],$this->headers)->original;
        $this->assertEquals(count($result['data']['items']), 2);
        $this->assertEquals($result['data']['_meta']['last_page'], 2);

        # Test params
        $result = $this->json('GET', route('Admin.list'), [
            'page' => 1,
            'email' => 'email1@gmail.com'
        ], $this->headers)->original;
        $this->assertEquals(count($result['data']['items']), 1);

        # Test keyword
        $result = $this->json('GET', route('Admin.list'),[
            'page' => 1,
            'keyword' => 'email1'
        ], $this->headers)->original;
        $this->assertEquals(count($result['data']['items']), 2);
    }

    public function test_obj(){
        self::removeAllRecords();

        Admin::create([
            'email' => 'email1@gmail.com',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_hash,
            'role_id' => $this->roleId,
        ]);
        $id = Admin::where('email', 'email1@gmail.com')->first()->id;

        # Test false
        $result = $this->json('GET', route('Admin.obj'), [
            'id' => $id - 2,
        ], $this->headers)->original;
        $this->assertEquals($result['success'], false);

        # Test success
        $result = $this->json('GET', route('Admin.obj'), [
            'id' => $id,
        ], $this->headers)->original;
        $this->assertEquals($result['success'], true);
    }

    public function test_add(){
        self::removeAllRecords();

        # Test success
        $result = $this->json('POST', route('Admin.addItem'),[
            'email' => 'email1@gmail.com',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_hash,
            'role_id' => $this->roleId
        ], $this->headers)->original;
        $this->assertEquals($result['success'], true);
        $this->assertEquals(Admin::count(), 2);

        # Test duplicate
        $result = $this->json('POST', route('Admin.addItem'), [
            'email' => 'email1@gmail.com',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_hash,
            'role_id' => $this->roleId
        ], $this->headers)->original;
        $this->assertEquals($result['success'], false);
    }

    public function test_edit(){
        self::removeAllRecords();

        Admin::create([
            'email' => 'email1@gmail.com',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_hash,
            'role_id' => $this->roleId,
        ]);
        Admin::create([
            'email' => 'email2@gmail.com',
            'first_name' => 'first name 2',
            'last_name' => 'last name 2',
            'password' => $this->password_hash,
            'role_id' => $this->roleId,
        ]);

        $id = Admin::where('email', 'email1@gmail.com')->first()->id;

        # Test not found
        $result = $this->json('POST', route('Admin.editItem'), [
            'id' => $id - 2,
            'email' => 'email1@gmail.com',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'role_id' => $this->roleId,
        ], $this->headers)->original;
        $this->assertEquals($result['success'], false);

        # Test duplicate
        $result = $this->json('POST', route('Admin.editItem'), [
            'id' => $id,
            'email' => 'email2@gmail.com',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'role_id' => $this->roleId,
        ], $this->headers)->original;
        $this->assertEquals($result['success'], false);

        # Test success
        $result = $this->json('POST', route('Admin.editItem'), [
            'id' => $id,
            'email' => 'email3@gmail.com',
            'first_name' => 'first name 3',
            'last_name' => 'last name 3',
            'role_id' => $this->roleId,
        ], $this->headers)->original;
        $this->assertEquals($result['success'], true);
        $item = Admin::find($id);
        $this->assertEquals($item->email, 'email3@gmail.com');
        $this->assertEquals($item->first_name, 'first name 3');
        $this->assertEquals($item->last_name, 'last name 3');
    }

    public function test_remove(){
        self::removeAllRecords();

        Admin::create([
            'email' => 'email1@gmail.com',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_hash,
            'role_id' => $this->roleId,
        ]);
        Admin::create([
            'email' => 'email2@gmail.com',
            'first_name' => 'first name 2',
            'last_name' => 'last name 2',
            'password' => $this->password_hash,
            'role_id' => $this->roleId,
        ]);
        Admin::create([
            'email' => 'email11@gmail.com',
            'first_name' => 'first name 11',
            'last_name' => 'last name 11',
            'password' => $this->password_hash,
            'role_id' => $this->roleId,
        ]);

        $id1 = Admin::where('email', 'email1@gmail.com')->first()->id;
        $id2 = Admin::where('email', 'email2@gmail.com')->first()->id;
        $id3 = Admin::where('email', 'email11@gmail.com')->first()->id;

        # Test not found
        $result = $this->json('POST', route('Admin.removeItem'), [
            'id' => $id1 - 2,
        ], $this->headers)->original;
        $this->assertEquals($result['success'], false);

        # Test remove single success
        $result = $this->json('POST', route('Admin.removeItem'), [
            'id' => $id1,
        ], $this->headers)->original;
        $this->assertEquals($result['success'], true);
        $this->assertEquals(Admin::count(), 3);

        # Test remove single success
        $result = $this->json('POST', route('Admin.removeItem'), [
            'id' => implode(',', [$id2, $id3]),
        ], $this->headers)->original;
        $this->assertEquals($result['success'], true);
        $this->assertEquals(Admin::count(), 1);
    }

    public function test_authenticate(){
        self::removeAllRecords();
        $this->json('POST', route('Admin.logout'), [], $this->headers);

        Admin::addItem([
            'email' => 'email1@gmail.com',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_md5,
            'role_id' => $this->roleId,
        ]);

        # Login wrong email
        $result = $this->json('POST', route('Admin.authenticate'), [
            'email' => 'email11@gmail.com',
            'password' => $this->password_md5
        ], $this->headers)->original;
        $this->assertEquals($result['success'], false);

        # Login wrong password
        $result = $this->json('POST', route('Admin.authenticate'), [
            'email' => 'email1@gmail.com',
            'password' => $this->password_md5.'a'
        ], $this->headers)->original;
        $this->assertEquals($result['success'], false);

        # Login success
        $result = $this->json('POST', route('Admin.authenticate'), [
            'email' => 'email1@gmail.com',
            'password' => $this->password_md5
        ], $this->headers)->original;
        # print_r($result);die;
        $this->assertEquals($result['success'], true);
        $this->token = $result['data']['token'];
        $this->headers = [
            'HTTP_Authorization' => 'Bearer ' . $this->token,
            'Fingerprint' => $this->fingerprint
        ];

        # profile
        $result = $this->json('GET', route('Admin.profile'), [
        ], $this->headers)->original;
        $this->assertEquals($result['success'], true);

        # update profile
        $result = $this->json('POST', route('Admin.updateProfile'), [
            'email' => 'email1@gmail.com',
            'first_name' => 'hello',
            'last_name' => 'world',
            'role_id' => $this->roleId
        ], $this->headers)->original;
        $this->assertEquals($result['success'], true);

        # Logout
        $result = $this->json('POST', route('Admin.logout'), [
        ], $this->headers)->original;
        $this->assertEquals($result['success'], true);
    }

    public function test_resetPassword(){
        self::removeAllRecords();
        $this->json('POST', route('Admin.logout'), [], $this->headers);

        Admin::addItem([
            'email' => 'email1@gmail.com',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_md5,
            'role_id' => $this->roleId,
        ]);

        # Reset password
        $result = $this->json('POST', route('Admin.resetPassword'), [
            'email' => 'email1@gmail.com',
            'password' => md5('helloworld')
        ], $this->headers)->original;
        $this->assertEquals($result['success'], true);

        $item = Admin::where('email', 'email1@gmail.com')->first();
        $resetPasswordToken = $item->reset_password_token;

        # Reset password confirm
        $result = $this->json('GET', route('Admin.resetPasswordConfirm'), [
            'token' => $resetPasswordToken,
        ], $this->headers)->original;
        $item = Admin::where('email', 'email1@gmail.com')->first();
        $this->assertEquals($result['success'], true);
        $this->assertEquals(Hash::check(md5('helloworld'), $item->password), true);
    }

    public function test_changePassword(){
        self::removeAllRecords();
        $this->json('POST', route('Admin.logout'), [], $this->headers);

        Admin::addItem([
            'email' => 'email1@gmail.com',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_md5,
            'role_id' => $this->roleId,
        ]);

        $result = $this->json('POST', route('Admin.authenticate'), [
            'email' => 'email1@gmail.com',
            'password' => $this->password_md5
        ], $this->headers)->original;
        $this->token = $result['data']['token'];
        $this->headers = [
            'HTTP_Authorization' => 'Bearer ' . $this->token,
            'Fingerprint' => $this->fingerprint
        ];

        # Change password
        $result = $this->json('POST', route('Admin.changePassword'), [
            'password' => md5('helloworld')
        ], $this->headers)->original;
        $this->assertEquals($result['success'], true);

        $item = Admin::where('email', 'email1@gmail.com')->first();
        $changePasswordToken = $item->change_password_token;

        # Reset password confirm
        $result = $this->json('GET', route('Admin.changePasswordConfirm'), [
            'token' => $changePasswordToken,
        ], $this->headers)->original;
        $item = Admin::where('email', 'email1@gmail.com')->first();
        $this->assertEquals($result['success'], true);
        $this->assertEquals(Hash::check(md5('helloworld'), $item->password), true);
    }
}
