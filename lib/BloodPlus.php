<?php

define( 'AUTH_URL', 'https://mobile.kenketsu.jp/nskc/mobile/logincheck.asp' );
define( 'AUTH_URL2', 'https://mobile.kenketsu.jp/nskc/mobile/passwdexe.asp' );
define( 'DONE_LIST', 'https://mobile.kenketsu.jp/nskc/mobile/done_list.asp' );
define( 'DESCRIPTION', 'https://mobile.kenketsu.jp/nskc/mobile/done_view.asp' );

class BloodPlus
{
  private $user_id;
  private $auth_c_id;
  private $auth_e_id;

  private $dates;

  function __construct($id,$pass,$pass2)
  {
    $data = 'type=chkreg&user_id='.$id.'&password='.$pass;
    $this->user_id = $id;
    if( preg_match('/passwd\.asp\?c=(.*)\&mode=login/', $this->curl('POST',AUTH_URL,$data) ,$ids) ){
      $this->auth_c_id = $ids[1];
    }

    $data = 'mode=login&c='.$this->auth_c_id.'&e=&passwd='.$pass2.'&submit=OK';
    if( preg_match('/\&e=([0-9A-Z]*)\&/', $this->curl('POST',AUTH_URL2,$data) ,$ids) ){
      $this->auth_e_id = $ids[1];
    }
  }

  private function curl($method,$url,$data)
  {
    return file_get_contents($url,false,
      stream_context_create(
        array(
          "http" => array(
            "method"  => $method,
            "header"  => implode("\r\n", array(
              "Content-Type: application/x-www-form-urlencoded",
              "Content-Length: ".strlen($data)
            )),
            "content" => $data
          )
        )
      ));
  }

  function auth_params()
  {
    return 'c='.$this->auth_c_id.'&e='.$this->auth_e_id;
  }

  function dates()
  {
    if( !$this->dates ){
      preg_match_all('/done_id=([0-9]*)\&cmdScroll/',$this->curl('GET',DONE_LIST,$this->auth_params()),$date_list);
      $this->dates = $date_list[1];
    }
    return $this->dates;
  }

  function donations()
  {
    foreach( $this->dates() as $date ){
      echo $this->curl('GET',DESCRIPTION,$this->auth_params().'&done_id='.$date);
    }
  }
}

