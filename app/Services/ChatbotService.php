<?php

namespace App\Services;

use App\Models\Faq;

class ChatbotService
{
    public function getReply(string $message): array
    {
        $message = trim($message);

        if ($this->isEmergency($message)) {
            return [
                'reply' => $this->emergencyReply(),
                'category' => 'Emergency',
                'is_emergency' => true,
                'suggestions' => [
                    'Call 112 / 999',
                    'Contact Trusted Adult',
                    'Find Safe Place',
                ],
                'link_label' => 'Talk to Childline (24/7)',
                'link_url' => 'https://www.childline.ie/',
            ];
        }

        $faq = $this->findBestFaqMatch($message);

        if ($faq) {
            return [
                'reply' => $faq->answer,
                'category' => $faq->category,
                'is_emergency' => (bool) $faq->is_emergency,
                'suggestions' => $this->getSuggestionsByCategory($faq->category),
                'link_label' => $faq->link_label,
                'link_url' => $faq->link_url,
            ];
        }

        return [
            'reply' => $this->fallbackReply($message),
            'category' => null,
            'is_emergency' => false,
            'suggestions' => [
                'Housing Help',
                'Education Support',
                'Jobs & CV',
                'Money Advice',
                'Wellbeing',
                'Emergency Help',
            ],
            'link_label' => null,
            'link_url' => null,
        ];
    }

    protected function isEmergency(string $message): bool
    {
        $message = strtolower($message);

        $keywords = [
            'suicide',
            'suicidal',
            'self-harm',
            'abuse',
            'unsafe',
            'homeless tonight',
            'nowhere to stay',
            'emergency',
            'panic attack',
            'danger',
            'i feel unsafe',
            'i am in danger',
            'help now',
            'urgent',
        ];

        foreach ($keywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }

        return false;
    }

    protected function emergencyReply(): string
    {
        return "This sounds urgent. Please contact a trusted adult, support worker, or emergency services immediately if you are in danger. You can also use CareHub to find emergency housing or wellbeing support.";
    }

    protected function findBestFaqMatch(string $message): ?Faq
    {
        $messageLower = strtolower(trim($message));
        $messageWords = $this->normalizeWords($message);
        $faqs = Faq::all();

        $bestFaq = null;
        $bestScore = 0;

        foreach ($faqs as $faq) {
            $score = 0;

            $question = strtolower($faq->question);
            $keywordsRaw = strtolower($faq->keywords ?? '');
            $category = strtolower($faq->category ?? '');

            $questionWords = $this->normalizeWords($faq->question);
            $keywordWords = $this->normalizeWords($faq->keywords ?? '');
            $faqWords = array_unique(array_merge($questionWords, $keywordWords));

            if ($messageLower === $question) {
                $score += 50;
            }

            $keywordPhrases = array_filter(array_map('trim', explode(',', $keywordsRaw)));
            foreach ($keywordPhrases as $phrase) {
                if ($phrase !== '' && str_contains($messageLower, $phrase)) {
                    $score += 40;
                }
            }

            if ($question !== '' && str_contains($messageLower, $question)) {
                $score += 20;
            }

            foreach ($messageWords as $word) {
                if (in_array($word, $faqWords, true)) {
                    $score += 4;
                }

                if (preg_match('/\b' . preg_quote($word, '/') . '\b/', $question)) {
                    $score += 3;
                }

                if ($keywordsRaw && preg_match('/\b' . preg_quote($word, '/') . '\b/', $keywordsRaw)) {
                    $score += 4;
                }

                if ($category && preg_match('/\b' . preg_quote($word, '/') . '\b/', $category)) {
                    $score += 2;
                }
            }

            foreach ($keywordPhrases as $phrase) {
                $phraseWords = $this->normalizeWords($phrase);
                $matchedWords = array_intersect($messageWords, $phraseWords);

                if (count($matchedWords) >= 2) {
                    $score += 15;
                }
            }

            $score += (int) $faq->priority;

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestFaq = $faq;
            }
        }

        return $bestScore >= 4 ? $bestFaq : null;
    }

    protected function normalizeWords(string $text): array
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);

        $stopWords = [
            'i', 'me', 'my', 'the', 'a', 'an', 'and', 'or', 'to', 'for', 'of',
            'is', 'am', 'are', 'do', 'how', 'can', 'please', 'with',
            'need', 'want', 'get', 'its', 'it', 'about',
        ];

        $words = preg_split('/\s+/', $text);

        return array_values(array_filter($words, function ($word) use ($stopWords) {
            return $word !== '' && !in_array($word, $stopWords, true);
        }));
    }

    protected function fallbackReply(string $message): string
    {
        $message = strtolower($message);

        if (
            str_contains($message, 'talk') ||
            str_contains($message, 'someone') ||
            str_contains($message, 'lonely') ||
            str_contains($message, 'feel')
        ) {
            return "It sounds like you may want someone to talk to. You can reach out to trusted support services like Childline who are available 24/7.";
        }

        if (
            str_contains($message, 'housing') ||
            str_contains($message, 'rent') ||
            str_contains($message, 'home') ||
            str_contains($message, 'accommodation')
        ) {
            return "It sounds like you may need housing support. CareHub can help with accommodation, emergency housing, and speaking to a support worker.";
        }

        if (
            str_contains($message, 'college') ||
            str_contains($message, 'education') ||
            str_contains($message, 'course') ||
            str_contains($message, 'student')
        ) {
            return "It sounds like you may need education support. CareHub can help with funding, study support, and course guidance.";
        }

        if (
            str_contains($message, 'job') ||
            str_contains($message, 'cv') ||
            str_contains($message, 'work') ||
            str_contains($message, 'interview')
        ) {
            return "It sounds like you may need job support. CareHub can help with CV writing, interviews, and finding work.";
        }

        if (
            str_contains($message, 'money') ||
            str_contains($message, 'budget') ||
            str_contains($message, 'bills') ||
            str_contains($message, 'financial')
        ) {
            return "It sounds like you may need money support. CareHub can help with budgeting, bills, and financial guidance.";
        }

        if (
            str_contains($message, 'stress') ||
            str_contains($message, 'sad') ||
            str_contains($message, 'wellbeing') ||
            str_contains($message, 'mental')
        ) {
            return "It sounds like you may need wellbeing support. CareHub can help you find emotional support and trusted people to talk to.";
        }

        if (
            str_contains($message, 'support worker') ||
            str_contains($message, 'support person') ||
            str_contains($message, 'trusted adult') ||
            str_contains($message, 'social worker')
        ) {
            return "It sounds like you may want to contact a support worker or trusted adult. CareHub can help guide you to the right support contact options.";
        }

        return "I’m not fully sure what you need yet. You can ask me about housing, education, jobs, money, wellbeing, or emergency support.";
    }

    protected function getSuggestionsByCategory(?string $category): array
    {
        return match ($category) {
            'Housing' => ['Accommodation', 'Emergency Housing', 'Support Worker'],
            'Education' => ['College Funding', 'Courses', 'Student Support'],
            'Jobs' => ['CV Help', 'Interview Tips', 'Job Search'],
            'Money' => ['Budgeting', 'Bills', 'Financial Support'],
            'Wellbeing' => ['Stress Support', 'Mental Health', 'Talk to Someone'],
            'Emergency' => ['Call 112 / 999', 'Contact Trusted Adult', 'Find Safe Place'],
            default => ['Housing Help', 'Education Support', 'Jobs & CV'],
        };
    }
}