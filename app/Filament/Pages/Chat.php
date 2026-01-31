<?php

namespace App\Filament\Pages;

use App\Services\RagChatService;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

class Chat extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationLabel = 'AI Chat';
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected string $view = 'filament.pages.chat';

    public string $question = '';
    public string $answer = '';

    public function ask()
    {
        $this->validate([
            'question' => 'required|string|min:3',
        ]);

        $this->answer = app(RagChatService::class)->ask($this->question);
    }

    protected function getFormSchema(): array
    {
        return [
           Textarea::make('question')
                ->label('Savolingiz')
                ->rows(3)
                ->required(),

           Textarea::make('answer')
                ->label('Javob')
                ->rows(10)
                ->disabled(),
        ];
    }
}
