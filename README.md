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

既存のhome.blade.phpにscriptを追加する形で実装します
環境構築はこちらの記事で

https://qiita.com/akitika/items/c451954d8890385e9641

```php:home.blade.php
```

## 3. 登録・更新処理を作る

## 4. 更新用のscriptを書く