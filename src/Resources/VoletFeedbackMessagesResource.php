<?php

namespace Mydnic\VoletFeedbackMessagesFilamentPlugin\Resources;

use App\Models\FeedbackMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Mydnic\Volet\Features\FeatureManager;
use Mydnic\VoletFeedbackMessagesFilamentPlugin\Resources\FeedbackMessageResource\Pages;
use Parallax\FilamentComments\Infolists\Components\CommentsEntry;

class VoletFeedbackMessagesResource extends Resource
{
    protected static ?string $model = FeedbackMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = '피드백';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Feedback Details')
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
                        Forms\Components\Textarea::make('message')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'new' => 'New',
                                'read' => 'Read',
                                'resolved' => 'Resolved',
                            ])
                            ->required(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Screenshots')
                    ->schema([
                        Forms\Components\Repeater::make('screenshots')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('filename')
                                            ->label('File Name')
                                            ->disabled()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('type')
                                            ->label('Type')
                                            ->disabled()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('size')
                                            ->label('Size (bytes)')
                                            ->disabled()
                                            ->columnSpan(1),
                                    ]),
                                Forms\Components\TextInput::make('path')
                                    ->label('File Path')
                                    ->disabled()
                                    ->columnSpanFull(),
                                Forms\Components\ViewField::make('preview')
                                    ->label('Preview')
                                    ->view('volet-feedback-messages-filament-plugin::components.screenshot-preview')
                                    ->viewData(fn ($state, $record, $get) => [
                                        'url' => $get('url'),
                                        'filename' => $get('filename'),
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->addActionLabel('Add Screenshot')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['filename'] ?? 'Screenshot')
                            ->columnSpanFull()
                            ->disabled(), // Screenshots are read-only in admin
                    ])
                    ->visible(fn ($record) => $record && ! empty($record->screenshots)),

                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\KeyValue::make('user_info')
                            ->columnSpanFull()
                            ->keyLabel('Property')
                            ->valueLabel('Value'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bug-report' => 'danger',
                        'feature-request' => 'success',
                        'question' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('message')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }

                        return $state;
                    }),
                Tables\Columns\IconColumn::make('has_screenshots')
                    ->label('Screenshots')
                    ->icon('heroicon-o-camera')
                    ->boolean()
                    ->getStateUsing(fn ($record) => ! empty($record->screenshots))
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'read' => 'info',
                        'resolved' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($state) => $state->format('Y-m-d H:i:s')),
            ])
            ->defaultSort('created_at', 'desc')
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
                Tables\Filters\Filter::make('has_screenshots')
                    ->label('Has Screenshots')
                    ->query(fn ($query) => $query->whereNotNull('screenshots')->where('screenshots', '!=', '[]')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Action::make('Mark As Read')
                        ->authorize('markAsRead')
                        ->action(fn (FeedbackMessage $record) => $record->markAsRead())
                        ->icon('heroicon-m-eye')
                        ->visible(fn (FeedbackMessage $record) => $record->status !== 'read'),
                    Action::make('Mark As Resolved')
                        ->authorize('markAsResolved')
                        ->action(fn (FeedbackMessage $record) => $record->markAsResolved())
                        ->icon('heroicon-m-check')
                        ->visible(fn (FeedbackMessage $record) => $record->status !== 'resolved'),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_as_read')
                        ->authorize('markAnyAsRead')
                        ->label('Mark as Read')
                        ->icon('heroicon-m-eye')
                        ->action(fn ($records) => $records->each->markAsRead()),
                    Tables\Actions\BulkAction::make('mark_as_resolved')
                        ->authorize('markAnyAsResolved')
                        ->label('Mark as Resolved')
                        ->icon('heroicon-m-check')
                        ->action(fn ($records) => $records->each->markAsResolved()),
                ]),
            ]);
    }

    public static function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Feedback Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('category')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'bug-report' => 'danger',
                                'feature-request' => 'success',
                                'question' => 'info',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'new' => 'warning',
                                'read' => 'info',
                                'resolved' => 'success',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('message')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Screenshots')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('screenshots')
                            ->schema([
                                Infolists\Components\Grid::make(1)
                                    ->schema([
                                        Infolists\Components\ViewEntry::make('url')
                                            ->label('Screenshot')
                                            ->view('volet-feedback-messages-filament-plugin::components.screenshot-view')
                                            ->viewData(fn ($state, $record) => [
                                                'url' => $state,
                                            ]),
                                        Infolists\Components\Grid::make(3)
                                            ->schema([
                                                Infolists\Components\TextEntry::make('filename'),
                                                Infolists\Components\TextEntry::make('type'),
                                                Infolists\Components\TextEntry::make('size')
                                                    ->formatStateUsing(fn ($state) => number_format($state).' bytes'),
                                            ]),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => ! empty($record->screenshots)),

                Infolists\Components\Section::make('User Information')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('user_info')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                CommentsEntry::make('filament_comments'),
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
            'index' => Pages\ListFeedbackMessages::route('/'),
            'create' => Pages\CreateFeedbackMessage::route('/create'),
            'view' => Pages\ViewFeedbackMessage::route('/{record}'),
            'edit' => Pages\EditFeedbackMessage::route('/{record}/edit'),
        ];
    }
}
