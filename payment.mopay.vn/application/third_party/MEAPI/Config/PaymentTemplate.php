<?php

class MEAPI_Config_PaymentTemplate {

    public static function exchange_group() {
        return array(
            'card' => array('card', 'card-gate'),
            'banking' => array('banking-atm', 'banking-credit'),
        );
    }

    public static function exchange_rate() {
        $result = array(
            'sms' => array(
                'title' => 'Nạp SMS',
                'identify' => 'sms',
                'items' => array(
                    '7665' => array(
                        'message' => '10,000 VNĐ',
                        'description' => '40 mCoin',
                    ),
                    '7765' => array(
                        'message' => '15,000 VNĐ',
                        'description' => '60 mCoin',
                    ))
            ),
            'sms' => array(// 9029
                'title' => 'Nạp SMS',
                'identify' => 'sms',
                'items' => array(
                    array(
                        'identify' => 'NAP10',
                        'message' => '10,000 VNĐ',
                        'description' => '50 mCoin'
                    ),
                    array(
                        'identify' => 'NAP15',
                        'message' => '15,000 VNĐ',
                        'description' => '75 mCoin'
                    ),
                    array(
                        'identify' => 'NAP20',
                        'message' => '20,000 VNĐ',
                        'description' => '100 mCoin'
                    ),
                    array(
                        'identify' => 'NAP30',
                        'message' => '30,000 VNĐ',
                        'description' => '150 mCoin'
                    ),
                    array(
                        'identify' => 'NAP40',
                        'message' => '40,000 VNĐ',
                        'description' => '200 mCoin'
                    ),
                    array(
                        'identify' => 'NAP50',
                        'message' => '50,000 VNĐ',
                        'description' => '250 mCoin'
                    ),
                    array(
                        'identify' => 'NAP100',
                        'message' => '100,000 VNĐ',
                        'description' => '500 mCoin'
                    )
                )
            ),
            'card' => array(
                'title' => 'Nạp thẻ điện thoại',
                'identify' => 'card',
                'items' => array(
                    array(
                        'identify' => '10000',
                        'message' => '10,000 VNĐ',
                        'description' => '85 mCoin'
                    ),
                    array(
                        'identify' => '20000',
                        'message' => '20,000 VNĐ',
                        'description' => '170 mCoin'
                    ),
                    array(
                        'identify' => '10000',
                        'message' => '50,000 VNĐ',
                        'description' => '425 mCoin'
                    ),
                    array(
                        'identify' => '100000',
                        'message' => '100,000 VNĐ',
                        'description' => '850 mCoin'
                    ),
                    array(
                        'identify' => '200000',
                        'message' => '200,000 VNĐ',
                        'description' => '1700 mCoin'
                    ),
                    array(
                        'identify' => '500000',
                        'message' => '500,000 VNĐ',
                        'description' => '4250 mCoin'
                    )
                )
            ),
            'card-gate' => array(
                'title' => 'Nạp thẻ Gate',
                'identify' => 'card-gate',
                'items' => array(
                    array(
                        'identify' => '10000',
                        'message' => '10,000 VNĐ',
                        'description' => '90 mCoin'
                    ),
                    array(
                        'identify' => '20000',
                        'message' => '20,000 VNĐ',
                        'description' => '180 mCoin'
                    ),
                    array(
                        'identify' => '50000',
                        'message' => '50,000 VNĐ',
                        'description' => '450 mCoin'
                    ),
                    array(
                        'identify' => '100000',
                        'message' => '100,000 VNĐ',
                        'description' => '900 mCoin'
                    ),
                    array(
                        'identify' => '500000',
                        'message' => '500,000 VNĐ',
                        'description' => '4500 mCoin'
                    ),
                    array(
                        'identify' => '1000000',
                        'message' => '1,000,000 VNĐ',
                        'description' => '9000 mCoin'
                    ),
                    array(
                        'identify' => '5000000',
                        'message' => '5,000,000 VNĐ',
                        'description' => '45000 mCoin'
                    )
                )
            ),
            'banking-atm' => array(
                'title' => 'Nạp ngân hàng ( Thẻ ATM )',
                'identify' => 'banking-atm',
                'items' => array(
                    /*
                      array(
                      'identify' => '10000',
                      'message' => '10,000 VNĐ',
                      'description' => '100 mCoin'
                      ),
                     */
                    array(
                        'identify' => '100000',
                        'message' => '100,000 VNĐ',
                        'description' => '1,000 mCoin'
                    ),
                    array(
                        'identify' => '200000',
                        'message' => '200,000 VNĐ',
                        'description' => '2,000 mCoin'
                    ),
                    /*
                      array(
                      'identify' => '300000',
                      'message' => '300,000 VNĐ',
                      'description' => '3,000 mCoin'
                      ),
                      array(
                      'identify' => '400000',
                      'message' => '400,000 VNĐ',
                      'description' => '4,000 mCoin'
                      ),
                     */
                    array(
                        'identify' => '500000',
                        'message' => '500,000 VNĐ',
                        'description' => '5,000 mCoin'
                    ),
                    array(
                        'identify' => '1000000',
                        'message' => '1,000,000 VNĐ',
                        'description' => '10,000 mCoin'
                    ),
                    /*
                      array(
                      'identify' => '2000000',
                      'message' => '2,000,000 VNĐ',
                      'description' => '20,000 mCoin'
                      ),
                      array(
                      'identify' => '3000000',
                      'message' => '3,000,000 VNĐ',
                      'description' => '30,000 mCoin'
                      ),
                     */
                    array(
                        'identify' => '5000000',
                        'message' => '5,000,000 VNĐ',
                        'description' => '50,000 mCoin'
                    ),
                /*
                  array(
                  'identify' => '10000000',
                  'message' => '10,000,000 VNĐ',
                  'description' => '100,000 mCoin'
                  ),
                  array(
                  'identify' => '20000000',
                  'message' => '20,000,000 VNĐ',
                  'description' => '200,000 mCoin'
                  ),
                  array(
                  'identify' => '50000000',
                  'message' => '50,000,000 VNĐ',
                  'description' => '500,000 mCoin'
                  ),
                  array(
                  'identify' => '100000000',
                  'message' => '100,000,000 VNĐ',
                  'description' => '1,000,000 mCoin'
                  )
                 */
                )
            ),
            'banking-credit' => array(
                'title' => 'Nạp ngân hàng ( Thẻ quốc tê )',
                'identify' => 'banking-credit',
                'items' => array(
                    array(
                        'identify' => '500000',
                        'message' => '500,000 VNĐ',
                        'description' => '5,000 mCoin',
                    )
                )
            ),
            'inapp' => array(
                'title' => 'Nạp inapp',
                'identify' => 'inapp',
                'items' => array(
                    array(
                        'identify' => '1',
                        'message' => '20,000 VNĐ',
                        'description' => '140 mCoin',
                    ),
                    array(
                        'identify' => '10',
                        'message' => '200,000 VNĐ',
                        'description' => '1,400 mCoin',
                    ),
                    array(
                        'identify' => '50',
                        'message' => '1,000,000 VNĐ',
                        'description' => '7,000 mCoin',
                    ),
                    array(
                        'identify' => '100',
                        'message' => '2,000,000 VNĐ',
                        'description' => '14,000 mCoin',
                    )
                )
            )
        );
        if (is_mecorp() === TRUE) {
            $banking_atm_10k = array(
                'identify' => '10000',
                'message' => '10,000 VNĐ',
                'description' => '100 mCoin'
            );
            array_unshift($result['banking-atm']['items'], $banking_atm_10k);
        }
        return $result;
    }
/*
    public static function payment_list_v3($direct = TRUE, $language = 'vi', $target = 'mopay') {
        $response = array(
            'title' => '',
            'desc' => '',
            'icon' => '',
            'options' => array(
                'hotline' => '1900 6611',
                'rate' => 'Bảng tỉ giá',
                'rate_type' => 'inside'
            ),
            'action' => 'list',
            'identify' => $target . '_main',
            'data' => array(
                array(
                    'title' => 'Nạp qua SMS',
                    'desc' => '',
                    'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_sms.png',
                    'options' => '',
                    'action' => 'list',
                    'identify' => $target . '_1',
                    'data' => array(
                        array(
                            'title' => 'SMS 10000 VNĐ',
                            'right_title' => '10000',
                            'desc' => 'Nhận 20 Vàng',
                            'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_sms.png',
                            'options' => array(
                                "phone" => "10000@9029",
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
                                "phone" => "100000@9029",
                            ),
                            'action' => 'pay_sms',
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
                    'identify' => $target . '_2',
                    'data' => array(
                        array(
                            'title' => 'Nạp thẻ Mobifone',
                            'desc' => '',
                            'icon' => "http://service.mobo.vn/assets/icon/vms.png",
                            'options' => array(
                                "confirm" => "Bạn có muốn nạp không",
                                "keyboard_state" => "number",
                                "card" => "vms",
                                "input" => array(
                                    "pin" => "number",
                                    "serial" => "full"
                                )
                            ),
                            'action' => 'pay_card',
                            'data' => ""
                        ),
                        array(
                            'title' => 'Nạp thẻ Vinaphone',
                            'desc' => '',
                            'icon' => "http://service.mobo.vn/assets/icon/vina.png",
                            'options' => array(
                                "confirm" => "Bạn có muốn nạp không",
                                "keyboard_state" => "number",
                                "card" => "vina",
                                "input" => array(
                                    "pin" => "number",
                                    "serial" => "full"
                                )
                            ),
                            'action' => 'pay_card',
                            'data' => ""
                        ),
                        array(
                            'title' => 'Nạp thẻ Gate',
                            'desc' => '',
                            'icon' => "http://service.mobo.vn/assets/icon/gate.png",
                            'options' => array(
                                "confirm" => "Bạn có muốn nạp không",
                                "keyboard_state" => "number",
                                "card" => "gate",
                                "input" => array(
                                    "pin" => "full",
                                    "serial" => "full"
                                )
                            ),
                            'action' => 'pay_card',
                            'data' => ""
                        )
                    )
                ),
                array(
                    'title' => 'Nạp qua ngân hàng',
                    'desc' => '',
                    'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_bank.png',
                    'options' => '',
                    'action' => 'list',
                    'identify' => $target . '_3',
                    'data' => array(
                        array(
                            'title' => 'Ngân hàng ACB',
                            'desc' => '',
                            'icon' => 'http://api.sky.mobo.vn/assets/icon/ico_eximbank_3x.png',
                            'options' => array(
                                "prices" => array(
                                    array(
                                        "message" => "10000",
                                        "description" => "10,000 VNĐ"
                                    ),
                                    array(
                                        "message" => "100000",
                                        "description" => "100,000 VNĐ"
                                    ),
                                    array(
                                        "message" => "200000",
                                        "description" => "200,000 VNĐ"
                                    ),
                                    array(
                                        "message" => "500000",
                                        "description" => "500,000 VNĐ"
                                    ),
                                    array(
                                        "message" => "1000000",
                                        "description" => "1,000,000 VNĐ"
                                    ),
                                    array(
                                        "message" => "5000000",
                                        "description" => "5,000,000 VNĐ"
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
                                        "message" => "10000",
                                        "description" => "10,000 VNĐ"
                                    ),
                                    array(
                                        "message" => "100000",
                                        "description" => "100,000 VNĐ"
                                    ),
                                    array(
                                        "message" => "200000",
                                        "description" => "200,000 VNĐ"
                                    ),
                                    array(
                                        "message" => "500000",
                                        "description" => "500,000 VNĐ"
                                    ),
                                    array(
                                        "message" => "1000000",
                                        "description" => "1,000,000 VNĐ"
                                    ),
                                    array(
                                        "message" => "5000000",
                                        "description" => "5,000,000 VNĐ"
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
            )
        );
        if ($target == 'mopay') {
            $response['data'][] = array(
                'title' => 'Ví mopay',
                'desc' => '',
                'icon' => '',
                'options' => array(
                    'show_balance' => TRUE
                ),
                'action' => 'list',
                'identify' => $target . '_wallet_category',
                'data' => array(
                    array(
                        'title' => 'Nạp vào ví',
                        'desc' => '',
                        'icon' => '',
                        'options' => '',
                        'action' => $target . '_wallet',
                        'identify' => $target . '_mopay',
                        'data' => ''
                    ),
                    array(
                        'title' => 'Sử dụng ví',
                        'desc' => '',
                        'icon' => '',
                        'options' => '',
                        'action' => 'pay_wallet',
                        'identify' => $target . '_5',
                        'data' => ''
                    )
                )
            );
        }
        return $response;
    }
*/
    public static function payment_list($direct = TRUE, $params = array(), $target = 'mobo') {
        $language=($params['language'])?$params['language']:'vi';

        if ($_GET['dev'] == 1) {
            return self::payment_list_v3x($direct, $language, $target, $params);
        }
        if ($_GET['dev'] == 2) {
            return self::payment_list_v2x($direct, $language, $target, $params);
        }
        if (API_VERSION == 3 || $_GET['dev'] == 3) {
           // return self::payment_list_v3($direct, $language, $target);
            return self::payment_list_v3($direct, $language, $target,$params);
        }
        $CI = & get_instance();
        $CI->load->MEAPI_Library('Language');
        $CI->Language->init($language);
        if (empty($direct) === FALSE) {
            $CI->load->MEAPI_Library('Mecorp/MecorpPayment', 'MecorpPayment');
            $cache = $CI->cache->load('memcache', 'mopay_info');
            //$partner_config = $CI->MecorpPayment->get_app_exchange(SCOPE_ID);
            $key_group = 'PAYMENT_GROUP_ID_'.CONNECTION_ID;
            $key_ItemGame = 'MOPAY_PARTNER_APP_EXCHANGE_RATE_' . SCOPE_ID;
            $cache->append_key($key_ItemGame, $key_group);
			$partner_config = $cache->store($key_ItemGame, $CI->MecorpPayment, 'get_app_exchange', array(SCOPE_ID));
						
            if (empty($partner_config) === FALSE) {
                $rate = $partner_config['rate'];
                $unit = $partner_config['unit'];
            } else {
                $rate = 0;
                $unit = 'Error';
            }
        } else {
            $rate = 1;
            $unit = 'mCoin';
        }
        $sms = array(
            'title' => $CI->Language->item('PAYMENT_SMS'),
            'data' => array(
                array(
                    'phone' => '50000@9029',
                    'message' => $CI->Language->item('PAYMENT_SMS_PAY', array('money' => '50.000')),
                    'confirm' => '',
                    'description' => $CI->Language->item('PAYMENT_SMS_RECEIVE', array('credit' => intval(250 * $rate), 'unit' => $unit)),
                    'money' => 50000
                ),
                array(
                    'phone' => '100000@9029',
                    'message' => $CI->Language->item('PAYMENT_SMS_PAY', array('money' => '100.000')),
                    'confirm' => '',
                    'description' => $CI->Language->item('PAYMENT_SMS_RECEIVE', array('credit' => intval(500 * $rate), 'unit' => $unit)),
                    'money' => 100000
                ),
            )
        );
        $response = array(
            'title' => '',
            'description' => $CI->Language->item('PAYMENT_DESC'),
            'sms' => $sms,
            'card' => array(
                'title' => $CI->Language->item('PAYMENT_CARD'),
                'data' => array(
                    array(
                        'type' => 'card',
                        'card' => 'gate', // VũLH yêu cầu
                        'message' => 'Gate',
                        'description' => 'Gate',
                        'confirm' => $CI->Language->item('PAYMENT_CARD_CONFIRM'),
                        'icon' => 'http://service.mobo.vn/assets/icon/gate.png',
                        'keyboard_state' => 'full',
                        'input' => array('serial', 'pin')
                    ),
                    array(
                        'type' => 'card',
                        'card' => 'vms',
                        'message' => 'Mobifone',
                        'description' => 'Mobifone',
                        'confirm' => $CI->Language->item('PAYMENT_CARD_CONFIRM'),
                        'icon' => 'http://service.mobo.vn/assets/icon/vms.png',
                        'keyboard_state' => 'number',
                        'input' => array('serial', 'pin')
                    ),
                    array(
                        'type' => 'card',
                        'card' => 'vina',
                        'message' => 'Vinaphone',
                        'description' => 'Vinaphone',
                        'confirm' => $CI->Language->item('PAYMENT_CARD_CONFIRM'),
                        'icon' => 'http://service.mobo.vn/assets/icon/vina.png',
                        'keyboard_state' => 'full',
                        'input' => array('serial', 'pin')
                    ),
                    array(
                        'type' => 'card',
                        'card' => 'viettel',
                        'message' => 'Viettel',
                        'description' => 'Viettel',
                        'confirm' => $CI->Language->item('PAYMENT_CARD_CONFIRM'),
                        'icon' => 'http://service.mobo.vn/assets/icon/viettel.png',
                        'keyboard_state' => 'number',
                        'input' => array('serial', 'pin')
                    )
                )
            ),
            'banking' => array(
                'title' => $CI->Language->item('PAYMENT_BANKING'),
                'data' => array(
                    /* array(
                      'icon' => 'http://service.mobo.vn/assets/icon/ico_abbank_3x.png',
                      'type' => '1',
                      'code' => 'abbank',
                      'message' => 'Ngân hàng An Bình',
                      'description' => '',
                      'interface' => 'ibanking'
                      ), */
                    array(
                        'icon' => 'http://service.mobo.vn/assets/icon/ico_acb_3x.png',
                        'type' => '1',
                        'code' => 'acbbank',
                        'message' => 'ACB',
                        'description' => '',
                        'interface' => 'ibanking'
                    ),
                    array(
                        'icon' => 'http://service.mobo.vn/assets/icon/ico_agribank_3x.png',
                        'type' => '1',
                        'code' => 'agribank',
                        'message' => 'AgriBank',
                        'description' => '',
                        'interface' => 'ibanking'
                    ),
					array(
                        'icon' => 'http://service.mobo.vn/assets/icon/ico_bidv_3x.png',
                        'type' => '1',
                        'code' => 'bidv',
                        'message' => 'BIDV',
                        'description' => '',
                        'interface' => 'ibanking'
                    ),
                    array(
                        'icon' => 'http://service.mobo.vn/assets/icon/ico_donga_3x.png',
                        'type' => '1',
                        'code' => 'dongabank',
                        'message' => 'DongABank',
                        'description' => '',
                        'interface' => 'ibanking'
                    ),
                    array(
                        'icon' => 'http://service.mobo.vn/assets/icon/ico_eximbank_3x.png',
                        'type' => '1',
                        'code' => 'eximbank',
                        'message' => 'EximBank',
                        'description' => '',
                        'interface' => 'ibanking'
                    ),
                    array(
                        'icon' => 'http://service.mobo.vn/assets/icon/ico_maritime_3x.png',
                        'type' => '1',
                        'code' => 'maritimebank',
                        'message' => 'MaritimeBank',
                        'description' => '',
                        'interface' => 'ibanking'
                    ),
                    array(
                        'icon' => 'http://service.mobo.vn/assets/icon/ico_mbbank_3x.png',
                        'type' => '1',
                        'code' => 'mbbank',
                        'message' => 'MBBank',
                        'description' => '',
                        'interface' => 'ibanking'
                    ),
                    array(
                        'icon' => 'http://service.mobo.vn/assets/icon/ico_sacombank_3x.png',
                        'type' => '1',
                        'code' => 'sacombank',
                        'message' => 'SacomBank',
                        'description' => '',
                        'interface' => 'ibanking'
                    ),
					array(
						'icon' => 'http://service.mobo.vn/assets/icon/ico_scb_3x.png',
						'type' => '1',
						'code' => 'saigonbank',
						'message' => 'SCB',
						'description' => '',
						'interface' => 'ibanking'
					),
                    array(
                        'icon' => 'http://service.mobo.vn/assets/icon/ico_tech_3x.png',
                        'type' => '1',
                        'code' => 'techcombank',
                        'message' => 'TechcomBank',
                        'description' => '',
                        'interface' => 'ibanking'
                    ),
                    array(
                        'icon' => 'http://service.mobo.vn/assets/icon/ico_tienphong_3x.png',
                        'type' => '1',
                        'code' => 'tienphongbank',
                        'message' => 'TPBank',
                        'description' => '',
                        'interface' => 'ibanking'
                    ),
                    array(
                        'icon' => 'http://service.mobo.vn/assets/icon/ico_vietcombank_3x.png',
                        'type' => '1',
                        'code' => 'vietcombank',
                        'message' => 'VietcomBank',
                        'description' => '',
                        'interface' => 'ibanking'
                    ),
					array(
                        'icon' => 'http://service.mobo.vn/assets/icon/ico_vib_3x.png',
                        'type' => '1',
                        'code' => 'vibbank',
                        'message' => 'VIB',
                        'description' => '',
                        'interface' => 'ibanking'
                    ),
                    array(
                        'icon' => 'http://service.mobo.vn/assets/icon/ico_vietinbank_3x.png',
                        'type' => '1',
                        'code' => 'vietinbank',
                        'message' => 'VietinBank',
                        'description' => '',
                        'interface' => 'ibanking'
                    ),
                    array(
                        'icon' => 'http://service.mobo.vn/assets/icon/ico_vpbank_3x.png',
                        'type' => '1',
                        'code' => 'vpbank',
                        'message' => 'VPBank',
                        'description' => '',
                        'interface' => 'ibanking'
                    )
                ),
                'prices' => array(
                    array(
                        "message" => "100000",
                        "description" => "100,000 VNĐ"
                        ),
                    array(
                        "message" => "200000",
                        "description" => "200,000 VNĐ"
                    ),
                    array(
                        "message" => "500000",
                        "description" => "500,000 VNĐ"
                    ),
                    array(
                        "message" => "1000000",
                        "description" => "1,000,000 VNĐ"
                    ),
					array(
                        "message" => "2000000",
                        "description" => "2,000,000 VNĐ"
                    ),
                    array(
                        "message" => "5000000",
                        "description" => "5,000,000 VNĐ"
                    ),
                    array(
                        "message" => "10000000",
                        "description" => "10,000,000 VNĐ"
                    )
                )
            ),
            /*
              'wallet' => array(
              'title' => $CI->Language->item('PAYMENT_WALLET'),
              ),
             */
            'hotline' => '19006611',
        );
        if ((APP_NAME != 'bog' && APP_NAME != 'mgh') OR is_mecorp() === TRUE) {
            $response['wallet'] = array(
                'title' => $CI->Language->item('PAYMENT_WALLET'),
            );
        }
		
		if(	APP_NAME == '155' || APP_NAME == '150'  || APP_NAME == '154'){
			unset($response['wallet']);
		}
        if ($params['direct'] == 1){//geoip_country_code_by_name($_SERVER['REMOTE_ADDR']) != 'VN' || is_mecorp() === TRUE) {
			$banking_visa = array(
				'icon' => 'http://service.mobo.vn/assets/icon/v2/visa.png',
				'type' => '2',
				'code' => 'visa',
				'message' => 'Thẻ tín dụng (Visa, Master...)',
				'description' => '',
				'interface' => 'ibanking'
			);
			array_unshift($response['banking']['data'], $banking_visa);
        }



        if (is_mecorp() === TRUE) {
            $sms_9029_10k = array(
                'phone' => '10000@9029',
                'message' => $CI->Language->item('PAYMENT_SMS_PAY', array('money' => '10.000')),
                'confirm' => '',
                'description' => $CI->Language->item('PAYMENT_SMS_RECEIVE', array('credit' => intval(50 * $rate), 'unit' => $unit)),
                'money' => 10000
            );
            $sms_9029_20k = array(
                'phone' => '20000@9029',
                'message' => $CI->Language->item('PAYMENT_SMS_PAY', array('money' => '20.000')),
                'confirm' => '',
                'description' => $CI->Language->item('PAYMENT_SMS_RECEIVE', array('credit' => intval(100 * $rate), 'unit' => $unit)),
                'money' => 20000
            );

            $bank_10k = array(
                "message" => "10000",
                "description" => "10,000 VNĐ"
            );

            array_unshift($response['sms']['data'], $sms_9029_20k);
            //mở mệnh giá 10k để QC test
            array_unshift($response['banking']['prices'], $bank_10k);
            //array_unshift($response['sms']['data'], $sms_9029_10k);
        } else {
            if (SCOPE_ID == 128 || SCOPE_ID == 125 || SCOPE_ID == 108) {
                $sms_9029_20k = array(
                    'phone' => '20000@9029',
                    'message' => $CI->Language->item('PAYMENT_SMS_PAY', array('money' => '20.000')),
                    'confirm' => '',
                    'description' => $CI->Language->item('PAYMENT_SMS_RECEIVE', array('credit' => 100 * $rate, 'unit' => $unit)),
                    'money' => 20000
                );
                array_unshift($response['sms']['data'], $sms_9029_20k);
            }
        }		
        return $response;
    }

