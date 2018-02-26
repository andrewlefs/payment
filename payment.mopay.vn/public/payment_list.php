<?php
// http://10.8.13.82/
$paymentInfo = array(
    'title' => 'Mopay',
    'desc' => 'Mời bạn chọn phương thức nạp',
    'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_logo.png',
    'options' => array(
		"hotline" => "1900 6611",
		"rate" => "Bảng tỉ giá"	
	),
    'action' => 'list',
    'data' => array(
        array(
            'title' => 'Nạp tiền qua Wap(MOL, Unipin,...)',
            'desc' => '',
            'icon' => '',
            'options' => '',
            'action' => 'list',
            'data' => array(
                array(
                    'title' => 'Unipin',
                    'desc' => '',
					'icon' => '',
                    'options' => array(
						"link" => "http://s1-service.mobo.vn/?control=paymentunipin&func=redirect&order_id=554ceb968d25b"				
					),
                    'action' => 'pay_wap',
                    'data' => ""
                ),			
                array(
                    'title' => 'MOL',
                    'desc' => '',
					'icon' => '',
                    'options' => array(
						"link" => "http://s1-service.mobo.vn/?control=paymentmol&func=redirect&order_id=554ceb968d25b"				
					),
                    'action' => 'pay_wap',
                    'data' => ""
                )
            )
        ),	
        array(
            'title' => 'Nạp card',
            'desc' => '',
            'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_card.png',
            'options' => '',
            'action' => 'list',
            'data' => array(
                array(
                    'title' => 'Nạp thẻ Mobifone',
                    'desc' => '',
                    'icon' => "http://service.mobo.vn/assets/icon/vms.png",
                    'options' => array(
						"confirm"=> "Bạn có muốn nạp không",
						"keyboard_state"=> "number",
						"card"=> "vms",
						"input"=> array(
							"pin" => "number",
							"serial" => "full"
						)
                    ),
                    'action' => 'pay_card',
                    'data' => ""
                )
            )
        ),	
        array(
            'title' => 'Multi level',
            'desc' => '',
            'icon' => '',
            'options' => '',
            'action' => 'list',
            'data' =>  array(
                array(
                    'title' => 'Level 1',
                    'desc' => '',
					'icon' => '',
                    'options' => '',
                    'action' => 'list',
                    'data' => array(
						array(
							'title' => 'Level 1.1',
							'desc' => '',
							'icon' => '',
							'options' => '',
							'action' => 'list',
							'data' => array(
								array(
									'title' => 'Level 1.2.1',
									'desc' => '',
									'icon' => '',
									'options' => array(
										"link" => "http://s1-service.mobo.vn/?control=paymentmol&func=redirect&order_id=123456"				
									),
									'action' => 'pay_wap',
									'data' => ""
								),
								array(
									'title' => 'Level 1.2.2',
									'desc' => '',
									'icon' => '',
									'options' => array(
										"link" => "http://s1-service.mobo.vn/?control=paymentmol&func=redirect&order_id=123456"				
									),
									'action' => 'pay_wap',
									'data' => ""
								)								
							)
						)
					)
                )
            )
        ),		
        array(
            'title' => 'Nạp qua ngân hàng',
            'desc' => '',
            'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_bank.png',
            'options' => '',
            'action' => 'list',
            'data' => array(
                array(
                    'title' => 'Ngân hàng ACB',
                    'desc' => '',
                    'icon' => 'http://api.sky.mobo.vn/assets/icon/ico_eximbank_3x.png',
                    'options' => array(
						"prices" => array(
							array(
							  "message"=> "10000",
							  "description"=> "10,000 VNĐ"
							),
							array(
							  "message"=> "100000",
							  "description"=> "100,000 VNĐ"
							),
							array(
							  "message"=> "200000",
							  "description"=> "200,000 VNĐ"
							),
							array(
							  "message"=> "500000",
							  "description"=> "500,000 VNĐ"
							),
							array(
							  "message"=> "1000000",
							  "description"=> "1,000,000 VNĐ"
							),
							array(
							  "message"=> "5000000",
							  "description"=> "5,000,000 VNĐ"
							)
						),
						"type" => "1",
						"code" => "acbbank",						
					),
                    'action' => 'pay_ibanking',
                    'data' => ""
                ),
                array(
                    'title' => 'Ngân hàng Đông Á',
                    'desc' => '',
                    'icon' => 'http://api.sky.mobo.vn/assets/icon/ico_eximbank_3x.png',
                    'options' => array(
						"prices" => array(
							array(
							  "message"=> "10000",
							  "description"=> "10,000 VNĐ"
							),
							array(
							  "message"=> "100000",
							  "description"=> "100,000 VNĐ"
							),
							array(
							  "message"=> "200000",
							  "description"=> "200,000 VNĐ"
							),
							array(
							  "message"=> "500000",
							  "description"=> "500,000 VNĐ"
							),
							array(
							  "message"=> "1000000",
							  "description"=> "1,000,000 VNĐ"
							),
							array(
							  "message"=> "5000000",
							  "description"=> "5,000,000 VNĐ"
							)
						),
						"type" => "1",
						"code" => "acbbank",			
					),
                    'action' => 'pay_ibanking',
                    'data' => ""
                )
            )
        ),
        array(
            'title' => 'Inapp Google',
            'desc' => '',
            'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_inapp.png',
            'options' => '',
            'action' => 'list',
            'data' => array(
                array(
                    'title' => 'Mua 126 Vàng',
                    'desc' => '',
                    'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_inapp.png',
                    'options' => array(
						"code" => "monggiangho.vn.game.mobo.01",
						"confirm" => "Bạn có muốn nạp không",
						"transaction_id" => "563178983a4e48.07561491"
                    ),
                    'action' => 'pay_inapp',
                    'data' => ""
                ),
                array(
                    'title' => 'Mua 127 Vàng',
                    'desc' => '',
                    'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_inapp.png',
                    'options' => array(
						"code" => "monggiangho.vn.game.mobo.01",
						"confirm" => "Bạn có muốn nạp không",
						"transaction_id" => "563178983a4e48.07561491"
                    ),
                    'action' => 'pay_inapp',
                    'data' => ""
                )
            )
        ),
        array(
            'title' => 'Inapp Apple',
            'desc' => '',
            'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_inapp.png',
            'options' => '',
            'action' => 'list',
            'data' => array(
                array(
                    'title' => 'Mua 126 Vàng',
					'right_title' => '0.99',
                    'desc' => '',
                    'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_inapp.png',
                    'options' => array(
						"code" => "monggiangho.vn.game.mobo.01",
						"confirm" => "Bạn có muốn nạp không",
						"transaction_id" => "563178983a4e48.07561491"
                    ),
                    'action' => 'pay_inapp',
                    'data' => ""
                ),
                array(
                    'title' => 'Mua 127 Vàng',
					'right_title' => '0.99',
                    'desc' => '',
                    'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_inapp.png',
                    'options' => array(
						"code" => "monggiangho.vn.game.mobo.01",
						"confirm" => "Bạn có muốn nạp không",
						"transaction_id" => "563178983a4e48.07561491"
                    ),
                    'action' => 'pay_inapp',
                    'data' => ""
                )
            )
        ),
        array(
            'title' => 'Nạp qua SMS',
            'desc' => '',
            'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_sms.png',
            'options' => '',
            'action' => 'list',
            'data' => array(
                array(
                    'title' => 'SMS 10000 VNĐ',
					'right_title' => '10000',
                    'desc' => 'Nhận 20 Vàng',
					'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_sms.png',
                    'options' => array(
						"content" => "TCM NAP 754123681 562ef6cb5bb0a me",
						"phone" => "7665",
						"confirm" => "Bạn có muốn mua 20 Vàng với phí 10000 VNĐ không?"						
					),
                    'action' => 'pay_sms',
                    'data' => ""
                ),
                array(
                    'title' => 'SMS 15000 VNĐ',
					'right_title' => '10000',
                    'desc' => 'Nhận 20 Vàng',
					'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_sms.png',
                    'options' => array(
						"content" => "TCM NAP 754123681 562ef6cb5bb0a me",
						"phone" => "7665",
						"confirm" => "Bạn có muốn mua 20 Vàng với phí 15000 VNĐ không?"						
					),
                    'action' => 'pay_sms',
                    'data' => ""
                )
            )
        ),  
		array(
            'title' => 'Chuyển từ ví',
            'desc' => '',
            'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_mopay.png',
            'options' => '',
            'action' => 'pay_wallet',
            'data' => ""
        )
    )
);
header('Content-Type: application/json');
echo json_encode($paymentInfo);
?>