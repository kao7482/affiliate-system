<?php

use phpseclib\Crypt\Rijndael;

class Model_Service
{
    public static function getSystemInfo($system_id){

        try{

            if( intval($system_id) == 0 ) return null;
            $sql = "select id, name, username, password, base_domain, sys_type, security_key, ref_code, email, tel, country_id, currency_id, settings, status from systems where status='active' and id=$system_id limit 1";
            $query = DB::query($sql);
            $result = $query->as_object()->execute();
            if( count($result) == 0 ) return null;
            return $result[0];

        }catch (Exception $ex){
            throw $ex;
        }

        return false;
    }

    public static function getSystemInfoByToken($token){

        try{

            $sql = "select id, name, username, password, base_domain, sys_type, security_key, ref_code, email, tel, country_id, currency_id, settings, status from systems where status='active' and ";
            $sql .= "MD5(CONCAT(lower(username),lower(password),lower(security_key)))='$token' limit 1";
            $query = DB::query($sql);
            $result = $query->as_object()->execute();
            if( count($result) == 0 ) return null;
            return $result[0];

        }catch (Exception $ex){
            throw $ex;
        }

        return false;
    }

    public static function updateSystemInfo($system_id, $params = array()){

        try{
            if(\Model_System::crudUpdate($system_id, $params))
                return true;
            else
                return false;
        }catch (Exception $ex){
            throw $ex;
        }

        return false;
    }

    public static function createAffiliatePlayer($params = array()){

        try{
            $player = \Model_Player::find('first', array('where' => array(array('username',$params['username']),array('system_id',$params['system_id']))));
            if( $player ) return null;

            if( !isset($params['affiliate_setting_id']) || !self::checkAffiliateSetting($params['system_id'], $params['affiliate_setting_id']) )
                return -1;

            $result = \Model_Player::crudCreate($params);
            if( $result )
                return $result->id;
            else
                return 0;

        }catch (Exception $ex){
            throw $ex;
        }

        return 0;
    }

    public static function checkAffiliateSetting($system_id, $affiliate_setting_id){

        try{
            $affset = \Model_AffiliateSetting::find($affiliate_setting_id);
            if( !$affset ) return false;
            if( $affset->system_id != $system_id )
                return false;
            else
                return true;

        }catch (Exception $ex){
            throw $ex;
        }

        return false;
    }

    public static function checkPllayerIsExists($system_id, $player_id){

        try{
            $result = \Model_Player::find('first', array('where' => array(array('id',$player_id),array('system_id',$system_id))));

            if( $result )
                return true;
            else
                return false;

        }catch (Exception $ex){
            throw $ex;
        }

        return false;
    }

    public static function updateAffiliatePlayer($system_id, $player_id, $params = array()){

        try{

            $player = \Model_Player::find('first', array('where' => array(array('id',$params['id']),array('system_id',$params['system_id']))));
            if( !$player ) return null;

            if( !self::checkAffiliateSetting($params['system_id'], $params['affiliate_setting_id']) )
                return -1;

            if(\Model_Player::crudUpdate($params['id'], $params))
                return 1;
            else
                return 0;

        }catch (Exception $ex){
            throw $ex;
        }

        return 0;
    }

    public static function createAffiliateSetting($params = array()){

        try{

            $result = \Model_AffiliateSetting::crudCreate($params);
            if( $result )
                return $result->id;
            else
                return 0;

        }catch (Exception $ex){
            throw $ex;
        }

        return 0;
    }

    public static function updateAffiliateSetting($params = array()){

        try{
            $result = \Model_Player::find('first', array('where' => array(array('id',$params['id']),array('system_id',$params['system_id']))));
            if( !$result ) return null;

            $result = \Model_Player::crudUpdate($params);
            if( $result )
                return 1;
            else
                return 0;

        }catch (Exception $ex){
            throw $ex;
        }

        return 0;
    }

    public static function getAffiliateSetting($params = array()){

        try{
            $system_id = $params['system_id'];

            $sql = "select id, affiliate_type,period_type, carryover, base_rate, min_limit, max_limit, closing_date, pay_date from affiliate_settings where deleted_at is null and system_id=$system_id";
            if( isset( $params['affiliate_setting_id']) ) {
                $sql .= " where id='" .$params['affiliate_setting_id']."'";
            }

            $query = DB::query($sql);
            $result = $query->execute()->as_array();
            return $result;

        }catch (Exception $ex){
            throw $ex;
        }
    }

