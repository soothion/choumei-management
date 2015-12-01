<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PresentArticleCode extends Model
{
    protected $table = 'present_article_code';
    protected $primaryKey = 'article_code_id';
    public $timestamps = false;
}
