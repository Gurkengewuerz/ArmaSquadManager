<?php
////////////////////////////////////////////////////////////////////////////////////
//                                                                                
// Code made by Marcuz,                             
// Feel free to modify, but remember to credit me as the original author!                                                                    
//                                                                                
////////////////////////////////////////////////////////////////////////////////////

// This file might be messy and unexplainable, pending cleanup but hey, it works?

	$_SESSION['loggedin'] = 'false';
	$_SESSION['loggedin_user'] = 'false';
	SESSION_START();
	include_once('inc/FlashMessages.class.php');
	require_once('config.php');
	require_once('inc/rank_images.php');
	$message = new FlashMessages();
	$xml = simplexml_load_file("squad.xml");
	if (!$xml) {
		die('Couldn\'t find squad.xml!');
	}

	if(isset($_POST['squad_submit'])){
		if(count($xml)){
		   	$result = $xml->xpath("//squad");
		   	foreach($result as $squad_info){
		   		$dom=dom_import_simplexml($squad_info);	
		   		if($_POST['squad_tag'] != ''){
		   			$squad_info['nick'] = $_POST['squad_tag'];
		   		} elseif ($_POST['squad_tag'] == ''){
		   			$squad_info['nick'] = $squad_info['nick'];
		   		}
		   		if($_POST['squad_name'] != ''){
		   			$squad_info->name = $_POST['squad_name'];
					$squad_info->title = $_POST['squad_name'];
		   		} elseif ($_POST['squad_name'] == ''){
		   			$squad_info->name = $squad_info->name;
					$squad_info->title = $squad_info->name;
		   		}
		   		if($_POST['squad_email'] != ''){
		   			$squad_info->email = $_POST['squad_email'];
		   		} elseif ($_POST['squad_email'] == ''){
		   			$squad_info->email = $squad_info->email;
		   		}
		   		if($_POST['squad_web'] != ''){
		   			$squad_info->web = $_POST['squad_web'];
		   		} elseif ($_POST['squad_web'] == ''){
		   			$squad_info->web = $squad_info->web;
		   		}
		   		if($_POST['squad_picture'] != ''){
		   			$squad_info->picture = $_POST['squad_picture'];
		   		} elseif ($_POST['squad_picture'] == ''){
		   			$squad_info->picture = $squad_info->picture;
		   		}

		   		$dom = new DOMDocument("1.0");
				$dom->preserveWhiteSpace = false;
				$dom->formatOutput = true;
				$dom->loadXML($xml->asXML());
				//echo $dom->saveXML();
				$dom->save('squad.xml');
		   	}
		}
	} 
	
