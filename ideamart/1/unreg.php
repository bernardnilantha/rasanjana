<?php 			$applicationId = "APP_054822";
				$address="tel:A#3B4iP6Qk4ftcT14NLF5jXZai1zrW1KVL/41xfyD6jktJAnxUBv9NUJfX/kTc2bgddSf";
				if($applicationId=="APP_054822"){
					
					echo $url ="https://jobmaster.today/home/unreg/".urlencode($address);
					$ch = curl_init($url);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_POST, 1);
					//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
					//curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonObjectFields);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					echo $res = curl_exec($ch);
					curl_close($ch);
				}
				?>
