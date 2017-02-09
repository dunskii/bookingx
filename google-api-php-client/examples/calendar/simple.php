<?php

require_once '../../src/Google_Client.php';
require_once '../../src/contrib/Google_CalendarService.php';
session_start();

$client = new Google_Client();
$client->setApplicationName("Google Calendar PHP Starter Application");
//print_r($client);exit;
// Visit https://code.google.com/apis/console?api=calendar to generate your
// client id, client secret, and to register your redirect uri.
// $client->setClientId('insert_your_oauth2_client_id');
// $client->setClientSecret('insert_your_oauth2_client_secret');
// $client->setRedirectUri('insert_your_oauth2_redirect_uri');
// $client->setDeveloperKey('insert_your_developer_key');

 $client->setClientId('935351891856-0ru57ev8q13ugp3v73co8kf6inun34kp.apps.googleusercontent.com');
 $client->setClientSecret('8ZbDiWPCAlrDFGGC4TSIU_SX');
 $client->setRedirectUri('http://booking.php-dev.in/wp-content/plugins/yfbizbooking/google-api-php-client/examples/calendar/simple.php');
 //$client->setDeveloperKey('AIzaSyAbZ5lr4XKSYPGGPGHvjByfLg8u5JnDRxE');

$cal = new Google_CalendarService($client);
if (isset($_GET['logout'])) {
  unset($_SESSION['token']);
}

if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['token'] = $client->getAccessToken();
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()) {
  $calList = $cal->calendarList->listCalendarList();
  print "<h1>Calendar List</h1><pre>" . print_r($calList, true) . "</pre>";

	$event = new Google_Event();
	$event->setSummary('Appointment');
	$event->setLocation('Somewhere');
	$start = new Google_EventDateTime();
	$start->setDateTime('2013-06-25T16:44:37+05:30');
	$event->setStart($start);
	$end = new Google_EventDateTime();
	$end->setDateTime('2013-06-26T16:44:37+05:30');
	$event->setEnd($end);
	
	$event->attendees = $attendees;
	$createdEvent = $cal->events->insert('primary', $event);
	echo "Event Created";
	print_r($createdEvent);
	echo $createdEvent->getId();


$_SESSION['token'] = $client->getAccessToken();
} else {
  $authUrl = $client->createAuthUrl();
  print "<a class='login' href='$authUrl'>Connect Me!</a>";
}
