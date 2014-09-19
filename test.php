<?php

$polo=new poloniex('UFI8JVZN-G0ST4X5P-QU8UHRZA-3YQEYQ5J','600b0ccc60ccbbcf0afe8842f74fdcf3b88b846f7fba61be308b35fd6129c981628440c86ca6b764cee287a68e766aedfd61c5d844ff7c9ac8219ef3bf12957d');
$market='BTSX';
$depth=20;
$price=$polo->return_latest_average_price($market);
//print_r($history);
//$chart=$polo->return_chart_data();
//print_r($chart);
//$balances=$polo->get_balances();
//print_r($balances);
$pair='BTC_'.$market;
$rate=.121212;
$amount=12;
$buy=$polo->buy($pair, $price, $amount);
//$order_book=$polo->get_trade_history($pair);
//print_r($order_book);
//$sell_maid=$polo->sell($pair, $rate, $amount); 
//print_r($sell_maid);
	// FINAL TESTED CODE - Created by Compcentral
	
	// NOTE: currency pairs are reverse of what most exchanges use...
	//       For instance, instead of XPM_BTC, use BTC_XPM

	class poloniex {
		protected $api_key;
		protected $api_secret;
		protected $trading_url = "https://poloniex.com/tradingApi";
		protected $public_url = "https://poloniex.com/public";
		
		public function __construct($api_key, $api_secret) {
			$this->api_key = $api_key;
			$this->api_secret = $api_secret;
		}
			
		private function query(array $req = array()) {
			// API settings
			$key = $this->api_key;
			$secret = $this->api_secret;
		 
			// generate a nonce to avoid problems with 32bit systems
			$mt = explode(' ', microtime());
			$req['nonce'] = $mt[1].substr($mt[0], 2, 6);
		 
			// generate the POST data string
			$post_data = http_build_query($req, '', '&');
			$sign = hash_hmac('sha512', $post_data, $secret);
		 
			// generate the extra headers
			$headers = array(
				'Key: '.$key,
				'Sign: '.$sign,
			);

			// curl handle (initialize if required)
			static $ch = null;
			if (is_null($ch)) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_USERAGENT, 
					'Mozilla/4.0 (compatible; Poloniex PHP bot; '.php_uname('a').'; PHP/'.phpversion().')'
				);
			}
			curl_setopt($ch, CURLOPT_URL, $this->trading_url);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			// run the query
			$res = curl_exec($ch);

			if ($res === false) throw new Exception('Curl error: '.curl_error($ch));
			//echo $res;
			$dec = json_decode($res, true);
			if (!$dec){
				//throw new Exception('Invalid data: '.$res);
				return false;
			}else{
				return $dec;
			}
		}
		
		protected function retrieveJSON($URL) {
			$opts = array('http' =>
				array(
					'method'  => 'GET',
					'timeout' => 10 
				)
			);
			$context = stream_context_create($opts);
			$feed = file_get_contents($URL, false, $context);
			$json = json_decode($feed, true);
			return $json;
		}
		
		public function get_balances() {
			return $this->query( 
				array(
					'command' => 'returnBalances'
				)
			);
		}
public function return_chart_data(){
	$ch = curl_init();
	$url='https://poloniex.com/public?command=returnChartData&currencyPair=BTC_XMR&start=1405699200&end=9999999999&period=14400';
	curl_setopt($ch,CURLOPT_URL, $url);
	$response = curl_exec($ch);
	if($response === FALSE){
    		die(curl_error($ch));
	}
	$responseData = json_decode($response, TRUE);
	return $responseData;
	}
