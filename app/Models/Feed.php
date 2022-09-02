<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    use HasFactory;

    protected $fillable = [
        "feedly_id",
        "url",
        "rss_path",
        "update_period_in_minute",
        "last_build_date",
        "last_crawled_at",
    ];

    /**
     * @param $value
     */
    public function setUrlAttribute($value)
    {
        $this->attributes["url"] = trim(strtolower($value));
    }

    /**
     * @param $value
     */
    public function setRssPathAttribute($value)
    {
        $this->attributes["rss_path"] = trim(strtolower($value));
    }

    /**
     *  rss_url is a generating by model accessors
     *  rss_url = url + rss_path
     * @return string
     */
    public function getRssUrlAttribute()
    {
        return "$this->url/$this->rss_path";
    }
}
