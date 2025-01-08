<?php
function ebmail( $to, $from, $subject, $message, $bcc="" ,$br="1")
{
				$owner_m_smtp = $GLOBALS['CONF']['owner_m_smtp'];
				$owner_m_user = $GLOBALS['CONF']['owner_m_user'];
				$owner_m_pass = $GLOBALS['CONF']['owner_m_pass'];
				$owner_m_check = $GLOBALS['CONF']['owner_m_check'];
				$owner_m_mail = $GLOBALS['CONF']['owner_m_mail'];
				$ownersys = $GLOBALS['CONF']['ownersys'];
				$subject22 = $subject;
				$subject = "=?UTF-8?B?".base64_encode( $subject )."?=";
				//$message = mb_eregi_replace( "\r\n", "<br>", $message );
				/*$chktemp = $message;
				if($message != strip_tags($chktemp)){
					$message = preg_replace('/\s+/', '', $message);
				}else{
					$message = nl2br( $message );
				}*/
				//$message = "<html><body style='font-size:12px'>".$message."</body></html>";
				if($br == 1){
					$message = nl2br( $message );
				}
				list($from) = explode(",",$from);
				
				//修正寄件人outlook中文亂碼
				$froms = explode("<",$from);
				if($froms[1] != ""){
					$froma = "=?UTF-8?B?".base64_encode( $froms[0] )."?=";
					$from = trim($froma)." <".$froms[1];
				}
				
				$tos = explode("<",$to);
				if($tos[1] != ""){
					$toa = "=?UTF-8?B?".base64_encode( $tos[0] )."?=";
					$to = trim($toa)." <".$tos[1];
				}
								
				if ( $ownersys == "1" )
				{
					if(!$bcc){
						mail( $to, $subject, $message, "From: {$from}\nReply-To: {$from}\nContent-Type:text/html;charset=UTF-8 \nX-Mailer: PHP/".phpversion( ) );
					}else{
						foreach($bcc AS $key=>$bccmail){
							$bccs .= $bccs? ",".$bccmail:$bccmail;
						}
						mail( $to, $subject, $message, "From: {$from}\nReply-To: {$from}\nBcc: {$bccs}\nContent-Type:text/html;charset=UTF-8 \nX-Mailer: PHP/".phpversion( ) );
					}
				}
				else if ( $ownersys == "2" )
				{
								send22( $to, $from, $subject22, $message, $bcc );
				}
}

//電子報使用的寄信函數
function ebmails( $to, $from, $subject, $message, $bcc="" ,$br="1")
{
				//$ownersys = $GLOBALS['CONF']['ownersys'];
				$ownersys = 2; //強制使用外部郵件
				
				if($br == 1){
					$message = nl2br( $message );
				}
				list($from) = explode(",",$from);
				if ( $ownersys == "1" )
				{
					send2( $to, $from, $subject, $message, $bcc );
				}
				else if ( $ownersys == "2" )
				{
					send222( $to, $from, $subject, $message, $bcc );
				}
}

