<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once BASE_PATH . '/src/Controllers/TravelStoryController.php';

$controller = new TravelStoryController();
$id = $_GET['id'] ?? null;
$controller->show($id);
