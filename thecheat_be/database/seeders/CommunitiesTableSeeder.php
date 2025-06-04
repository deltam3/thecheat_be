<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CommunitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $communities = [
            'policenews', //1 검거소식 
            'preventedscamcases', //2 사기 예방했어요
            'casepictures',  //3 사건 사진자료
            'scamnewsbriefing', //4 사기뉴스 브리핑
            'financialscamvideos', //5 금융사기 관련 영상
            'free', //6 자유게시판

            'thankyoucamp', //7 고맙습니다! 캠페인
            'trendingscam', //8 신종 사기 주의/제보
            
            'scampreventidea', //9 사기 방지/검거 아이디어
            'victimsite', //10 피해자 공동대응 사이트
        ];

        foreach ($communities as $community) {
            DB::table('communities')->updateOrInsert(
                ['name' => $community], 
                ['name' => $community]
            );
        }
    }
}
