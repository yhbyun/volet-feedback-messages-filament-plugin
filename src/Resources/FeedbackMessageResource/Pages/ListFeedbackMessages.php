<?php

namespace Mydnic\VoletFeedbackMessagesFilamentPlugin\Resources\FeedbackMessageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Mydnic\VoletFeedbackMessagesFilamentPlugin\Resources\VoletFeedbackMessagesResource;

class ListFeedbackMessages extends ListRecords
{
    protected static string $resource = VoletFeedbackMessagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
