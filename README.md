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

## 1. テーブルを書き換える

## 2. 表示画面を作る

## 3. 登録・更新処理を作る

## 4. 更新用のscriptを書く