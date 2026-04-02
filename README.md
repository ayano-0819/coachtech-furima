## アプリケーション名
coachtech-furima

## サービス概要
本アプリは、商品の出品および購入ができるフリマアプリです。

## 制作の目的
ユーザーが簡単に商品を出品・購入できる環境を提供することを目的としています。

## ターゲットユーザー
10代〜30代の一般ユーザーを想定しています。

## 対応環境
PC（Chrome / Firefox / Safari の最新バージョン）

---

## 環境構築

### 1. リポジトリをクローン
```bash
git clone git@github.com:ayano-0819/coachtech-furima.git
```

### 2. プロジェクトに移動
```bash
cd coachtech-furima
```

### 3. Dockerコンテナをビルド
```bash
docker compose up -d --build
```

### 4. Laravelのパッケージをインストール
```bash
docker compose exec php bash
composer install
```

### 5. .envファイルを作成（PHPコンテナ内で入力）
```bash
cp .env.example .env
```

### 6. アプリケーションキーを生成（PHPコンテナ内で入力）
```bash
php artisan key:generate
```

### 7. .envファイルを修正
4で作成された「.env 」を以下のように修正してください。
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

### 8. マイグレーション実行
```bash
php artisan migrate
```

### 9. シーディング実行
```bash
php artisan db:seed
```

---

## 使用技術
- PHP 8.1.34
- Laravel 8.7
- Laravel Fortify
- MySQL 8.0
- Nginx 1.21
- Docker 28.4.0
- Mailtrap（メール認証確認用）

---

## URL
- アプリ: http://localhost
- phpMyAdmin: http://localhost:8080

---

## ログイン情報

### 一般ユーザー（シーディングデータ）
- メールアドレス: test1@example.com
- パスワード: password

- メールアドレス: test2@example.com
- パスワード: password

- メールアドレス: test3@example.com
- パスワード: password
- メールアドレス: test@example.com
- パスワード: password

※ すべてのユーザーはメール認証済みの状態で登録されています。

---

## メール認証について
本アプリでは Laravel Fortify を用いてメール認証機能を実装しています。  
会員登録後、認証メールが送信され、メール内の認証リンクをクリックすることで認証が完了します。  

開発環境では Mailtrap を使用して認証メールを確認してください。  

また、未認証ユーザーが認証必須ページへアクセスした場合は、メール認証誘導画面へリダイレクトされます。

---

## ER図
![ER図](src/docs/furima-er-diagram0322.png)