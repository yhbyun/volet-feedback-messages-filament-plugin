<?php

namespace Mydnic\VoletFeedbackMessagesFilamentPlugin\Resources;

use Filament\Forms;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Mydnic\Volet\Features\FeatureManager;
use Mydnic\Volet\Models\FeedbackMessage;
use Mydnic\VoletFeedbackMessagesFilamentPlugin\Resources\FeedbackMessageResource\Pages;

class VoletFeedbackMessagesResource extends Resource
{
    protected static ?string $model = FeedbackMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $modelLabel = 'Feedback Messages';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category')
                    ->options(
                        collect(
                            app(FeatureManager::class)->getFeature('feedback-messages')
                                ->getCategories()
                        )
                            ->mapWithKeys(fn ($category) => [$category['slug'] => $category['name']])
                    )
                    ->required(),
                Forms\Components\Textarea::make('message'),
                KeyValue::make('user_info')
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options([
                        'new' => 'New',
                        'read' => 'Read',
                        'resolved' => 'Resolved',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('created_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('message'),
                Tables\Columns\TextColumn::make('status')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(
                        collect(
                            app(FeatureManager::class)->getFeature('feedback-messages')
                                ->getCategories()
                        )
                            ->mapWithKeys(fn ($category) => [$category['slug'] => $category['name']])
                    ),
                SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'read' => 'Read',
                        'resolved' => 'Resolved',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Action::make('Mark As Read')
                        ->action(fn (FeedbackMessage $record) => $record->markAsRead())
                        ->icon('heroicon-m-eye'),
                    Action::make('Mark As Resolved')
                        ->action(fn (FeedbackMessage $record) => $record->markAsResolved())
                        ->icon('heroicon-m-check'),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMovieReviews::route('/'),
            'create' => Pages\CreateMovieReview::route('/create'),
            'edit' => Pages\EditMovieReview::route('/{record}/edit'),
        ];
    }
}
