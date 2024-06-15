<?php

namespace App\Filament\Resources\NoticeBoardResource\Pages;

use App\Filament\Resources\NoticeBoardResource;
use App\Models\Employee;
use App\Models\NoticeEmployee;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class CreateNoticeBoard extends CreateRecord
{
    protected static string $resource = NoticeBoardResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate() : void
    {   
        $record = $this->record;

        if($record->users_id){
            foreach($record->users_id as $user_id){

                $user = User::find($user_id);

                if(isset($user)) {

                    NoticeEmployee::create([
                        'notice_board_id' => $record->notice_board_id,
                        'user_id' => $user->user_id,
                    ]);
           
                    self::sendNotification($user);
                }
            }
        }
    }

    public static function sendNotification($recipient){

        Notification::make()
            ->title('HR Notice')
            ->body('You recieved a notice from the HR management')
            ->icon('heroicon-o-bell-alert')
            ->warning()
            ->actions([
                Action::make('view')
                    ->button()
                    ->color('warning')
                    ->url(route('filament.admin.home'), shouldOpenInNewTab: true)
            ])
            ->sendToDatabase($recipient);
        
        event(new DatabaseNotificationsSent($recipient));

        Notification::make()
        ->title('HR Notice')
        ->icon('heroicon-o-bell-alert')
        ->body('You recieved a notice from the HR management')
        ->seconds(5)
        ->actions([
            Action::make('view')
                ->button()
                ->color('warning')
                ->url(route('filament.admin.home'), shouldOpenInNewTab: true)
        ])
        ->warning()
        ->broadcast($recipient);
    }
}
