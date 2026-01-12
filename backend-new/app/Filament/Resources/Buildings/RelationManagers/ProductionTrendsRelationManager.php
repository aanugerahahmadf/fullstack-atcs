<?php

namespace App\Filament\Resources\Buildings\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;

class ProductionTrendsRelationManager extends RelationManager
{
    protected static string $relationship = 'productionTrends';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->required()
                    ->default(now()),
                TextInput::make('production')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('target')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('traffic_volume')
                    ->numeric()
                    ->default(0),
                TextInput::make('average_speed')
                    ->numeric()
                    ->default(0),
                TextInput::make('incidents')
                    ->integer()
                    ->default(0),
                TextInput::make('congestion_index')
                    ->numeric()
                    ->default(0),
                TextInput::make('signal_changes')
                    ->integer()
                    ->default(0),
                TextInput::make('green_wave_efficiency')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('production')
                    ->numeric()
                    ->alignment('center'),
                TextColumn::make('target')
                    ->numeric()
                    ->alignment('center'),
                TextColumn::make('traffic_volume')
                    ->label('Traffic')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('average_speed')
                    ->label('Avg Speed')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->button()
                    ->color('info')
                    ->size('lg'),
                EditAction::make()
                    ->button()
                    ->color('warning')
                    ->size('lg')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Trend data updated')
                            ->body('The production trend data has been updated successfully.')
                    ),
                DeleteAction::make()
                    ->button()
                    ->color('danger')
                    ->size('lg')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Trend data deleted')
                            ->body('The production trend data has been deleted successfully.')
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
