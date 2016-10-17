<?php


class Controller_Service extends Controller_Rest
{

#region "VAR DEFINE"

    private $post_data = array();
    private $system_info;

#endregion

#region "API ERROR DEFINE"

    private static $ErrorCode = array
    (
        'SUCCESS'                      => '0',
        'ERROR_LOGIN'                  => '1',
        'ERROR_PARAM'                  => '2',
        'ERROR_NOT_LOGIN'              => '3',
        'ERROR_ACCOUNT_LOCKED'         => '4',
        'ERROR_PLAYER_NOT_EXISTS'      => '5',
        'ERROR_PASSWORD_INVALID'       => '6',
        'ERROR_TEL_INVALID'            => '7',
        'ERROR_SEND_SMS_FAILED'        => '8',
        'ERROR_WAY_INVALID'            => '9',
        'ERROR_BANKID_INVALID'         => '10',
        'ERROR_BET_TEAM_INVALID'       => '11',
        'ERROR_BET_GAME_INVALID'       => '12',
        'ERROR_ACCOUNT_TYPE_INVALID'   => '13',
        'ERROR_BANK_MASTER_ID_INVALID' => '14',
        'ERROR_NOT_ENOUGH_BALANCE'     => '15',
        'ERROR_API_ACCOUNT_INVALID'    => '16',
        'ERROR_API_EXECUTE_FAILS'      => '17',
        'ERROR_OUT_OF_BET_LIMIT'       => '18',
        'ERROR_PLAYER_EXISTS'          => '19',
        'ERROR_AFFILIATE_SETTING_NOT_EXISTS' => '20',
        'ERROR_AUTHTOKEN_INVALID'      => '97',
        'ERROR_AUTHTOKEN_EXPIRES'      => '98',
        'ERROR_UNKNOWN'                => '999',
        'ERROR_NOT_IMPLEMENT'          => '1000',
        'ERROR_NOT_DEFINE'             => '9999',
    );

#endregion

#region "common"

    public function before()
    {
        parent::before();
        $this->format = "json";
        $this->post_data = \Input::all();

        //set test data
        $this->setTestData();

        Lang::load('service', 'SERVICE');

    }

    private function getParam($key){
        if( isset($this->post_data[$key]) )
            return $this->post_data[$key];
        return null;
    }

    private function responseSuccess(array $data = null)
    {
        if( $data === null )
            return $this->response(array('Status' => 'true', 'errorCode' => 0, 'message' => __('SERVICE.SUCCESS')));
        else
            return $this->response(array('Status' => 'true', 'errorCode' => 0, 'message' => __('SERVICE.SUCCESS'), 'data' => $data));
    }

    private function responseError($err = 'ERROR_UNKNOWN')
    {
        if (!isset(self::$ErrorCode[$err])) $err = 'ERROR_NOT_DEFINE';

        return $this->response(array('Status' => 'false', 'errorCode' => self::$ErrorCode[$err], 'message' => __('SERVICE.'.$err)));
    }

    private function ErrorLog($err){
        Log::error($err);
    }

    private function getParamsFromJson(&$data = array(), $checkFields = array()){

        $params = Format::forge($this->getParam('params'), 'json')->to_array();

        if( count($checkFields) > 0 ) {
            foreach ($params as $k => $v){
                if( in_array($k, $checkFields) )
                    $data[$k] = $v;
                else
                    return false;
            }
        }else{
            $data += $params;
        }

        return true;

    }

    private function checkParamIsExists($data, $fields){

        foreach ($fields as $param){
            if( !isset($data[$param]) )
                return false;
        }
        return true;

    }

#endregion

#region "check token"