    public static function getAffiliateLevelRate($system_id, $scope, $object_id = null){

        try{

            if( $scope == 'system' )
                $sql = "call sp_getLevelRate($system_id,null,null)";
            else if( $scope == 'setting' )
                $sql = "call sp_getLevelRate($system_id,$object_id,null)";
            else if( $scope == 'player')
                $sql = "call sp_getLevelRate($system_id,null,$object_id)";
            else
                $sql = "call sp_getLevelRate(null,null,null)";

            $query = DB::query($sql, DB::SELECT);
            $result = $query->execute()->as_array();
            return $result;

        }catch (Exception $ex){
            throw $ex;
        }
    }

    public static function setAffiliateLevelRate($levelrate, $system_id, $scope, $object_id = null){

        try{

            $data = array();
            for($i=1; $i <= MAX_LEVEL; $i++){
                if( isset($levelrate['level'.$i]) ){
                    $rate = floatval($levelrate['level'.$i]);
                    if( $rate > 0 )
                        $data[$i] = $rate;
                    else
                        return -1;
                }
                else
                    return -1;
            }

            DB::start_transaction();

            if( $scope == 'system' ){

                $sql = "delete from affiliate_level_rate where system_id=$system_id and scope='system'";
                DB::query($sql)->execute();

                for($i=1; $i <= MAX_LEVEL; $i++){
                    $sql = "insert into affiliate_level_rate (system_id, scope, level, rate) values ($system_id, 'system', $i, $data[$i])";
                    DB::query($sql)->execute();
                }

            }else  if( $scope == 'setting' || $scope == 'player' ) {

                $sql = "delete from affiliate_level_rate where system_id=$system_id and scope='$scope' and object_id=$object_id";
                DB::query($sql)->execute();

                for($i=1; $i <= MAX_LEVEL; $i++){
                    $sql = "insert into affiliate_level_rate (system_id, scope, object_id, level, rate) values ($system_id, '$scope', $object_id, $i, $data[$i])";
                    DB::query($sql)->execute();
                }

            }

            DB::commit_transaction();
            return true;

        }catch (Exception $ex){
            DB::rollback_transaction();
            throw $ex;
        }
    }
    public static function getAffiliatePlayers($system_id, $usernames){

        try{
            $players = explode(",",$usernames);
            $usernames = implode("','",$players);

            $sql = "select id, partner_id, external_Player_id, username, password, balance, first_name, last_name, currency_id, country_id, gender, tel, email, address1, address2, affiliate_setting_id, affiliate_code, profiles, status ";
            $sql .= " from players where system_id='$system_id' and username in ('$usernames') order by username";

            $query = DB::query($sql);
            $result = $query->execute()->as_array();
            return $result;

        }catch (Exception $ex){
            throw $ex;
        }
    }

    public static function createPlayerTransaction($params){
        try{
            $result = \Model_PlayerTransaction::crudCreate($params);
            if( $result )
                return $result->id;
            else
                return 0;
        }catch (Exception $ex){
            throw $ex;
        }
    }

    public static function getAffiliatePlayerTransactionHistories($params){
        try{
            $system_id = $params['system_id'];
            $player_id = $params['player_id'];

            $pageNumber = isset($params['pageNumber'])?$params['pageNumber']:1;
            $pageSize = isset($params['pageSize'])?$params['pageSize']:PAGE_SIZE;
            $table = \Model_PlayerTransaction::table();
            $field = "player_id, amount, external_tran_id, details, created_at";

            $date = date("Y-m-d");
            $datetime = date("Y-m-d 23:59:59");

            $beginDate = isset($params['beginDate'])? $params['beginDate'] : $date;
            $startTime = strtotime($beginDate);
            if( $startTime == false)
                $startTime = strtotime($date);

            $endDate = isset($params['endDate'])? $params['endDate'] : $datetime;
            $endTime = strtotime($endDate);
            if( $endTime == false)
                $endTime = strtotime($datetime);

            $where = "where system_id=$system_id and player_id=$player_id and created_at>=$startTime and created_at<=$endTime";

            $sql = "call sp_Pagination($pageNumber, $pageSize, '$table', '$field', '$where', '')";
            $query = DB::query($sql, DB::MULTI_QUERY, 2);
            $result = $query->execute();

            return array('TOTAL'=> $result[1], 'DATA'=> $result[0]);

        }catch (Exception $ex){
            throw $ex;
        }
    }

