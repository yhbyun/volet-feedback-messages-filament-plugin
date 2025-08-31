<?php

namespace Mydnic\VoletFeedbackMessagesFilamentPlugin\Resources\FeedbackMessageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Mydnic\VoletFeedbackMessagesFilamentPlugin\Resources\VoletFeedbackMessagesResource;

class ViewFeedbackMessage extends ViewRecord
{
    protected static string $resource = VoletFeedbackMessagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('mark_as_read')
                ->label('Mark as Read')
                ->icon('heroicon-m-eye')
                ->action(fn () => $this->record->markAsRead())
                ->visible(fn () => $this->record->status !== 'read'),
            Actions\Action::make('mark_as_resolved')
                ->label('Mark as Resolved')
                ->icon('heroicon-m-check')
                ->action(fn () => $this->record->markAsResolved())
                ->visible(fn () => $this->record->status !== 'resolved'),
            Actions\DeleteAction::make(),
        ];
    }
}