    private function checkToken()
    {
        $this->responseError();

        try{
            $token = $this->getParam('token');
            if( $token == null )
                return false;

            $this->system_info = Model_Service::getSystemInfoByToken($token);

            if( $this->system_info == null ){
                return false;
            }else{
                return true;
                /*
                if( $this->system_info->sys_type == INTERNAL_SYSTEM){
                    //内部に使うAPI、TOKENなどの認証は不要、今後ここに追加するのもOK
                    return true;
                }else if( $this->system_info->sys_type == EXTERNAL_SYSTEM){

                    $token = $this->getParam('token');
                    if( !isset($token) ) {
                        $this->responseError('ERROR_AUTHTOKEN_INVALID');
                        return false;
                    }

                    //tokenのチェック
                    if( !$this->check_security($token) ){
                        return false;
                    }else
                        return true;

                }else
                    return false;
                //*/
            }

        }catch (Exception $ex){
            $this->ErrorLog($ex);
        }

        return false;
    }

    private function check_security($token){

        try{

            $username = $this->system_info->username;
            $password = $this->system_info->password;
            $security_key = $this->system_info->security_key;

            //$this->responseError('ERROR_AUTHTOKEN_INVALID');
            //$this->responseError('ERROR_AUTHTOKEN_EXPIRES');

            /*
            $hash = new Rijndael();
            $hash->setKey('f09348ujf');
            $ret = bin2hex($hash->encrypt('1'));
            $dret = $hash->decrypt(hex2bin($ret));
            return $this->responseSuccess(array("text"=> $dret ,"Rijndael"=>$ret));
            //*/

            return true;
        }catch (Exception $ex){
            $this->ErrorLog($ex);
        }

        return false;
    }

#endregion

#region "外部システム情報取得"