    public static function getAffiliatePlayerReward($params){
        try{
            $system_id = $params['system_id'];

            $where_playerid = isset($params['system_id'])?"and player_id=".$params['system_id']:"";

            $pageNumber = isset($params['pageNumber'])?$params['pageNumber']:1;
            $pageSize = isset($params['pageSize'])?$params['pageSize']:PAGE_SIZE;
            $table = "v_player_rewards";
            $field = "reward_id, player_id, reward, period_type, paid_at, start_time, end_time, status, remarks, created_at, partner_id, external_player_id, username, first_name, last_name";

            $date = date("Y-m-d");
            $datetime = date("Y-m-d 23:59:59");

            $beginDate = isset($params['beginDate'])? $params['beginDate'] : $date;
            $startTime = strtotime($beginDate);
            if( $startTime == false)
                $startTime = strtotime($date);

            $endDate = isset($params['endDate'])? $params['endDate'] : $datetime;
            $endTime = strtotime($endDate);
            if( $endTime == false)
                $endTime = strtotime($datetime);

            $where = "where system_id=$system_id $where_playerid and created_at>=$startTime and created_at<=$endTime";

            $sql = "call sp_Pagination($pageNumber, $pageSize, '$table', '$field', '$where', '')";
            $query = DB::query($sql, DB::MULTI_QUERY, 2);
            $result = $query->execute();

            return array('TOTAL'=> $result[1], 'DATA'=> $result[0]);

        }catch (Exception $ex){
            throw $ex;
        }
    }

    public static function getAffiliateRewardDetail($params){
        try{
            $system_id = $params['system_id'];
            $reward_id = $params['reward_id'];

            $sql = "call sp_getRewardDetail($system_id, $reward_id)";
            $query = DB::query($sql, DB::MULTI_QUERY, 2);
            $result = $query->execute();

            return array('HEADER'=> $result[0], 'DETAIL'=> $result[0]);

        }catch (Exception $ex){
            throw $ex;
        }
    }

    public static function updateeAffiliatePlayerReward($params){
        try{

            $system_id = $params['system_id'];
            $update_data = $params['update_data'];
            $time = time();

            DB::start_transaction();
            foreach ($update_data as $data_row){
                $id = $data_row['id'];
                $status = $data_row['status'];
                $remarks = $data_row['remarks'];
                $sql = "update player_rewards set status='$status', remarks='$remarks', updated_at=$time where system_id=$system_id and id=$id";
                $ret = DB::query($sql)->execute();
                if( $ret == 0 ){
                    DB::rollback_transaction();
                    return false;
                }
            }
            DB::commit_transaction();

            return true;

        }catch (Exception $ex){
            DB::rollback_transaction();
            throw $ex;
        }
    }

    public static function getAffiliatePartners($params){
        try{
            $system_id = $params['system_id'];
            $player_id = $params['player_id'];

            $level = isset($params['lowerlevel'])?$params['lowerlevel']:0;
            $level = intval($level);
            if( $level < 0 or $level > MAX_LEVEL)
                $level = 0;


            $sql = "call sp_getPlayerPartners($system_id, $player_id, $level, 0)";
            $query = DB::query($sql, DB::SELECT);
            $result = $query->execute()->as_array();

            return $result;

        }catch (Exception $ex){
            throw $ex;
        }
    }

    public static function getAffiliateRankingBySystem($params){
        try{
            $system_id = $params['system_id'];
            $minRank = isset($params['minRank'])?$params['minRank']:SYSTEM_RANKING_TOP;
            $minRank = intval($minRank);
            if( $minRank <= 0 or $minRank > SYSTEM_RANKING_TOP)
                $minRank = SYSTEM_RANKING_TOP;

            $sql = "call sp_getRanking($system_id, null, $minRank)";
            $query = DB::query($sql, DB::SELECT);
            $result = $query->execute()->as_array();

            return $result;

        }catch (Exception $ex){
            throw $ex;
        }
    }

    public static function getAffiliateRankingByPlayer($params){
        try{
            $system_id = $params['system_id'];
            $player_id = $params['player_id'];

            $minRank = isset($params['minRank'])?$params['minRank']:PLAYER_RANKING_TOP;
            $minRank = intval($minRank);
            if( $minRank <= 0 or $minRank > PLAYER_RANKING_TOP)
                $minRank = PLAYER_RANKING_TOP;

            $sql = "call sp_getRanking($system_id, $player_id, $minRank)";
            $query = DB::query($sql, DB::SELECT);
            $result = $query->execute()->as_array();

            return $result;

        }catch (Exception $ex){
            throw $ex;
        }
    }

    public static function validateFields()
    {
        $validation = \Validation::forge();

        //$validation->add('game_date', __('game.label.game_date', [], 'game_date'))->add_rule('required');


        return $validation;
    }
}
