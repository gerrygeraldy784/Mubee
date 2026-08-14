<?php
$apiKey = '15d2ea6d0dc1d476efbca3eba2b9bbfb';
$actors = [
    'Lee Jung-jae', 'Lee Byung-hun', 'Wi Ha-jun', 'Gong Yoo',
    'Kim Yoo-jung', 'Song Kang', 'Lee Sang-yi', 'Cho Hye-joo',
    'Choi Min-sik', 'Kim Go-eun', 'Yoo Hae-jin', 'Lee Do-hyun',
    'Ma Dong-seok', 'Kim Mu-yeol', 'Park Ji-hwan', 'Lee Dong-hwi',
    'Kim Woo-bin', 'Kim Sung-kyun', 'Gang Dong-won', 'Park Jeong-min',
    'Cha Seung-won', 'Kim Shin-rock', 'Kim Soo-hyun', 'Kim Ji-won',
    'Park Sung-hoon', 'Kwak Dong-yeon', 'Byeon Woo-seok', 'Kim Hye-yoon',
    'Song Geon-hee', 'Lee Seung-hyub', 'Hyun Bin', 'Son Ye-jin',
    'Seo Ji-hye', 'Kim Jung-hyun', 'Song Joong-ki', 'Song Hye-kyo',
    'Jin Goo', 'Lim Ji-yeon', 'Yeom Hye-ran', 'Song Kang-ho',
    'Lee Sun-kyun', 'Cho Yeo-jeong', 'Choi Woo-shik', 'Jung Yu-mi',
    'Kim Su-an', 'Ryu Seung-ryong', 'Kal So-won', 'Park Shin-hye',
    'Oh Dal-su', 'Lee Hanee', 'Jin Seon-kyu', 'Park Seo-joon',
    'Han So-hee', 'Claudia Kim', 'Lee Seung-gi', 'Bae Suzy',
    'Shin Sung-rok', 'Lee Jong-suk', 'Im Yoon-ah', 'Kim Joo-hun',
    'Park Hyung-sik', 'Yoon Park', 'Shin Min-a', 'Kim Seon-ho',
    'Kim Tae-ri', 'Oh Jung-se', 'Hong Kyung', 'Lee Jin-wook',
    'Lee Si-young', 'Ji Chang-wook', 'Shin Hye-sun', 'Kim Mi-kyung',
    'Jung Hae-in', 'Jung So-min', 'Kim Ji-eun', 'Yun Ji-on',
    'Koo Kyo-hwan', 'Son Suk-ku', 'Ahn Hyo-seop', 'Kim Se-jeong',
    'Kim Min-kyu', 'Seol In-ah', 'Lee Dong-wook', 'Kim Hye-jun',
    'Seo Hyun-woo', 'Kim Jae-young', 'Kim In-kwon', 'Shin Ye-eun',
    'Ra Mi-ran', 'Jung Eun-chae', 'Kim Nam-gil', 'Honey Lee',
    'Bibi', 'Ahn Bo-hyun', 'Park Ji-hyun', 'Kang Sang-jun',
    'Kim Shin-bi', 'Bona', 'Jang Da-ah', 'Ryu Da-in',
    'Shin Seul-ki', 'Park Min-young', 'Na In-woo', 'Lee Yi-kyung',
    'Song Ha-yoon', 'Han Hyo-joo', 'Zo In-sung', 'Cha Tae-hyun'
];

$results = [];
foreach ($actors as $actor) {
    $url = "https://api.themoviedb.org/3/search/person?api_key={$apiKey}&query=" . urlencode($actor);
    $ctx = stream_context_create(['http' => ['timeout' => 3]]);
    $json = @file_get_contents($url, false, $ctx);
    if ($json) {
        $data = json_decode($json, true);
        if (!empty($data['results'][0]['profile_path'])) {
            $results[$actor] = "https://image.tmdb.org/t/p/w500" . $data['results'][0]['profile_path'];
        }
    }
}

file_put_contents('d:/Gary/tugas1/actor_photos.json', json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "SUCCESS: " . count($results);
