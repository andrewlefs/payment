<?php

class ConfigPayment {

    /**
     * @var CI_Controller
     */
    private $CI;

    public function __construct() {
        $this->CI = &get_instance();
    }
    public function update_config_payment_all(){
        $params = array();

        $this->CI->load->MEAPI_Model('ConfigPaymentModel');
        $objConfigPay   =   $this->CI->ConfigPaymentModel->getConfigPayment();
        $dataItem ['method_payment']=   json_decode($objConfigPay['key_payment'],true);
        $dataItem ['value_payment'] =   json_decode($objConfigPay['value_payment'],true);
        $method_payment =   array();
        $data=array();
        $key=array();
        $keyPublic=array();
        $keyInternal=array();
        if (is_array(($dataItem ['method_payment']['mopay']))) {
            foreach ($dataItem ['method_payment']['mopay'] as $keyM => $valueM) {
                $method_payment['mopay'][] = $valueM;
                $data[$valueM] = array();
                $data_All[$valueM] = array();
                if (array_key_exists($valueM, $dataItem ['value_payment'])) {
                    $dataValue = $dataItem ['value_payment'][$valueM];
                    $data_All[$valueM] = array();
                    $data_public[$valueM] = array();
                    $data_internal[$valueM] = array();
                    if (is_array($dataValue)) {
                        foreach ($dataValue as $keyI => $valueI) {
                            if( $valueI['is_active']=='yes'){
                                if ($valueM != '_nodepaybank') {
                                    if($valueI['view']=='all'){
                                        $data_All[$valueM][] = $keyI;
                                    }elseif($valueI['view']=='public') {
                                        $data_public[$valueM] [] = $keyI;
                                        // $data_internal[$valueM]  [] = $keyI;
                                    }else{
                                        $data_internal[$valueM]  [] = $keyI;
                                    }
                                } else {
                                    $price = array();
                                    $price_all = array();
                                    if (is_array($valueI['price'])) {
                                        foreach ($valueI['price'] as $keyPrice => $valuePrice) {
                                            if($valueI['view']=='all') {
                                                $price_all[] = $keyPrice;
                                                $price[] = $keyPrice;
                                            }else{ $price[] = $keyPrice;}
                                        }
                                        if($valueI['view']=='all') {
                                            $data_All[$valueM][$keyI] = $price_all;
                                        }elseif($valueI['view']=='public') {
                                            // $data_internal[$valueM] [$keyI]['price'] = $price;
                                            $data_public[$valueM] [$keyI] = $price;
                                        }else{
                                            $data_internal[$valueM] [$keyI] = $price;

                                        }
                                    }
                                }
                            }
                        }
                    }


                }
            }
        }

        if($data_All){
            foreach($data_All as $keyI=>$nRow){
                $key['mopay'][]=$keyI;
            }
        }
        if($data_public){
            foreach($data_public as $keyI=>$nRow){
                $keyPublic['mopay'][]=$keyI;
            }
        }
        if($data_internal){
            foreach($data_internal as $keyI=>$nRow){
                $keyInternal['mopay'][]=$keyI;
            }
        }
        $params ['service_id']='0';
        $params ['connection_id']='0';
        $params ['value_payment_all'] = @json_encode($data_All);
        $params ['value_payment_public'] = @json_encode($data_public);
        $params ['value_payment_internal'] = @json_encode($data_internal);

        $params ['key_payment_all'] = @json_encode($key);
        $params ['key_payment_public'] = @json_encode($keyPublic);
        $params ['key_payment_internal'] = @json_encode($keyInternal);
        $params ['is_active'] = 'yes';
        $needle = array('service_id','connection_id','key_payment_all','value_payment_all','key_payment_public','value_payment_public','key_payment_internal','value_payment_internal');
        if (is_required($params, $needle) == TRUE) {
            $where = make_array($params, $needle);
            $result = $this->CI->ConfigPaymentModel->config_payment_all_game($where);

        }else{
            return false;
        }


    }
    public function array_multisort($data,$col,$sort_by='asc'){
        $sort_col = array();
        define('col',$col);
        define('sort_by',$sort_by);
        if($data) {
            foreach ($data as $key => $nRow) {
                uasort($data[$key], function ($a, $b) {
                    if (sort_by == 'asc') {
                        return $a[col] < $b[col];
                    } elseif (sort_by == 'desc') {
                        return $a[col] > $b[col];
                    } else {
                        return $a[col] - $b[col];
                    }
                });
                $sort_col[$key] = $data[$key];

            }
            return $sort_col;
        }else{return NULL;}

    }
    public function compareSort($data,$col,$sort_by='asc'){
        define('col',$col);
        define('sort_by',$sort_by);
        uasort($data, function ($a, $b) {
            if (sort_by == 'asc') {
                return $a[col] < $b[col];
            } elseif (sort_by == 'desc') {
                return $a[col] > $b[col];
            } else {
                return $a[col] - $b[col];
            }
        });
        return $data;
    }

}

?>