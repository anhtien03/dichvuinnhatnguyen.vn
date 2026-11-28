<?php
namespace Tests\Feature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;
class PublicSeoDiagnosticTest extends TestCase {
 protected function setUp(): void {
  parent::setUp();
  $this->assertSame('testing',app()->environment());
  $this->assertSame('sqlite',config('database.default'));
  $this->assertSame(':memory:',config('database.connections.sqlite.database'));
  foreach(['categories','products','posts'] as $name){
   Schema::create($name,function(Blueprint $t) use($name){
    $t->id();$t->string('slug');$t->string($name==='posts'?'title':'name');$t->timestamps();
    $t->boolean($name==='posts'?'is_published':'is_active');
    if($name==='posts') $t->timestamp('published_at')->nullable();
    else {$t->integer('sort_order')->default(0);$t->unsignedBigInteger($name==='categories'?'parent_id':'category_id')->nullable();}
   });
   foreach([true,false] as $visible) DB::table($name)->insert(['slug'=>$visible?'visible-fixture':'hidden-fixture',($name==='posts'?'title':'name')=>'Fixture',($name==='posts'?'is_published':'is_active')=>$visible,'updated_at'=>now()]);
  }
 }
 public function test_t04_hidden_content_is_not_public():void {
  foreach(['categories','products','posts'] as $type){
   $this->getJson('/api/v1/'.$type.'?all=1')->assertOk()->assertJsonCount(1,'data');
   $this->getJson('/api/v1/'.$type.'/hidden-fixture?all=1')->assertNotFound();
   $this->getJson('/api/v1/'.$type.'/visible-fixture')->assertOk();
  }
 }
 public function test_t05_dynamic_sitemap_matches_visible_records():void {
  $r=$this->get('/sitemap.xml')->assertOk();
  $xml=simplexml_load_string($r->getContent());$this->assertNotFalse($xml);
  $urls=[];foreach($xml->url as $entry)$urls[]=(string)$entry->loc;
  $expected=array_map(fn($p)=>'https://dichvuinnhatnguyen.vn'.$p,['/','/san-pham','/yeu-cau-bao-gia','/tin-tuc','/gioi-thieu','/lien-he','/danh-muc/visible-fixture','/san-pham/visible-fixture','/tin-tuc/visible-fixture']);
  $this->assertEqualsCanonicalizing($expected,$urls);
 }
}
