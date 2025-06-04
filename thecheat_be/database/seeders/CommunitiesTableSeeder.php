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
            'policenews', // 검거소식
            'preventedscamcases', // 사기 예방했어요
            'casepictures',  // 사건 사진자료
            'scamnewsbriefing', // 사기뉴스 브리핑
            'financialscamvideos', // 금융사기 관련 영상
            'free', // 자유게시판
            'thankyoucamp', // 고맙습니다! 캠페인
            'trendingscam', // 신종 사기 주의/제보
            'scampreventidea', // 사기 방지/검거 아이디어
            'victimsite', // 피해자 공동대응 사이트
        ];

        foreach ($communities as $community) {
            DB::table('communities')->updateOrInsert(
                ['name' => $community], 
                ['name' => $community]
            );
        }
    }
}
