<?php

namespace ofarukbicer\Regexify;

class Regexify
{
  public $email = "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix";
  public $username = "/^[A-Za-z][A-Za-z0-9]{5,16}$/";
  public $url = "/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/";
  public $only_number = "/^\d+$/";
  public $only_char = "/^[A-Za-z]+$/";
  public $alphanumeric = "/^[a-zA-Z0-9]+$/";
  public $all = "/^(.*)$/";
  public $iban = "/^([A-Z]{2})\s*\t*(\d\d)\s*\t*(\d\d\d\d)\s*\t*(\d\d\d\d)\s*\t*(\d\d)\s*\t*(\d\d\d\d\d\d\d\d\d\d)$/";
  public $telephone = "/^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]\d{3}[\s.-]\d{4}$/";

  private $regex_text;
  private $regex_name;

  public $patterns = [
    "number" => '0-9',
    "uppercase" => 'A-Z',
    "lowercase" => 'a-z',
    "letter" => 'a-zA-Z',
    "alphanumeric" => 'a-zA-Z0-9',
    "anything" => '.',
    "whitespace" => '\\s',
    "tab" => '\\t',
    "space" => ' '
  ];

  /**
   * Kendiniz özel bir regex kaydettirerek istediğiniz zaman kolay bir erişim sağlıyabilirsiniz.
   * 
   * ör: 
   * 
   * $regexify->custom("deneme", "deneme_regex")
   * 
   * @param string $name Özel bir isimlendirme
   * @param string $regex İstediğiniz bir türde regex
   * @return object|string
   */
  public function custom(string $name, string $regex) : object|string
  {
    if ($name != "regex_text" || $name != "regex_name" || $name != "patterns") {
      $this->$name = $regex;

      return (object) [
        "name" => $name,
        "regex" => $regex
      ];
    }
    return "You cannot open a new regex record with this name.";
  }

  /**
   * Özel oluşturulmuş regexlerinizi çağırabilirsiniz.
   * 
   * ör: 
   * 
   * $regexify->custom("deneme", "deneme_regex")
   * $regexify->get("deneme")
   * 
   * @param string $name Çağıracağınız değerin ismi
   * @return string|bool
   */
  public function get(string $name) : string|bool
  {
    if (isset($this->$name)) {
      return $this->$name;
    }
    return false;
  }

  /**
   * Çok kolay biçimde istediğiniz bir şekilde düzenli ifade yazabileceksiniz.
   * 
   * @param string $name Boş girilmesi durumunda regex'i kaydetmez
   * @return mixed
   */
  public function create(string $name = "") : self
  {
    $this->regex_name = $name;
    return $this;
  }

  /**
   * Regex başlangıcını tanımlar ve '/^' ekler
   * 
   * @param array|string $args
   * @param int $exactly kesinlikle bu uzunlukta olmalıdır sorgusu
   * @param array $range aralık belirtme
   * @return self
   */
  public function begin_with(array|string $args, int $exactly = null, array $range = null) : self
  {
    $this->regex_text = "/^";

    if (is_array($args)) {
      if ($this->contains_symbols($args)) {
        $this->regex_text .= "[";
        for ($i=0; $i < count($args); $i++) { 
          if (substr($args[$i], 0, 1) == ":") {
            $this->regex_text .= isset($this->patterns[substr($args[$i], 1)]) ? $this->patterns[substr($args[$i], 1)] : $args[$i];
          }else {
            $this->regex_text .= $args[$i];
          }
        }
        $this->regex_text .= "]";
      }else {
        $this->regex_text .= "(";
        for ($i=0; $i < count($args); $i++) {
          $this->regex_text .= $args[$i] . "|";
        }
        $this->regex_text = substr($this->regex_text, 0, -1);
        $this->regex_text .= ")";
      }
    }else if (substr($args, 0, 1) == ":") {
      $this->regex_text .= "[" . (isset($this->patterns[substr($args, 1)]) ? $this->patterns[substr($args, 1)] : $args) . "]";
    }else{
      if (in_array($args, [",","!",":","."])) {
        $this->regex_text .= $args;
      }else {
        $this->regex_text .= "(" . $args . ")";
      }
    }
    $this->regex_text .= $this->quantity($exactly, $range);
    return $this;
  }

