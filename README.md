# Laravel8 blade vue 同じ画面で登録・更新処理を行う

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

Userの登録・更新処理を題材に、Controller -> vue -> bladeとデータを渡していきます

[記事](https://qiita.com/akitika/items/837aa9a0932756eb542a)
