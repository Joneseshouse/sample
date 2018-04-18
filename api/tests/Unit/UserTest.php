<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Helpers\DataGenerator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon as Carbon;
use App\Helpers\Tools;
use App\Modules\User\Models\User;
use App\Modules\RoleType\Models\RoleType;
use App\Modules\Role\Models\Role;
use App\Modules\Atoken\Models\Atoken;


class UserTest extends TestCase{
	use DatabaseTransactions;

    public function setUp(){
        parent::setUp();
        $this->fingerprint = 'test';
        $this->password = 'Qwerty!@#456';
        $this->password_md5 = 'b4e14b357c18dc9cd74b3c0f648c07c0';
        $this->password_hash = '$2y$10$/zo2NiM6ZzE1V7OUFkA7veYGbFnYxv.be/49XlrGLpGYfEVPTsmz.';
    }

    private function removeAllRecords(){
        foreach (User::all() as $item) {$item->delete();}
    }

    public function test_list(){
        self::removeAllRecords();

        User::create([
            'email' => 'email1@gmail.com',
            'uid' => 'uid1',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_hash,
        ]);

        User::create([
            'email' => 'email2@gmail.com',
            'uid' => 'uid2',
            'first_name' => 'first name 2',
            'last_name' => 'last name 2',
            'password' => $this->password_hash,
        ]);

        User::create([
            'email' => 'email11@gmail.com',
            'uid' => 'uid11',
            'first_name' => 'first name 3',
            'last_name' => 'last name 3',
            'password' => $this->password_hash,
        ]);

        # Test pure
        $result = User::list();
        $this->assertEquals(count($result['data']['items']), 2);
        $this->assertEquals($result['data']['_meta']['last_page'], 2);
        /*
        # Test params
        $result = User::list(['email' => 'email1@gmail.com']);
        $this->assertEquals(count($result['data']['items']), 1);

        # Test keyword
        $result = User::list([], 'email1');
        $this->assertEquals(count($result['data']['items']), 2);

        # Test order
        $result = User::list([], null, '-email');
        $this->assertEquals($result['data']['items'][0]['email'], 'email2@gmail.com');

        $result = User::list([], null, '-id');
        $this->assertEquals($result['data']['items'][0]['email'], 'email11@gmail.com');

        $result = User::list([], null, 'id');
        $this->assertEquals($result['data']['items'][0]['email'], 'email1@gmail.com');
        */
    }
    public function test_obj(){
        self::removeAllRecords();

        User::create([
            'email' => 'email1@gmail.com',
            'uid' => 'uid1',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_hash,
        ]);

        $id = User::where('email', 'email1@gmail.com')->first()->id;

        # Test not found
        $item = User::obj(['email' => 'email2@gmail.com']);
        $this->assertEquals($item['success'], false);

        # Test null input
        $item = User::obj();
        $this->assertEquals($item['success'], false);

        # Test string input
        $item = User::obj('hello');
        $this->assertEquals($item['success'], false);

        # Test id
        $item = User::obj(['id' => $id]);
        $this->assertEquals($item['success'], true);

        # Test id
        $item = User::obj($id);
        $this->assertEquals($item['success'], true);

        # Test other key
        $item = User::obj(['email' => 'email1@gmail.com']);
        $this->assertEquals($item['success'], true);
    }

    public function test_add(){
        self::removeAllRecords();

        # Test success with password
        $item = User::addItem([
            'email' => 'email1@gmail.com',
            'uid' => 'uid1',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_md5,
        ]);
        $this->assertEquals($item['success'], true);
        $this->assertEquals(User::count(), 1);

        $item = User::where('email', 'email1@gmail.com')->first();
        $this->assertEquals(Hash::check($this->password_md5, $item->password), true);


        # Test success without password
        $item = User::addItem([
            'email' => 'email2@gmail.com',
            'uid' => 'uid2',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1'
        ]);
        $item = User::where('email', 'email2@gmail.com')->first();
        $this->assertEquals(Hash::check($this->password_md5, $item->password), false);


        # Test duplicate
        $item = User::addItem([
            'email' => 'email1@gmail.com',
            'uid' => 'uid1',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_md5,
        ]);
        $this->assertEquals($item['success'], false);


        # Test error result
        $input = [
            'success' => false,
            'status_code' => 400
        ];
        $item = User::addItem($input);
        $this->assertEquals($item, $input);
    }