public function return_order_book($market,$depth){
	$ch=curl_init();
	$url='https://poloniex.com/public?command=returnOrderBook&currencyPair=BTC_'.$market.'&depth='.$depth;
	curl_setopt($ch,CURLOPT_URL,$url);
	$response=curl_exec($ch);
	if($response === FALSE){
    		die(curl_error($ch));
	}
	$responseData=json_decode($response,TRUE);
	//print_r($responseData);
	return $responseData;
}
public function return_latest_bid(){
$polo=new poloniex('UFI8JVZN-G0ST4X5P-QU8UHRZA-3YQEYQ5J','600b0ccc60ccbbcf0afe8842f74fdcf3b88b846f7fba61be308b35fd6129c981628440c86ca6b764cee287a68e766aedfd61c5d844ff7c9ac8219ef3bf12957d');
}
public function return_latest_average_price($market){
        $ch=curl_init();
        $url='https://poloniex.com/public?command=returnTradeHistory&currencyPair=BTC_'.$market;
        curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response=curl_exec($ch);
        if($response === FALSE){
                die(curl_error($ch));
        }
        $responseData=json_decode($response,TRUE);
        //print_r($responseData);
	$sum=0;
	foreach($responseData as $rd){
		$sum+=$rd['rate'];	
	}
//get latest bid
	
//if latest bid > average, set buy-in price at 1% lower than latest bid
	$average=$sum/200;
        return $average;
}
		public function get_open_orders($pair) {		
			return $this->query( 
				array(
					'command' => 'returnOpenOrders',
					'currencyPair' => strtoupper($pair)
				)
			);
		}
		
		public function get_my_trade_history($pair) {
			return $this->query(
				array(
					'command' => 'returnTradeHistory',
					'currencyPair' => strtoupper($pair)
				)
			);
		}
		
		public function buy($pair, $rate, $amount) {
			return $this->query( 
				array(
					'command' => 'buy',	
					'currencyPair' => strtoupper($pair),
					'rate' => $rate,
					'amount' => $amount
				)
			);
		}
		
		public function sell($pair, $rate, $amount) {
			return $this->query( 
				array(
					'command' => 'sell',	
					'currencyPair' => strtoupper($pair),
					'rate' => $rate,
					'amount' => $amount
				)
			);
		}
		
		public function cancel_order($pair, $order_number) {
			return $this->query( 
				array(
					'command' => 'cancelOrder',	
					'currencyPair' => strtoupper($pair),
					'orderNumber' => $order_number
				)
			);
		}
		
		public function withdraw($currency, $amount, $address) {
			return $this->query( 
				array(
					'command' => 'withdraw',	
					'currency' => strtoupper($currency),				
					'amount' => $amount,
					'address' => $address
				)
			);
		}
		
		public function get_trade_history($pair) {
			$trades = $this->retrieveJSON($this->public_url.'?command=returnTradeHistory&currencyPair='.strtoupper($pair));
			return $trades;
		}
		
		public function get_order_book($pair) {
			$orders = $this->retrieveJSON($this->public_url.'?command=returnOrderBook&currencyPair='.strtoupper($pair));
			return $orders;
		}
		
		public function get_volume() {
			$volume = $this->retrieveJSON($this->public_url.'?command=return24hVolume');
			return $volume;
		}
	
		public function get_ticker($pair = "ALL") {
			$pair = strtoupper($pair);
			$prices = $this->retrieveJSON($this->public_url.'?command=returnTicker');
			if($pair == "ALL"){
				return $prices;
			}else{
				$pair = strtoupper($pair);
				if(isset($prices[$pair])){
					return $prices[$pair];
				}else{
					return array();
				}
			}
		}
		
		public function get_trading_pairs() {
			$tickers = $this->retrieveJSON($this->public_url.'?command=returnTicker');
			return array_keys($tickers);
		}
		
		public function get_total_btc_balance() {
			$balances = $this->get_balances();
			$prices = $this->get_ticker();
			
			$tot_btc = 0;
			
			foreach($balances as $coin => $amount){
				$pair = "BTC_".strtoupper($coin);
			
				// convert coin balances to btc value
				if($amount > 0){
					if($coin != "BTC"){
						$tot_btc += $amount * $prices[$pair];
					}else{
						$tot_btc += $amount;
					}
				}

				// process open orders as well
				if($coin != "BTC"){
					$open_orders = $this->get_open_orders($pair);
					foreach($open_orders as $order){
						if($order['type'] == 'buy'){
							$tot_btc += $order['total'];
						}elseif($order['type'] == 'sell'){
							$tot_btc += $order['amount'] * $prices[$pair];
						}
					}
				}
			}

			return $tot_btc;
		}
	}
?>
