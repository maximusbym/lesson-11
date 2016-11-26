<?php

namespace App\Controllers;

use App\Models\Lot;
use App\Models\Bet;
use App\Models\LotPhoto;
use App\Controllers\Controller;
use App\Database\DB;
use App\Framework\View;
use App\Services\ServicesContainer;

class LotController
{
    public function showLot(){
        $lotID=isset($_GET['lot_id']) ? $_GET['lot_id'] : 0;
        $user=isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        $bet=isset($_POST['bet']) ? (int)$_POST['bet'] : 0;
        if($lotID)
        {
            $lot = Lot::get($lotID);
            $allBets=$this->makeBet($lotID, $bet, $user);
        }
        View::show("lot", [
            'lot_page'=>$lot,
            'maxBet'=>$allBets
        ]);
    }

    public function makeBet($lotID, $bet, $user){
        $sql="SELECT * FROM `bets` WHERE `lot_id`='{$lotID}' ORDER BY `price`";
        if($bet)
        {
            $allBets=DB::select($sql);
            $maxBet=!empty($allBets) ? array_pop($allBets) : 0;
            $bet= ($bet>$maxBet['price']) ?  $bet : (int)$maxBet['price'];
            if ($user)
            {
                $time=date("Y-m-d H:i:s");
                $userBet=DB::select("SELECT * FROM `bets` 
                                      WHERE (`user_id`='{$user}' AND `lot_id`='{$lotID}')");
                if($userBet){
                    DB::update("UPDATE `bets`
                                SET `price`='{$bet}', `created_at`='{$time}'
                                WHERE(`user_id`='{$user}' AND `lot_id`='{$lotID}')");
                   /* $Bet=new Bet();
                    $updateBet=$Bet->hydrate($userBet);
                    $Bet=$updateBet[0];
                    $Bet->setPrice($bet);
                    $Bet->save();     error*/
                }else{
                    $Bet=new Bet();
                    $Bet->setPrice($bet);
                    $Bet->setUser_id($user);
                    $Bet->setLot_id($lotID);
                    $Bet->setCreated_at($time);
                    $Bet->save();
                }
            } else {
                header("location: /login");
                exit();
            }
        }
        return $allBets=DB::select($sql);
    }
}