    public function payment_type($mobo_id, $language = 'vi', $target = 'mobo') {
        $response = array(
            'title' => APP_TITLE,
            'desc' => $mobo_id,
            'icon' => '',
            'options' => array(
                'hotline' => '1900 6611',
                'rate' => 'Bảng tỉ giá',
                'rate_type' => 'inside'
            ),
            'action' => 'list',
            'identify' => $target . '_main',
            'data' => array(
                array(
                    'title' => 'Mopay (sms, card, bank...)',
                    'desc' => '',
                    'icon' => '',
                    'options' => '',
                    'action' => 'list',
                    'identify' => $target . '_1',
                )
            )
        );
        return $response;
    }

    public static function payment_list_v2x($direct = TRUE, $language = 'vi', $target = 'mopay',$params=array()) {
        $CI = & get_instance();
        $CI->load->MEAPI_Library('Language');
        $CI->Language->init($language);
        $CI->load->library('cache');
        $cache = $CI->cache->load('memcache', 'payment_list');
        $type_target='mopay';

        if (empty($direct) === FALSE) {
            $CI->load->MEAPI_Library('Mecorp/MecorpPayment', 'MecorpPayment');
            $cache = $CI->cache->load('memcache', 'mopay_info');

            $partner_config = $cache->store('MOPAY_PARTNER_APP_EXCHANGE_' . SCOPE_ID, $CI->MecorpPayment, 'get_app_exchange', array(SCOPE_ID));
            if (empty($partner_config) === FALSE) {
                $rate = $partner_config['rate'];
                $unit = $partner_config['unit'];
            } else {
                $rate = 0;
                $unit = 'Error';
            }
        } else {
            $rate = 1;
            $unit = 'mCoin';
        }
        if(is_numeric(SERVICE_ID) AND is_numeric(CONNECTION_ID)) {
            $CI->load->MEAPI_Model('ConfigPaymentModel');

            $key_ItemGame = CONNECTION_ID.'+'.SERVICE_ID.'_payment_getItemGameConfig';
            $key_PaymentDefault = CONNECTION_ID.'+'.SERVICE_ID.'_payment_getConfigPaymentDefault';
            $key_CheckItem = CONNECTION_ID.'+'.SERVICE_ID.'_payment_getConfigCheckItem';
            $key_ConfigDefault = CONNECTION_ID.'+'.SERVICE_ID.'_payment_getConfigDefault';
           // $cache->delete($key_ItemGame);
            $key_group = 'PAYEMENT_GROUP_ID_'.CONNECTION_ID;
           // $cache->delete_group($key_group);
            $objConfigItem = $cache->store($key_ItemGame,$CI->ConfigPaymentModel,getItemGameConfig,array(SERVICE_ID,CONNECTION_ID));
            $cache->append_key($key_ItemGame, $key_group);
            $objConfigPayment =  $cache->store($key_PaymentDefault,$CI->ConfigPaymentModel,getConfigPayment,array(0,0));
            $cache->append_key($key_PaymentDefault, $key_group);
            $payDefault['value_payment'] = json_decode($objConfigPayment['value_payment'], true);
            $CI->load->MEAPI_Library('ConfigPayment');
            $payDefault  =   $CI->ConfigPayment->array_multisort($payDefault ['value_payment'],'sort','asc');
            $methodPaymentAll = array();
            $methodPaymentEx = array();
            $paymentItemEx = array();
            if (is_mecorp() === FALSE OR (isset($_GET['test']) AND $_GET['test']==1)) {
                if (empty($objConfigItem) OR $objConfigItem == FALSE) { // chứ có config iteem game OR chua có config default game
                    $methodPaymentItem = json_decode($objConfigPayment['key_payment_public'], true);
                    $paymentItem = json_decode($objConfigPayment['value_payment_public'], true);
                    $methodPaymentEx = array();
                    $paymentItemEx = array();
                } else {
                    $methodPaymentItem = json_decode($objConfigItem['method_payment'], true);
                    $paymentItem = json_decode($objConfigItem['value_payment'], true);
                    $methodPaymentEx = json_decode($objConfigItem['method_payment_ex'], true);
                    $paymentItemEx = json_decode($objConfigItem['value_payment_ex'], true);
                }

                $methodPaymentAll = json_decode($objConfigPayment['key_payment_all'], true);
                $objMethodPay = $methodPaymentItem[$type_target];
                $objMethodPay_All = $methodPaymentAll[$type_target];
                $objMethodPay = @array_keys(array_flip(array_merge($objMethodPay, $objMethodPay_All)));
                $paymentItemAll = json_decode($objConfigPayment['value_payment_all'], true);



            }
            else {

                $objGame = $cache->store($key_CheckItem,$CI->ConfigPaymentModel,getPaymentItemGame,array(SERVICE_ID, CONNECTION_ID));
                $cache->append_key($key_CheckItem, $key_group);
                if ($objGame == false && empty($objConfigItem) OR $objConfigItem == FALSE) { // if chưa co config itemm game, config default
                    $methodPaymentItem_me = array();
                    $paymentItem_me = array();
                    $methodPayment_Item = json_decode($objConfigPayment['key_payment_internal'], true);
                    $payment_Item = json_decode($objConfigPayment['value_payment_internal'], true);
                    $objMethodPay = $methodPayment_Item[$target];
                    $paymentItem = $payment_Item;

                } else {
                    $methodPaymentItem_me = json_decode($objConfigItem['method_payment_me'], true);
                    $paymentItem_me = json_decode($objConfigItem['value_payment_me'], true);

                    if ($objGame == false) { // neu chua co config item game se lay defaul game
                        $methodPaymentItem = json_decode(($objConfigItem['method_payment_me']!=NULL?$objConfigItem['method_payment_me']:$objConfigItem['method_payment']), true);
                        $paymentItem = json_decode(($objConfigItem['value_payment_me'])?$objConfigItem['value_payment_me']:$objConfigItem['value_payment'], true);
                        $objMethodPay = ($methodPaymentItem[$target]!=NULL)?$methodPaymentItem[$target]:array();


                    } else { // co config item game

                        $objConfigDefault =  $objConfigDefault = $cache->store($key_ConfigDefault,$CI->ConfigPaymentModel,getItemGameConfig,array(0, 0));
                        $cache->append_key($key_ConfigDefault, $key_group);

                        $methodPaymentItem = $methodPaymentItem_me;
                        $paymentItem = $paymentItem_me;

                        // neu chua co config test cho item game
                        if (empty($paymentItem_me) OR !is_array($paymentItem_me)) {
                            $methodPaymentItem = json_decode(($objConfigDefault['method_payment_me']!=NULL?$objConfigDefault['method_payment_me']:$objConfigDefault['method_payment']), true);
                            $paymentItem = json_decode(($objConfigDefault['value_payment_me'])?$objConfigDefault['value_payment_me']:$objConfigDefault['value_payment'], true);
                        }

                        $objMethodPay = ($methodPaymentItem[$type_target]!=NULL)?$methodPaymentItem[$type_target]:array();

                    }

                }
            }

            $data = array();
            $allData = array();
            $objMethodPay= self::sortMethod($objMethodPay);
            if ($objMethodPay) {
                foreach ($objMethodPay as $key) {
                    if (!is_array($key)) {
                        $data[$key] = array();
                        if (is_mecorp() == FALSE) {

                            if (is_array($paymentItemAll[$key])) {
                                if (@array_key_exists($key,$paymentItemEx) && !empty($paymentItemEx[$key]) && is_array($paymentItemEx[$key] )) {

                                    foreach ($paymentItemEx[$key] as $keyEx=>$ValEx) {
                                        if ($key == '_nodepaybank') {


                                            if(empty($paymentItemEx[$key][$keyEx])){
                                                unset($paymentItemAll[$key][$keyEx]);
                                            }else {
                                                foreach($paymentItemEx[$key][$keyEx] as $vk=>$vl){
                                                    $kItem  = array_keys($paymentItemAll[$key][$keyEx],$vl,false);
                                                    if(!empty($kItem)){
                                                        unset($paymentItemAll[$key][$keyEx][$kItem[0]]);
                                                    }
                                                }

                                            }

                                            $set_paymentItem    =($paymentItem[$key]!=NULL)?$paymentItem[$key]:array();
                                            $set_paymentItemAll =($paymentItemAll[$key]!=NULL)?$paymentItemAll[$key]:array();
                                            $paymentItem[$key] = array_merge($set_paymentItem, $set_paymentItemAll);
                                        }else{
                                            unset($paymentItemAll[$key][$keyEx]);
                                            $set_paymentItem    =($paymentItem[$key]!=NULL)?$paymentItem[$key]:array();
                                            $set_paymentItemAll =($paymentItemAll[$key]!=NULL)?$paymentItemAll[$key]:array();
                                            if(is_array($paymentItem[$key]) && !empty($paymentItemAll[$key])){
                                                $paymentItem[$key] = @array_keys(array_flip(array_merge($set_paymentItem, $set_paymentItemAll)));
                                            }
                                        }
                                    }

                                }else{

                                    $set_paymentItem    =($paymentItem[$key]!=NULL)?$paymentItem[$key]:array();
                                    $set_paymentItemAll =($paymentItemAll[$key]!=NULL)?$paymentItemAll[$key]:array();
                                    $paymentItem[$key] = @array_merge($set_paymentItem, $set_paymentItemAll);
                                }


                            }
                        }

                        $arrListDefault = $payDefault[$key];

                        if(isset($params['platform']) && $params['platform']!='web') { // detect device
                            if ($key == '_nodepaysms') {
                                $arrItem = array();
                                $dataValue = array();
                                $arrItem = $paymentItem[$key];

                                $data[$key] = array(
                                    'title' => $CI->Language->item('PAYMENT_SMS'),
                                    'data' => ''
                                );
                                if (array_key_exists($key, $paymentItem)) {
                                    foreach ($arrListDefault as $keynode => $ValItem) {
                                        if ($ValItem['is_active'] == 'yes') {
                                            $nItem = $arrListDefault[$keynode];
                                            if ($nItem AND array_keys($arrItem, $keynode)) {


                                                $dataValue[] = array(
                                                    'phone' => $nItem['money'] . '@' . $nItem['phone'],
                                                    'message' => $CI->Language->item('PAYMENT_SMS_PAY', array('money' => $nItem['money'])),
                                                    'confirm' => '',
                                                    'description' => $CI->Language->item('PAYMENT_SMS_RECEIVE', array('credit' => 250 * $rate, 'unit' => $unit)),
                                                    'money' => $nItem['money']
                                                );
                                                $data[$key]['data'] = $dataValue;
                                            }

                                        }
                                    }
                                    if (!empty($dataValue)) {
                                        $allData[] = $data[$key];
                                    }
                                }
                            }
                        }else{unset($data[$key]);}
                        if ($key == '_nodepaycard') {
                            $arrItem=array();
                            $dataValue = array();
                            $arrItem   = $paymentItem[$key];
                            if (array_key_exists($key, $paymentItem)) {
                                $data[$key] = array(
                                    'title' => $CI->Language->item('PAYMENT_CARD'),
                                    'data' => ''
                                );
                                foreach ($arrListDefault as $keynode => $ValItem) {
                                    if ($ValItem['is_active'] == 'yes') {
                                        $nItem = $arrListDefault[$keynode];
                                        if ($nItem AND array_keys($arrItem, $keynode)) {

                                            $dataValue[] = array(
                                                'type' => 'card',
                                                'card' => $keynode, // VũLH yêu cầu
                                                'message' =>  $nItem['title'],
                                                'description' =>  $nItem['desc'],
                                                'confirm' => $CI->Language->item('PAYMENT_CARD_CONFIRM'),
                                                'icon' => $nItem['icon'],
                                                'keyboard_state' => $nItem['keyboard_state'],
                                                'input' => array('serial', 'pin')
                                            );
                                            $data[$key]['data']=$dataValue;
                                        }
                                    }
                                }
                                if (!empty($dataValue)) {
                                    $allData[] = $data[$key];
                                }
                            }

                        }

                        if ($key == '_nodepaybank') {
                            if (array_key_exists($key, $paymentItem)) {
                                $dataValue = array();

                                $data[$key]= array('title' => $CI->Language->item('PAYMENT_BANKING'),
                                    'data' =>'');
                                if(!empty($paymentItem[$key])) {
                                    $arrItem=array();
                                    $arrItem    = $paymentItem[$key];

                                    foreach ($arrListDefault as $keynode => $ValItem) {
                                        if($ValItem['is_active']=='yes'){
                                            $nItem = $arrListDefault[$keynode];
                                            $price = array();
                                            if($keynode=='visa'){
                                                $payType=2;
                                            }else{$payType=1;}
                                            if($nItem AND array_key_exists($keynode,$arrItem)) {
                                                    $dataValue[] = array(
                                                        'icon' => $nItem['icon'],
                                                        'type' => $payType,
                                                        'code' => $keynode,
                                                        'message' => $nItem['title'],
                                                        'description' => $nItem['desc'],
                                                        'interface' => 'ibanking'
                                                    );
                                            }
                                        }
                                    }

                                    $data[$key]['data'] = $dataValue;
                                    $allData[] = $data[$key];
                                }

                            }
                        }

                    }
                }
            }

        }

        $response = array(
            'title' => '',
            'description' => $CI->Language->item('PAYMENT_DESC'),
            'sms'=>$data['_nodepaysms'],
            'card'=>$data['_nodepaycard'],
            'banking'=>$data['_nodepaybank'],
            'hotline' => '19006611',

        );
        if ((APP_NAME != 'bog' && APP_NAME != 'mgh') OR is_mecorp() === TRUE) {
            $response['wallet'] = array(
                'title' => $CI->Language->item('PAYMENT_WALLET'),
            );
        }

        return $response;
    }




