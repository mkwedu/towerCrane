<?php
require_once "towerCrane.php";

$crane = new TowerCrane();

// http://{url}/api.php?command={command}
echo $crane->control($_GET['command']);
