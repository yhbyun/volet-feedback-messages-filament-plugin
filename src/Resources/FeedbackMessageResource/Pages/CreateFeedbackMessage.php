<?php

namespace Mydnic\VoletFeedbackMessagesFilamentPlugin\Resources\FeedbackMessageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Mydnic\VoletFeedbackMessagesFilamentPlugin\Resources\VoletFeedbackMessagesResource;

class CreateFeedbackMessage extends CreateRecord
{
    protected static string $resource = VoletFeedbackMessagesResource::class;
}