    public function test_edit(){
        self::removeAllRecords();

        User::create([
            'email' => 'email1@gmail.com',
            'uid' => 'uid1',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_hash,
        ]);
        User::create([
            'email' => 'email2@gmail.com',
            'uid' => 'uid2',
            'first_name' => 'first name 2',
            'last_name' => 'last name 2',
            'password' => $this->password_hash,
        ]);

        $id = User::where('email', 'email1@gmail.com')->first()->id;

        # Test not found
        $item = User::editItem($id -1, ['first_name' => 'first name 3']);
        $this->assertEquals($item['success'], false);

        # Test duplicate uid
        $item = User::editItem($id, ['uid' => 'uid2']);
        $this->assertEquals($item['success'], false);

        # Test error result
        $input = [
            'success' => false,
            'status_code' => 400
        ];
        $item = User::editItem($id, $input);
        $this->assertEquals($item, $input);

        # Test success
        $item = User::editItem($id, ['uid' => 'uid3']);
        $this->assertEquals($item['success'], true);
        $item = User::find($id);
        $this->assertEquals($item->uid, 'uid3');

        # Test success edit password
        $item = User::editItem($id, ['password' => md5('helloworld')]);
        $this->assertEquals($item['success'], true);
        $item = User::find($id);
        $this->assertEquals(Hash::check(md5('helloworld'), $item->password), true);
    }

    public function test_remove(){
        self::removeAllRecords();

        User::create([
            'email' => 'email1@gmail.com',
            'uid' => 'uid1',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_hash,
        ]);
        User::create([
            'email' => 'email2@gmail.com',
            'uid' => 'uid2',
            'first_name' => 'first name 2',
            'last_name' => 'last name 2',
            'password' => $this->password_hash,
        ]);
        User::create([
            'email' => 'email11@gmail.com',
            'uid' => 'uid11',
            'first_name' => 'first name 11',
            'last_name' => 'last name 11',
            'password' => $this->password_hash,
        ]);

        $id1 = User::where('uid', 'uid1')->first()->id;
        $id2 = User::where('uid', 'uid2')->first()->id;
        $id3 = User::where('uid', 'uid11')->first()->id;

        # Test not found
        $item = User::removeItem($id1 - 1);
        $this->assertEquals($item['success'], false);

        # Test remove single success
        $item = User::removeItem($id1);
        $this->assertEquals($item['success'], true);
        $this->assertEquals(User::count(), 2);

        # Test remove multiple success
        $item = User::removeItem(implode(',', [$id2, $id3]));
        $this->assertEquals($item['success'], true);
        $this->assertEquals(User::count(), 0);
    }

    public function test_authenticate(){
        self::removeAllRecords();

        User::addItem([
            'email' => 'email1@gmail.com',
            'uid' => 'uid1',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_md5,
        ]);

        # Login wrong email
        $result = User::authenticate('email11@gmail.com', $this->password_md5, $this->fingerprint);
        $this->assertEquals($result['success'], false);

        # Login wrong uid
        $result = User::authenticate('uid2', $this->password_md5, $this->fingerprint);
        $this->assertEquals($result['success'], false);

        # Login wrong password with email
        $result = User::authenticate('email1@gmail.com', md5($this->password.'a'), $this->fingerprint);
        $this->assertEquals($result['success'], false);

        # Login wrong password with uid
        $result = User::authenticate('uid1', md5($this->password.'a'), $this->fingerprint);
        $this->assertEquals($result['success'], false);

        # Login success with email
        $result = User::authenticate('email1@gmail.com', $this->password_md5, $this->fingerprint);
        $this->assertEquals($result['success'], true);

        # Login success with uid
        $result = User::authenticate('uid1', $this->password_md5, $this->fingerprint);
        $this->assertEquals($result['success'], true);

        $token = Atoken::where('token', $result['data']['token'])->first();
        # Logout fail
        $result = User::logout($token, $this->fingerprint.'a');
        $this->assertEquals($result['success'], false);

        # Logout success
        $result = User::logout($token, $this->fingerprint);
        $this->assertEquals($result['success'], true);
    }