if(isset($_POST['submit']) && $_POST['addInput_UID'] == NULL && $_POST['addInput_IGN'] != NULL) {
		$message->add('danger', "You need to enter your UID!");
	} elseif(isset($_POST['submit']) && $_POST['addInput_IGN'] == NULL && $_POST['addInput_UID'] != NULL) {
		$message->add('danger', "You need to enter your ingame name!");
	} elseif(isset($_POST['submit']) && $_POST['addInput_IGN'] == NULL && $_POST['addInput_UID'] == NULL){
		$message->add('danger', "You need to enter your UID and ingame name!");
	} elseif(isset($_POST['submit']) && strlen($_POST['addInput_UID']) > 20 or strlen($_POST['addInput_IGN']) > 20 or strlen($_POST['addInput_Name']) > 20 or strlen($_POST['addInput_IM']) > 20){
		$message->add('danger', "UID, IGN, Name or IM can't exceed 20 characters!");
	} elseif(isset($_POST['submit']) && strlen($_POST['addInput_Email']) > 30){
		$message->add('danger', "Email can't exceed 30 characters!");
	} elseif(isset($_POST['submit'])){
		$UID = $_POST['addInput_UID'];
		$IGN = $_POST['addInput_IGN'];
		if(isset($_POST['addInput_Name'])){
			$Name = $_POST['addInput_Name'];
		} if($_POST['addInput_Name'] == NULL) {
			$Name = 'N/A';
		}
		if(isset($_POST['addInput_Email'])){
			$Email = $_POST['addInput_Email'];
		} if($_POST['addInput_Email'] == NULL) {
			$Email = 'N/A';
		}
		if(isset($_POST['addInput_ICQ'])){
			$ICQ = $_POST['addInput_ICQ'];
		} if($_POST['addInput_ICQ'] == NULL) {
			$ICQ = 'N/A';
		}
		if($_POST['addInput_Remark'] != '' && $enable_ranks == "true"){
			$Remark = str_replace("_", " ", $ranks[0]) . " - " . $_POST['addInput_Remark'];
		} elseif($_POST['addInput_Remark'] == '' && $enable_ranks == "true") {
			$Remark = str_replace("_", " ", $ranks[0]);
		} elseif($_POST['addInput_Remark'] != '' && $enable_ranks == "false") {
			$Remark = $_POST['addInput_Remark'];
		} elseif($_POST['addInput_Remark'] == '' && $enable_ranks == "true") {
			$Remark = "";
		}

		$member = $xml->addChild('member');
		$member->addAttribute('id', $UID);
		$member->addAttribute('nick', $IGN);
		$member->addChild('name', $Name);
		$member->addChild('email', $Email);
		$member->addChild('icq', $ICQ);
		if($enable_remark == 'true'){
			$member->addChild('remark', $Remark);
		} else {
			$member->addChild('remark', str_replace("_", " ", $ranks[0]));
		}
		//$xml->asXML('squad.xml');

		$dom = new DOMDocument("1.0");
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($xml->asXML());
		//echo $dom->saveXML();
		$dom->save('squad.xml');
		$message->add('success', "Successfully added to the squad!");
	}
	
	if(isset($_POST['submit_admin']) && $_POST['password_admin'] == $admin_pass){
		$message->add('success', "Successfully signed in as admin!");
		$_SESSION['loggedin'] = 'true';
	} elseif(isset($_POST['submit_admin']) && $_POST['password_admin'] != $admin_pass) {
		$message->add('danger', "Wrong password!");
	}

	if(isset($_POST['logout_admin'])){
		header('location: logout.php');
	}

	if(isset($_POST['remove_submit'])){
		foreach($xml as $seg){
			if($seg['id'] == $_POST['remove_hidden'] && $seg['nick'] == $_POST['remove_hidden2']){
				$remove_UID = $_POST['remove_hidden'];
				$remove_NICK = $_POST['remove_hidden2'];

				$dom=dom_import_simplexml($seg);
      		    $dom->parentNode->removeChild($dom);

      		   	$dom = new DOMDocument("1.0");
				$dom->preserveWhiteSpace = false;
				$dom->formatOutput = true;
				$dom->loadXML($xml->asXML());
				//echo $dom->saveXML();
				$dom->save('squad.xml');
			}
		}
	}

	if(isset($_POST['rank_submit']) && isset($_POST['rank_speciality']) && isset($_POST['new_ign'])){
		foreach($xml as $seg){
			if($seg['id'] == $_POST['select_hidden']){
				$dom=dom_import_simplexml($seg);
				$newrank = str_replace("_", " ", $_POST['rank_select']);
				$speciality =$_POST['rank_speciality'];
				$newIGN =$_POST['new_ign'];
				if(!empty($speciality)){
					$seg->remark = $speciality;	
				}
				if(!empty($newIGN)){
					$seg['nick'] = $newIGN;
				}
				
			 	$dom = new DOMDocument("1.0");
				$dom->preserveWhiteSpace = false;
				$dom->formatOutput = true;
				$dom->loadXML($xml->asXML());
				//echo $dom->saveXML();
				$dom->save('squad.xml');
			}
		}
	}
	
	if(isset($_GET['pid'])){
		include('inc/password_generator.php');
		die();
	}

?>

<html>

<head>
	<title>[FOne] Squad Manager</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css">
	<link rel="shortcut icon" href="favicon.ico" />
	<!-- Latest compiled and minified JavaScript -->
	<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
	<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
</head>

