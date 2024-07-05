<?php
return [
    'common' => [
        'success' => 'Success',
        'serverError' => 'Server Error',
        'unauthenticated' => 'unauthenticated',
        'forbidden' => 'forbidden',
        'notFound' => 'Not Found',
    ],
    'request' => [
        'input_required' => "Vui lòng nhập :attribute",
        'select_required' => "Vui lòng chọn :attribute",
        'input_regex' => "Vui lòng đúng định dạng :attribute",
        'input_min' => "Vui lòng nhập độ dài :attribute ít nhất :min kí tự",
        'input_max' => "Vui lòng nhập độ dài :attribute lớn nhất :max kí tự",
        'input_min_value' => "Vui lòng nhập :attribute lớn hơn hoặc bằng :min_value",
        'input_max_value' => "Vui lòng nhập :attribute nhỏ hơn hoặc bằng :max_value",
    ],
    'attributes' => [
        'name' => 'họ và tên',
        'username' => 'tên tài khoản',
        'email' => 'email',
    ],
    'user' => [
        'register' => [
            'success' => 'Đăng ký thành công.',
            'fail' => 'Đăng ký thất bại, vui lòng kiểm tra thông tin đăng nhập'
        ],
        'login' => [
            'success' => 'Đăng nhập thành công',
            'fail' => 'Đăng nhập thất bại, vui lòng kiểm tra thông tin đăng nhập'
        ]
    ],
];
