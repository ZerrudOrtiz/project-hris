<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class EmployeeLeaveRequest extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public $record;

    public function mount($record)
    {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Leave::query()->where('approver_id', $this->record->user_id))
            ->columns([
                Split::make([
                    TextColumn::make('date_filling')
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        return Carbon::parse($state)->format('F d, Y');
                    })
                    ->sortable(),
                    ImageColumn::make('avatar')
                    ->grow(false)
                    ->getStateUsing(function (Leave $data): string {
    
                        $employee = Employee::find($data->employee_id);
                        return isset($employee) ? $employee->picture : '';
                    })
                    ->circular(),
                    Stack::make([
                        TextColumn::make('employee_reference')
                        ->getStateUsing(function (Leave $data): string {
        
                            $employee = Employee::find($data->employee_id);
                            return isset($employee) ? $employee->employee_reference : '';
                        }), 
                        TextColumn::make('employee_id')
                        ->weight(FontWeight::Bold)
                        ->getStateUsing(function (Leave $data): string {
        
                            $employee = Employee::find($data->employee_id);
                            return isset($employee->user->name) ? $employee->user->name : '';
                        }), 
                    ]),
                    Stack::make([
                        Split::make([
                            TextColumn::make('from')
                            ->formatStateUsing(function ($state) {
                                return 'From :';
                            })
                            ->grow(false)
                            ->weight(FontWeight::Bold),
                            TextColumn::make('from')
                            ->formatStateUsing(function ($state) {
                                return Carbon::parse($state)->format('F d, Y');
                            })
                            ->grow(false),
                            TextColumn::make('to')
                            ->formatStateUsing(function ($state) {
                                return 'To :';
                            })
                            ->grow(false)
                            ->weight(FontWeight::Bold),
                            TextColumn::make('to')
                            ->formatStateUsing(function ($state) {
                                return Carbon::parse($state)->format('F d, Y');
                            })->grow(false),
                        ]),
                        Split::make([
                            TextColumn::make('hours')
                            ->formatStateUsing(function ($state) {
                                return 'Hours :';
                            })
                            ->grow(false)
                            ->weight(FontWeight::Bold),
                            TextColumn::make('hours')
                            ->grow(false),
                        ]),
                        Split::make([
                            TextColumn::make('remarks')
                            ->formatStateUsing(function ($state) {
                                return 'Remarks :';
                            })
                            ->grow(false)
                            ->weight(FontWeight::Bold),
                            TextColumn::make('remarks')
                            ->limit(10)
                            ->tooltip(function (TextColumn $column): ?string {
                                $state = $column->getState();
                        
                                if (strlen($state) <= $column->getCharacterLimit()) {
                                    return null;
                                }
                        
                                return $state;
                            }), 
                        ]),

                ])
                ->alignment(Alignment::Start)
                ->grow(false),
                TextColumn::make('status')
                ->color(fn (string $state): string => match($state) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'denied' => 'danger',
                    'void' => 'danger',
                })
                ->sortable()
                ->alignCenter(),
                ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->badge()
                ->color('primary'),
                Tables\Actions\Action::make('Approved')
                ->badge()
                ->color('success')
                ->icon('heroicon-s-check-circle')
                ->action(function (Leave $record, array $data) {
                    
                    // $record['status'] = 'void';
                    // $record->save();
                })->requiresConfirmation(),
                Tables\Actions\Action::make('Disapproved')
                ->badge()

                ->color('danger')
                ->icon('heroicon-s-x-circle')
                ->action(function (Leave $record, array $data) {
                    
                    // $record['status'] = 'void';
                    // $record->save();
                })->requiresConfirmation()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ])
            ->defaultPaginationPageOption(5);
    }

    public function render(): View
    {
        return view('livewire.employee-leave-request');
    }
}
