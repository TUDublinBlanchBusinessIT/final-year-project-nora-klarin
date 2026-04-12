<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questionTags = [
['question_id'=>1,'tag_id'=>1],
['question_id'=>2,'tag_id'=>1],
['question_id'=>3,'tag_id'=>2],
['question_id'=>4,'tag_id'=>7],
['question_id'=>7,'tag_id'=>3],
['question_id'=>8,'tag_id'=>3],
['question_id'=>14,'tag_id'=>4],
['question_id'=>18,'tag_id'=>5],
['question_id'=>22,'tag_id'=>6],
['question_id'=>24,'tag_id'=>7],
['question_id'=>15,'tag_id'=>8],
];
    }
}
