<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('faqs')->insert([

            // ── WELLBEING ──────────────────────────────────────────────────
            [
                'question'     => 'I am feeling sad',
                'answer'       => "It's okay to feel sad sometimes — your feelings are valid. Try talking to someone you trust, like your carer or social worker. You can also call Childline any time on 116 111, it's free and confidential. 💙",
                'category'     => 'Wellbeing',
                'keywords'     => 'sad, upset, unhappy, down, crying, depressed, low, feel bad',
                'priority'     => 10,
                'is_emergency' => false,
                'link_label'   => 'Talk to Childline (free)',
                'link_url'     => 'https://www.childline.ie/',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'question'     => 'I am feeling anxious or worried',
                'answer'       => "Feeling anxious is really common, especially when things feel uncertain. Try taking slow deep breaths — in for 4, hold for 2, out for 4. If it keeps happening, speak to your social worker or visit Jigsaw for free mental health support for young people.",
                'category'     => 'Wellbeing',
                'keywords'     => 'anxious, anxiety, worried, panic, stress, stressed, nervous, scared, fear',
                'priority'     => 10,
                'is_emergency' => false,
                'link_label'   => 'Jigsaw — free mental health support',
                'link_url'     => 'https://ie.jigsaw.ie/',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'question'     => 'I am feeling angry',
                'answer'       => "Anger is a normal emotion — it's what you do with it that matters. Try stepping away from the situation for a few minutes, take some deep breaths, or write down how you feel. If anger is becoming hard to manage, your social worker can help you find support.",
                'category'     => 'Wellbeing',
                'keywords'     => 'angry, anger, mad, furious, frustrated, rage, annoyed',
                'priority'     => 8,
                'is_emergency' => false,
                'link_label'   => null,
                'link_url'     => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'question'     => 'I feel lonely',
                'answer'       => "Feeling lonely is hard, and you don't have to go through it alone. Try reaching out to someone you trust, or contact Childline on 116 111 — they're there to listen any time, day or night, for free.",
                'category'     => 'Wellbeing',
                'keywords'     => 'lonely, alone, isolated, no friends, nobody, no one',
                'priority'     => 9,
                'is_emergency' => false,
                'link_label'   => 'Childline — 116 111',
                'link_url'     => 'https://www.childline.ie/',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'question'     => 'I cannot sleep',
                'answer'       => "Sleep problems can be really tough. Try to avoid screens for 30 minutes before bed, stick to a regular bedtime, and keep your room cool and dark. If sleep problems continue, mention it to your carer or social worker — they can help.",
                'category'     => 'Wellbeing',
                'keywords'     => 'sleep, sleeping, insomnia, cant sleep, tired, exhausted, rest',
                'priority'     => 7,
                'is_emergency' => false,
                'link_label'   => null,
                'link_url'     => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],

            // ── HOUSING ────────────────────────────────────────────────────
            [
                'question'     => 'I need help with housing',
                'answer'       => "If you need housing support, speak to your social worker as soon as possible — they can help with placement, aftercare, or emergency accommodation. Threshold also provides free housing advice across Ireland.",
                'category'     => 'Housing',
                'keywords'     => 'housing, house, home, accommodation, rent, homeless, living, placement, stay',
                'priority'     => 10,
                'is_emergency' => false,
                'link_label'   => 'Threshold — free housing advice',
                'link_url'     => 'https://www.threshold.ie/',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'question'     => 'I have nowhere to stay tonight',
                'answer'       => "This is urgent — please contact your social worker or aftercare worker right away. If you can't reach them, call Focus Ireland on 01 881 5900 or the Tusla out-of-hours service. You don't have to sleep rough.",
                'category'     => 'Housing',
                'keywords'     => 'nowhere to stay, no place, homeless tonight, emergency housing, rough sleeping',
                'priority'     => 20,
                'is_emergency' => true,
                'link_label'   => 'Focus Ireland',
                'link_url'     => 'https://www.focusireland.ie/',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],

            // ── EDUCATION ─────────────────────────────────────────────────
            [
                'question'     => 'I need help with school',
                'answer'       => "If you're struggling with school, talk to your teacher or year head first. Your social worker or carer can also help you access extra support. Tusla's Education Support Service (TESS) can help if you're having trouble attending school.",
                'category'     => 'Education',
                'keywords'     => 'school, class, lessons, teacher, learning, study, homework, education, struggling, attendance',
                'priority'     => 9,
                'is_emergency' => false,
                'link_label'   => null,
                'link_url'     => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'question'     => 'How do I get into college',
                'answer'       => "Young people leaving care can access the Dare scheme and the 1916 Bursary to help with college fees and living costs. Talk to your aftercare worker about what you're entitled to — there's more support available than most people realise.",
                'category'     => 'Education',
                'keywords'     => 'college, university, third level, CAO, course, further education, leaving cert, dare scheme',
                'priority'     => 8,
                'is_emergency' => false,
                'link_label'   => 'DARE scheme — disability access to college',
                'link_url'     => 'https://accesscollege.ie/dare/',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],

            // ── JOBS ──────────────────────────────────────────────────────
            [
                'question'     => 'I need help finding a job',
                'answer'       => "Looking for work can feel overwhelming but there's lots of help available. Intreo centres offer free job advice, CV help and training. Your aftercare worker can also connect you with local employment programmes.",
                'category'     => 'Jobs',
                'keywords'     => 'job, work, employment, career, cv, resume, interview, jobseeker, apply',
                'priority'     => 8,
                'is_emergency' => false,
                'link_label'   => 'Intreo — employment services',
                'link_url'     => 'https://www.gov.ie/en/service/intreo-services/',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'question'     => 'How do I write a CV',
                'answer'       => "A CV should include your name and contact details, any education or training, work experience (even volunteering counts!), and a short personal statement. Keep it to one page if you can. Your local Intreo centre or LMETB can help you write one for free.",
                'category'     => 'Jobs',
                'keywords'     => 'cv, resume, curriculum vitae, cover letter, job application',
                'priority'     => 7,
                'is_emergency' => false,
                'link_label'   => null,
                'link_url'     => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],

            // ── MONEY ─────────────────────────────────────────────────────
            [
                'question'     => 'I need help with money',
                'answer'       => "If you're struggling financially, your aftercare worker can help you access grants and entitlements. MABS (Money Advice and Budgeting Service) offers free confidential money advice across Ireland.",
                'category'     => 'Money',
                'keywords'     => 'money, financial, broke, bills, debt, budget, rent, cost, afford, payment, grant',
                'priority'     => 9,
                'is_emergency' => false,
                'link_label'   => 'MABS — free money advice',
                'link_url'     => 'https://www.mabs.ie/',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],

            // ── RIGHTS ────────────────────────────────────────────────────
            [
                'question'     => 'What are my rights in care',
                'answer'       => "As a young person in care, you have the right to be kept safe, to have your voice heard in decisions about your life, to know your social worker, and to have a care plan. Barnardos and the Ombudsman for Children can support you if you feel your rights aren't being respected.",
                'category'     => 'Rights',
                'keywords'     => 'rights, care, entitlements, voice, say, decisions, tusla, social worker, aftercare',
                'priority'     => 9,
                'is_emergency' => false,
                'link_label'   => 'Barnardos — your rights in care',
                'link_url'     => 'https://www.barnardos.ie/',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'question'     => 'What is aftercare',
                'answer'       => "Aftercare is the support you're entitled to from Tusla after you leave the care system. If you were in care at age 16 or 17, you have a legal right to an aftercare plan and support up to age 21 (or 23 if in education). Ask your social worker about your aftercare entitlements.",
                'category'     => 'Rights',
                'keywords'     => 'aftercare, leaving care, after care, 18, turning 18, support after care',
                'priority'     => 10,
                'is_emergency' => false,
                'link_label'   => 'Tusla aftercare',
                'link_url'     => 'https://www.tusla.ie/services/alternative-care/aftercare/',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],

            // ── EMERGENCY ─────────────────────────────────────────────────
            [
                'question'     => 'I need emergency help',
                'answer'       => "If you're in immediate danger, call 999 or 112 right now. If you need someone to talk to urgently, Childline is free and available 24/7 on 116 111. You can also text 'HELLO' to 50808 for free crisis text support.",
                'category'     => 'Emergency',
                'keywords'     => 'emergency, danger, help now, urgent, crisis, safe, unsafe',
                'priority'     => 20,
                'is_emergency' => true,
                'link_label'   => 'Childline — 116 111 (free, 24/7)',
                'link_url'     => 'https://www.childline.ie/',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ]);
    }
}