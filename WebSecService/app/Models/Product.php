<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'model',
        'description',
        'price',
        'stock',
        'photo'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer'
    ];

    public function isInStock()
    {
        return $this->stock > 0;
    }

    public function decreaseStock()
    {
        if ($this->isInStock()) {
            $this->stock--;
            $this->save();
            return true;
        }
        return false;
    }

    public function getPhotoUrlAttribute()
    {
        if (!$this->photo) {
            return asset('images/no-image.jpg');
        }
        
        // Check if the photo is a URL
        if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
            return $this->photo;
        }
        
        // If not a URL, treat as local storage path
        return asset('storage/' . $this->photo);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}