function shopmail( $to, $from, $smtext ,$type )
{
				global $msql,$fsql,$tsql,$GLOBALS;
				
				$owner_m_smtp = $GLOBALS['CONF']['owner_m_smtp'];
				$owner_m_user = $GLOBALS['CONF']['owner_m_user'];
				$owner_m_pass = $GLOBALS['CONF']['owner_m_pass'];
				$owner_m_check = $GLOBALS['CONF']['owner_m_check'];
				$owner_m_mail = $GLOBALS['CONF']['owner_m_mail'];
				$ownersys = $GLOBALS['CONF']['ownersys'];
				list($from) = explode(",",$from);
				//修正寄件人outlook中文亂碼
				$froms = explode("<",$from);
				if($froms[0] != ""){
					$froma = "=?UTF-8?B?".base64_encode( $froms[0] )."?=";
					$from = trim($froma)." <".$froms[1];
				}
				/*信件模版*/
				$msql->query( "SELECT * FROM {P}_shop_mailtemp WHERE tid='{$type}' AND status='1' " );//抓取樣板
        		if($msql->next_record()){    			
        			$getsmtext = explode("|",$smtext);//解析替換參數:1.會員姓名、2.網站名稱、3.時間、4.訂單編號、5.付款方式、6.金額、7.網址、8.貨運單編號
        			$subject = mb_eregi_replace("{site_name}",$getsmtext[1],$msql->f("subject"));
        			
        			$subject=$getsmtext[3] != "0" ? mb_eregi_replace("{order_no}",$getsmtext[3],$subject):$subject;//標題替換第四個參數
        			
        			$sendmsg=$msql->f("fix_content")? $msql->f("fix_content"):$msql->f("content");//樣板
        				$getsmtext[2] = date("Y-m-d H:i:s",$getsmtext[2]);
        				//託運單號分析
        				list($yun_no, $yun_type, $yun_api) = explode("^",$getsmtext[7]);
        				//$yun_api = "<a href=\"".$yun_api.$yun_no."\" target=\"_blank\">".$yun_api.$yun_no."</a>";
        				
        				$sendmsg=$getsmtext[0] != "0" ? mb_eregi_replace("{member_name}",$getsmtext[0],$sendmsg):$sendmsg;//替換第一個參數
        				$sendmsg=$getsmtext[1] != "0" ? mb_eregi_replace("{site_name}",$getsmtext[1],$sendmsg):$sendmsg;//替換第二個參數
        				$sendmsg=$getsmtext[2] != "0" ? mb_eregi_replace("{this_time}",$getsmtext[2],$sendmsg):$sendmsg;//替換第三個參數
        				$sendmsg=$getsmtext[3] != "0" ? mb_eregi_replace("{order_no}",$getsmtext[3],$sendmsg):$sendmsg;//替換第四個參數
        				$sendmsg=$getsmtext[4] != "0" ? mb_eregi_replace("{pay_type}",$getsmtext[4],$sendmsg):$sendmsg;//替換第五個參數
        				$sendmsg=$getsmtext[5] != "0" ? mb_eregi_replace("{pay_total}",$getsmtext[5],$sendmsg):$sendmsg;//替換第六個參數
        				$sendmsg=$getsmtext[6] != "0" ? mb_eregi_replace("{site_url}",$getsmtext[6],$sendmsg):$sendmsg;//替換第七個參數
        				$sendmsg=$getsmtext[7] != "0" ? mb_eregi_replace("{yun_no}",$yun_no,$sendmsg):$sendmsg;//替換第八個參數
        				$sendmsg=$getsmtext[8] != "0" ? mb_eregi_replace("{yun_type}",$yun_type,$sendmsg):$sendmsg;//替換第八個參數
        				$sendmsg=$getsmtext[9] != "0" ? mb_eregi_replace("{yun_api}",$yun_api,$sendmsg):$sendmsg;//替換第八個參數
        				/*避免標籤打錯*/
        				/*$sendmsg=$getsmtext[0] != "0" ? mb_eregi_replace("{#member_name#}",$getsmtext[0],$sendmsg):$sendmsg;//替換第一個參數
        				$sendmsg=$getsmtext[1] != "0" ? mb_eregi_replace("{#site_name#}",$getsmtext[1],$sendmsg):$sendmsg;//替換第二個參數
        				$sendmsg=$getsmtext[2] != "0" ? mb_eregi_replace("{#this_time#}",$getsmtext[2],$sendmsg):$sendmsg;//替換第三個參數
        				$sendmsg=$getsmtext[3] != "0" ? mb_eregi_replace("{#order_no#}",$getsmtext[3],$sendmsg):$sendmsg;//替換第四個參數
        				$sendmsg=$getsmtext[4] != "0" ? mb_eregi_replace("{#pay_type#}",$getsmtext[4],$sendmsg):$sendmsg;//替換第五個參數
        				$sendmsg=$getsmtext[5] != "0" ? mb_eregi_replace("{#pay_total#}",$getsmtext[5],$sendmsg):$sendmsg;//替換第六個參數
        				$sendmsg=$getsmtext[6] != "0" ? mb_eregi_replace("{#site_url#}",$getsmtext[6],$sendmsg):$sendmsg;//替換第七個參數
        				$sendmsg=$getsmtext[7] != "0" ? mb_eregi_replace("{#yun_no#}",$yun_no,$sendmsg):$sendmsg;//替換第八個參數
        				$sendmsg=$getsmtext[8] != "0" ? mb_eregi_replace("{#yun_type#}",$yun_type,$sendmsg):$sendmsg;//替換第八個參數
        				$sendmsg=$getsmtext[9] != "0" ? mb_eregi_replace("{#yun_api#}",$yun_api,$sendmsg):$sendmsg;//替換第八個參數*/
        		}else{
        			return false;
        			exit;
        		}
				$message = $sendmsg;
				
				$subject22 = $subject;
 				$subject = "=?UTF-8?B?".base64_encode( $subject )."?=";

				$message = nl2br( $message );
				
				$toppic = array("","pay","yun","unpay","tui","order","tuiitem");
				
				$mailbody = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html><head>';
				$mailbody .='<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body bgcolor="#ffffff">';
				$mailbody .='<table style="display: inline-table;" border="0" cellpadding="0" cellspacing="0" width="800">';
				//$mailbody .='<tr><td><img name="n1_r1_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_top_'.$toppic[$type].'.png" width="800" height="208" alt=""></td></tr>';
				$mailbody .='<tr><td width="100%" valign="top" style="padding:0px;">';
				$mailbody .='<table width="800" border="0" align="left" cellpadding="0" cellspacing="0"><tr><td width="80" height="250">&nbsp;</td>';
				
				$mailbody .= $toppic[$type] == 'yun'? '<td width="400" style="font-family:\'微軟正黑體\',Century Gothic;vertical-align: top;font-size:17px;">'.$message.'</td><td width="30">&nbsp;</td>':'<td width="400" style="font-family:\'微軟正黑體\',Century Gothic;vertical-align: top;font-size:17px;">'.$message.'</td><td width="80">&nbsp;</td>';
				
				$mailbody .=$toppic[$type] == 'yun'? '<td style="vertical-align: top;"><img name="n1_r3_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_right_yun.png"></td></tr></table>':'<td style="vertical-align: top;"><img name="n1_r3_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_right_order.png"></td></tr></table>';
				//$mailbody .='</td></tr><tr><td><img name="n1_r3_c1" src="'.$GLOBALS['CONF']['SiteHttp'].'images/mail_bt.png" width="800" height="240" alt=""></td></tr></table>';
				$mailbody .='</td></tr></table>';
				$mailbody .='</body></html>';
				
				if ( $ownersys == "1" )
				{
								mail( $to, $subject, $mailbody, "From: {$from}\nReply-To: {$from}\nContent-Type:text/html;charset=UTF-8 \nX-Mailer: PHP/".phpversion( ) );
				}
				else if ( $ownersys == "2" )
				{
								send22( $to, $from, $subject22, $mailbody ,$bcc);
				}
				return true;
        		exit;
}
function send22( $to, $from, $subject, $message, $bcc="" )
{
				require_once('class.phpmailer.old.php');
				$SiteName = $GLOBALS['CONF']['SiteName'];
				$owner_m_smtp = $GLOBALS['CONF']['owner_m_smtp'];
				$owner_m_user = $GLOBALS['CONF']['owner_m_user'];
				$owner_m_pass = $GLOBALS['CONF']['owner_m_pass'];
				$owner_m_check = $GLOBALS['CONF']['owner_m_check'];
				$owner_m_mail = $GLOBALS['CONF']['owner_m_mail'];
				$owner_m_ssl = $GLOBALS['CONF']['owner_m_ssl'];
				$owner_m_port = $GLOBALS['CONF']['owner_m_port'];
				$ownersys = $GLOBALS['CONF']['ownersys'];
				$smtp = $owner_m_smtp;
				$check = $owner_m_check;
				$toname = $subject;
				list($from) = explode(",",$from);
				
				$froms = explode("<",$from);
				if($froms[1] != ""){
					$froms[1] = str_replace(">","",$froms[1]);
					$owner_m_mail = $froms[1];
					$SiteName = trim($froms[0]);
				}
				$tos = explode("<",$to);
				if($tos[1] != ""){
					$tos[1] = str_replace(">","",$tos[1]);
					$to = trim($tos[1]);
					$toname = trim($tos[0]);
				}
				/**/
				$mail = new PHPMailer();
				$mail->CharSet = "utf-8";
				$mail->Encoding = "base64";
				$mail->SetLanguage('zh');
				$mail->IsSMTP(); // telling the class to use SMTP
				//$mail->SMTPDebug = 1;
				if($check)
				{
					$mail->SMTPAuth   = true;
					$mail->Username   = $owner_m_user;
					$mail->Password   = $owner_m_pass;
				}
				if($owner_m_ssl){
					$mail->SMTPSecure = "ssl";
				}
				$mail->Host       = $smtp;
				$mail->Port       = $owner_m_port? $owner_m_port:'25';
				$mail->SetFrom($owner_m_mail, $SiteName); //寄件者信箱
				$mail->AddReplyTo($owner_m_mail, $subject);//回信的信箱
				$mail->AddAddress($to, $toname);
				if($bcc){
					foreach($bcc AS $key=>$bccmail){
							$mail->AddBCC($bccmail);
					}
				}
				$mail->Subject    = $subject;
				//$mail->AltBody    = "觀看這封信件, 請使用可觀看 HTML編碼的 email瀏覽器!"; // optional, comment out and test
				$body = $message;
				$mail->MsgHTML($body);
				if(!$mail->Send()) {
					
					//修正寄件人outlook中文亂碼
					$owner_m_mail = "=?UTF-8?B?".base64_encode( $SiteName )."?=<".$owner_m_mail.">";
					$to = "=?UTF-8?B?".base64_encode( $subject )."?=<".$to.">";
					
					if(!$bcc){
						mail( $to, $subject, $message, "From: {$owner_m_mail}\nReply-To: {$owner_m_mail}\nContent-Type:text/html;charset=UTF-8 \nX-Mailer: PHP/".phpversion( ) );
					}else{
						foreach($bcc AS $key=>$bccmail){
							$bccs .= $bccs? ",".$bccmail:$bccmail;
						}
						mail( $to, $subject, $message, "From: {$owner_m_mail}\nReply-To: {$owner_m_mail}\nBcc: {$bccs}\nContent-Type:text/html;charset=UTF-8 \nX-Mailer: PHP/".phpversion( ) );
					}
					exit;
					//echo "Mailer Error: " . $mail->ErrorInfo;exit;
				} else {         
					return 0;
				}
}

