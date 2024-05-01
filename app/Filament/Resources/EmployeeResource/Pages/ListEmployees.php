<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Resources\Components\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    public function getTabs(): array
    {
        return [
            'All' => Tab::make(),
            'Probation' => Tab::make(),
            'Regular' => Tab::make(),
            'Employed' => Tab::make(),
            'Terminated' => Tab::make(),
            'Seperated' => Tab::make(),
            'Resigned' => Tab::make(),
            'Others' => Tab::make(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->model(User::class)  
            ->form([
                Section::make('Employee Basic Details')
                ->description('Fill out the required * Employee Details')
                ->icon('heroicon-s-user-circle')
                ->schema([
                    TextInput::make('first_name')->required()->default("John"),
                    TextInput::make('last_name')->required()->default("Doe"),
                    TextInput::make('middle_name'),
                    TextInput::make('suffix'),
                    TextInput::make('mobile')
                    ->unique()
                    ->required()->default("09302244437")
                    ->suffixIcon('heroicon-o-device-phone-mobile'),
                    TextInput::make('email')
                    ->suffixIcon('heroicon-o-envelope')
                    ->unique()
                    ->placeholder('morepower.ph')
                    ->required()
                    ->default("moretesting@morepower.ph"),
                    TextInput::make('password')->revealable()
                    ->password()
                    ->confirmed()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->default("test1234"),
                    TextInput::make('password_confirmation')->label('Password Confirmation')->password()->revealable()
                    ->default("test1234"),
                ])->columns(2)
            ])
            ->successNotification(
                Notification::make()
                     ->success()
                     ->title('New Employee added')
                     ->body('The Employee has been created successfully.'),
             )->after(function ($record) {
                $user = $record;
                
                $user->assignRole(config('constants.USER_ROLE_IS_EMPLOYEE'));

                $reference = 'MP-' . str_pad($user->user_id, 6, '0', STR_PAD_LEFT);

                $employee = $user->employee()->create([
                    'employee_reference' => $reference,
                    'created_by' => auth()->id(),
                ]);

               // Create temporary and permanent addresses
                for ($i = 0; $i < 2; $i++) {
                    
                    $type = $i == 0 ? 'TEMPORARY' : 'PERMANENT';
                    $employee->addresses()->create([
                        'type' => $type,
                    ]);
                }

                // Create Father, Mother, Spouse default type
                $family = ['FATHER','MOTHER','SPOUSE'];

                foreach($family as $type ){
                    $employee->family()->create([
                        'relationship' => $type,
                    ]);
                }


                // Create a asynchronos to generate timesheet from current date to end of the month
                // when done, Notify that timesheet is created for this user. 
                $currentDate = Carbon::now();
                $endOfMonth = Carbon::now()->endOfMonth();

                while ($currentDate <= $endOfMonth) {

                    $employee->employee_timesheets()->create([
                        'date' => $currentDate,
                    ]);

                    $currentDate->addDay();
                }
         
     

                // //delete for continue using
                // $result = $user->delete();
                // dd($result);
             }),
        ];
    }
}
