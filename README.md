# 使用说明
### 使用composer安装

```
composer require z-golly/authority -vvv
```

### 执行命令生成配置信息和数据库迁移文件

php artisan vendor:publish --provider="Golly\Authority\AuthorityServiceProvider"

### 数据迁移

```
php artisan migrate
```

