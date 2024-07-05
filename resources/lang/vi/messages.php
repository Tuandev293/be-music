<?php
return [
    'common' => [
        'success' => 'Success',
        'serverError' => 'Có lỗi xảy ra. Vui lòng thử lại!',
        'unauthenticated' => 'unauthenticated',
        'forbidden' => 'forbidden',
        'notFound' => 'Not Found',
        'new_version' => 'Phiên bản của bạn đã cũ. Vui lòng cập nhật phiên bản mới nhất để sử dụng.',
        'new_version_updating' => 'Hệ thống đang bảo trì. Vui lòng truy cập lại sau khi hệ thống bảo trì xong.',
        'request_timeout'=> 'Yêu cầu đã vượt quá thời gian chờ. Vui lòng thử lại sau!'
    ],
    'request' => [
        'input_required' => "Vui lòng nhập :attribute!",
        'select_required' => "Vui lòng chọn :attribute!",
        'input_regex' => "Vui lòng nhập đúng định dạng :attribute!",
        'input_min' => "Vui lòng nhập :attribute ít nhất :min kí tự!",
        'input_max' => "Vui lòng nhập :attribute lớn nhất :max kí tự!",
        'input_min_or_equal_value' => "Vui lòng nhập :attribute lớn hơn hoặc bằng :min_value!",
        'input_max_or_equal_value' => "Vui lòng nhập :attribute nhỏ hơn hoặc bằng :max_value!",
        'input_min_value' => "Vui lòng nhập :attribute lớn hơn :min_value!",
        'input_max_value' => "Vui lòng nhập :attribute nhỏ hơn :max_value!",
        'input_required_with' => "Vui lòng nhập :attribute!",
        'input_same' => "Vui lòng nhập :attribute khớp với :same!",
        'input_date_format' => "Vui lòng nhập :attribute đúng định dạng :date_format!",
        'input_exists' => ":attribute không tồn tại!",
        'input_in' => ":attribute đã chọn không hơp lệ.",
        'input_unique' => ":attribute đã tồn tại.",
        'input_required_without' => "Vui lòng nhập :attribute!",
        'input_required_if' => "Vui lòng nhập :attribute!",
        'input_required_unless' => "Vui lòng nhập :attribute!",
        'input_integer' => ":attribute phải là số nguyên.",
        'input_numeric' => ":attribute chỉ được nhập số!",
        'upload_image' => ':attribute không đúng định dạng jpeg, png, jpg, gif, svg, jfif.',
        'upload_mine' => ':attribute không đúng định dạng :extension',
        'upload_max' => 'Vui lòng chọn :attribute nhỏ hơn :max!',
        'input_before' => ":attribute không được lớn hơn ngày hiện tại.",
        'after_or_equal' => ':attribute phải lớn hoăn hoặc bằng ngày :dateTime.',
        'input_between' => "Vui lòng nhập :attribute nằm trong khoảng :min_value - :max_value!",
        'input_after' => ":attribute phải lớn hơn :value.",
        'select_distinct' => ":attribute không được trùng nhau"
    ],
    'attributes' => [
        'name' => 'họ và tên',
        'username' => 'tên tài khoản',
        'email' => 'email',
        'address' => 'Địa chỉ',
        'birthday' => 'Ngày sinh',
        'gender' => 'Giới tính',
        'password' => 'Mật khẩu',
        'Album' => 'Album'
    ],
    'logout' => [
        'success' => 'Đăng xuất thành công!',
    ],
    'user' => [
        'register' => [
            'success' => 'Đăng ký tài khoản thành công!',
            'fail' => 'Đăng ký tài khoản thất bại!',
            'viewSuccess' => 'Hiển thị thông tin thành công.',
            'viewFail' => 'Hiển thị thông tin thất bại.'
        ],
        'show' => [
            'success' => 'Hiển thị thông tin tài khoản thành công!',
        ],
        'login' => [
            'success' => 'Đăng nhập thành công!',
            'fail' => 'Đăng nhập thất bại. Vui lòng kiểm tra thông tin đăng nhập!',
            'warring' => 'Đăng nhập không thành công. Vui lòng xác thực tài khoản!'
        ],
        'chanePassword' => [
            'success' => 'Đổi mật khẩu thành công!',
            'fail' => 'Đổi mật khẩu thất bại!',
            'invalid_old_pass' => 'Mật khẩu cũ không trùng khớp!'
        ],
        'forgotPassword' => [
            'success' => 'Yêu cầu cấp mật khẩu thành công!',
            'fail' => 'Yêu cầu cấp mật khẩu thất bại!',
            'timeFail' => 'Bạn đã vượt quá số lần gửi mã xác thực. Vui lòng thử lại sau!',
        ],
        'verifyForgotPassword' => [
            'success' => 'Cấp mật khẩu thành công!',
            'fail' => 'Cấp mật khẩu thất bại. Vui lòng kiểm tra thông tin đã gửi!',
            'confirm' => 'Xác thực quên mật khẩu thành công!',
            'expire' => 'Mã code hết hạn. Vui lòng yêu cầu cấp lại mã code!',
        ],
        'verifyRegister' => [
            'success' => 'Xác thực tài khoản thành công!',
            'fail' => 'Xác thực tài khoản thất bại. Vui lòng kiểm tra thông tin đã gửi!',
            'expire' => 'Mã code hết hạn, yêu cầu cấp lại mã code',
        ],
        'update' => [
            'success' => 'Cập nhật thông tin tài khoản thành công!',
            'fail' => 'Cập nhật thông tin tài khoản thất bại!'
        ],
    ],
    'song' => [
        'create' => [
            'success' => 'Thêm bài hát thành công!',
            'fail' => 'Thêm bài hát thất bại!'
        ],
        'update' => [
            'success' => 'Cập nhật bài hát thành công!',
            'fail' => 'Cập nhật bài hát thất bại!'
        ],
        'list' => [
            'success' => 'Hiển thị danh sách bài hát thành công!',
            'fail' => 'Hiển thị danh sách bài hát thất bại!'
        ],
        'list-popular' => [
            'success' => 'Hiển thị danh sách bài hát nổi bật thành công!',
            'fail' => 'Hiển thị danh sách bài hát nổi bật thất bại!'
        ],
        'delete' => [
            'success' => 'Xóa bài hát thành công!',
            'fail' => 'Xóa bài hát thất bại!'
        ],
        'list-category' =>[
            'success' => 'Hiển thị danh sách thể loại bài hát thành công!',
            'fail' => 'Hiển thị danh sách thể loại bài hát thất bại!'
        ]
    ],
    'payment' => [
        'create' => [
            'success' => 'Tạo yêu cầu thanh toán thành công!',
            'success_free' => 'Đăng ký dùng thử thành công!',
            'fail_free' => 'Bạn đã đăng ký dùng thử trước đó!',
            'fail' => 'Tạo yêu cầu thanh toán thất bại!'
        ],
    ],
    'songFavorite' => [
        'list' => [
            'success' => 'Hiển thị danh sách bài hát yêu thích thành công!',
            'fail' => 'Hiển thị danh sách bài hát yêu thích thành công'
        ],
        'action' => [
            'like' => 'Yêu thích bài hát thành công!',
            'unLike' => 'Bỏ thích bài hát thành công!'
        ]
    ],
    'artist' => [
        'create' => [
            'success' => 'Thêm ca sĩ thành công!',
            'fail' => 'Thêm ca sĩ thất bại!'
        ],
        'update' => [
            'success' => 'Cập nhật ca sĩ thành công!',
            'fail' => 'Cập nhật ca sĩ thất bại!'
        ],
        'list' => [
            'success' => 'Hiển thị danh sách ca sĩ thành công!',
            'fail' => 'Hiển thị danh sách ca sĩ thất bại!'
        ],
        'delete' => [
            'success' => 'Xóa ca sĩ thành công!',
            'fail' => 'Xóa ca sĩ thất bại!'
        ],
        'detail' => [
            'success' => 'Hiển thị chi tiết ca sĩ thành công!',
            'fail' => 'Hiển thị chi tiết ca sĩ thất bại!'
        ]
    ],
    'album' => [
        'create' => [
            'success' => 'Thêm album thành công!',
            'fail' => 'Thêm album thất bại!'
        ],
        'update' => [
            'success' => 'Cập nhật album thành công!',
            'fail' => 'Cập nhật album thất bại!'
        ],
        'list' => [
            'success' => 'Hiển thị danh sách album thành công!',
            'fail' => 'Hiển thị danh sách album thất bại!'
        ],
        'delete' => [
            'success' => 'Xóa album thành công!',
            'fail' => 'Xóa album thất bại!'
        ]
    ],
    'playlist' => [
        'create' => [
            'success' => 'Tạo danh sách phát thành công!',
            'fail' => 'Tạo danh sách phát thất bại!'
        ],
        'remove' => [
            'success' => 'Xóa danh sách phát thành công!',
            'fail' => 'Xóa danh sách phát thất bại!'
        ],
        'list' => [
            'success' => 'Hiển thị danh sách phát thành công!',
            'fail' => 'Hiển thị danh sách phát thất bại!'
        ],
        'add_song' => [
            'success' => 'Thêm bài hát vào danh sách thành công!',
            'fail' => 'Thêm bài hát vào danh sách thất bại!'
        ],
        'list_song' => [
            'success' => 'Hiển thị danh sách bài hát trong danh sách phát thành công!',
            'fail' => 'Hiển thị danh sách bài hát trong danh sách phát thất bại!'
        ],
        'remove_song' => [
            'success' => 'Xóa bài hát trong danh sách thành công!',
            'fail' => 'Xóa bài hát trong danh sách thất bại!'
        ]
    ],
    'dashboard' => [
        'list' => [
            'success' => 'Hiển thị danh sách thông kê thành công!',
            'fail' => 'Hiển thị danh sách thông kê thất bại!'
        ],
    ],
    'transaction' => [
        'list' => [
            'success' => 'Hiển thị danh sách giao dịch thành công!',
            'fail' => 'Hiển thị danh sách giao dịch thất bại!'
        ],
    ],
];
