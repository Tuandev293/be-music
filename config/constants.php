<?php
return [
    'expiredCodeUser'=> 5,  //đơn vị là phút
    'userGender' => [ 1, 2, 3 ], // status male/female/other
    'userAvatarDefault' => 'user/avatar/avatar_default.jpg',
    'userAvatar' => 'user/avatar/', 
    'userCover' => 'user/cover/',
    'fileSongPath' => 'song/songs/',
    'fileImgSongPath' => 'song/song_imgs/',
    'fileImgArtistPath' => 'artist/',
    'fileImgAlbumPath' => 'album/',
    'ipnUrl' => 'https://luongnguyendev.site/api/v1/payment/momo-return',
    'redirectUrl' => 'https://soundtrack.top/',
    'perPage' => 20, // type : asc / desc
    'momoPartnerCode' => env('MOMO_PARTNER_CODE', ''),
    'momoAccessKey' => env('MOMO_ACCESS_KEY', ''),
    'momoSecretKey' => env('MOMO_SECRET_KEY', ''),
    'momoBaseUrl' => env('MOMO_BASE_URL', 'https://test-payment.momo.vn/v2/gateway/api/'),
];