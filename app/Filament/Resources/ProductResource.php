<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Admin';
    protected static ?string $navigationLabel = 'Products';

    public static function getSlug(): string
    {
        return 'sv23810310080-products';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->label('Category')
                        ->required(),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->reactive()
                        ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(Product::class, 'slug', ignoreRecord: true)
                        ->readOnly(),
                    TextInput::make('price')
                        ->label('Price')
                        ->required()
                        ->numeric()
                        ->rules(['numeric', 'min:0']),
                    TextInput::make('stock_quantity')
                        ->label('Stock quantity')
                        ->required()
                        ->numeric()
                        ->rules(['integer', 'min:0']),
                    Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'published' => 'Published',
                            'out_of_stock' => 'Out of stock',
                        ])
                        ->required()
                        ->default('draft'),
                    TextInput::make('warranty_period')
                        ->label('Warranty period (months)')
                        ->numeric()
                        ->default(0)
                        ->rules(['integer', 'min:0']),
                    FileUpload::make('image_path')
                        ->label('Cover image')
                        ->image()
                        ->directory('products')
                        ->required(),
                ]),
                RichEditor::make('description')
                    ->label('Description')
                    ->columnSpan('full')
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
                ImageColumn::make('image_path')
                    ->label('Photo')
                    ->rounded(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Category')->sortable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->money('VND')
                    ->sortable(),
                TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'draft' => 'secondary',
                        'published' => 'success',
                        'out_of_stock' => 'danger',
                    ])
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'out_of_stock' => 'Out of stock',
                        default => 'Unknown',
                    })
                    ->sortable(),
                TextColumn::make('warranty_label')->label('Warranty'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