function send222( $to, $from, $subject, $message, $bcc="" )
{
				require_once('class.phpmailer.old.php');
				$owner_m_smtp = $GLOBALS['CONF']['owner_m_smtp'];
				$owner_m_user = $GLOBALS['CONF']['owner_m_user'];
				$owner_m_pass = $GLOBALS['CONF']['owner_m_pass'];
				$owner_m_check = $GLOBALS['CONF']['owner_m_check'];
				$owner_m_mail = $GLOBALS['CONF']['owner_m_mail'];
				$owner_m_ssl = $GLOBALS['CONF']['owner_m_ssl'];
				$owner_m_port = $GLOBALS['CONF']['owner_m_port'];
				$ownersys = $GLOBALS['CONF']['ownersys'];
				$smtp = $owner_m_smtp;
				$check = $owner_m_check;
				$toname = "Rema 會員";
				$fromname = $subject;
				list($from) = explode(",",$from);
				
				$froms = explode("<",$from);
				if($froms[1] != ""){
					$froms[1] = str_replace(">","",$froms[1]);
					$from = $froms[1];
					$fromname = $froms[0];
				}
				$tos = explode("<",$to);
				if($tos[1] != ""){
					$tos[1] = str_replace(">","",$tos[1]);
					$to = $tos[1];
					$toname = $tos[0];
				}
				
				/**/
				$mail = new PHPMailer();
				$mail->CharSet = "utf-8";
				$mail->Encoding = "base64";
				$mail->SetLanguage('zh');
				$mail->IsSMTP(); // telling the class to use SMTP
				//$mail->SMTPDebug = 1;
				if($check)
				{
					$mail->SMTPAuth   = true;
					$mail->Username   = $owner_m_user;
					$mail->Password   = $owner_m_pass;
				}
				if($owner_m_ssl){
					$mail->SMTPSecure = "ssl";
				}
				$mail->Host       = $smtp;
				$mail->Port       = $owner_m_port? $owner_m_port:'25';
				$mail->SetFrom($from, $fromname); //寄件者信箱
				$mail->AddReplyTo($from, $fromname);//回信的信箱
				$mail->AddAddress($to, $toname);
				if($bcc){
					foreach($bcc AS $key=>$bccmail){
							$mail->AddBCC($bccmail);
					}
				}
				$mail->isHTML(true);
				$mail->Subject    = $subject;
				//$mail->AltBody    = "觀看這封信件, 請使用可觀看 HTML編碼的 email瀏覽器!"; // optional, comment out and test
				$body = $message;
				$mail->MsgHTML($body);
				if(!$mail->Send()) {
					//修正寄件人outlook中文亂碼
					$owner_m_mail = "=?UTF-8?B?".base64_encode( $fromname )."?=<".$owner_m_mail.">";
					$to = "=?UTF-8?B?".base64_encode( $toname )."?=<".$to.">";
					
					if(!$bcc){
						mail( $to, $subject, $message, "From: {$owner_m_mail}\nReply-To: {$owner_m_mail}\nContent-Type:text/html;charset=UTF-8 \nX-Mailer: PHP/".phpversion( ) );
					}else{
						foreach($bcc AS $key=>$bccmail){
							$bccs .= $bccs? ",".$bccmail:$bccmail;
						}
						mail( $to, $subject, $message, "From: {$owner_m_mail}\nReply-To: {$owner_m_mail}\nBcc: {$bccs}\nContent-Type:text/html;charset=UTF-8 \nX-Mailer: PHP/".phpversion( ) );
					}
					exit;
					//echo "Mailer Error: " . $mail->ErrorInfo;exit;
				} else {         
					return 0;
				}
				/*if(!$mail->send()) {
				    echo 'Message could not be sent.';
				    echo 'Mailer Error: ' . $mail->ErrorInfo;
				} else {
				    //echo 'Message has been sent';
				}*/
}

