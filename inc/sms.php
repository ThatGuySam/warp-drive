<?php

function getSMS( $trigger_key ){
	
	$replies_field = get_field('replies', "8668");
	
	$replies = array();
	
	foreach ($replies_field as $key => $value) {
		$trigger = $replies_field[$key]['trigger'];
		$trigger = preg_replace("/[^A-Za-z ]/", '', $trigger);
		$replies[$trigger] = $replies_field[$key]['response'];
	}
	
	$replies = array_change_key_case($replies, CASE_LOWER);
	
//	$trigger_key = strtolower($_REQUEST['Body']);
	
	
	if ( array_key_exists($trigger_key, $replies) ) {
    	$message = $replies[$trigger_key];
	} else {
		$message = "Please retype and try again";
	}
	
	
	
	return $message;
}


function sendSMS($trigger_key, $number) {

	require('static/sms/Services/Twilio.php'); 
 
	$account_sid = get_site_option( 'twilio_sid' );
	$auth_token = get_site_option( 'twilio_tkn' );
	$client = new Services_Twilio($account_sid, $auth_token);
	
	$message = getSMS( $trigger_key );
	 
	$output = $client->account->messages->create(array( 
		'To' => "+1".$number, 
		'From' => "+19182233950", 
		'Body' => $message,   
	));

}

function sendMessageSMS($message, $number) {

	require('static/sms/Services/Twilio.php'); 
 
	$account_sid = get_site_option( 'twilio_sid' );
	$auth_token = get_site_option( 'twilio_tkn' );
	$client = new Services_Twilio($account_sid, $auth_token);
	
//	$message = getSMS( $trigger_key );
	 
	$output = $client->account->messages->create(array( 
		'To' => "+1".$number, 
		'From' => "+19182233950", 
		'Body' => $message,   
	));

}


class SMS {
	static $add_script;

	static function init() {
		add_shortcode('sms', array(__CLASS__, 'handle_shortcode'));

		add_action('init', array(__CLASS__, 'sms_register_script'));
		add_action('wp_footer', array(__CLASS__, 'sms_print_script'));
		
		add_action('wp_footer', array(__CLASS__, 'sms_internal_script') );
	}

	static function handle_shortcode($atts) {
		self::$add_script = true;
		
		extract( shortcode_atts( array(
			'key' => false,
		), $atts, 'sms' ) );
		
		
		ob_start();
		?>
			<div class="phone-share mode-closed" data-key="<?php echo $key; ?>">
				<input class="phone-box" type="text" placeholder="(918) 555-4422"/>
				<span class="inner-button"></span>
				<span class="front-button" data-message="Share to Phone"></span>
			</div>
		<?php
		$content = ob_get_clean();
			
		return $content;
	}

	static function sms_register_script() {
		//js
		wp_register_script('masked-input', '//cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.3.1/jquery.maskedinput.min.js', array('jquery'), '1.3.1', true);
	}

	static function sms_print_script() {
		if ( ! self::$add_script )
			return;
			
			//JS
			wp_print_scripts('masked-input');
	}
	
