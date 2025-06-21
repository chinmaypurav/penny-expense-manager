<?php

namespace App\Filament\Concerns;

use App\Enums\PanelId;
use Filament\Actions\DeleteBulkAction;

trait BulkDeleter
{
    public static function deleteBulkAction(): DeleteBulkAction
    {
        return DeleteBulkAction::make()->visible(PanelId::APP->isCurrentPanel());
    }
}
