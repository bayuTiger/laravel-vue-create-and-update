# Laravel8 blade vue 同じ画面で登録と更新を行う

## 環境

```zsh
// envのDB接続部を書き換えておく
php artisan migrate:fresh
composer require laravel/ui
php artisan ui vue --auth
npm install
npm install resolve-url-loader@^5.0.0 --save-dev --legacy-peer-deps
npm run dev
composer require laravel/pint --dev
php artisan serve
```

## 概要


## 1. テーブルを書き換える

1. id,name,email,timestampsのシンプルなテーブルを作成します
   - 既存のusersテーブルから余分なものを削除して、nameにunique属性を付与します

```php:create_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
```

2. 再定義したテーブルに合うように、Factoryのメソッドを修正します

```php:UserFactory.php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ];
    }
}
```

3. fakerの生成されるデータを日本語化します

```php:config/app.php
// ...
    'faker_locale' => 'ja_JP',
```

4. seederでfactoryを使うようにします

```php:UserSeeder.php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(User $user)
    {
        User::factory()->count(10)->create();
    }
}
```

5. UserSeederがコマンド実行時に走るようにします

```php:DatabaseSeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (config('app.env') === 'production') {
            Log::error('本番環境でSeederの一括実行はできません。処理を終了します。');
            return;
        }
        $this->call([
            UserSeeder::class,
        ]);
    }
}
```

ここまでできたら、プロジェクトルートで`php artisan db:seed`を実行し、DBにダミーデータが挿入されているか確認してください。
10件分のUserが登録されていたら成功です！

## 2. 表示画面を作る

既存のhome.blade.phpを改変する形で実装します
(機能的にはRegister画面と同じことをします)
※個々のbladeファイルに個別のvueを適用させる方法は以下の記事で↓

https://qiita.com/akitika/items/c451954d8890385e9641

1. まずRoutingを書き換えます

```php:route/web.php
// ...
Route::post('/store', [App\Http\Controllers\HomeController::class, 'store'])->name('store');
```

2. 次に登録画面を作成します
   - 画面レイアウトはhome.blade.phpを、入力欄はauth/login.phpを踏襲します
   - 登録処理にscriptは何ら影響を及ぼしませんが、更新時に必要なので、動作確認用にscriptを記載します

```php:home.blade.php
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">登録・更新</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('store') }}">
                            @csrf
                            {{-- 名前 --}}
                            <div class="row mb-3">
                                <label for="name"
                                    class="col-md-4 col-form-label text-md-end">{{ __('名前') }}</label>

                                <div class="col-md-6">
                                    <input v-model="name" id="name" type="text"
                                        class="form-control @error('name') is-invalid @enderror" name="name" required>

                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            {{-- メールアドレス --}}
                            <div class="row mb-3">
                                <label for="email"
                                    class="col-md-4 col-form-label text-md-end">{{ __('メールアドレス') }}</label>

                                <div class="col-md-6">
                                    <input v-model="email" id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email" required>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            {{-- パスワード --}}
                            <div class="row mb-3">
                                <label for="email"
                                    class="col-md-4 col-form-label text-md-end">{{ __('パスワード') }}</label>

                                <div class="col-md-6">
                                    <input v-model="password" id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password" required>

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" @click="confirm">登録</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('js')
    <script>
        const app = new Vue({
            el: '#app',
            data: () => {
                return {
                    name: '',
                    email: '',
                    password: '',
                }
            },
            methods: {
                confirm: function(e) {
                    if (confirm('登録しますか？')) {
                        return true;
                    } else {
                        console.log(this.name);
                        console.log(this.email);
                        console.log(this.password);
                        e.preventDefault();
                        return false;
                    }
                }
            }
        });
    </script>
@endsection
```
## 3. 登録・更新処理を作る

1. まずHomeController.phpに登録処理(storeメソッド)を記述します

```php:HomeController.php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            User::create([
                'name' => $request->input(['name']),
                'email' => $request->input(['email']),
                'password' => Hash::make($request->input(['password'])),
            ]);
        });

        return redirect()->route('home');
    }
}
```

usersテーブルを確認してみてください
入力したuserが登録されていればOKです！

2. 次にupdateOrCreateメソッドを使用した形に書き換える

```php:HomeController.php
// ...
    public function store(Request $request)
    {
        $user = DB::transaction(function () use ($request) {
            User::updateOrCreate([
                'name' => $request->input(['name']),
            ], [
                'name' => $request->input(['name']),
                'email' => $request->input(['email']),
                'password' => Hash::make($request->input(['password'])),
            ]);
        });

        dd($user);

        return redirect()->route('home', compact('user'));
    }
```

[updateOrCreateメソッド](https://readouble.com/laravel/8.x/ja/eloquent.html#upserts)を使用することで、入力されたnameと同じnameを持つデータが既にDBにある場合は更新処理、なければ新規登録処理になります
1の新規登録処理で入力したデータと、name以外が違う入力値で登録ボタンを押した後、DBの値が更新されていればOKです！

3. 登録・更新したユーザーの情報を持たせて、元の画面に遷移させる

updateOrCreateメソッドの条件にnameを使用しているので同様に、nameを元に直近で操作したUserを引っ張ってきます

```php:HomeController.php
$saved_user = User::firstWhere('name', $request->input(['name']));
return redirect()->route('home', compact('saved_user'));
```

## 4. 更新画面用にscriptを修正する

