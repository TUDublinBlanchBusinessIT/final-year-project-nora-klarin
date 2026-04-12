<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    public function run()
    {
        DB::table('questions')->insert([
            [
'domain_id'=>1,'text'=>'Over the last two weeks, how often have you felt cheerful and in good spirits?','response_type'=>'likert_5','source_framework'=>'HBSC-WHO5','min_value'=>0,'max_value'=>5,'is_positive'=>1,'risk_weight'=>1.5,'risk_level'=>'medium','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>1,'text'=>'How often have you felt low or down?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>0,'risk_weight'=>2.5,'risk_level'=>'high','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>1,'text'=>'How often have you felt nervous or anxious?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>0,'risk_weight'=>2.5,'risk_level'=>'high','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>1,'text'=>'How often do you feel lonely?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>0,'risk_weight'=>3.0,'risk_level'=>'critical','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>1,'text'=>'I feel calm and relaxed','response_type'=>'likert_5','source_framework'=>'WHO5','min_value'=>0,'max_value'=>5,'is_positive'=>1,'risk_weight'=>1.5,'risk_level'=>'medium','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>1,'text'=>'I feel confident in myself','response_type'=>'likert_5','source_framework'=>'HBSC-SE','min_value'=>1,'max_value'=>5,'is_positive'=>1,'risk_weight'=>2.0,'risk_level'=>'medium','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],



[
'domain_id'=>2,'text'=>'How often do you get irritated or lose your temper?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>0,'risk_weight'=>2.0,'risk_level'=>'high','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>2,'text'=>'How often do you find it hard to concentrate?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>0,'risk_weight'=>2.0,'risk_level'=>'medium','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>2,'text'=>'How often do you complete tasks you start?','response_type'=>'likert_5','source_framework'=>'Self-Efficacy','min_value'=>1,'max_value'=>5,'is_positive'=>1,'risk_weight'=>1.5,'risk_level'=>'low','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>2,'text'=>'How often do you feel in control of your behaviour?','response_type'=>'likert_5','source_framework'=>'PSS','min_value'=>1,'max_value'=>5,'is_positive'=>1,'risk_weight'=>2.0,'risk_level'=>'medium','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>2,'text'=>'How often do you argue with others?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>0,'risk_weight'=>2.0,'risk_level'=>'medium','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],



[
'domain_id'=>3,'text'=>'How would you rate your overall health?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>1,'risk_weight'=>1.5,'risk_level'=>'medium','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>3,'text'=>'How often have you had headaches?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>0,'risk_weight'=>1.5,'risk_level'=>'medium','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>3,'text'=>'How often have you had difficulty sleeping?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>0,'risk_weight'=>2.0,'risk_level'=>'high','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>3,'text'=>'How many days were you physically active for at least 60 minutes?','response_type'=>'slider','source_framework'=>'HBSC','min_value'=>0,'max_value'=>7,'is_positive'=>1,'risk_weight'=>1.5,'risk_level'=>'medium','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>3,'text'=>'How often do you eat breakfast on school days?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>1,'risk_weight'=>1.0,'risk_level'=>'low','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>3,'text'=>'How often do you brush your teeth?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>1,'risk_weight'=>1.0,'risk_level'=>'low','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],



[
'domain_id'=>4,'text'=>'Do you feel safe at home?','response_type'=>'likert_5','source_framework'=>'Adapted-HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>1,'risk_weight'=>3.0,'risk_level'=>'critical','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>4,'text'=>'Do you feel safe in your school?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>1,'risk_weight'=>2.5,'risk_level'=>'critical','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>4,'text'=>'Have you been in a physical fight in the past 12 months?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>0,'risk_weight'=>2.5,'risk_level'=>'high','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>4,'text'=>'How often have you been injured and needed medical attention?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>0,'risk_weight'=>2.0,'risk_level'=>'medium','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],



[
'domain_id'=>5,'text'=>'How do you feel about school at present?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>1,'risk_weight'=>2.0,'risk_level'=>'medium','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>5,'text'=>'How pressured do you feel by schoolwork?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>0,'risk_weight'=>2.0,'risk_level'=>'medium','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>5,'text'=>'I can talk about my problems with my family or carers','response_type'=>'likert_5','source_framework'=>'MSPSS','min_value'=>1,'max_value'=>5,'is_positive'=>1,'risk_weight'=>3.0,'risk_level'=>'high','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>5,'text'=>'My family supports me','response_type'=>'likert_5','source_framework'=>'MSPSS','min_value'=>1,'max_value'=>5,'is_positive'=>1,'risk_weight'=>3.0,'risk_level'=>'high','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>5,'text'=>'How often do you spend time with friends?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>1,'risk_weight'=>1.5,'risk_level'=>'medium','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
[
'domain_id'=>5,'text'=>'Do you feel accepted by others your age?','response_type'=>'likert_5','source_framework'=>'HBSC','min_value'=>1,'max_value'=>5,'is_positive'=>1,'risk_weight'=>2.5,'risk_level'=>'high','age_band_min'=>11,'age_band_max'=>18,'is_active'=>1,'version'=>1],
        ]);
    }
}