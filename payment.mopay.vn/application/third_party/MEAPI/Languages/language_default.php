<?php

$language = array(
    'default' => array(
        'INVALID_PARAMS' => 'Vui lòng nhập đầy đủ thông tin',
        'INVALID_SCOPE' => 'Không có quyền truy cập',
        'INVALID_TOKEN' => 'Chuỗi xác thực không hợp lệ',
        'ACCOUNT_EXIST' => 'Tài khoản đã tồn tại',
        'ACCOUNT_NOT_EXIST' => 'Tài khoản không tồn tại',
        'REQUEST_SUCCESS' => 'Thành công',
        'REQUEST_FAIL' => 'Thất bại',
        'BANKING_SUCCESS' => 'Nạp thành công {credit} mcoin. Số mcoin hiện tại của bạn là {balance} mcoin',
        'BANKING_FAIL' => '({error_code}) Nạp ngân hàng thất bại. Liên hệ 19006611 để biết thêm chi tiết. Mã giao dịch {transaction}',
        'BANKING_DUPLICATE' => 'Giao dịch thành công',
        'BANKING_LIMIT' => 'Mức nạp qua thẻ tín dụng tối thiểu là 500,000 VNĐ',
        'CARD_SUCCESS' => 'Nạp thành công {credit} mcoin. Số mcoin hiện tại của bạn là {balance} mcoin',
        'CARD_FAIL' => '({error_code}) Nạp thẻ thất bại. Liên hệ 19006611 để biết thêm chi tiết. Mã giao dịch {transaction}',
        'CARD_INVALID' => 'Thông tin nạp thẻ không chính xác',
        'WITHDRAW_SUCCESS' => 'Rút thành công {credit} mcoin. Số mcoin hiện tại của bạn là {balance} mcoin',
        'WITHDRAW_FAIL' => '({error_code}) Rút mcoin thất bại. Liên hệ 19006611 để biết thêm chi tiết. Mã giao dịch {transaction}',
        'WITHDRAW_BALANCE_NOT_ENOUGHT' => '({error_code}) Tài khoản của bạn không đủ mcoin. Vui lòng nạp thêm. Mã giao dịch {transaction}',
        'EXCHANGE_SUCCESS' => 'Bạn đã chuyển thành công',
        /*
         * Support web banking
         */
        'WEB_BANKING_PAY_SUCCESS' => 'Bạn đã nạp thành công <span><span class="money">{money}</span> VNĐ</span> vào tài khoản',
        'WEB_BANKING_RECEIVED' => 'Bạn nhận được : ',
        'WEB_BANKING_BALANCE' => 'Số dư : ',
        'WEB_BANKING_CONTINUE' => 'Bạn có thể tiếp tục',
        'WEB_BANKING_PAY_BUTTON' => 'Nạp {unit}',
        'WEB_BANKING_FAIL' => 'Giao dịch thất bại. Liên hệ 19006611 để biết thêm chi tiết. Mã giao dịch ngân hàng {banking_transaction}',
        'WEB_BANKING_INVALID' => 'Thông tin giao dịch không hợp lệ',
        /*
         * Payment Info
         */
        'PAYMENT_SMS' => 'Tin nhắn SMS',
        'PAYMENT_SMS_PAY' => 'Nạp {money} VNĐ',
        'PAYMENT_SMS_RECEIVE' => 'Nhận được {credit} {unit}',
        'PAYMENT_DESC' => 'Mời bạn chọn phương thức nạp',
        'PAYMENT_CARD' => 'Thẻ cào điện thoại',
        'PAYMENT_CARD_CONFIRM' => 'Bạn có muốn nạp không?',
        'PAYMENT_BANKING' => 'Ngân Hàng',
        'PAYMENT_WALLET' => 'Nạp từ ví mopay',
        'PAYMENT_SUCCESS' => 'Bạn đã mua thành công',
        /*
         * Vietcombank
         */
        'ACTIVE_SUCCESS' => 'Kích hoạt thành công',
        'ACTIVE_FAIL' => 'Kích hoạt không thành công',
        'CHECK_SUCCESS' => 'Tài khoản đã được kích hoạt',
        'CHECK_FAIL' => 'Tài khoản chưa được kích hoạt',
        'DEACTIVE_SUCCESS' => 'Hủy kích hoạt thành công',
        'DEACTIVE_FAIL' => 'Hủy kích hoạt thất bại'
    ),
    'en' => array(
        'INVALID_PARAMS' => 'Please enter all required information!',
        'REQUEST_SUCCESS' => 'Success',
        'REQUEST_FAIL' => 'Failure',
        'BANKING_SUCCESS' => 'Successful top-up of {credit} mcoin, your current mcoin is {balance}',
        'BANKING_FAIL' => '({error_code}) Transaction failed. Please contact 19006611 for more information. Your transaction number is {transaction}',
        'BANKING_DUPLICATE' => 'Successful top-up',
        'BANKING_LIMIT' => 'Min top-up value via Credit Card is 500,000 VNĐ',
        'CARD_SUCCESS' => 'Successful top-up of {credit} mcoin, your current mcoin is {balance}',
        'CARD_FAIL' => '({error_code}) Transaction failed. Please contact 19006611 for more information. Your transaction number is {transaction}',
        'CARD_INVALID' => 'Invalid top-up card information',
        'WITHDRAW_SUCCESS' => 'Successful withdraw of {credit} mcoin. Your current mcoin is {balance} mcoin',
        'WITHDRAW_FAIL' => '({error_code}) Failed withdraw mcoin. Contact 19006611 for further information. Your transaction is {transaction}',
        'WITHDRAW_BALANCE_NOT_ENOUGHT' => '({error_code}) Your account is not enough mcoin. Please top it up. Your transaction is {transaction}',
        'EXCHANGE_SUCCESS' => 'Successful exchange',
        /*
         * Support web banking
         */
        'WEB_BANKING_PAY_SUCCESS' => 'Successful top-up <span><span class="money">{money}</span> VNĐ</span>',
        'WEB_BANKING_RECEIVED' => 'You receive : ',
        'WEB_BANKING_BALANCE' => 'Balance : ',
        'WEB_BANKING_CONTINUE' => 'You may continue ',
        'WEB_BANKING_PAY_BUTTON' => 'Top-up {unit}',
        'WEB_BANKING_FAIL' => 'Transaction failed. Contact 19006611 for more information. Bank transaction is {banking_transaction}',
        'WEB_BANKING_INVALID' => 'Transaction information is not valid',
        /*
         * Payment Info
         */
        'PAYMENT_SMS' => 'Top up by SMS',
        'PAYMENT_SMS_PAY' => 'Top up {money} VNĐ',
        'PAYMENT_SMS_RECEIVE' => 'Receive {credit} {unit}',
        'PAYMENT_DESC' => 'Please choose a top up method',
        'PAYMENT_CARD' => 'Top up by Card',
        'PAYMENT_CARD_CONFIRM' => 'Do you want to top up?',
        'PAYMENT_BANKING' => 'Top up by eBanking',
        'PAYMENT_WALLET' => 'Top up by eWallet',
        'PAYMENT_SUCCESS' => 'Successfully top up'
    )
);
