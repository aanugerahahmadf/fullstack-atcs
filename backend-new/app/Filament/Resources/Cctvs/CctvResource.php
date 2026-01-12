<?php

namespace App\Filament\Resources\Cctvs;

use UnitEnum;
use BackedEnum;
use App\Models\Cctv;
use App\Models\Building;
use App\Models\Room;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ExportAction;
use App\Filament\Exports\CctvExporter;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\Cctvs\Pages\ManageCctvs;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class CctvResource extends Resource
{
    protected static ?string $model = Cctv::class;

    protected static string|UnitEnum|null $navigationGroup = 'CRUD For All Pages';

    protected static string|BackedEnum|null $navigationIcon = 'fluentui-camera-dome-28';

    protected static ?string $navigationLabel = 'Cctv';

    protected static ?string $modelLabel = 'Cctv Management';

    protected static ?string $pluralModelLabel = 'Cctv Management';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return true; // Allow all authenticated users to view the list
    }

    public static function canCreate(): bool
    {
        return Gate::allows('Create:Cctv');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('building_id')
                    ->label('Building')
                    ->options(Building::pluck('name', 'id')->unique())
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->searchPrompt('Search Building...')
                    ->required()
                    ->live(),
                Select::make('room_id')
                    ->label('Room')
                    ->options(Room::pluck('name', 'id')->unique())
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->searchPrompt('Search Room...')
                    ->required()
                    ->live(),
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('username')
                    ->default('admin'),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->default('password.123'),
                TextInput::make('ip_address')
                    ->label('IP Address')
                    ->required()
                    ->ipv4()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $ip = $get('ip_address');
                        $rtspPort = $get('rtsp_port') ?? 554;
                        $hlsPort = $get('hls_port') ?? 8000;
                        
                        if ($ip) {
                            $set('ip_rtsp_url', "rtsp://admin:password.123@{$ip}:{$rtspPort}/stream");
                            // Replace dots with underscores for HLS URL
                            $hlsStreamId = str_replace('.', '_', $ip);
                            $set('hls_url', "http://127.0.0.1:{$hlsPort}/live/{$hlsStreamId}/index.m3u8");
                        }
                    }),
                TextInput::make('rtsp_port')
                    ->label('RTSP Port')
                    ->numeric()
                    ->default(554)
                    ->minValue(1)
                    ->maxValue(65535)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $ip = $get('ip_address');
                        $rtspPort = $get('rtsp_port') ?? 554;
                        $hlsPort = $get('hls_port') ?? 8000;
                        
                        if ($ip) {
                            $set('ip_rtsp_url', "rtsp://admin:password.123@{$ip}:{$rtspPort}/stream");
                            // Replace dots with underscores for HLS URL
                            $hlsStreamId = str_replace('.', '_', $ip);
                            $set('hls_url', "http://127.0.0.1:{$hlsPort}/live/{$hlsStreamId}/index.m3u8");
                        }
                    }),
                TextInput::make('hls_port')
                    ->label('HLS Port')
                    ->numeric()
                    ->default(8000)
                    ->minValue(1)
                    ->maxValue(65535)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $ip = $get('ip_address');
                        $rtspPort = $get('rtsp_port') ?? 554;
                        $hlsPort = $get('hls_port') ?? 8000;
                        
                        if ($ip) {
                            $set('ip_rtsp_url', "rtsp://admin:password.123@{$ip}:{$rtspPort}/stream");
                            // Replace dots with underscores for HLS URL
                            $hlsStreamId = str_replace('.', '_', $ip);
                            $set('hls_url', "http://127.0.0.1:{$hlsPort}/live/{$hlsStreamId}/index.m3u8");
                        }
                    }),
                TextInput::make('ip_rtsp_url')
                    ->label('IP RTSP URL')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(function ($state, $record, callable $get) {
                        if ($record) {
                            return $record->ip_rtsp_url ?? '';
                        }
                        
                        // Show preview of what the URL will look like
                        $ip = $get('ip_address');
                        $rtspPort = $get('rtsp_port') ?? 554;
                        if ($ip) {
                            return "rtsp://admin:password.123@{$ip}:{$rtspPort}/stream";
                        }
                        return '';
                    })
                    ->visible(fn ($record) => $record !== null),
                TextInput::make('hls_url')
                    ->label('IP HLS URL')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(function ($state, $record, callable $get) {
                        if ($record) {
                            return $record->hls_url ?? '';
                        }
                        
                        // Show preview of what the URL will look like
                        $ip = $get('ip_address');
                        $hlsPort = $get('hls_port') ?? 8000;
                        if ($ip) {
                            $hlsStreamId = str_replace('.', '_', $ip);
                            return "http://127.0.0.1:{$hlsPort}/live/{$hlsStreamId}/index.m3u8";
                        }
                        return '';
                    })
                    ->visible(fn ($record) => $record !== null),

                Section::make('ATCS (Area Traffic Control System)')
                    ->description('Record specific dates for your ATCS data history. Metrics are auto-calculated based on this CCTV unit.')
                    ->schema([
                        Repeater::make('ATCSHistory')
                            ->label('ATCS Data History')
                            ->addActionLabel('Add ATCS History')
                            ->helperText('Simply select the dates you want to highlight/record. Metrics for these dates will be auto-calculated.')
                            ->relationship()
                            ->schema([
                                DatePicker::make('date')
                                    ->required()
                                    ->default(now())
                                    ->unique(ignorable: fn ($record) => $record),
                            ])
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => $state['date'] ?? null)
                            ->columns(1),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('position')
                    ->label('ID')
                    ->getStateUsing(function ($record, $rowLoop) {
                        return $rowLoop->iteration;
                    })
                    ->alignment('center'),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->alignment('center')
                    ->weight('bold')
                    ->color('primary'),
                TextColumn::make('ATCSHistory.date')
                    ->label('ATCS Performance Dates')
                    ->date('d F Y')
                    ->badge()
                    ->color('success')
                    ->separator(', ')
                    ->wrap()
                    ->alignment('center')
                    ->placeholder('No Dates Recorded'),
                TextColumn::make('building.name')
                    ->label('Building')
                    ->searchable()
                    ->alignment('center'),
                TextColumn::make('room.name')
                    ->label('Room')
                    ->searchable()
                    ->alignment('center'),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->alignment('center'),
                TextColumn::make('ip_rtsp_url')
                    ->label('IP RTSP URL')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->alignment('center'),
                TextColumn::make('hls_url')
                    ->label('IP HLS URL')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->alignment('center'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignment('center'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignment('center'),
            ])
            ->filters([
                //
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
                    ->visible(fn (): bool => Gate::allows('Update:Cctv'))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('CCTV updated')
                            ->body('The CCTV has been updated successfully.')
                    ),
                DeleteAction::make()
                    ->button()
                    ->color('danger')
                    ->size('lg')
                    ->visible(fn (): bool => Gate::allows('Delete:Cctv'))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('CCTV deleted')
                            ->body('The CCTV has been deleted successfully.')
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Gate::allows('Delete:Cctv'))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('CCTVs deleted')
                                ->body('The selected CCTVs have been deleted successfully.')
                        ),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCctvs::route('/'),
        ];
    }

     public static function getNavigationBadge(): ?string
    {
        return static::$model::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'The Number Of CCTV';
    }
}