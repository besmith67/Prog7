<?php

  session_start();
if(!isset($_SESSION["coachid"])){ // if "username" (email) is not set,
	session_destroy();			// end session
	header('Location: login.php');     // redirect to login page
	exit;
}  

// include the class that handles database connections
require "../database/database.php";

// include the class containing functions/methods for "player" table
require "player.class.php";
$player = new Player();
 
// set active record field values, if any 
// (field values not set for display_list and display_create_form)
if(isset($_GET["player_id"]))          $player_id = $_GET["player_id"]; 
if(isset($_POST["player_name"]))       $player->player_name = htmlspecialchars($_POST["player_name"]);
if(isset($_POST["points"]))      $player->points = htmlspecialchars($_POST["points"]);
if(isset($_POST["assists"]))     $player->assists = htmlspecialchars($_POST["assists"]);
if(isset($_POST["rebounds"]))     $player->rebounds = htmlspecialchars($_POST["rebounds"]);
if(isset($_POST["steals"]))     $player->steals = htmlspecialchars($_POST["steals"]);

$coach_id = $_SESSION["coachid"];
$player->coach_id = $coach_id;

// "fun" is short for "function" to be invoked 
if(isset($_GET["fun"])) $fun = $_GET["fun"];
else $fun = "display_list"; //default function if not specified

switch ($fun) {
    case "display_list":        $player->list_records($coach_id);
        break;
    case "display_create_form": $player->create_record(); 
        break;
    case "display_read_form":   $player->read_record($player_id, $coach_id); 
        break;
    case "display_update_form": $player->update_record($player_id, $coach_id);
        break;
    case "display_delete_form": $player->delete_record($player_id, $coach_id); 
        break;
    case "insert_db_record":    $player->insert_db_record($coach_id); 
        break;
    case "update_db_record":    $player->update_db_record($player_id);
        break;
    case "delete_db_record":    $player->delete_db_record($player_id);
        break;
    default: 
        echo "Error: Invalid function call (player.php)";
        exit();
        break;
}

