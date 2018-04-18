<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Helpers\DataGenerator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon as Carbon;
use App\Helpers\Tools;
use App\Modules\Admin\Models\Admin;
use App\Modules\RoleType\Models\RoleType;
use App\Modules\Role\Models\Role;
use App\Modules\Atoken\Models\Atoken;


class AdminTest extends TestCase{
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
    }

    private function removeAllRecords(){
        foreach (Admin::all() as $item) {$item->delete();}
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
        $result = Admin::list();
        $this->assertEquals(count($result['data']['items']), 2);
        $this->assertEquals($result['data']['_meta']['last_page'], 2);

        # Test params
        $result = Admin::list(['email' => 'email1@gmail.com']);
        $this->assertEquals(count($result['data']['items']), 1);

        # Test keyword
        $result = Admin::list([], 'email1');
        $this->assertEquals(count($result['data']['items']), 2);

        # Test order
        $result = Admin::list([], null, '-email');
        $this->assertEquals($result['data']['items'][0]['email'], 'email2@gmail.com');

        $result = Admin::list([], null, '-id');
        $this->assertEquals($result['data']['items'][0]['email'], 'email11@gmail.com');

        $result = Admin::list([], null, 'id');
        $this->assertEquals($result['data']['items'][0]['email'], 'email1@gmail.com');
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

        # Test not found
        $item = Admin::obj(['email' => 'email2@gmail.com']);
        $this->assertEquals($item['success'], false);

        # Test null input
        $item = Admin::obj();
        $this->assertEquals($item['success'], false);

        # Test string input
        $item = Admin::obj('hello');
        $this->assertEquals($item['success'], false);

        # Test id
        $item = Admin::obj(['id' => $id]);
        $this->assertEquals($item['success'], true);

        # Test id
        $item = Admin::obj($id);
        $this->assertEquals($item['success'], true);

        # Test other key
        $item = Admin::obj(['email' => 'email1@gmail.com']);
        $this->assertEquals($item['success'], true);
    }

    public function test_add(){
        self::removeAllRecords();

        # Test success with password
        $item = Admin::addItem([
            'email' => 'email1@gmail.com',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_md5,
            'role_id' => $this->roleId,
        ]);
        $this->assertEquals($item['success'], true);
        $this->assertEquals(Admin::count(), 1);

        $item = Admin::where('email', 'email1@gmail.com')->first();
        $this->assertEquals(Hash::check($this->password_md5, $item->password), true);


        # Test success without password
        $item = Admin::addItem([
            'email' => 'email2@gmail.com',
            'first_name' => 'first name 2',
            'last_name' => 'last name 2',
            'role_id' => $this->roleId,
        ]);

        $item = Admin::where('email', 'email2@gmail.com')->first();
        $this->assertEquals(Hash::check($this->password_md5, $item->password), false);


        # Test duplicate
        $item = Admin::addItem([
            'email' => 'email1@gmail.com',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_md5,
            'role_id' => $this->roleId,
        ]);
        $this->assertEquals($item['success'], false);

        # Test error result
        $input = [
            'success' => false,
            'status_code' => 400
        ];
        $item = Admin::addItem($input);
        $this->assertEquals($item, $input);
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
        $item = Admin::editItem($id -1, ['email' => 'email2@gmail.com']);
        $this->assertEquals($item['success'], false);

        # Test duplicate
        $item = Admin::editItem($id, ['email' => 'email2@gmail.com']);
        $this->assertEquals($item['success'], false);

        # Test error result
        $input = [
            'success' => false,
            'status_code' => 400
        ];
        $item = Admin::editItem($id, $input);
        $this->assertEquals($item, $input);

        # Test success edit email
        $item = Admin::editItem($id, ['email' => 'email3@gmail.com']);
        $this->assertEquals($item['success'], true);
        $item = Admin::find($id);
        $this->assertEquals($item->email, 'email3@gmail.com');

        # Test success not edit email
        $item = Admin::editItem($id, ['first_name' => 'hello']);
        $this->assertEquals($item['success'], true);
        $item = Admin::find($id);
        $this->assertEquals($item->email, 'email3@gmail.com');
        $this->assertEquals($item->first_name, 'hello');

        # Test success edit password
        $item = Admin::editItem($id, ['password' => $this->password_md5]);
        $this->assertEquals($item['success'], true);
        $item = Admin::find($id);
        $this->assertEquals(Hash::check($this->password_md5, $item->password), true);
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
        $item = Admin::removeItem($id1 - 1);
        $this->assertEquals($item['success'], false);

        # Test remove single success
        $item = Admin::removeItem($id1);
        $this->assertEquals($item['success'], true);
        $this->assertEquals(Admin::count(), 2);

        # Test remove multiple success
        $item = Admin::removeItem(implode(',', [$id2, $id3]));
        $this->assertEquals($item['success'], true);
        $this->assertEquals(Admin::count(), 0);
    }

    public function test_authenticate(){
        self::removeAllRecords();

        Admin::addItem([
            'email' => 'email1@gmail.com',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_md5,
            'role_id' => $this->roleId,
        ]);

        # Login wrong email
        $result = Admin::authenticate('email11@gmail.com', $this->password_md5, $this->fingerprint);
        $this->assertEquals($result['success'], false);

        # Login wrong password
        $result = Admin::authenticate('email1@gmail.com', md5($this->password.'a'), $this->fingerprint);
        $this->assertEquals($result['success'], false);

        # Login success
        $result = Admin::authenticate('email1@gmail.com', $this->password_md5, $this->fingerprint);
        $this->assertEquals($result['success'], true);

        $token = Atoken::where('token', $result['data']['token'])->first();
        # Logout fail
        $result = Admin::logout($token, $this->fingerprint.'a');
        $this->assertEquals($result['success'], false);

        # Logout success
        $result = Admin::logout($token, $this->fingerprint);
        $this->assertEquals($result['success'], true);
    }

    public function test_resetPassword(){
        self::removeAllRecords();

        Admin::addItem([
            'email' => 'email1@gmail.com',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_md5,
            'role_id' => $this->roleId,
        ]);

        # Reset wrong email
        $result = Admin::resetPassword('email11@gmail.com', md5('helloworld'), $this->fingerprint);
        $this->assertEquals($result['success'], true);

        # Reset missing password
        $result = Admin::resetPassword('email1@gmail.com', null, $this->fingerprint);
        $this->assertEquals($result['success'], false);

        # Reset success
        $result = Admin::resetPassword('email1@gmail.com', md5('helloworld'), $this->fingerprint);
        $this->assertEquals($result['success'], true);

        $item = Admin::where('email', 'email1@gmail.com')->first();
        $resetPasswordToken = $item->reset_password_token;

        # Reset password confirm missing token
        $result = Admin::resetPasswordConfirm(null);
        $this->assertEquals($result['success'], false);

        # Reset password confirm missing token
        $result = Admin::resetPasswordConfirm('');
        $this->assertEquals($result['success'], false);

        # Reset password confirm wrong token
        $result = Admin::resetPasswordConfirm($resetPasswordToken.'a');
        $this->assertEquals($result['success'], false);

        # Reset password confirm success
        $result = Admin::resetPasswordConfirm($resetPasswordToken);
        $item = Admin::where('email', 'email1@gmail.com')->first();
        $this->assertEquals($result['success'], true);
        $this->assertEquals(Hash::check(md5('helloworld'), $item->password), true);

        # Reset success for timeout testing
        $result = Admin::resetPassword('email1@gmail.com', md5('helloworld'), $this->fingerprint);
        $this->assertEquals($result['success'], true);

        $item = Admin::where('email', 'email1@gmail.com')->first();
        $resetPasswordToken = $item->reset_password_token;

        $tokenCreatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $item->reset_password_token_created);
        $tokenCreatedAt->addMinutes(config('app.password_token_life') + 5);
        $item->reset_password_token_created = $tokenCreatedAt;
        $item->save();

        # Reset password confirm wrong token
        $result = Admin::resetPasswordConfirm($resetPasswordToken);
        $this->assertEquals($result['success'], false);
    }

    public function test_changePassword(){
        self::removeAllRecords();

        Admin::addItem([
            'email' => 'email1@gmail.com',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_md5,
            'role_id' => $this->roleId,
        ]);
        # Authenticate
        Admin::authenticate('email1@gmail.com', $this->password_md5, $this->fingerprint);
        $item = Admin::where('email', 'email1@gmail.com')->first();

        # Change password missing password
        $result = Admin::changePassword(null, $item);
        $this->assertEquals($result['success'], false);

        # Change password missing password
        $result = Admin::changePassword('', $item);
        $this->assertEquals($result['success'], false);

        # Change password success
        $result = Admin::changePassword(md5('helloworld'), $item);
        $this->assertEquals($result['success'], true);

        $item = Admin::where('email', 'email1@gmail.com')->first();
        $changePasswordToken = $item->change_password_token;


        # Change password confirm missing token
        $result = Admin::changePasswordConfirm(null);
        $this->assertEquals($result['success'], false);

        # Change password confirm missing token
        $result = Admin::changePasswordConfirm('');
        $this->assertEquals($result['success'], false);

        # Change password confirm wrong token
        $result = Admin::changePasswordConfirm($changePasswordToken.'a');
        $this->assertEquals($result['success'], false);

        # Change password confirm success
        $result = Admin::changePasswordConfirm($changePasswordToken);
        $item = Admin::where('email', 'email1@gmail.com')->first();
        $this->assertEquals($result['success'], true);
        $this->assertEquals(Hash::check(md5('helloworld'), $item->password), true);

        # Change success for timeout testing
        $result = Admin::changePassword(md5('helloworld'), $item);
        $this->assertEquals($result['success'], true);

        $item = Admin::where('email', 'email1@gmail.com')->first();
        $changePasswordToken = $item->change_password_token;

        $tokenCreatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $item->change_password_token_created);
        $tokenCreatedAt->addMinutes(config('app.password_token_life') + 5);
        $item->change_password_token_created = $tokenCreatedAt;
        $item->save();

        # Change password confirm wrong token
        $result = Admin::changePasswordConfirm($changePasswordToken);
        $this->assertEquals($result['success'], false);
    }
}
