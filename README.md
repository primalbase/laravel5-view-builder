# リファレンス

## dwtファイルからlayout.phpを生成する

```bash
$ php artisan make:layout
$ php artisan make:layout --source=other.dwt # other.dwtから生成(defaultはmain)
```

## htmlファイルから*.blade.phpを生成する

```bash
$ php artisan make:view index.html home.index
```

## レイアウトを一括更新

```bash
$ php artisan update:layout
packages/primalbase/laravel-view-build/config.phpの定義に従ってファイルを一括更新(layout:make)する
```

## ビューを一括更新 

```bash
$ php artisan update:view
packages/primalbase/laravel-view-build/config.phpの定義に従ってファイルを一括更新(view:make)する
```

# Examples

## レイアウトを作成

```bash
$ php artisan make:layout
```

views/layout/base/main.blade.phpとviews/layout/main.blade.phpが作成される

ファイルが既に存在する場合は上書きしない

ベースファイルは常に上書きされる

## レイアウトを作成(ベースファイルなし)

```bash
$  php artisan make:layout --no-base
```

views/layout/main.blade.phpが作成される

ファイルが既に存在する場合は上書き確認[y/N]

## mainMEMBER.dwtからviews/layout/member.blade.phpを作成

```bash
$ php artisan make:layout --source=mainMember.dwt --layout=layout.member
```

## ビューを作成

```bash
$ php artisan make:view index.html home.index
```

views/home/base/index.blade.phpとviews/home/index.blade.phpが作成される

ファイルが既に存在する場合は上書きしない

ベースファイルは常に上書きされる

## ビューを作成(ベースファイルなし)

```bash
$ php artisan make:view index.html home.index --no-base
```

views/home/index.blade.phpが作成される

ファイルが既に存在する場合は上書き確認[y/N]

## ビューを作成(モジュール内、レイアウト指定)

```bash
php artisan make:view member/bbs/detail.html member::board.show --layout=layout.member
```

modules/member/views/home/base/show.blade.phpとmodules/member/views/home/show.blade.phpが作成される

レイアウトは@extends('layout.member')となる
