<?php 
namespace App\Models\Traits;

use Carbon\Carbon;

trait UseTimestamps
{
    public function getCreatedDateAttribute()
    {
        return $this->created_at ? Carbon::parse($this->created_at)->locale('vi_VN')->diffForHumans() : null;
    }

    public function getUpdatedDateAttribute()
    {
        return $this->updated_at ? Carbon::parse($this->updated_at)->locale('vi_VN')->diffForHumans() : null;
    }
}