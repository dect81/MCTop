<?php
if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class buy_minecraft_form extends join_edit {
  function buy_minecraft_form() {
    global $FORM, $LNG, $TMPL;

    $TMPL['header'] = "Купить minecraft за 300 рублей";

    if (!isset($FORM['submit'])) {
      $this->form();
    }
    else {
      $this->process();
    }
  }

  function form() {
    global $CONF, $DB, $LNG, $TMPL;
    $form = <<<HTML
<table align="center">
<tr><td><p>Здесь можно заказать&nbsp;<strong>лицензионный gift-код для minecraft </strong> всего за 300 рублей! <br>Оплатить можно через систему оплаты Webmoney, QIWI, или же через терминал. <br>Подробнее об акции написано <a href="http://mctop.su/buy" target="_blank">Здесь</a>.</p></td></tr>
<form action="http://mctop.su/buy-minecraft-form" method="post">
<tr><td><p>Способ оплаты<br><center>
<select size="3" name="oplata[]" multiple="multiple">
<option>Webmoney</option>
<option>QIWI</option>
<option>Яндекс.Деньги</option>
</select></center> </p></td></tr>
<tr><td><p>Ваш Email:<br> <center ><input type="text" name="email" value="" size="40"></center> </p></td></tr>
<tr><td><p>Ваш Skype (если нет, оставьте поле пустым):<br> <center ><input type="text" name="skype" value="" size="40"></center> </p></td></tr>
<tr><td><p>Ваш ICQ (если нет, оставьте поле пустым):<br> <center ><input type="text" name="icq" value="" size="40"></center> </p></td></tr>
<tr><td><p>Откуда узнали об акции?<br><center><textarea name="yznali" cols="100%" rows="2"></textarea></center> </p></td></tr>
<tr><td><p>Будете ли приобретать коды для своих друзей?<br><center ><textarea name="dlya_dryzei" cols="100%" rows="1"></textarea></center> </p></td></tr>
<tr><td><p>Пожелания<br><center><textarea name="message" cols="100%" rows="10"></textarea></center> </p></td></tr>
<tr><td><p><input type="submit" name="submit" value="Отправить заявку" class="submit" /></p></td></tr>
</form>
</table>
	
HTML;
  //  $TMPL['content'] = $form;
	}

  function process() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
    $form = <<<HTML
<table align="center">
<tr><td><p>Здесь можно заказать&nbsp;<strong>лицензионный gift-код для minecraft </strong> всего за 300 рублей! <br>Оплатить можно через систему оплаты Webmoney, QIWI, или же через терминал. <br>Подробнее об акции написано <a href="http://mctop.su/buy" target="_blank">Здесь</a>.</p></td></tr>
<form action="http://mctop.su/buy-minecraft-form" method="post">
<tr><td><p>Способ оплаты<br><center>
<select size="3" name="oplata[]" multiple="multiple">
<option>Webmoney</option>
<option>QIWI</option>
<option>Яндекс.Деньги</option>
</select></center> </p></td></tr>
<tr><td><p>Ваш Email:<br> <center ><input type="text" name="email" value="" size="40"></center> </p></td></tr>
<tr><td><p>Ваш Skype (если нет, оставьте поле пустым):<br> <center ><input type="text" name="skype" value="" size="40"></center> </p></td></tr>
<tr><td><p>Ваш ICQ (если нет, оставьте поле пустым):<br> <center ><input type="text" name="icq" value="" size="40"></center> </p></td></tr>
<tr><td><p>Откуда узнали об акции?<br><center><textarea name="yznali" cols="100%" rows="2"></textarea></center> </p></td></tr>
<tr><td><p>Будете ли приобретать коды для своих друзей?<br><center ><textarea name="dlya_dryzei" cols="100%" rows="1"></textarea></center> </p></td></tr>
<tr><td><p>Пожелания<br><center><textarea name="message" cols="100%" rows="10"></textarea></center> </p></td></tr>
<tr><td><p><input type="submit" name="submit" value="Отправить заявку" class="submit" /></p></td></tr>
</form>
</table>
	
HTML;
	
$from = "buyminecraft@mctop.su";
$to = "mitorus@yandex.ru";
$i = 0;
if(empty($_POST['oplata'])) {$TMPL['content'] = '<center><font color="red">Вы не заполнили обязательное поле "Способ оплаты"</font></center><br>'; $i++;}
if(empty($_POST['email'])) {$TMPL['content'] .= '<center><font color="red">Вы не заполнили обязательное поле "Email"</font></center><br>'; $i++;}
if(empty($_POST['yznali'])) {$TMPL['content'] .= '<center><font color="red">Вы не заполнили обязательное поле "Узнал об акции"</font></center><br>'; $i++;}


if($i == 0) {
  $oplata = '';

  foreach($_POST['oplata'] as $t) {
    $oplata .= trim(stripslashes($t)).",";
  }  

  $email = Trim(stripslashes($_POST['email'])); 
  $icq = Trim(stripslashes($_POST['icq'])); 
  $skype = Trim(stripslashes($_POST['skype'])); 
  $yznali = Trim(stripslashes($_POST['yznali'])); 
  $dlya_dryzei = Trim(stripslashes($_POST['dlya_dryzei'])); 
  $message = Trim(stripslashes($_POST['message'])); 
  $subject = "mctop";

  // prepare email body text
  $body = "";
  $body .= "Заявка с сайта mctop.su";
  $body .= "\n\n";
  $body .= "email: $email";
  $body .= "\n";
  $body .= "skype: $skype";
  $body .= "\n";
  $body .= "icq: $icq";
  $body .= "\n\n";
  $body .= "Способ оплаты: $oplata";
  $body .= "\n";
  $body .= "Узнал об акции: $yznali";
  $body .= "\n";
  $body .= "Будете ли приобретать коды для своих друзей: $dlya_dryzei";
  $body .= "\n\n";
  $body .= "Пожелания: $message";

  $success = mail($to, $subject, $body, "From: <$email>");
  // redirect to success page 
  if ($success){
    $TMPL['content'] = "<center><br><br><h3>Ваше предложение будет рассмотрено в течении нескольких дней</h3></center>";
    mail("medvedkoo@xakep.ru", $subject, $body, "From: <$email>");
    // хотел посчитать сколько покупают в день, да тебя не будить. Enelar 22.01.12 3.19 
	// умница, я тебя люблю 30.04.12
  }
  
} else {
  //$TMPL['content'] = $form;
}


      }
    }
?>
