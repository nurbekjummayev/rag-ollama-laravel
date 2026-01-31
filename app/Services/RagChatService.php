<?php

namespace App\Services;

class RagChatService
{
    public function ask(string $question): string
    {
        $rewrittenQuestion = $this->rewriteQuestion($question);

        // 1. Savolni vector qilamiz
        $questionVector = app(EmbeddingService::class)->embed($rewrittenQuestion);

        // 2. Vector DB dan eng mos chunklarni olamiz
        $results = app(VectorStoreService::class)->search($questionVector, 5);

        if (empty($results)) {
            return 'Tegishli hujjatlardan ma’lumot topilmadi.';
        }

        // 3. Context yig‘amiz
        $context = collect($results)
            ->pluck('payload.text')
            ->implode("\n\n---\n\n");

        // 4. Prompt tayyorlaymiz
        $prompt = <<<PROMPT
Siz O‘zbekiston davlat hujjatlari bilan ishlaydigan AI assistentsiz.
Faqat berilgan hujjatlardagi ma’lumot asosida javob bering.
Taxmin qilmang, o‘ylab topmang.
Javob qisqa, rasmiy va aniq bo‘lsin.


HUJJATLAR:
$context

SAVOL:
$question
PROMPT;
        // 5. Ollama'dan javob olamiz
        return app(OllamaChatService::class)->chat($prompt);
    }

    protected function rewriteQuestion(string $question): string
    {
        $prompt = <<<PROMPT
Siz HUJJATLAR bilan ishlaydigan AI tizimsiz.

Vazifa:
- Foydalanuvchi savolini HUJJAT ichidan javob topish mumkin
  bo‘lgan rasmiy savolga aylantiring.

QATTIQ QOIDALAR:
- Faqat BIRTA SAVOL qaytaring
- Izoh, tushuntirish, gap YAZMANG
- Agar savol hujjatga mos kelmasa,
  uni "Ushbu hujjatda qanday ma’lumot bor?" shakliga keltiring
- Qo‘shtirnoq, belgi, emoji YO‘Q

MISOLLAR:
Savol: nurbek kim
Natija: Ushbu hujjatda Nurbek haqida qanday ma’lumot bor?

Savol: nurbek haqida nima bilasan
Natija: Ushbu hujjatda Nurbek haqida qanday ma’lumot bor?

Savol: {$question}
Natija:
PROMPT;

        $result = app(OllamaChatService::class)->chat($prompt);

        // ⚠️ xavfsizlik uchun
        return trim(preg_replace('/[\r\n]+/', ' ', $result));
    }

}