  /**
   * Düzenli ifadeye yeni bir blok ekler
   * 
   * @param array|string $args
   * @param int $exactly kesinlikle bu uzunlukta olmalıdır sorgusu
   * @param array $range aralık belirtme
   * @return self
   */
  public function then(array|string $args, int $exactly = null, array $range = null) : self
  {
    if (is_array($args)) {
      if ($this->contains_symbols($args)) {
        $this->regex_text .= "[";
        for ($i=0; $i < count($args); $i++) { 
          if (substr($args[$i], 0, 1) == ":") {
            $this->regex_text .= isset($this->patterns[substr($args[$i], 1)]) ? $this->patterns[substr($args[$i], 1)] : $args[$i];
          }else {
            $this->regex_text .= $args[$i];
          }
        }
        $this->regex_text .= "]";
      }else {
        $this->regex_text .= "(";
        for ($i=0; $i < count($args); $i++) {
          $this->regex_text .= $args[$i] . "|";
        }
        $this->regex_text = substr($this->regex_text, 0, -1);
        $this->regex_text .= ")";
      }
    }else if (substr($args, 0, 1) == ":") {
      $this->regex_text .= "[" . (isset($this->patterns[substr($args, 1)]) ? $this->patterns[substr($args, 1)] : $args) . "]";
    }else{
      if (in_array($args, [",","!",":","."])) {
        $this->regex_text .= $args;
      }else {
        $this->regex_text .= "(" . $args . ")";
      }
    }
    $this->regex_text .= $this->quantity($exactly, $range);
    return $this;
  }

  /**
   * Reddedilen düzenli ifadeye yeni bir blok ekler ve '^' ekler
   * 
   * @param array|string $args
   * @param int $exactly kesinlikle bu uzunlukta olmalıdır sorgusu
   * @param array $range aralık belirtme
   * @return self
   */
  public function not(array|string $args, int $exactly = null, array $range = null) : self
  {
    if (is_array($args)) {
      if ($this->contains_symbols($args)) {
        $this->regex_text .= "[^";
        for ($i=0; $i < count($args); $i++) { 
          if (substr($args[$i], 0, 1) == ":") {
            $this->regex_text .= isset($this->patterns[substr($args[$i], 1)]) ? $this->patterns[substr($args[$i], 1)] : $args[$i];
          }else {
            $this->regex_text .= $args[$i];
          }
        }
        $this->regex_text .= "]";
      }else {
        $this->regex_text .= "(^";
        for ($i=0; $i < count($args); $i++) {
          $this->regex_text .= $args[$i] . "|";
        }
        $this->regex_text = substr($this->regex_text, 0, -1);
        $this->regex_text .= ")";
      }
    }else if (substr($args, 0, 1) == ":") {
      $this->regex_text .= "[" . (isset($this->patterns[substr($args, 1)]) ? $this->patterns[substr($args, 1)] : $args) . "]";
    }else{
      $this->regex_text .= "(^" . $args . ")";
    }
    $this->regex_text .= $this->quantity($exactly, $range);
    return $this;
  }

  /**
   * Regex sonunu tanımlar ve '$/' ekler
   * 
   * @param string $end regex sonuna eklemek istediğiniz
   * @return string
   */
  public function end_with(string $end = "") : string
  {
    $this->regex_text .= "$/$end";
    if ($this->regex_name && $this->regex_name != "") {
      $this->custom($this->regex_name, $this->regex_text);
    }
    
    return $this->regex_text;
  }

  private function contains_symbols($args) : bool
  {
    for ($i=0; $i < count($args); $i++) { 
      if (substr($args[$i], 0, 1) == ":") {
        if (isset($this->patterns[substr($args[$i], 1)])) {
          return true;
        }
      }
    }
    return false;
  }

  private function quantity($exact, $range) : string
  {
    if ($range && count($range) == 2){
      return '{'.$range[0].','.$range[1].'}';
    }else if ($range){
      return '{'.$range[0].',}';
    }elseif (intval($exact) > 1){
      return '{'.$exact.'}';
    }else{
      return "";
    }
  }
}