	static function sms_internal_script() {
		if ( ! self::$add_script )
			return;
		?>
			
			<script type="text/javascript">
				if ( undefined !== window.jQuery ) { jQuery(function ($) { 'use strict';
					
					var selectorStart = "mode-";
					
					var status = "closed";
					
					$.fn.findSelector = function(str, strArray) {
					    for (var j=0; j<strArray.length; j++) {
					        if (strArray[j].substring(0,5) == selectorStart) return j;
					    }
					    return -1;
					}
	
					$(".phone-share").on( "click", function() {
						var smsKey = $(this).data("key");
						
						switch(status) {
						  case "closed":
						  	$(this)
						  		.addClass("opened");
						  	$(".phone-box").focus();
						  	status = "opened";
						      break;
						  case "opened":
						  	
						  	$(this)
						    	.removeClass("opened");
						    	status = "closed";
						  	break;
						  case "send":
						  	
						  	status = "sending";
						  	
						  	var request = { 
					  			"mode": "send",
					  			"smsKey": smsKey, 
					  			"toNumber": $(this).find(".phone-box").val().replace(/\D/g,'')
						  	};
						  	
						  	console.log(request);
						  	$(this)
						  		.removeClass("opened")
						  		.addClass("message-"+status);
						  	
						  	var smsRequest = $.post( "http://gutschurch.com/sms-replier", request,
						  		function(data) {
							  		 console.log( "Before: "+status );
							  		 console.log( data );
							  	})
								.done(function() {
									console.log( "Done: "+status );
									$(".phone-share").removeClass("message-"+status)
									status = "sent";
									$(".phone-share").addClass("message-"+status);
									
									console.log( "Success" );
								})
								.fail(function() {
									$(".phone-share").removeClass("message-"+status)
									status = "fail";
									$(".phone-share").addClass("message-"+status);
								})
								.always(function() {
									console.log( "Always: "+status );
									setTimeout(function(){
										$(".phone-share")
											.removeClass("valid-number message-"+status);
											status = "closed";
									}, 1000);
								});
							
							break;
						  default:
					     
						}//switch(mode)
					
						$(".phone-box")
							.mask("(999) 999-9999",{
							placeholder:"  ",
							completed:function(){
								$(".phone-share")
							  		.addClass("valid-number");
							  		status = "send";
							}
						});//.mask for .phone-box
					
					});//$(".phone-share").on( "click")
	
				}); }
			</script>
			
			<style>
				
				div.phone-share {
					display: block;
					font-size: 0.8em;
					font-size: 1rem;
					position: relative;
					width: 250px;
					height: 3em;
					border-radius: 30px;
					-webkit-user-select: none;
					-moz-user-select: none;
					-ms-user-select: none;
					user-select: none;
					background-color: #d0d0d0;
					background-image: -moz-linear-gradient(#d0d0d0, #fefbf7);
					background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#d0d0d0), to(#fefbf7));
					background-image: -webkit-linear-gradient(#d0d0d0, #fefbf7);
					background-image: -o-linear-gradient(#d0d0d0, #fefbf7);
				}
				div.phone-share:hover {
					cursor: pointer;
				}
				div.phone-share:after:hover {
					cursor: pointer;
				}
				
				input.phone-box {
					position: absolute;
					left: 3%;
					top: 15%;
					width: 75%;
					color: #ffffff;
					font-size: 1em;
					line-height: 1.75em;
					text-indent: 1em;
					margin: 0;
					padding: 0.17em 0;
					border-radius: 30px;
					background-color: #ffa120;
					background-image: -moz-linear-gradient(#ffa120, #e75800);
					background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#ffa120), to(#e75800));
					background-image: -webkit-linear-gradient(#ffa120, #e75800);
					background-image: -o-linear-gradient(#ffa120, #e75800);
					box-shadow: inset 0 3px 10px rgba(0, 0, 0, 0.5);
					border: none;
					outline: none;
				}
				input.phone-box::-webkit-input-placeholder {
					color: #ffa120;
				}
				
				input.phone-box:-moz-placeholder { /* Firefox 18- */
					color: #ffa120;
				}
				
				input.phone-box::-moz-placeholder {  /* Firefox 19+ */
					color: #ffa120;  
				}
				
				input.phone-box:-ms-input-placeholder {  
					color: #ffa120;  
				}
				div.phone-share span {
					display: block;
					position: absolute;
				}
				div.phone-share span:hover {
					cursor: pointer;
				}
				div.phone-share span.inner-button { 
					-webkit-transition: all 0.31s ease-in-out;
					-moz-transition: all 0.31s ease-in-out;
					-o-transition: all 0.31s ease-in-out;
					-ms-transition: all 0.31s ease-in-out;
					transition: all 0.31s ease-in-out;
					z-index: 1;
					width: 123px;
					height: 3.3em;
					top: -.2em;
					right: 20px;
					border-radius: 40px;
					background-color: #d7d7d7;
					background-image: -moz-linear-gradient(#d7d7d7, #a39f9e);
					background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#d7d7d7), to(#a39f9e));
					background-image: -webkit-linear-gradient(#d7d7d7, #a39f9e);
					background-image: -o-linear-gradient(#d7d7d7, #a39f9e);
					clip: rect(0.65em 40px 2.75em -10px);
					box-shadow: 0 3px 6px rgba(0, 0, 0, 0.6);
					
					-webkit-transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					   -moz-transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					    -ms-transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					     -o-transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000);
					        transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000);
					
					-webkit-transition-timing-function: cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					   -moz-transition-timing-function: cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					     -o-transition-timing-function: cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					        transition-timing-function: cubic-bezier(0.230, 1.000, 0.320, 1.000);
				}
				
				div.phone-share span.front-button {
					position: absolute;
					z-index: 3;
					padding: 0.45em;
					color: #777;
					text-shadow: 0 1px 0 rgba(255,255,255,.8);
					text-align: center;
					white-space: nowrap;
					overflow: hidden;
					width: 220px;
					height: 20px;
					top: 5px;
					right: 7px;
					border-radius: 30px;
					background-color: #f1f1f1;
					box-shadow: 0 7px 7px rgba(0, 0, 0, 0.5);
					border: 1px solid #bcb9b8;
					
					background-image: -moz-linear-gradient(#f1f1f1, #bcb9b8);
					background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#f1f1f1), to(#bcb9b8));
					background-image: -webkit-linear-gradient(#f1f1f1, #bcb9b8);
					background-image: -o-linear-gradient(#f1f1f1, #bcb9b8);
					
					-webkit-transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					   -moz-transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					    -ms-transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					     -o-transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000);
					        transition: all 500ms cubic-bezier(0.230, 1.000, 0.320, 1.000);
					
					-webkit-transition-timing-function: cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					   -moz-transition-timing-function: cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					     -o-transition-timing-function: cubic-bezier(0.230, 1.000, 0.320, 1.000); 
					        transition-timing-function: cubic-bezier(0.230, 1.000, 0.320, 1.000);
				}
				div.phone-share span.front-button:before {
					content: attr(data-message);
					/* content: "Share to Phone"; */
				}
				div.phone-share.opened span {
					display: block;
					position: absolute;
				}
				div.phone-share.opened span.inner-button {
					-webkit-transition: all 0.24s ease-in-out;
					-moz-transition: all 0.24s ease-in-out;
					-o-transition: all 0.24s ease-in-out;
					-ms-transition: all 0.24s ease-in-out;
					transition: all 0.24s ease-in-out;
					width: 70px;
				}
				div.phone-share.opened span.front-button {
					width: 60px;
				}
				div.phone-share.opened span.front-button:before {
					content: "Close";
				}
				div.phone-share.valid-number span.front-button:before {
					content: "Send";
				}
				div.phone-share.message-sending span.front-button:before {
					content: "Sending...";
				}
				div.phone-share.message-sent span.front-button:before {
					content: "Sent!";
				}
				div.phone-share.message-fail span.front-button:before {
					content: "Something went wrong";
					color: #ff0000;
				}
				div.phone-share.valid-number span.front-button {
					border: solid 1px #00aa00;
				}
				div.phone-share.valid-number span.front-button:hover {
					border: solid 1px #00ff00;
				}
				div.phone-share.valid-number span.front-button:active {
					background-color: #f1f1f1;
					background-image: none;
				}
			</style>
		<?php
	}
}

SMS::init();