    /*
     * 外部システム情報取得
     * @system_id
     * @token
     */
    public function post_getSystemInformation(){

        try{
            if( !$this->checkToken() ) return;
            return $this->responseSuccess((array)$this->system_info);
        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

#endregion

#region "外部システム情報更新"

    /*
     * 外部システム情報更新
     * @system_id
     * @token
     * @params
     */
    public function post_updateSystemInformation(){

        try{

            if( !$this->checkToken() ) return;

            $fields = array('name','username','password','base_domain','ref_code','email','tel','settings');
            $data = array();

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if(!Model_Service::updateSystemInfo($this->system_info->id, $data))
                return $this->responseError();

            foreach ($data as $k => $v )
                $this->system_info->{$k} = $v;

            return $this->responseSuccess((array)$this->system_info);

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }

    }

#endregion

#region "アフィリエイト設定作成"

    /*
     * アフィリエイト設定作成
     */
    function post_createAffiliateSetting(){

        try{

            if( !$this->checkToken() ) return;

            $fields = array('affiliate_type','period_type','carryover','base_rate','min_limit','max_limit','closing_date','pay_date');
            $data = array();
            $data['system_id'] = $this->system_info->id;

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if( !$this->checkParamIsExists($data, $fields))
                return $this->responseError('ERROR_PARAM');

            $result = Model_Service::createAffiliateSetting($data);
            if( $result > 0 )
                return $this->responseSuccess(array('affiliate_setting_id'=>$result));
            else
                return $this->responseError();


        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

#endregion

#region "アフィリエイト設定更新"

    /*
     * アフィリエイト設定更新
     */
    function post_updateAffiliateSetting(){

        try{

            if( !$this->checkToken() ) return;

            $fields = array('id','affiliate_type','period_type','carryover','base_rate','min_limit','max_limit','closing_date','pay_date');
            $data = array();
            $data['system_id'] = $this->system_info->id;

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if( !$this->checkParamIsExists($data, array('id')))
                return $this->responseError('ERROR_PARAM');

            $result = Model_Service::updateAffiliateSetting($data);
            if( $result > 0 )
                return $this->responseSuccess();
            else
                return $this->responseError();


        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

#endregion


#region "アフィリエイト設定取得"

    /*
     * アフィリエイト設定取得
     * @system_id
     * @token
     * @params ($playerUsernames)
     */
    function post_getAffiliateSetting(){

        try{
            if( !$this->checkToken() ) return;

            $fields = array('affiliate_setting_id');

            $data = array();
            $data['system_id'] = $this->system_info->id;

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');


            $result = \Model_Service::getAffiliateSetting($data);

            return $this->responseSuccess($result);

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

#endregion

#region "アフィリエイト各レベル報酬レート取得（設定は存在しない場合はデフォルトの5:3:2をリターンする）"

    /*
     * システムに対する設定
     */
    function post_getAffiliateLevelRateBySystem(){

        try{
            if( !$this->checkToken() ) return;

            $result = \Model_Service::getAffiliateLevelRate($this->system_info->id, 'system');

            return $this->responseSuccess($result);

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

    /*
     * アフィリエイト設定に対する設定
     */
    function post_getAffiliateLevelRateBySetting(){

        try{
            if( !$this->checkToken() ) return;

            $fields = array('affiliate_setting_id');
            $data = array();

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if( !$this->checkParamIsExists($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if( !\Model_Service::checkAffiliateSetting($this->system_info->id, $data['affiliate_seetting_id']) )
                return $this->responseError('ERROR_AFFILIATE_SETTING_NOT_EXISTS');

            $result = \Model_Service::getAffiliateLevelRate($this->system_info->id, 'setting', $data['affiliate_seetting_id']);

            return $this->responseSuccess($result);

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

    /*
     * プレイヤーに対する設定
     */
    function post_getAffiliatelevelRateByPlayer(){

        try{
            if( !$this->checkToken() ) return;

            $fields = array('player_id');
            $data = array();

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if( !$this->checkParamIsExists($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if( !\Model_Service::checkPllayerIsExists($this->system_info->id, $data['player_id']) )
                return $this->responseError('ERROR_PLAYER_NOT_EXISTS');

            $result = \Model_Service::getAffiliateLevelRate($this->system_info->id, 'player', $data['player_id']);

            return $this->responseSuccess($result);

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

#endregion

#region "アフィリエイト各レベル報酬レート設定"

    /*
     * システムに対する設定
     */
    function post_setAffiliateLevelRateBySystem(){

        try{
            if( !$this->checkToken() ) return;

            $fields = array('levelrate');
            $data = array();

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if( !$this->checkParamIsExists($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            $result = \Model_Service::setAffiliateLevelRate($data['levelrate'], $this->system_info->id, 'system');
            if($result === -1)
                return $this->responseError('ERROR_PARAM');
            else if( $result === false )
                return $this->responseError();
            else
                return $this->responseSuccess();

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

    /*
     * アフィリエイト設定に対する設定
     */
    function post_setAffiliateLevelRateBySetting(){

        try{
            if( !$this->checkToken() ) return;

            $fields = array('affiliate_setting_id', 'levelrate');
            $data = array();

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if( !$this->checkParamIsExists($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if( !\Model_Service::checkAffiliateSetting($this->system_info->id, $data['affiliate_seetting_id']) )
                return $this->responseError('ERROR_AFFILIATE_SETTING_NOT_EXISTS');

            $result = \Model_Service::setAffiliateLevelRate($data['levelrate'], $this->system_info->id, 'setting', $data['affiliate_seetting_id']);
            if($result === -1)
                return $this->responseError('ERROR_PARAM');
            else if( $result === false )
                return $this->responseError();
            else
                return $this->responseSuccess();

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

    /*
     * プレイヤーに対する設定
     */
    function post_setAffiliatelevelRateByPlayer(){

        try{
            if( !$this->checkToken() ) return;

            $fields = array('player_id', 'levelrate');
            $data = array();

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if( !$this->checkParamIsExists($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if( !\Model_Service::checkPllayerIsExists($this->system_info->id, $data['player_id']) )
                return $this->responseError('ERROR_PLAYER_NOT_EXISTS');

            $result = \Model_Service::setAffiliateLevelRate($data['levelrate'], $this->system_info->id, 'player', $data['player_id']);
            if($result === -1)
                return $this->responseError('ERROR_PARAM');
            else if( $result === false )
                return $this->responseError();
            else
                return $this->responseSuccess();

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

#endregion

#region "プレイヤー作成"

    /*
     * プレイヤー作成
     * @system_id
     * @token
     * @params
     */
    function post_createAffiliatePlayer(){

        try{

            if( !$this->checkToken() ) return;

            $fields = array('external_player_id','username','password','first_name','last_name','currency_id','country_id','gender','tel','email','address1','address2','affiliate_setting_id','partner_id','affiliate_code','profiles','status');
            $data = array();
            $data['system_id'] = $this->system_info->id;

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            $player_id = Model_Service::createAffiliatePlayer($data);
            if( $player_id === null )
                return $this->responseError('ERROR_PLAYER_EXISTS');
            else if( $player_id > 0 )
                return $this->responseSuccess(array('player_id'=>$player_id));
            else if( $player_id == -1 )
                return $this->responseError('ERROR_AFFILIATE_SETTING_NOT_EXISTS');
            else
                return $this->responseError();


        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

#endregion

#region "プレイヤー更新"

    /*
     * プレイヤー更新
     * @system_id
     * @token
     * @params
     */
    function post_updateAffiliatePlayer(){

        try{

            if( !$this->checkToken() ) return;

            $fields = array('id','external_player_id','username','password','first_name','last_name','currency_id','country_id','gender','tel','email','address1','address2','affiliate_setting_id','partner_id','affiliate_code','profiles','status');

            $data = array();
            $data['system_id'] = $this->system_info->id;

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if( !$this->checkParamIsExists($data, array('id')))
                return $this->responseError('ERROR_PARAM');

            $result = Model_Service::updateAffiliatePlayer($data);

            if( $result === null )
                return $this->responseError('ERROR_PLAYER_NOT_EXISTS');
            else if( $result > 0 )
                return $this->responseSuccess();
            else if( $result == -1 )
                return $this->responseError('ERROR_AFFILIATE_SETTING_NOT_EXISTS');
            else
                return $this->responseError();

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

#endregion

#region "プレイヤー情報取得"

    /*
     * プレイヤー情報取得
     * @system_id
     * @token
     * @params ($playerUsernames)
     */
    function post_getAffiliatePlayers(){

        try{
            if( !$this->checkToken() ) return;

            $fields = array('usernames');

            $data = array();
            $data['system_id'] = $this->system_info->id;

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if( !$this->checkParamIsExists($data, array('usernames')))
                return $this->responseError('ERROR_PARAM');

            $result = \Model_Service::getAffiliatePlayers($this->system_info->id, $data['usernames']);

            return $this->responseSuccess($result);

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

#endregion

#region "ベットや入金などの履歴を記録する"

    /*
     * ベットや入金などの履歴を記録する
     * @system_id
     * @token
     * @params
     */
    function post_generateAffiliatePlayerTransaction(){

        try{

            if( !$this->checkToken() ) return;

            $fields = array('player_id','amount','external_tran_id','details');

            $data = array();
            $data['system_id'] = $this->system_info->id;

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            $result = Model_Service::createPlayerTransaction($data);

            if( $result > 0 )
                return $this->responseSuccess(array('tran_id'=>$result));
            else
                return $this->responseError();

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

#endregion

#region "履歴を取得する"

    /*
     * 履歴を取得する
     * @system_id
     * @token
     * @params (beginDate, endDate, sortBy, orderBy, pageNumber, pageSize)
     */
    function post_getAffiliatePlayerTransactionHistories(){

        try{

            if( !$this->checkToken() ) return;

            $fields = array('player_id','beginDate', 'endDate', 'sortBy', 'pageNumber', 'pageSize');

            $data = array();
            $data['system_id'] = $this->system_info->id;

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            //check need params
            if( !$this->checkParamIsExists($data, array('player_id')) )
                return $this->responseError('ERROR_PARAM');

            $result = \Model_Service::getAffiliatePlayerTransactionHistories($data);

            return $this->responseSuccess($result);

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

#endregion

#region "報酬情報を取得する"

    /*
     * 報酬情報を取得する
     * @system_id
     * @token
     * @params (player, beginDate, endDate, sortBy, orderBy, pageNumber, perPage)
     */
    function post_getAffiliatePlayerRewardByPlayer(){

        try{

            if( !$this->checkToken() ) return;

            $fields = array('player_id','beginDate', 'endDate', 'sortBy', 'pageNumber', 'pageSize');

            $data = array();
            $data['system_id'] = $this->system_info->id;

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            //check need params
            //if( !$this->checkParamIsExists($data, array('player_id')) )
            //    return $this->responseError('ERROR_PARAM');

            $result = \Model_Service::getAffiliatePlayerReward($data);

            return $this->responseSuccess($result);

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

#endregion

#region "報酬情報の詳細を取得する"

    /*
     * 報酬情報の詳細を取得する
     * @system_id
     * @token
     * @params (rewardid)
     */
    function post_getAffiliateRewardDetail(){

        try{
            if( !$this->checkToken() ) return;

            $fields = array('reward_id');

            $data = array();
            $data['system_id'] = $this->system_info->id;

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if( !$this->checkParamIsExists($data, array('reward_id')) )
                return $this->responseError('ERROR_PARAM');

            $result = \Model_Service::getAffiliateRewardDetail($data);

            return $this->responseSuccess($result);

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

#endregion

#region "報酬情報を更新する"

    /*
     * 報酬情報を更新する
     * @system_id
     * @token
     * @params
     */
    function post_updateeAffiliatePlayerReward(){

        try{
            if( !$this->checkToken() ) return;

            $fields = array('update_data');

            $data = array();
            $data['system_id'] = $this->system_info->id;

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if( !$this->checkParamIsExists($data, array('update_data')) )
                return $this->responseError('ERROR_PARAM');

            $result = \Model_Service::updateeAffiliatePlayerReward($data);
            if( $result )
                return $this->responseSuccess();
            else
                return $this->responseError();

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

#endregion

#region "アフィリエイトパートナー一覧"

    /*
     * アフィリエイトパートナー一覧
     * @system_id
     * @token
     * @params (player, $lowerlevel)
     */
    function post_getAffiliatePartners(){

        try{

            if( !$this->checkToken() ) return;

            $fields = array('player_id','lowerlevel');

            $data = array();
            $data['system_id'] = $this->system_info->id;

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if( !$this->checkParamIsExists($data, array('player_id')) )
                return $this->responseError('ERROR_PARAM');

            $result = \Model_Service::getAffiliatePartners($data);

            return $this->responseSuccess($result);

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

#endregion

#region "アフィリエイトランキング(システム全体)"

    /*
     * アフィリエイトランキング(システム全体)
     * @system_id
     * @token
     * @params ($minRank=100)
     */
    function post_getAffiliateRankingBySystem(){

        try{

            if( !$this->checkToken() ) return;

            $fields = array('minRank');

            $data = array();
            $data['system_id'] = $this->system_info->id;

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');


            $result = \Model_Service::getAffiliateRankingBySystem($data);

            return $this->responseSuccess($result);

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

#endregion

#region "アフィリエイトランキング(プレイヤー単位)"

    /*
     * アフィリエイトランキング(プレイヤー単位)
     * @system_id
     * @token
     * @params ($player)
     */
    function post_getAffiliateRankingByPlayer(){

        try{

            if( !$this->checkToken() ) return;

            $fields = array('minRank','player_id');

            $data = array();
            $data['system_id'] = $this->system_info->id;

            if( !$this->getParamsFromJson($data, $fields) )
                return $this->responseError('ERROR_PARAM');

            if( !$this->checkParamIsExists($data, array('player_id')) )
                return $this->responseError('ERROR_PARAM');

            $result = \Model_Service::getAffiliateRankingByPlayer($data);

            return $this->responseSuccess($result);

        }catch (Exception $ex){
            $this->ErrorLog($ex);
            return $this->responseError();
        }
    }

#endregion

#region "for test"

    public function post_index()
    {
        if( !$this->checkToken() ) return;
        //test
        return $this->responseSuccess();
    }

    public function setTestData(){

        //set test data:
        if( $this->request->method_params[0] == 'test' ) {
            $func_name = $this->request->method_params[1][0];
            //system id = 1 , token = c33f72b33f640c0557e2fdc9e1da3f1c
            //system id = 2 , token = 4a75a73a4da217e85056122508dab91f
            $token = 'c33f72b33f640c0557e2fdc9e1da3f1c';
            switch ($func_name){
                case 'index':
                case 'getSystemInformation':
                    $this->post_data = array(
                        'token' => $token,
                    );
                    break;
                case 'updateSystemInformation':
                    $this->post_data = array(
                        'token' => $token,
                        'params' => '{"name":"name111","username":"username1","email":"email@gmail.com","tel":"12345678901","ref_code":"RC01"}',
                    );
                    break;
                case 'createAffiliatePlayer':
                    $this->post_data = array(
                        'token' => $token,
                        'params' => '{"username":"testplayer001","password":"password","email":"email@gmail.com","tel":"12345678901"}',
                    );
                    break;
                case 'getAffiliatePlayers':
                    $this->post_data = array(
                        'token' => $token,
                        'params' => '{"usernames":"PAS002075"}',
                    );
                    break;

                case 'generateAffiliatePlayerTransaction':
                    $this->post_data = array(
                        'token' => $token,
                        'params' => '{"player_id":77,"amount":1000,"external_tran_id":"extran01","details":""}',
                    );
                    break;

                case 'getAffiliatePlayerTransactionHistories':
                    $this->post_data = array(
                        'token' => $token,
                        'params' => '{"player_id":77,"beginDate":"2016-10-10","endDate":"2016-10-11"}',
                    );
                    break;
                case 'updateeAffiliatePlayerReward':
                    $this->post_data = array(
                        'token' => $token,
                        'params' => '{"update_data":[{"id":1,"status":"active","remarks":"remark1"},{"id":2,"status":"active","remarks":"remark2"}]}',
                    );
                    break;
                case 'getAffiliatePartners':
                    $this->post_data = array(
                        'token' => $token,
                        'params' => '{"player_id":1}',
                    );
                    break;
                case 'setAffiliateLevelRateBySystem':
                    $this->post_data = array(
                        'token' => $token,
                        'params' => '{"levelrate":{"level1":0.1,"level2":0.2,"level3":0.3}}',
                    );
                    break;
                case 'setAffiliateLevelRateBySetting':
                    $this->post_data = array(
                        'token' => $token,
                        'params' => '{"affiliate_seetting_id":1, "levelrate":{"level1":0.7,"level2":0.2,"level3":0.1}}',
                    );
                    break;
                case 'setAffiliateLevelRateByPlayer':
                    $this->post_data = array(
                        'token' => $token,
                        'params' => '{"player_id":3, "levelrate":{"level1":0.7,"level2":0.2,"level3":0.1}}',
                    );
                    break;
            }
        }
    }

    public function get_test($fun_name = null){
        if( !$fun_name ) return;
        $this->func_name = $fun_name;
        $fun_name = 'post_'.$fun_name;
        if(method_exists('Controller_Service',$fun_name)){
            $this->{$fun_name}();
        }else
            return $this->response(array('err',-1));
    }

#endregion

}