    public static function payment_list_v3($direct = TRUE, $language = 'vi', $target = 'mopay',$params=array()) {
        $CI = & get_instance();
        $CI->load->MEAPI_Library('Language');
        $CI->Language->init($language);
        $CI->load->library('cache');
        $cache = $CI->cache->load('memcache', 'payment_list');
        //if($target = 'wallet'){$target = 'mopay';}
        $type_target='mopay';
        if (empty($direct) === FALSE) {
            $CI->load->MEAPI_Library('Mecorp/MecorpPayment', 'MecorpPayment');
            $cache = $CI->cache->load('memcache', 'mopay_info');

            $partner_config = $cache->store('MOPAY_PARTNER_APP_EXCHANGE_' . SCOPE_ID, $CI->MecorpPayment, 'get_app_exchange', array(SCOPE_ID));
            if (empty($partner_config) === FALSE) {
                $rate = $partner_config['rate'];
                $unit = $partner_config['unit'];
            } else {
                $rate = 0;
                $unit = 'Error';
            }
        } else {
            $rate = 1;
            $unit = 'mCoin';
        }
        if(is_numeric(SERVICE_ID) AND is_numeric(CONNECTION_ID)) {
            $CI->load->MEAPI_Model('ConfigPaymentModel');

            $key_ItemGame = CONNECTION_ID.'+'.SERVICE_ID.'_payment_getItemGameConfig';
            $key_PaymentDefault = CONNECTION_ID.'+'.SERVICE_ID.'_payment_getConfigPaymentDefault';
            $key_CheckItem = CONNECTION_ID.'+'.SERVICE_ID.'_payment_getConfigCheckItem';
            $key_ConfigDefault = CONNECTION_ID.'+'.SERVICE_ID.'_payment_getConfigDefault';
           // $cache->delete($key_ItemGame);
            $key_group = 'PAYEMENT_GROUP_ID_'.CONNECTION_ID;
            //$cache->delete_group($key_group);
            $objConfigItem = $cache->store($key_ItemGame,$CI->ConfigPaymentModel,getItemGameConfig,array(SERVICE_ID,CONNECTION_ID));
            $cache->append_key($key_ItemGame, $key_group);
            $objConfigPayment =  $cache->store($key_PaymentDefault,$CI->ConfigPaymentModel,getConfigPayment,array(0,0));
            $cache->append_key($key_PaymentDefault, $key_group);
            $payDefault['value_payment'] = json_decode($objConfigPayment['value_payment'], true);
            $CI->load->MEAPI_Library('ConfigPayment');
            $payDefault  =   $CI->ConfigPayment->array_multisort($payDefault ['value_payment'],'sort','asc');
            $methodPaymentAll = array();
            $methodPaymentEx = array();

            $paymentItemEx = array();
            if (is_mecorp() === FALSE) {
                if (empty($objConfigItem) OR $objConfigItem == FALSE) { // chứ có config iteem game OR chua có config default game
                    $methodPaymentItem = json_decode($objConfigPayment['key_payment_public'], true);
                    $paymentItem = json_decode($objConfigPayment['value_payment_public'], true);
                    $methodPaymentEx = array();
                    $paymentItemEx = array();
                } else {
                    $methodPaymentItem = json_decode($objConfigItem['method_payment'], true);
                    $paymentItem = json_decode($objConfigItem['value_payment'], true);
                    $methodPaymentEx = json_decode($objConfigItem['method_payment_ex'], true);
                    $paymentItemEx = json_decode($objConfigItem['value_payment_ex'], true);
                }

                $methodPaymentAll = json_decode($objConfigPayment['key_payment_all'], true);
                $objMethodPay = $methodPaymentItem[$type_target];
                $objMethodPay_All = $methodPaymentAll[$type_target];
                $objMethodPay = @array_keys(array_flip(array_merge($objMethodPay, $objMethodPay_All)));
                $paymentItemAll = json_decode($objConfigPayment['value_payment_all'], true);



            } else {

                $objGame = $cache->store($key_CheckItem,$CI->ConfigPaymentModel,getPaymentItemGame,array(SERVICE_ID, CONNECTION_ID));
                $cache->append_key($key_CheckItem, $key_group);
                if ($objGame == false && empty($objConfigItem) OR $objConfigItem == FALSE) { // if chưa co config itemm game, config default
                    $methodPaymentItem_me = array();
                    $paymentItem_me = array();
                    $methodPayment_Item = json_decode($objConfigPayment['key_payment_internal'], true);
                    $payment_Item = json_decode($objConfigPayment['value_payment_internal'], true);
                    $objMethodPay = $methodPayment_Item[$type_target];
                    $paymentItem = $payment_Item;

                } else {
                    $methodPaymentItem_me = json_decode($objConfigItem['method_payment_me'], true);
                    $paymentItem_me = json_decode($objConfigItem['value_payment_me'], true);

                    if ($objGame == false) { // neu chua co config item game se lay defaul game
                        $methodPaymentItem = json_decode(($objConfigItem['method_payment_me']!=NULL?$objConfigItem['method_payment_me']:$objConfigItem['method_payment']), true);
                        $paymentItem = json_decode(($objConfigItem['value_payment_me'])?$objConfigItem['value_payment_me']:$objConfigItem['value_payment'], true);
                        $objMethodPay = ($methodPaymentItem[$type_target]!=NULL)?$methodPaymentItem[$type_target]:array();


                    } else { // co config item game

                        $objConfigDefault =  $objConfigDefault = $cache->store($key_ConfigDefault,$CI->ConfigPaymentModel,getItemGameConfig,array(0, 0));
                        $cache->append_key($key_ConfigDefault, $key_group);

                        $methodPaymentItem = $methodPaymentItem_me;
                        $paymentItem = $paymentItem_me;

                        // neu chua co config test cho item game
                        if (empty($paymentItem_me) OR !is_array($paymentItem_me)) {
                            $methodPaymentItem = json_decode(($objConfigDefault['method_payment_me']!=NULL?$objConfigDefault['method_payment_me']:$objConfigDefault['method_payment']), true);
                            $paymentItem = json_decode(($objConfigDefault['value_payment_me'])?$objConfigDefault['value_payment_me']:$objConfigDefault['value_payment'], true);
                        }

                        $objMethodPay = ($methodPaymentItem[$type_target]!=NULL)?$methodPaymentItem[$type_target]:array();

                    }

                }
            }

            $data = array();
            $allData = array();
            $objMethodPay= self::sortMethod($objMethodPay);

            if ($objMethodPay) {
                foreach ($objMethodPay as $key) {
                    if (!is_array($key)) {
                        $data[$key] = array();

                        if (is_mecorp() == FALSE  OR (isset($_GET['test']) AND $_GET['test']==1)) {

                            if (is_array($paymentItemAll[$key])) {
                                if (@array_key_exists($key,$paymentItemEx) && !empty($paymentItemEx[$key]) && is_array($paymentItemEx[$key] )) {

                                    foreach ($paymentItemEx[$key] as $keyEx=>$ValEx) {
                                        if ($key == '_nodepaybank') {


                                            if(empty($paymentItemEx[$key][$keyEx])){
                                                unset($paymentItemAll[$key][$keyEx]);
                                            }else {
                                                foreach($paymentItemEx[$key][$keyEx] as $vk=>$vl){
                                                    $kItem  = array_keys($paymentItemAll[$key][$keyEx],$vl,false);
                                                    if(!empty($kItem)){
                                                        unset($paymentItemAll[$key][$keyEx][$kItem[0]]);
                                                    }
                                                }

                                            }

                                            $set_paymentItem    =($paymentItem[$key]!=NULL)?$paymentItem[$key]:array();
                                            $set_paymentItemAll =($paymentItemAll[$key]!=NULL)?$paymentItemAll[$key]:array();
                                            $paymentItem[$key] = array_merge($set_paymentItem, $set_paymentItemAll);
                                        }else{
                                            unset($paymentItemAll[$key][$keyEx]);
                                            $set_paymentItem    =($paymentItem[$key]!=NULL)?$paymentItem[$key]:array();
                                            $set_paymentItemAll =($paymentItemAll[$key]!=NULL)?$paymentItemAll[$key]:array();
                                            if(is_array($paymentItem[$key]) && !empty($paymentItemAll[$key])){
                                                $paymentItem[$key] = @array_keys(array_flip(array_merge($set_paymentItem, $set_paymentItemAll)));
                                            }
                                        }
                                    }

                                }else{

                                    $set_paymentItem    =($paymentItem[$key]!=NULL)?$paymentItem[$key]:array();
                                    $set_paymentItemAll =($paymentItemAll[$key]!=NULL)?$paymentItemAll[$key]:array();
                                    $paymentItem[$key] = @array_merge($set_paymentItem, $set_paymentItemAll);


                                }


                            }
                        }

                        $arrListDefault = $payDefault[$key];

                        if(isset($params['platform']) && $params['platform']!='web') { // detect device
                            if ($key == '_nodepaysms') {
                                $arrItem = array();
                                $dataValue = array();
                                $arrItem = $paymentItem[$key];

                                $data[$key] = array(
                                    'title' => 'Nạp qua SMS',
                                    'desc' => '',
                                    'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_sms.png',
                                    'options' => '',
                                    'action' => 'list',
                                    'identify' => $target . '_nodepaysms',
                                    'data' => ''
                                );
                                if (array_key_exists($key, $paymentItem)) {
                                    foreach ($arrListDefault as $keynode => $ValItem) {
                                        if ($ValItem['is_active'] == 'yes') {
                                            $nItem = $arrListDefault[$keynode];
                                            if ($nItem AND array_keys($arrItem, $keynode)) {
                                                $dataValue[] = array(
                                                    'title' => $CI->Language->item('PAYMENT_SMS_PAY', array('money' => $nItem['money'])),
                                                    'right_title' => $nItem['money'],
                                                    'desc' => $CI->Language->item('PAYMENT_SMS_RECEIVE', array('credit' => $nItem['credit'] * $rate, 'unit' => $unit)),
                                                    'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_sms.png',
                                                    'options' => array(
                                                        'phone' => $nItem['money'] . '@' . $nItem['phone'],
                                                        'money' => str_replace('.', '', $nItem['money']),
                                                        //"content" => "TCM NAP 754123681 562ef6cb5bb0a me",
                                                        "confirm" => ""
                                                    ),
                                                    'action' => 'pay_sms',
                                                    'data' => ""
                                                );
                                                $data[$key]['data'] = $dataValue;
                                            }

                                        }
                                    }
                                    if (!empty($dataValue)) {
                                        $allData[] = $data[$key];
                                    }
                                }
                            }
                        }else{unset($data[$key]);}
                        if ($key == '_nodepaycard') {
                            $arrItem=array();
                            $dataValue = array();
                            $arrItem   = $paymentItem[$key];
                            if (array_key_exists($key, $paymentItem)) {
                                $data[$key] = array(
                                    'title' => 'Nạp card',
                                    'desc' => '',
                                    'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_card.png',
                                    'options' => '',
                                    'action' => 'list',
                                    'identify' => $target . '_nodepaycard',
                                    'data' => ''
                                );
                                foreach ($arrListDefault as $keynode => $ValItem) {
                                    if ($ValItem['is_active'] == 'yes') {
                                        $nItem = $arrListDefault[$keynode];
                                        if ($nItem AND array_keys($arrItem, $keynode)) {

                                            $dataValue[] = array(
                                                'title' => $nItem['title'],
                                                'desc' => $nItem['desc'],
                                                'icon' => $nItem['icon'],
                                                'options' => array(
                                                    'type' => 'card',
                                                    'card' => $keynode,
                                                    'confirm' => $CI->Language->item('PAYMENT_CARD_CONFIRM'),
                                                    'keyboard_state' => $nItem['keyboard_state'],
                                                    'input' => array( 'pin'=>'number','serial'=>$nItem['keyboard_state'])
                                                ),
                                                'action' => 'pay_card',
                                                'data' => ""
                                            );
                                            $data[$key]['data']=$dataValue;
                                        }
                                    }
                                }
                                if (!empty($dataValue)) {
                                    $allData[] = $data[$key];
                                }
                            }

                        }

                        if ($key == '_nodepaybank') {
                            $price=array("vn_1"=>array('10000','all'));
                            $data['price']= $price;
                            if (array_key_exists($key, $paymentItem)) {
                                $dataValue = array();

                                $data[$key] = array('title' => 'Nạp qua ngân hàng',
                                    'desc' => '',
                                    'icon' => 'http://s1-service.mobo.vn/assets/icon/v2/icon_bank.png',
                                    'options' => '',
                                    'action' => 'list',
                                    'identify' => $target . '_nodepaybank',
                                    'data' => '');
                                if(!empty($paymentItem[$key])) {
                                    $arrItem=array();
                                    $arrItem    = $paymentItem[$key];

                                    foreach ($arrListDefault as $keynode => $ValItem) {
                                        if($ValItem['is_active']=='yes'){
                                            $nItem = $arrListDefault[$keynode];
                                            $price = array();
                                            if($nItem AND array_key_exists($keynode,$arrItem)) {
                                                foreach ($arrItem[$keynode] as $keyprice => $valueprice) {

                                                    if (@array_key_exists($valueprice, $nItem['price'])) {
                                                        if ((is_mecorp() == FALSE  OR (isset($_GET['test']) AND $_GET['test']==1))) {
                                                        //if(is_mecorp()==false AND $nItem['view_ex'][$valueprice]==NULL){
                                                          if( $nItem['view_ex'][$valueprice]==NULL){

                                                            $price[] = array("message" => $nItem['price'][$valueprice],
                                                                "description" => $nItem['price'][$valueprice]
                                                            );
                                                          }
                                                        }elseif(is_mecorp()==TRUE ){
                                                            $price[] = array("message" => $nItem['price'][$valueprice],
                                                                "description" => $nItem['price'][$valueprice]
                                                            );
                                                        }
                                                    }

                                                }
                                                if($keynode=='visa'){
                                                    $payType=2;
                                                }else{$payType=1;}
                                                if(!empty($price)){
                                                    $dataValue[] = array(
                                                        'title' => $nItem['title'],
                                                        'desc' => $nItem['desc'],
                                                        'icon' => $nItem['icon'],
                                                        'options' => array(
                                                            "prices" => $price,
                                                            'type' => $payType,
                                                            'code' => $keynode,
                                                            'interface' => 'ibanking'
                                                        ),
                                                        'action' => 'pay_ibanking',
                                                        'data' => ""
                                                    );
                                                }
                                            }
                                        }
                                    }

                                    $data[$key]['data'] = $dataValue;
                                    $allData[] = $data[$key];
                                }

                            }
                        }

                    }
                }
            }


        }
        /**
         * // response return
         */
        $response = array(
            'title' => '',
            'desc' => ''.$CI->Language->item('PAYMENT_DESC').'',
            'icon' => '',
            'options' => array(
                'hotline' => '1900 6611',
                'rate' => 'Bảng tỉ giá',
                'rate_type' => 'inside'
            ),
            'action' => 'list',
            'identify' => $target . '_main',
            'data' => $allData
        );

        $response = self::type_payment_mopay($target, $response);
        return $response;
    }
    public function sortMethod($arr){
        $arrMethod=array('_nodepaysms','_nodepaycard','_nodepaybank');
        $data =array();
        foreach($arrMethod as $key=>$iValue){
            $keyArr =   @array_keys($arr,$iValue);
            if(empty($keyArr)==FALSE){
                $data[]= $arrMethod[$key];
            }
        }
        return $data;
    }
    public function type_payment_mopay($target, $response) {
        if ($target == 'mopay') {
            $response['data'][] = array(
                'title' => 'Ví mopay',
                'desc' => '',
                'icon' => 'http://service.mobo.vn/assets/icon/v3/ico_mopay_vi.png',
                'options' => array(
                    'show_balance' => TRUE
                ),
                'action' => 'list',
                'identify' => $target . '_wallet_category',
                'data' => array(
                    array(
                        'title' => 'Nạp vào ví',
                        'desc' => '',
                        'icon' => 'http://service.mobo.vn/assets/icon/v3/ico_xai_vi.png',
                        'options' => '',
                        'action' => $target . '_wallet',
                        'identify' => $target . '_mopay',
                        'data' => ''
                    ),
                    array(
                        'title' => 'Sử dụng ví',
                        'desc' => '',
                        'icon' => 'http://service.mobo.vn/assets/icon/v3/ico_xai_vi.png',
                        'options' => '',
                        'action' => 'pay_wallet',
                        'identify' => $target . '_5',
                        'data' => ''
                    )
                )
            );
        }
        return $response;
    }

}