    public function test_resetPassword(){
        self::removeAllRecords();

        User::addItem([
            'email' => 'email1@gmail.com',
            'uid' => 'uid1',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_md5,
        ]);

        # Reset wrong email
        $result = User::resetPassword('email11@gmail.com', md5('helloworld'), $this->fingerprint);
        $this->assertEquals($result['success'], true);

        # Reset missing password
        $result = User::resetPassword('email1@gmail.com', null, $this->fingerprint);
        $this->assertEquals($result['success'], false);

        # Reset success
        $result = User::resetPassword('email1@gmail.com', md5('helloworld'), $this->fingerprint);
        $this->assertEquals($result['success'], true);

        $item = User::where('email', 'email1@gmail.com')->first();
        $resetPasswordToken = $item->reset_password_token;

        # Reset password confirm missing token
        $result = User::resetPasswordConfirm(null);
        $this->assertEquals($result['success'], false);

        # Reset password confirm missing token
        $result = User::resetPasswordConfirm('');
        $this->assertEquals($result['success'], false);

        # Reset password confirm wrong token
        $result = User::resetPasswordConfirm($resetPasswordToken.'a');
        $this->assertEquals($result['success'], false);

        # Reset password confirm success
        $result = User::resetPasswordConfirm($resetPasswordToken);
        $item = User::where('email', 'email1@gmail.com')->first();
        $this->assertEquals($result['success'], true);
        $this->assertEquals(Hash::check(md5('helloworld'), $item->password), true);

        # Login success with email
        $result = User::authenticate('email1@gmail.com', md5('helloworld'), $this->fingerprint);
        $this->assertEquals($result['success'], true);

        # Login success with uid
        $result = User::authenticate('uid1', md5('helloworld'), $this->fingerprint);
        $this->assertEquals($result['success'], true);

        # Reset success for timeout testing
        $result = User::resetPassword('email1@gmail.com', md5('helloworld'), $this->fingerprint);
        $this->assertEquals($result['success'], true);

        $item = User::where('email', 'email1@gmail.com')->first();
        $resetPasswordToken = $item->reset_password_token;

        $tokenCreatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $item->reset_password_token_created);
        $tokenCreatedAt->addMinutes(config('app.password_token_life') + 5);
        $item->reset_password_token_created = $tokenCreatedAt;
        $item->save();

        # Reset password confirm wrong token
        $result = User::resetPasswordConfirm($resetPasswordToken);
        $this->assertEquals($result['success'], false);
    }

    public function test_changePassword(){
        self::removeAllRecords();

        User::addItem([
            'email' => 'email1@gmail.com',
            'uid' => 'uid1',
            'first_name' => 'first name 1',
            'last_name' => 'last name 1',
            'password' => $this->password_md5,
        ]);

        # Authenticate
        User::authenticate('email1@gmail.com', $this->password_md5, $this->fingerprint);
        $item = User::where('email', 'email1@gmail.com')->first();

        # Change password missing password
        $result = User::changePassword(null, $item);
        $this->assertEquals($result['success'], false);

        # Change password missing password
        $result = User::changePassword('', $item);
        $this->assertEquals($result['success'], false);

        # Change password success
        $result = User::changePassword(md5('helloworld'), $item);
        $this->assertEquals($result['success'], true);

        $item = User::where('email', 'email1@gmail.com')->first();
        $this->assertEquals(Hash::check(md5('helloworld'), $item->password), true);
    }
}
