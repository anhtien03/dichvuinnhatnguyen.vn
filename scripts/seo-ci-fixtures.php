<?php
require __DIR__.'/../backend/vendor/autoload.php';
$app = require __DIR__.'/../backend/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (!app()->environment('testing') || config('database.default') !== 'sqlite') throw new RuntimeException('CI SQLite only');
$c = App\Models\Category::create(['slug'=>'fixture-category','name'=>'Danh mục kiểm thử','is_active'=>true]);
App\Models\Product::create(['slug'=>'fixture-product','name'=>'Ấn phẩm kiểm thử','category_id'=>$c->id,'is_active'=>true,'base_price'=>1000,'min_quantity'=>100,'unit'=>'cái','image'=>'/logo.png','description'=>'Mô tả kiểm thử','specs'=>['quantities'=>[['count'=>100,'label'=>'100','pricePerUnit'=>1000],['count'=>200,'label'=>'200','pricePerUnit'=>900]],'sizes'=>[['value'=>'a','label'=>'Khổ A','extra'=>0],['value'=>'b','label'=>'Khổ B','extra'=>200]],'materials'=>[],'finishings'=>[]]]);
App\Models\Post::create(['slug'=>'fixture-post','title'=>'Bài viết kiểm thử','excerpt'=>'Mô tả bài viết','content'=>'<p>Nội dung kiểm thử</p>','is_published'=>true,'published_at'=>now()]);
App\Models\Product::create(['slug'=>'hidden-fixture','name'=>'Hidden fixture','category_id'=>$c->id,'is_active'=>false,'base_price'=>1000,'min_quantity'=>100,'unit'=>'cái']);