<body class="custom-body">
<div class="squad_container">
	<div class="row">
		<div class="col-lg-4">
			<div class="left_box">
				<div class="header_logo">
					Squad Logo
				</div>
				<div class="logo_bg">
					<div class="logo">
						<img src="<?php echo $image_url; ?>" width="200px" height="200px"></img>
					</div>
				</div>
				<div class="header_left">
					Squad Information
				</div>
				<div class="box">
					<form name="squad_form" method="POST">
					<?php 	
					if($_SESSION['loggedin']) {
						if(count($xml)){
							$result = $xml->xpath("//squad");
							foreach($result as $squad_info){
								echo'
								<input class="addInput" name="squad_tag" type="text" placeholder="Tag: '.$squad_info['nick'].'">
								<input class="addInput" name="squad_name" type="text" placeholder="Name: '.$squad_info->name.'">
								<input class="addInput" name="squad_email" type="text" placeholder="Email: '. $squad_info->email.'">
								<input class="addInput" name="squad_web" type="text" placeholder="Web: '.$squad_info->web.'">
								<input class="addInput" name="squad_picture" type="text" placeholder="Picture: '.$squad_info->picture.'">
								<input class="addBtn" type="submit" value="Save" name="squad_submit">';
							}
						}
					} elseif(!$_SESSION['loggedin']){
						if(count($xml)){
							$result = $xml->xpath("//squad");
							foreach($result as $squad_info){
								echo '
								Clantag = [' .$squad_info["nick"]. ']<br>
								Name = ' . $squad_info->name .'<br>
								Email = <a href="mailto:help@unknown.tld"> Email</a><br>
								Internetseite = <a href="http://www.mysite.tld/"> Homepage</a>
								<br><font color="green"><b>Player ID</b></font> = <a href="http://community.bistudio.com/wiki/squad.xml#How_to_get_your_Player-UID">How to find it</a>
								<br><br>
								<b>Squad URL - Dieser Link kommt  in dein Profil:</b><br>' . $squad_url;
							}
						}
					}
					?>
					</form>
				</div>
				<?php
				if($_SESSION['loggedin']) {
				?>
				<div class="header_left">
					Spieler Hinzufügen
				</div>
				<div class="box">
					<?php if(isset($_POST['submit'])){ $message->display(); } ?>
					<form name="regform" method="POST">
						<input class="addInput" type="text" name="addInput_UID" placeholder="* Player ID">
						<input class="addInput" type="text" name="addInput_IGN" placeholder="* Ingame Name(Muss 1:1 sein)">
						<input class="addInput" type="text" name="addInput_Name" placeholder="Name">
						<input class="addInput" type="text" name="addInput_Email" placeholder="Email Addresse">
						<input class="addInput" type="text" name="addInput_ICQ" placeholder="IM/Skype/ICQ">
						<?php
							if($enable_remark == 'true'){
								echo '<input class="addInput" type="text" name="addInput_Remark" placeholder="Bemerkung">';
							}
						?>
						<input class="addBtn" type="submit" name="submit">
					</form>
				</div>
				<?php
				}
				?>
				<div class="header_left">
					Administrator Login
				</div>
				<div class="box">
					<?php if(isset($_POST['submit_admin'])){ $message->display(); } ?>
					<?php if(!$_SESSION['loggedin']){ ?>
					<form name="adminform" method="POST">
						<input class="addInput" type="password" name="password_admin" placeholder="Passwort">
						<input class="addBtn" type="submit" name="submit_admin">
					</form>
					<?php } elseif($_SESSION['loggedin']) { ?>
					<form name="adminform" method="POST">
					You are already signed in as admin!<br>
					<input class="addBtn" type="submit" value="Sign Out" name="logout_admin">
					</form>
					<?php } ?>
				</div>
				<div class="squad_footer">
				&copy; Niklas 
					<div style="font-size:.8em"> Marcuz </div>
				</div>
			</div>
		</div>
		<div class="col-lg-8">
			<div class="squad_box">
				<div class="header_squad">
					Squad Mitglieder
				</div>
				<div class="squad_content">
					<table class="table-custom table-striped-custom" width="100%">
						<thead>
							<th>
								User ID
							</th>
							<th>
								Nick
							</th>
							<th>
								IM
							</th>
							<?php
							if($_SESSION['loggedin']){
								echo "<th>Action</th>";
								echo "<th></th>";
								echo "<th></th>";
							}
							?>
						</thead>
						<tbody>
							<?php
								foreach($ranks as $rankslist) {
									$rank_list .= "<option value=".$rankslist.">".str_replace("_", " ", $rankslist)."</option>";
								}
								if(count($xml)){
									$result = $xml->xpath("//member");
									foreach($result as $member){
										$members_uid = $member["id"];
										$members_name = $member["nick"];
										$members_im = $member->icq;
										$members_remark = $member->remark;
										if(!$_SESSION['loggedin']){
										echo "<tr>
										<td>". $members_uid ."</td>
										<td>". $members_name ."</td>
										<td>". $members_im ."</td>
										</tr>";
										}
										
										if ($_SESSION['loggedin'] && $enable_ranks != 'true'){
										echo "<tr>
										<td>". $members_uid ."</td>
										<td>". $members_name ."</td>
										<td>". $members_im ."</td>
										<form name='promoteform' method='POST'>
										<input type='hidden' name='select_hidden' value='". $members_uid ."'>
										<td style='margin-top: 10px;'><input class='adminInput' type='text' placeholder=' " . $members_name . "' name='new_ign'></td>
										<td style='margin-top: 10px;'><input class='adminInput' type='text' placeholder=' " . $members_remark . "' name='rank_speciality'></td>
										</tr>";		
										}

										if(!$_SESSION['loggedin']){
										echo '<tr class="remark">
										<td></td>
										<td><i>-"' . str_replace("Banned", "<font color='red'><b>Banned</b></font>", $members_remark) . '"</i></td>
										<td></td>
										</tr>';
										} elseif($_SESSION['loggedin']) {
										echo '<tr class="remark">
										<td></td>
										<td><i>-"' . str_replace("Banned", "<font color='red'><b>Banned</b></font>", $members_remark) . '"</i></td>
										<td></td>
										<input type="hidden" name="remove_hidden" value="'. $members_uid .'">
										<input type="hidden" name="remove_hidden2" value="'. $members_name .'">
										<td><input class="addBtn_danger" type="submit" value="Remove" name="remove_submit"></td>
										<td style="margin-top: 10px;""><input class="addBtn_success" type="submit" value="Submit" name="rank_submit"></td>
										
										
										<div class="modal fade" id="Remove">
											  <div class="modal-dialog">
												<div class="modal-content">
												  <div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
													<h4 class="modal-title">Are you sure?</h4>
												  </div>
												  <div class="modal-body">
													<p>Are you sure you want to remove this person from the squad?</p>
												  </div>
												  <div class="modal-footer">
													<button type="button" class="addBtn_danger" data-dismiss="modal">No</button>
													<input class="addBtn_success" type="submit" value="Yes" name="remove_submit">
												  </div>
												</div><!-- /.modal-content -->
											  </div><!-- /.modal-dialog -->
										</div><!-- /.modal -->
										</form>
										<td></td>
										</tr>';	
										}
									}
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	</div>
</body>


</html>