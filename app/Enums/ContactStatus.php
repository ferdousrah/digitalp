<?php
namespace App\Enums;

enum ContactStatus: string
{
    case New = 'new';
    case Read = 'read';
    case Replied = 'replied';
    
    public function label(): string
    {
        return match($this) {
            self::New => 'New',
            self::Read => 'Read',
            self::Replied => 'Replied',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::New => 'danger',
            self::Read => 'warning',
            self::Replied => 'success',
        };
    }
}
