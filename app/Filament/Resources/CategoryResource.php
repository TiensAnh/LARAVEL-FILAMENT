<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Admin';
    protected static ?string $navigationLabel = 'Categories';

    public static function getSlug(): string
    {
        return 'sv23810310080-categories';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->label('Category Name')
                        ->required()
                        ->maxLength(255)
                        ->reactive()
                        ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(Category::class, 'slug', ignoreRecord: true)
                        ->readOnly(),
                    Toggle::make('is_visible')
                        ->label('Visible')
                        ->default(true),
                ]),
                RichEditor::make('description')
                    ->label('Description')
                    ->toolbarButtons([
                        'bold',
                        'bulletList',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'undo',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug')->sortable(),
                TextColumn::make('description')->limit(50),
                TextColumn::make('is_visible')
                    ->label('Visible')
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_visible')
                    ->label('Visibility')
                    ->options([
                        1 => 'Visible',
                        0 => 'Hidden',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
