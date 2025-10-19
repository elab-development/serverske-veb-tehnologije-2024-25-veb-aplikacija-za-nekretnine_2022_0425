<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PropertyMessage extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $fillable = [
        'user_id',
        'agent_id', 
        'property_id',
        'msg_name',
        'msg_email',
        'msg_phone',
        'message',
        'sent_date',
        'status'
    ];

    protected $casts = [
        'sent_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'sent_date',
        'created_at',
        'updated_at'
    ];

    // Relationships
    public function property(){
        return $this->belongsTo(Property::class,'property_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function agent(){
        return $this->belongsTo(User::class,'agent_id','id');
    }

    // Accessors & Mutators
    public function setSentDateAttribute($value)
    {
        $this->attributes['sent_date'] = $value ?? now();
    }

    public function getSentDateFormattedAttribute()
    {
        return $this->sent_date ? $this->sent_date->format('d.m.Y H:i') : null;
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    public function scopeForAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update(['status' => 'read']);
    }

    public function markAsReplied()
    {
        $this->update(['status' => 'replied']);
    }

    public function isUnread()
    {
        return $this->status === 'unread';
    }

    public function isRead()
    {
        return $this->status === 'read';
    }

    public function isReplied()
    {
        return $this->status === 'replied';
    }
}