function send2( $to, $from, $subject, $message, $bcc="" )
{
				require_once('class.phpmailer.old.php');
				$owner_m_smtp = $GLOBALS['CONF']['owner_m_smtp'];
				$owner_m_user = $GLOBALS['CONF']['owner_m_user'];
				$owner_m_pass = $GLOBALS['CONF']['owner_m_pass'];
				$owner_m_check = $GLOBALS['CONF']['owner_m_check'];
				$owner_m_mail = $GLOBALS['CONF']['owner_m_mail'];
				$owner_m_ssl = $GLOBALS['CONF']['owner_m_ssl'];
				$owner_m_port = $GLOBALS['CONF']['owner_m_port'];
				$ownersys = $GLOBALS['CONF']['ownersys'];
				$smtp = $owner_m_smtp;
				$check = $owner_m_check;
				$toname = "Rema 會員";
				$fromname = $subject;
				list($from) = explode(",",$from);
				
				$froms = explode("<",$from);
				if($froms[1] != ""){
					$froms[1] = str_replace(">","",$froms[1]);
					$from = $froms[1];
					$fromname = $froms[0];
				}
				$tos = explode("<",$to);
				if($tos[1] != ""){
					$tos[1] = str_replace(">","",$tos[1]);
					$to = $tos[1];
					$toname = $tos[0];
				}
				
				/**/
				$mail = new PHPMailer();
				$mail->CharSet = "utf-8";
				$mail->Encoding = "base64";
				$mail->SetLanguage('zh');
				$mail->Host       = "localhost";
				$mail->Port       = "25";
				$mail->SetFrom($from, $fromname); //寄件者信箱
				$mail->AddReplyTo($from, $fromname);//回信的信箱
				$mail->AddAddress($to, $toname);
				if($bcc){
					foreach($bcc AS $key=>$bccmail){
							$mail->AddBCC($bccmail);
					}
				}
				$mail->isHTML(true);
				$mail->Subject    = $subject;
				//$mail->AltBody    = "觀看這封信件, 請使用可觀看 HTML編碼的 email瀏覽器!"; // optional, comment out and test
				$body = $message;
				$mail->MsgHTML($body);
				if(!$mail->Send()) {
					//修正寄件人outlook中文亂碼
					$owner_m_mail = "=?UTF-8?B?".base64_encode( $fromname )."?=<".$owner_m_mail.">";
					$to = "=?UTF-8?B?".base64_encode( $toname )."?=<".$to.">";
					
					if(!$bcc){
						mail( $to, $subject, $message, "From: {$owner_m_mail}\nReply-To: {$owner_m_mail}\nContent-Type:text/html;charset=UTF-8 \nX-Mailer: PHP/".phpversion( ) );
					}else{
						foreach($bcc AS $key=>$bccmail){
							$bccs .= $bccs? ",".$bccmail:$bccmail;
						}
						mail( $to, $subject, $message, "From: {$owner_m_mail}\nReply-To: {$owner_m_mail}\nBcc: {$bccs}\nContent-Type:text/html;charset=UTF-8 \nX-Mailer: PHP/".phpversion( ) );
					}
					exit;
				} else {         
					return 0;
				}
}

?>